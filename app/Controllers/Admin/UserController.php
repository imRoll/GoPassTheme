<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Bought;
use App\Models\Ip;
use App\Models\Relay;
use App\Models\User;
use App\Models\DetectBanLog;
use App\Models\Shop;
use App\Services\Auth;
use App\Services\Config;
use App\Services\Mail;
use App\Utils;
use App\Utils\GA;
use App\Utils\Hash;
use App\Utils\QQWry;
use App\Utils\Radius;
use App\Utils\Tools;
use Exception;


class UserController extends AdminController
{
    public function index($request, $response, $args)
    {
        $table_config['total_column'] = array(
            'op' => '操作',
            'id' => 'ID',
            'user_name' => '用户名',
            'remark' => '备注',
            'email' => '邮箱',
            'money' => '金钱',
            'is_agent' => '代理商',
            'im_type' => '联络方式类型',
            'im_value' => '联络方式详情',
            'node_group' => '群组',
            'expire_in' => '账户过期时间',
            'class' => '等级',
            'class_expire' => '等级过期时间',
            'passwd' => '连接密码',
            'port' => '连接端口',
            'method' => '加密方式',
            'protocol' => '连接协议',
            'obfs' => '混淆方式',
            'obfs_param' => '混淆参数',
            'online_ip_count' => '在线IP数',
            'last_ss_time' => '上次使用时间',
            'used_traffic' => '已用流量/GB',
            'enable_traffic' => '总流量/GB',
            'last_checkin_time' => '上次签到时间',
            'today_traffic' => '今日流量/MB',
            'enable' => '是否启用',
            'reg_date' => '注册时间',
            'reg_ip' => '注册IP',
            'auto_reset_day' => '自动重置流量日',
            'auto_reset_bandwidth' => '自动重置流量/GB',
            'ref_by' => '邀请人ID',
            'ref_by_user_name' => '邀请人用户名',
            'top_up' => '累计充值');
        $table_config['default_show_column'] = array('op', 'id', 'user_name', 'remark', 'email');
        $table_config['ajax_url'] = 'user/ajax';
        return $this->view()->assign('table_config', $table_config)->display('admin/user/index.tpl');
    }

    public function createNewUser($request, $response, $args)
    {
        # 需要一个 userEmail
        $email = $request->getParam('userEmail');
        $email = trim($email);
        $email = strtolower($email);
        // not really user input
        //if (!Check::isEmailLegal($email)) {
        //    $res['ret'] = 0;
        //   $res['msg'] = '邮箱无效';
        //   return $response->getBody()->write(json_encode($res));
        //}
        // check email
        $user = User::where('email', $email)->first();
        if ($user != null) {
            $res['ret'] = 0;
            $res['msg'] = '邮箱已经被注册了';
            return $response->getBody()->write(json_encode($res));
        }
        // do reg user
        $user = new User();
        $pass = Tools::genRandomChar();
        $user->user_name = $email;
        $user->email = $email;
        $user->pass = Hash::passwordHash($pass);
        $user->passwd = Tools::genRandomChar(6);
        $user->port = Tools::getAvPort();
        $user->t = 0;
        $user->u = 0;
        $user->d = 0;
        $user->method = Config::get('reg_method');
        $user->protocol = Config::get('reg_protocol');
        $user->protocol_param = Config::get('reg_protocol_param');
        $user->obfs = Config::get('reg_obfs');
        $user->obfs_param = Config::get('reg_obfs_param');
        $user->forbidden_ip = Config::get('reg_forbidden_ip');
        $user->forbidden_port = Config::get('reg_forbidden_port');
        $user->im_type = 2;
        $user->im_value = $email;
        $user->transfer_enable = Tools::toGB(Config::get('defaultTraffic'));
        $user->invite_num = Config::get('inviteNum');
        $user->auto_reset_day = Config::get('reg_auto_reset_day');
        $user->auto_reset_bandwidth = Config::get('reg_auto_reset_bandwidth');
        $user->money = 0;
        $user->class_expire = date('Y-m-d H:i:s', time() + Config::get('user_class_expire_default') * 3600);
        $user->class = Config::get('user_class_default');
        $user->node_connector = Config::get('user_conn');
        $user->node_speedlimit = Config::get('user_speedlimit');
        $user->expire_in = date('Y-m-d H:i:s', time() + Config::get('user_expire_in_default') * 86400);
        $user->reg_date = date('Y-m-d H:i:s');
        $user->reg_ip = $_SERVER['REMOTE_ADDR'];
        $user->plan = 'A';
        $user->theme = Config::get('theme');

        $groups = explode(',', Config::get('ramdom_group'));

        $user->node_group = $groups[array_rand($groups)];

        $ga = new GA();
        $secret = $ga->createSecret();

        $user->ga_token = $secret;
        $user->ga_enable = 0;
        if ($user->save()) {
            $res['ret'] = 1;
            $res['msg'] = '新用户注册成功 用户名: ' . $email . ' 随机初始密码: ' . $pass;
            $res['email_error'] = 'success';
            $subject = Config::get('appName') . '-新用户注册通知';
            $to = $user->email;
            $text = '您好，管理员已经为您生成账户，用户名: ' . $email . '，登录密码为：' . $pass . '，感谢您的支持。 ';
            try {
                Mail::send($to, $subject, 'newuser.tpl', [
                    'user' => $user, 'text' => $text,
                ], [
                ]);
            } catch (Exception $e) {
                $res['email_error'] = $e->getMessage();
            }
            return $response->getBody()->write(json_encode($res));
        }
        $res['ret'] = 0;
        $res['msg'] = '未知错误';
        return $response->getBody()->write(json_encode($res));
    }

    public function buy($request, $response, $args)
    {
        #shop 信息可以通过 App\Controllers\UserController:shop 获得
        # 需要shopId，disableothers，autorenew,userEmail

        $shopId = $request->getParam('shopId');
        $shop = Shop::where('id', $shopId)->where('status', 1)->first();
        $disableothers = $request->getParam('disableothers');
        $autorenew = $request->getParam('autorenew');
        $email = $request->getParam('userEmail');
        $user = User::where('email', '=', $email)->first();
        if ($user == null) {
            $result['ret'] = 0;
            $result['msg'] = '未找到该用户';
            return $response->getBody()->write(json_encode($result));
        }
        if ($shop == null) {
            $result['ret'] = 0;
            $result['msg'] = '请选择套餐';
            return $response->getBody()->write(json_encode($result));
        }
        if ($disableothers == 1) {
            $boughts = Bought::where('userid', $user->id)->get();
            foreach ($boughts as $disable_bought) {
                $disable_bought->renew = 0;
                $disable_bought->save();
            }
        }
        $bought = new Bought();
        $bought->userid = $user->id;
        $bought->shopid = $shop->id;
        $bought->datetime = time();
        if ($autorenew == 0 || $shop->auto_renew == 0) {
            $bought->renew = 0;
        } else {
            $bought->renew = time() + $shop->auto_renew * 86400;
        }

        $price = $shop->price;
        $bought->price = $price;
        $bought->save();

        $shop->buy($user);
        $result['ret'] = 1;
        $result['msg'] = '套餐添加成功';
        return $response->getBody()->write(json_encode($result));
    }

    public function search($request, $response, $args)
    {
        $pageNum = 1;
        $text = $args['text'];
        if (isset($request->getQueryParams()['page'])) {
            $pageNum = $request->getQueryParams()['page'];
        }

        $users = User::where('email', 'LIKE', '%' . $text . '%')->orWhere('user_name', 'LIKE', '%' . $text . '%')->orWhere('im_value', 'LIKE', '%' . $text . '%')->orWhere('port', 'LIKE', '%' . $text . '%')->orWhere('remark', 'LIKE', '%' . $text . '%')->paginate(20, ['*'], 'page', $pageNum);
        $users->setPath('/admin/user/search/' . $text);

        //Ip::where("datetime","<",time()-90)->get()->delete();
        $total = Ip::where('datetime', '>=', time() - 90)->orderBy('userid', 'desc')->get();

        $userip = array();
        $useripcount = array();
        $regloc = array();

        $iplocation = new QQWry();
        foreach ($users as $user) {
            $useripcount[$user->id] = 0;
            $userip[$user->id] = array();

            $location = $iplocation->getlocation($user->reg_ip);
            $regloc[$user->id] = iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
        }

        foreach ($total as $single) {
            if (isset($useripcount[$single->userid]) && !isset($userip[$single->userid][$single->ip])) {
                ++$useripcount[$single->userid];
                $location = $iplocation->getlocation($single->ip);
                $userip[$single->userid][$single->ip] = iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
            }
        }

        return $this->view()->assign('users', $users)->assign('regloc', $regloc)->assign('useripcount', $useripcount)->assign('userip', $userip)->display('admin/user/index.tpl');
    }

    public function sort($request, $response, $args)
    {
        $pageNum = 1;
        $text = $args['text'];
        $asc = $args['asc'];
        if (isset($request->getQueryParams()['page'])) {
            $pageNum = $request->getQueryParams()['page'];
        }

        $users->setPath('/admin/user/sort/' . $text . '/' . $asc);

        //Ip::where("datetime","<",time()-90)->get()->delete();
        $total = Ip::where('datetime', '>=', time() - 90)->orderBy('userid', 'desc')->get();

        $userip = array();
        $useripcount = array();
        $regloc = array();

        $iplocation = new QQWry();
        foreach ($users as $user) {
            $useripcount[$user->id] = 0;
            $userip[$user->id] = array();

            $location = $iplocation->getlocation($user->reg_ip);
            $regloc[$user->id] = iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
        }

        foreach ($total as $single) {
            if (isset($useripcount[$single->userid]) && !isset($userip[$single->userid][$single->ip])) {
                ++$useripcount[$single->userid];
                $location = $iplocation->getlocation($single->ip);
                $userip[$single->userid][$single->ip] = iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
            }
        }

        return $this->view()->assign('users', $users)->assign('regloc', $regloc)->assign('useripcount', $useripcount)->assign('userip', $userip)->display('admin/user/index.tpl');
    }
   public function addclass($request, $response, $args)
    {
     	$user = User::find($id);
        $vip = $request->getParam('vip');
        $change_class = $request->getParam('change_class');

        $class_h = $request->getParam('class_h');
        $users = User::where('class', $vip)->where('enable', 1)->get();
          
          if ($class_h <= 0) {
             $res['ret'] = 0;
             $res['msg'] = "时长数值有误,请检查";
             return $response->getBody()->write(json_encode($res));  
            }
      
        foreach($users as $user){
		  if ($change_class == 1) {
            if ($user->class != 0) {
            	$user->class_expire=date("Y-m-d H:i:s", strtotime($user->class_expire)+$class_h*3600);
            } else {
            	$user->class_expire=date("Y-m-d H:i:s", time()+$class_h*3600);
            }
            $user->save();
          }else{
            if (time()>strtotime($user->expire_in)) {
            	$user->expire_in=date("Y-m-d H:i:s", time()+$class_h*3600);
            } else {
            	$user->expire_in=date("Y-m-d H:i:s", strtotime($user->expire_in)+$class_h*3600);
            }//如果当前时间>账户到期时间(已过期)，那么现在时间加上套餐的时间；否则在账户原有基础时间上相加   
            $user->save();
          }
          
        }
        $res['ret'] = 1;
        $res['msg'] = $vip." VIP等级时长批量增加完毕, 请查阅.";
        return $response->getBody()->write(json_encode($res)); 
      
    }
    
    public function addtraffic($request, $response, $args)
    {
     	$user = User::find($id);
        $vip = $request->getParam('vip');
        $user_traffic = $request->getParam('user_traffic');
        $users = User::where('class', $vip)->where('enable', 1)->get();
          
          if ($user_traffic <= 0) {
             $res['ret'] = 0;
             $res['msg'] = "流量数值有误,请检查";
             return $response->getBody()->write(json_encode($res));  
            }
      
        foreach($users as $user){
            $user->transfer_enable += $user_traffic * 1073741824;
            $user->save();
        }
        $res['ret'] = 1;
        $res['msg'] = $vip." VIP等级流量批量增加完毕, 请查阅.";
        return $response->getBody()->write(json_encode($res)); 
      
    }
    public function addmoney($request, $response, $args)
    {
     	$user = User::find($id);
        $vip = $request->getParam('vip');
        $user_money = $request->getParam('user_money');
        $users = User::where('class', $vip)->where('enable', 1)->get();
          
          if ($user_money <= 0) {
             $res['ret'] = 0;
             $res['msg'] = "金额数值有误,请检查";
             return $response->getBody()->write(json_encode($res));  
            }
      
        foreach($users as $user){
            $user->money += $user_money;
            $user->save();
        }
        $res['ret'] = 1;
        $res['msg'] = $vip." VIP等级余额批量增加完毕, 请查阅.";
        return $response->getBody()->write(json_encode($res)); 
      
    }
    
    public function edit($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);
        return $this->view()->assign('edit_user', $user)->display('admin/user/edit.tpl');
    }

    public function update($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);

        $email1 = $user->email;

        $user->email = $request->getParam('email');

        $email2 = $request->getParam('email');

        $passwd = $request->getParam('passwd');

        Radius::ChangeUserName($email1, $email2, $passwd);

        if ($request->getParam('pass') != '') {
            $user->pass = Hash::passwordHash($request->getParam('pass'));
            $user->clean_link();
        }

        $user->auto_reset_day = $request->getParam('auto_reset_day');
        $user->auto_reset_bandwidth = $request->getParam('auto_reset_bandwidth');
        $origin_port = $user->port;
        $user->port = $request->getParam('port');

        $relay_rules = Relay::where('user_id', $user->id)->where('port', $origin_port)->get();
        foreach ($relay_rules as $rule) {
            $rule->port = $user->port;
            $rule->save();
        }

        $user->passwd = $request->getParam('passwd');
        $user->protocol = $request->getParam('protocol');
        $user->protocol_param = $request->getParam('protocol_param');
        $user->obfs = $request->getParam('obfs');
        $user->creta = $request->getParam('creta');
        $user->obfs_param = $request->getParam('obfs_param');
        $user->is_multi_user = $request->getParam('is_multi_user');
        $user->transfer_enable = Tools::toGB($request->getParam('transfer_enable'));
        $user->invite_num = $request->getParam('invite_num');
        $user->method = $request->getParam('method');
        $user->node_speedlimit = $request->getParam('node_speedlimit');
        $user->node_connector = $request->getParam('node_connector');
        $user->ref_money = $request->getParam('ref_money');
        $user->enable = $request->getParam('enable');
        $user->is_admin = $request->getParam('is_admin');
        $user->is_agent = $request->getParam('is_agent');
        $user->ga_enable = $request->getParam('ga_enable');
        $user->node_group = $request->getParam('group');
        $user->ref_by = $request->getParam('ref_by');
        $user->remark = $request->getParam('remark');
        $user->money = $request->getParam('money');
        $user->class = $request->getParam('class');
        $user->class_expire = $request->getParam('class_expire');
        $user->expire_in = $request->getParam('expire_in');

        $user->forbidden_ip = str_replace(PHP_EOL, ',', $request->getParam('forbidden_ip'));
        $user->forbidden_port = str_replace(PHP_EOL, ',', $request->getParam('forbidden_port'));

        // 手动封禁
        $ban_time = (int) $request->getParam('ban_time');
        if ($ban_time > 0) {
            $user->enable                       = 0;
            $end_time                           = date('Y-m-d H:i:s');
            $user->last_detect_ban_time         = $end_time;
            $DetectBanLog                       = new DetectBanLog();
            $DetectBanLog->user_name            = $user->user_name;
            $DetectBanLog->user_id              = $user->id;
            $DetectBanLog->email                = $user->email;
            $DetectBanLog->detect_number        = '0';
            $DetectBanLog->ban_time             = $ban_time;
            $DetectBanLog->start_time           = strtotime('1989-06-04 00:05:00');
            $DetectBanLog->end_time             = strtotime($end_time);
            $DetectBanLog->all_detect_number    = $user->all_detect_number;
            $DetectBanLog->save();
        }

        if (!$user->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = '修改失败';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = '修改成功';
        return $response->getBody()->write(json_encode($rs));
    }

    public function delete($request, $response, $args)
    {
        $id = $request->getParam('id');
        $user = User::find($id);

        $email1 = $user->email;

        if (!$user->kill_user()) {
            $rs['ret'] = 0;
            $rs['msg'] = '删除失败';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = '删除成功';
        return $response->getBody()->write(json_encode($rs));
    }

    public function changetouser($request, $response, $args)
    {
        $userid = $request->getParam('userid');
        $adminid = $request->getParam('adminid');
        $user = User::find($userid);
        $admin = User::find($adminid);
        $expire_in = time() + 60 * 60;

        if (!$admin->is_admin || !$user || !Auth::getUser()->isLogin) {
            $rs['ret'] = 0;
            $rs['msg'] = '非法请求';
            return $response->getBody()->write(json_encode($rs));
        }

        Utils\Cookie::set([
            'uid' => $user->id,
            'email' => $user->email,
            'key' => Hash::cookieHash($user->pass, $expire_in),
            'ip' => md5($_SERVER['REMOTE_ADDR'] . Config::get('key') . $user->id . $expire_in),
            'expire_in' => $expire_in,
            'old_uid' => Utils\Cookie::get('uid'),
            'old_email' => Utils\Cookie::get('email'),
            'old_key' => Utils\Cookie::get('key'),
            'old_ip' => Utils\Cookie::get('ip'),
            'old_expire_in' => Utils\Cookie::get('expire_in'),
            'old_local' => $request->getParam('local'),
        ], $expire_in);
        $rs['ret'] = 1;
        $rs['msg'] = '切换成功';
        return $response->getBody()->write(json_encode($rs));
    }

    public function ajax($request, $response, $args)
    {
        //得到排序的方式
        $order = $request->getParam('order')[0]['dir'];
        //得到排序字段的下标
        $order_column = $request->getParam('order')[0]['column'];
        //根据排序字段的下标得到排序字段
        $order_field = $request->getParam('columns')[$order_column]['data'];
        $limit_start = $request->getParam('start');
        $limit_length = $request->getParam('length');
        $search = $request->getParam('search')['value'];

        if ($order_field == 'used_traffic') {
            $order_field = 'u + d';
        } elseif ($order_field == 'enable_traffic') {
            $order_field = 'transfer_enable';
        } elseif ($order_field == 'today_traffic') {
            $order_field = 'u +d - last_day_t';
        }

        $users = array();
        $count_filtered = 0;

        if ($search) {
            $users = User::where(
                static function ($query) use ($search) {
                    $query->where('id', 'LIKE', "%$search%")
                        ->orwhere('user_name', 'LIKE BINARY', "%$search%")
                        ->orwhere('email', 'LIKE BINARY', "%$search%")
                        ->orwhere('passwd', 'LIKE BINARY', "%$search%")
                        ->orwhere('port', 'LIKE BINARY', "%$search%")
                        ->orwhere('reg_date', 'LIKE BINARY', "%$search%")
                        ->orwhere('invite_num', 'LIKE BINARY', "%$search%")
                        ->orwhere('money', 'LIKE BINARY', "%$search%")
                        ->orwhere('ref_by', 'LIKE BINARY', "%$search%")
                        ->orwhere('method', 'LIKE BINARY', "%$search%")
                        ->orwhere('reg_ip', 'LIKE BINARY', "%$search%")
                        ->orwhere('node_speedlimit', 'LIKE BINARY', "%$search%")
                        ->orwhere('im_value', 'LIKE BINARY', "%$search%")
                        ->orwhere('class', 'LIKE BINARY', "%$search%")
                        ->orwhere('class_expire', 'LIKE BINARY', "%$search%")
                        ->orwhere('expire_in', 'LIKE BINARY', "%$search%")
                        ->orwhere('remark', 'LIKE BINARY', "%$search%")
                        ->orwhere('node_group', 'LIKE BINARY', "%$search%")
                        ->orwhere('auto_reset_day', 'LIKE BINARY', "%$search%")
                        ->orwhere('auto_reset_bandwidth', 'LIKE BINARY', "%$search%")
                        ->orwhere('protocol', 'LIKE BINARY', "%$search%")
                        ->orwhere('protocol_param', 'LIKE BINARY', "%$search%")
                        ->orwhere('obfs', 'LIKE BINARY', "%$search%")
                        ->orwhere('obfs_param', 'LIKE BINARY', "%$search%");
                }
            )
                ->orderByRaw($order_field . ' ' . $order)
                ->skip($limit_start)->limit($limit_length)
                ->get();
            $count_filtered = User::where(
                static function ($query) use ($search) {
                    $query->where('id', 'LIKE BINARY', "%$search%")
                        ->orwhere('user_name', 'LIKE BINARY', "%$search%")
                        ->orwhere('email', 'LIKE BINARY', "%$search%")
                        ->orwhere('passwd', 'LIKE BINARY', "%$search%")
                        ->orwhere('port', 'LIKE BINARY', "%$search%")
                        ->orwhere('reg_date', 'LIKE BINARY', "%$search%")
                        ->orwhere('invite_num', 'LIKE BINARY', "%$search%")
                        ->orwhere('money', 'LIKE BINARY', "%$search%")
                        ->orwhere('ref_by', 'LIKE BINARY', "%$search%")
                        ->orwhere('method', 'LIKE BINARY', "%$search%")
                        ->orwhere('reg_ip', 'LIKE BINARY', "%$search%")
                        ->orwhere('node_speedlimit', 'LIKE BINARY', "%$search%")
                        ->orwhere('im_value', 'LIKE BINARY', "%$search%")
                        ->orwhere('class', 'LIKE BINARY', "%$search%")
                        ->orwhere('class_expire', 'LIKE BINARY', "%$search%")
                        ->orwhere('expire_in', 'LIKE BINARY', "%$search%")
                        ->orwhere('remark', 'LIKE BINARY', "%$search%")
                        ->orwhere('node_group', 'LIKE BINARY', "%$search%")
                        ->orwhere('auto_reset_day', 'LIKE BINARY', "%$search%")
                        ->orwhere('auto_reset_bandwidth', 'LIKE BINARY', "%$search%")
                        ->orwhere('protocol', 'LIKE BINARY', "%$search%")
                        ->orwhere('protocol_param', 'LIKE BINARY', "%$search%")
                        ->orwhere('obfs', 'LIKE BINARY', "%$search%")
                        ->orwhere('obfs_param', 'LIKE BINARY', "%$search%");
                }
            )->count();
        } else {
            $users = User::orderByRaw($order_field . ' ' . $order)
                ->skip($limit_start)->limit($limit_length)
                ->get();
            $count_filtered = User::count();
        }

        $data = array();
        foreach ($users as $user) {
            $tempdata = array();
            //model里是casts所以没法直接 $tempdata=(array)$user
            $tempdata['op'] = '<a class="btn btn-brand" href="/admin/user/' . $user->id . '/edit">编辑</a>
                    <a class="btn btn-brand-accent" id="delete" href="javascript:void(0);" onClick="delete_modal_show(\'' . $user->id . '\')">删除</a>
                    <a class="btn btn-brand" href="/admin/user/' . $user->id . '/bought">查套餐</a>
                    <a class="btn btn-brand" id="changetouser" href="javascript:void(0);" onClick="changetouser_modal_show(\'' . $user->id . '\')">切换为该用户</a>';
            $tempdata['id'] = $user->id;
            $tempdata['user_name'] = $user->user_name;
            $tempdata['remark'] = $user->remark;
            $tempdata['email'] = $user->email;
            $tempdata['money'] = $user->money;
            $tempdata['is_agent'] = $user->is_agent;
            $tempdata['im_value'] = $user->im_value;
            switch ($user->im_type) {
                case 1:
                    $tempdata['im_type'] = '微信';
                    break;
                case 2:
                    $tempdata['im_type'] = 'QQ';
                    break;
                case 3:
                    $tempdata['im_type'] = 'Google+';
                    break;
                default:
                    $tempdata['im_type'] = 'Telegram';
                    $tempdata['im_value'] = '<a href="https://telegram.me/' . $user->im_value . '">' . $user->im_value . '</a>';
            }
            $tempdata['node_group'] = $user->node_group;
            $tempdata['expire_in'] = $user->expire_in;
            $tempdata['class'] = $user->class;
            $tempdata['class_expire'] = $user->class_expire;
            $tempdata['passwd'] = $user->passwd;
            $tempdata['port'] = $user->port;
            $tempdata['method'] = $user->method;
            $tempdata['protocol'] = $user->protocol;
            $tempdata['obfs'] = $user->obfs;
            $tempdata['obfs_param'] = $user->obfs_param;
            $tempdata['online_ip_count'] = $user->online_ip_count();
            $tempdata['last_ss_time'] = $user->lastSsTime();
            $tempdata['used_traffic'] = Tools::flowToGB($user->u + $user->d);
            $tempdata['enable_traffic'] = Tools::flowToGB($user->transfer_enable);
            $tempdata['last_checkin_time'] = $user->lastCheckInTime();
            $tempdata['today_traffic'] = Tools::flowToMB($user->u + $user->d - $user->last_day_t);
            $tempdata['enable'] = $user->enable == 1 ? '可用' : '禁用';
            $tempdata['reg_date'] = $user->reg_date;
            $tempdata['reg_ip'] = $user->reg_ip;
            $tempdata['auto_reset_day'] = $user->auto_reset_day;
            $tempdata['auto_reset_bandwidth'] = $user->auto_reset_bandwidth;
            $tempdata['ref_by'] = $user->ref_by;
            if ($user->ref_by == 0) {
                $tempdata['ref_by_user_name'] = '系统邀请';
            } else {
                $ref_user = User::find($user->ref_by);
                if ($ref_user == null) {
                    $tempdata['ref_by_user_name'] = '邀请人已经被删除';
                } else {
                    $tempdata['ref_by_user_name'] = $ref_user->user_name;
                }
            }

            $tempdata['top_up'] = $user->get_top_up();

            $data[] = $tempdata;
        }
        $info = [
            'draw' => $request->getParam('draw'), // ajax请求次数，作为标识符
            'recordsTotal' => User::count(),
            'recordsFiltered' => $count_filtered,
            'data' => $data,
        ];
        return json_encode($info, true);
    }

    public function cleanSubCache($request, $response, $args)
    {
        $id = $args['id'];
        $user_path = (BASE_PATH . '/storage/SubscribeCache/' . $id . '/');
        Tools::delDirAndFile($user_path);

        $res['ret'] = 1;
        $res['msg'] = '清理成功';

        return $this->echoJson($response, $res);
    }

    public function bought($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);
        $table_config['total_column'] = array(
            'op'         => '操作',
            'id'         => 'ID',
            'name'       => '商品名称',
            'valid'      => '是否有效期内',
            'auto_renew' => '自动续费时间',
            'reset_time' => '流量重置时间',
            'buy_time'   => '套餐购买时间',
            'exp_time'   => '套餐过期时间',
            'content'    => '商品详细内容',
        );
        $table_config['default_show_column'] = array('op', 'name', 'valid', 'reset_time');
        $table_config['ajax_url'] = 'bought/ajax';
        $shops = Shop::where('status', 1)->orderBy('name')->get();
        return $this->view()->assign('table_config', $table_config)->assign('shops', $shops)->assign('user', $user)->display('admin/user/bought.tpl');
    }

    public function bought_ajax($request, $response, $args)
    {
        $start = $request->getParam("start");
        $limit_length = $request->getParam('length');
        $id = $args['id'];
        $user = User::find($id);
        $boughts = Bought::where('userid', $user->id)->skip($start)->limit($limit_length)->orderBy('id', 'desc')->get();
        $total_conut = Bought::where('userid', $user->id)->count();
        $data = [];
        foreach ($boughts as $bought) {
            $shop = $bought->shop();
            if ($shop == null) {
                $bought->delete();
                continue;
            }
            $tempdata = [];
            $tempdata['op']          = '<a class="btn btn-brand-accent" id="delete" href="javascript:void(0);" onClick="delete_modal_show(\'' . $bought->id . '\')">删除</a>';
            $tempdata['id']          = $bought->id;
            $tempdata['name']        = $shop->name;
            $tempdata['content']     = $shop->content();
            $tempdata['auto_renew']  = ($bought->renew == 0 ? '不自动续费' : $bought->renew_date());
            $tempdata['buy_time']    = $bought->datetime();
            if ($bought->use_loop()) {
                $tempdata['valid'] = ($bought->valid() ? '有效' : '已过期');
            } else {
                $tempdata['valid'] = '-';
            }
            $tempdata['reset_time']  = $bought->reset_time();
            $tempdata['exp_time']    = $bought->exp_time();
            $data[] = $tempdata;
        }
        $info = [
            'draw' => $request->getParam('draw'),
            'recordsTotal' => $total_conut,
            'recordsFiltered' => $total_conut,
            'data' => $data
        ];
        return json_encode($info, true);
    }

    public function bought_delete($request, $response, $args)
    {
        $id = $request->getParam('id');
        $Bought = Bought::find($id);
        if (!$Bought->delete()) {
            $rs['ret'] = 0;
            $rs['msg'] = '删除失败';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = '删除成功';
        return $response->getBody()->write(json_encode($rs));
    }

    public function bought_add($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);
        $shop_id  = (int) $request->getParam('buy_shop');
        $buy_type = (int) $request->getParam('buy_type');
        if ($shop_id == '') {
            $rs['ret'] = 0;
            $rs['msg'] = '请选择套餐';
            return $response->getBody()->write(json_encode($rs));
        }
        $shop = Shop::find($shop_id);
        if ($shop == null) {
            $rs['ret'] = 0;
            $rs['msg'] = '套餐不存在';
            return $response->getBody()->write(json_encode($rs));
        }
        if ($buy_type != 0) {
            if (bccomp($user->money, $shop->price, 2) == -1) {
                $res['ret'] = 0;
                $res['msg'] = '喵喵喵~ 该用户余额不足。';
                return $response->getBody()->write(json_encode($res));
            }
            $user->money = bcsub($user->money, $shop->price, 2);
            $user->save();
        }
        $boughts = Bought::where('userid', $user->id)->get();
        foreach ($boughts as $disable_bought) {
            $disable_bought->renew = 0;
            $disable_bought->save();
        }
        $bought = new Bought();
        $bought->userid = $user->id;
        $bought->shopid = $shop->id;
        $bought->datetime = time();
        $bought->renew = 0;
        $bought->coupon = '';
        $bought->price = $shop->price;
        $bought->save();
        $shop->buy($user);
        $rs['msg'] = ($buy_type != 0 ? '套餐购买成功' : '套餐添加成功');
        $rs['ret'] = 1;
        return $response->getBody()->write(json_encode($rs));
    }
}
