<?php

//注释里请勿使用英文方括号、分号、单引号，否则迁移Config时会出错

//config迁移附注（由开发者填写本次config迁移后需要注意的地方，站长勿动）
//如需换行，直接换行即可，无需换行符
//【新增/删除】config无需写入迁移附注
$_ENV['config_migrate_notice']=
'enable_geetest_* 已变更为 enable_*_captcha
crisp已被替换为mylivechat
telegrma_qrcode被重命名为qrcode

';
$_ENV['version'] = '1';	//仅当涉及【需要修改config以外的文件】时才需要+1，站长勿动


//基本设置--------------------------------------------------------------------------------------------
$_ENV['key'] = '1145141919810';						//!!! 瞎鸡儿写，修改此key为随机字符串确保网站安全 !!!
$_ENV['debug'] =  false;								//正式环境请确保为 false
$_ENV['appName'] = 'sspanel';							//站点名称
$_ENV['baseUrl'] = 'http://url.com';					//站点地址
$_ENV['subUrl'] = $_ENV['baseUrl'] . '/link/';	//订阅地址，如需和站点名称相同，请不要修改
$_ENV['muKey'] = 'default_mu_key';								//用于校验魔改后端请求，可以随意修改，但请保持前后端一致，否则节点不能工作！
$_ENV['db_driver'] = 'mysql';							//数据库程序
$_ENV['db_host'] = 'localhost';						//数据库地址
$_ENV['db_database'] = 'sspanel';						//数据库名
$_ENV['db_username'] = 'root';							//数据库用户名
$_ENV['db_password'] = 'sspanel';						//用户名对应的密码

// 新旧首页设置--------------------------------------------------------------------------------------------
$_ENV['newIndex'] = true;	//使用新的 Node.js 开发的首页请填写 true，其他值为使用先前的首页，如您使用其他主题请保持 true

$_ENV['checkNodeIp'] = true; //是否webapi验证节点ip

// 订阅中的公告信息
// 使用数组形式，将会添加在订阅列表的顶端
// 可用于为用户推送最新地址等信息，尽可能简短且数量不宜太多
$_ENV['sub_message']       = [];
$_ENV['enable_sub_extend'] = true;      // 是否开启订阅中默认显示流量剩余以及账户到期时间以及 sub_message 中的信息
$_ENV['disable_sub_mu_port'] = false;   // 将订阅中单端口的信息去除

//邮件设置--------------------------------------------------------------------------------------------
$_ENV['mailDriver'] = 'none';	//发送邮件方式：none / mailgun / smtp / sendgrid
$_ENV['sendPageLimit']= 50;	//发信分页 解决大站发公告超时问题

# mailgun
$_ENV['mailgun_key'] = '';
$_ENV['mailgun_domain'] = '';
$_ENV['mailgun_sender'] = '';

# smtp
$_ENV['smtp_host'] = '';
$_ENV['smtp_username'] = '';
$_ENV['smtp_port'] = 465;
$_ENV['smtp_name'] = '';
$_ENV['smtp_sender'] = '';
$_ENV['smtp_passsword'] = '';
$_ENV['smtp_ssl'] = true;

# sendgrid
$_ENV['sendgrid_key'] = '';
$_ENV['sendgrid_sender'] = '';


//备份设置--------------------------------------------------------------------------------------------
$_ENV['auto_backup_email'] = '';		//接收备份的邮箱
$_ENV['auto_backup_passwd'] = '';	//备份的压缩密码
$_ENV['backup_notify'] = false;		//备份通知到TG群中


//用户注册设置-----------------------------------------------------------------------------------------
$_ENV['register_mode'] = 'open';					//注册模式。close：关闭，open：开放，invite：仅限邀请码
$_ENV['defaultTraffic'] = 1;						//用户初始流量 单位GB
$_ENV['user_expire_in_default'] = 3650;			//用户账户过期时间，在注册时设置。（天）
$_ENV['user_class_default'] = 0;					//用户注册等级，在注册时设置。
$_ENV['user_class_expire_default'] = 24;			//用户等级过期时间，在注册时设置。（小时）
$_ENV['user_conn'] = 0;							//用户注册客户端数量限制，0为不限制
$_ENV['user_speedlimit'] = 0;						//用户注册速度默认限制，0为不限制
$_ENV['reg_auto_reset_day'] = 0;					//注册时的流量重置日，0为不重置
$_ENV['reg_auto_reset_bandwidth'] = 0;				//需要重置的流量，0为不重置
$_ENV['ramdom_group'] = 0;							//注册时随机分组，注册时随机分配到的分组，多个分组请用英文半角逗号分隔。
$_ENV['reg_method'] = 'chacha20-ietf';						//注册时默认加密方式
$_ENV['reg_protocol'] = 'auth_aes128_sha1';					//注册时默认协议
$_ENV['reg_protocol_param'] = '';					//注册时默认协议参数
$_ENV['reg_obfs'] = 'http_simple';							//注册时默认混淆方式
$_ENV['reg_obfs_param'] = '';			//注册时默认混淆参数 设置单端口后 这边必须配置！填写www.jd.hk就行
$_ENV['reg_forbidden_ip'] = '127.0.0.0/8,::1/128';	//注册时默认禁止访问IP列表，半角英文逗号分割
$_ENV['min_port'] = 10000;							//用户端口池最小值
$_ENV['max_port'] = 65535;							//用户端口池最大值
$_ENV['reg_forbidden_port'] = '';					//注册时默认禁止访问端口列表，半角英文逗号分割，支持端口段
$_ENV['mu_suffix'] = 'microsoft.com';				//单端口多用户混淆参数后缀，可以随意修改，但请保持前后端一致
$_ENV['mu_regex'] = '%5m%id.%suffix';				//单端口多用户混淆参数表达式，%5m代表取用户特征 md5 的前五位，%id 代表用户id,%suffix 代表上面这个后缀。

#邀请链接
$_ENV['inviteNum'] = 10;			//注册后的邀请链接可用次数
$_ENV['invite_get_money'] = 1;		//新用户通过私人邀请链接注册时，获得奖励金额（作为初始资金）
$_ENV['invite_price'] = -1;		//用户购买邀请码所需要的价格，价格小于0时视为不开放购买
$_ENV['custom_invite_price'] = -1;		//用户定制邀请码所需要的价格，价格小于0时视为不开放购买

#邮箱验证
$_ENV['enable_email_verify'] = false;		//是否启用注册邮箱验证码
$_ENV['email_verify_ttl'] = 3600;			//邮箱验证码有效期
$_ENV['email_verify_iplimit'] = 10;		//验证码有效期内，单IP可请求验证码次数


//已注册用户设置---------------------------------------------------------------------------------------
#基础
$_ENV['checkinMin'] = 1;			//用户签到最少流量 单位MB
$_ENV['checkinMax'] = 50;			//用户签到最多流量
$_ENV['auto_clean_uncheck_days'] = -1;	        //自动清理多少天没签到的0级用户，小于等于0时关闭
$_ENV['auto_clean_unused_days'] = -1;	        //自动清理多少天没使用的0级用户，小于等于0时关闭
$_ENV['auto_clean_min_money'] = 1;		//余额低于多少的0级用户可以被清理
$_ENV['code_payback'] = 20;			//充值返利百分比
$_ENV['invite_gift'] = 2;			//邀请新用户获得流量奖励，单位G
$_ENV['enable_bought_reset'] = true;	        //购买时是否重置流量
$_ENV['enable_bought_extend'] = true;	        //购买时是否延长等级期限（同等级配套）
$_ENV['port_price'] = -1;			//用户随机重置端口所需要的价格，价格小于0时视为不开放购买
$_ENV['port_price_specify'] = -1;		//用户指明钦定端口所需要的价格，价格小于0时视为不开放购买

#高级
$_ENV['class_expire_reset_traffic'] = 0;		//等级到期时重置为的流量值，单位GB，小于0时不重置
$_ENV['account_expire_delete_days'] = -1;		//账户到期几天之后会删除账户，小于0时不删除
$_ENV['enable_kill'] = true;					//是否允许用户注销账户
$_ENV['notify_limit_mode'] = false;			//false为关闭，per为按照百分比提醒，mb为按照固定剩余流量提醒
$_ENV['notify_limit_value'] = 20;			//当上一项为per时，此处填写百分比；当上一项为mb时，此处填写流量
$_ENV['mergeSub'] = true;						//合并订阅设置 可选项 false / true

$_ENV['protocol_specify'] = true;			//允许用户自行切换加密、协议、混淆，允许请填写 true，禁止用户自行修改将使用下方配置的方案
$_ENV['keep_connect'] = false;				//是否开启用户流量耗尽后降低速率至 1Mbps 而不断网

#加密、协议、混淆切换方案
$_ENV['user_agreement_scheme'] = [
    ['id'=>1,'name'=>'SS 推荐配置','method'=>'chacha20-ietf-poly1305','protocol'=>'origin','obfs'=>'plain'],
    ['id'=>2,'name'=>'SSR 推荐配置','method'=>'chacha20-ietf','protocol'=>'auth_aes128_sha1','obfs'=>'http_simple_compatible'],
    ['id'=>3,'name'=>'SS/SSR 兼容配置','method'=>'chacha20-ietf','protocol'=>'auth_aes128_sha1_compatible','obfs'=>'plain']
];

$_ENV['subscribeLog'] = false;			    //是否记录用户订阅日志
$_ENV['subscribeLog_keep_days'] = 7;		    //订阅记录保留天数

//Bot 设置--------------------------------------------------------------------------------------------
#通用
$_ENV['finance_public'] = false;			//财务报告是否向群公开
$_ENV['enable_welcome_message'] = true;	//机器人发送欢迎消息

# Telegram BOT
$_ENV['use_new_telegram_bot']       = true;         //是否使用新的 Telegram Bot
$_ENV['enable_telegram']            = false;        //是否开启Telegram Bot
$_ENV['telegram_token']             = '';           //Telegram Bot 的 Token，跟 Father Bot 申请
$_ENV['telegram_bot']               = '';           //Telegram Bot 用户名
$_ENV['telegram_group_quiet']       = false;        //Telegram Bot 在群组中不回应
$_ENV['telegram_chatid']            = '';           //Telegram 群组会话 ID，把 Bot 拉进群里之后跟他 /ping 一下即可得到。
$_ENV['telegram_request_token']     = '';           //Telegram 机器人请求验证 Key，随意设置，由大小写英文和数字组成，更新这个参数之后请 php xcat setTelegram

$_ENV['enable_tuling']              = false;	    //是否开启图灵机器人
$_ENV['tuling_apikey']              = '';			//图灵机器人 API Key
$_ENV['tuling_apisecert']           = '';			//图灵机器人密钥

# Telegram BOT 其他选项
$_ENV['show_group_link']            = false;        //在 Bot 菜单中显示加入用户群
$_ENV['telegram_group_link']        = '';           //用户群的链接
$_ENV['group_bound_user']           = false;        //仅允许已绑定 Telegram 账户的用户加入 telegram_chatid 设定的群组
$_ENV['allow_to_join_new_groups']   = true;         //允许 Bot 加入下方配置之外的群组
$_ENV['group_id_allowed_to_join']   = [];           //允许加入的群组 ID，格式为 PHP 数组
$_ENV['telegram_admins']            = [];           //Telegram 管理员 ID，格式为 PHP 数组
$_ENV['unbind_kick_member']         = false;        //用户解绑 Telegram 账户后自动踢出群组
$_ENV['enable_not_admin_reply']     = true;         //非管理员操作管理员功能是否回复
$_ENV['not_admin_reply_msg']        = '!';          //非管理员操作管理员功能的回复内容
$_ENV['no_user_found']              = '!';          //管理员操作时，找不到用户的回复
$_ENV['no_search_value_provided']   = '!';          //管理员操作时，没有提供用户搜索值的回复
$_ENV['data_method_not_found']      = '!';          //管理员操作时，修改数据的字段没有找到的回复
$_ENV['delete_message_time']        = 180;          //在以下时间后删除用户命令触发的 bot 回复，单位：秒，删除时间可能会因为定时任务而有差异，为 0 代表不开启此功能
$_ENV['delete_admin_message_time']  = 86400;        //在以下时间后删除管理命令触发的 bot 回复，单位：秒，删除时间可能会因为定时任务而有差异，为 0 代表不开启此功能
$_ENV['enable_delete_user_cmd']     = false;        //自动删除群组中用户发送的命令，使用 delete_message_time 配置的时间，删除时间可能会因为定时任务而有差异
$_ENV['help_any_command']           = false;        //允许任意未知的命令触发 /help 的回复

$_ENV['remark_user_search_email']               = ['邮箱'];                     //用户搜索字段 email 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_search_port']                = ['端口'];                     //用户搜索字段 port 的别名，可多个，格式为 PHP 数组

$_ENV['remark_user_option_is_admin']            = ['管理员'];                   //用户搜索字段 is_admin 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_enable']              = ['用户启用'];                  //用户搜索字段 enable 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_money']               = ['金钱', '余额'];             //用户搜索字段 money 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_port']                = ['端口'];                     //用户搜索字段 port 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_transfer_enable']     = ['流量'];                     //用户搜索字段 transfer_enable 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_passwd']              = ['连接密码'];                 //用户搜索字段 passwd 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_method']              = ['加密'];                     //用户搜索字段 method 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_protocol']            = ['协议'];                     //用户搜索字段 protocol 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_protocol_param']      = ['协参', '协议参数'];         //用户搜索字段 protocol_param 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_obfs']                = ['混淆'];                     //用户搜索字段 obfs 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_obfs_param']          = ['混参', '混淆参数'];         //用户搜索字段 obfs_param 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_invite_num']          = ['邀请数量'];                 //用户搜索字段 invite_num 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_node_group']          = ['用户组', '用户分组'];       //用户搜索字段 node_group 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_class']               = ['等级'];                     //用户搜索字段 class 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_class_expire']        = ['等级过期时间'];             //用户搜索字段 class_expire 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_expire_in']           = ['账号过期时间'];             //用户搜索字段 expire_in 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_node_speedlimit']     = ['限速'];                    //用户搜索字段 node_speedlimit 的别名，可多个，格式为 PHP 数组
$_ENV['remark_user_option_node_connector']      = ['连接数', '客户端'];         //用户搜索字段 node_connector 的别名，可多个，格式为 PHP 数组

$_ENV['enable_user_email_group_show']           = false;                      //开启在群组搜寻用户信息时显示用户完整邮箱，关闭则会对邮箱中间内容打码，如 g****@gmail.com
$_ENV['user_not_bind_reply']                    = '您未绑定本站账号，您可以进入网站的 **资料编辑**，在右下方绑定您的账号.';                      //未绑定账户的回复
$_ENV['telegram_general_pricing']               = '产品介绍.';                  //面向游客的产品介绍
$_ENV['telegram_general_terms']                 = '服务条款.';                  //面向游客的服务条款

//沟通设置--------------------------------------------------------------------------------------------
#客服系统设置，注册地址 https://www.mylivechat.com
$_ENV['enable_mylivechat'] = false;		//是否开启客服系统
$_ENV['mylivechat_id'] = '';			//客服系统ID

# PushBear  基于微信模板的向关注了二维码的用户以微信方式推送消息 https://pushbear.ftqq.com/
$_ENV['usePushBear'] = false;		// true 启用	false 禁用
$_ENV['PushBear_sendkey'] = '';	//请填写您在PushBear获取的sendkey  请仔细检查勿粘贴错

#工单系统设置
$_ENV['enable_ticket'] = true;		//是否开启工单系统
$_ENV['mail_ticket'] = true;		//是否开启工单邮件提醒

# Server酱  用户提交新工单或者回复工单时用微信提醒机场主 http://sc.ftqq.com/
$_ENV['useScFtqq'] = false;		//是否开启工单Server酱提醒
$_ENV['ScFtqq_SCKEY'] = '';	//请填写您在Server酱获取的SCKEY  请仔细检查勿粘贴错

#管理员联系方式设置
$_ENV['enable_admin_contact'] = false;			//是否开启管理员联系方式
$_ENV['admin_contact1'] = 'QQ：1233456';		//QQ、邮箱、微信仅用于举例
$_ENV['admin_contact2'] = '邮箱123456@qq.com';	//也可以写电话、tg等其他联系方式
$_ENV['admin_contact3'] = '微信～123456';		//没有格式要求，想怎么写就怎么写，可留空


//验证码设置------------------------------------------------------------------------------------------

$_ENV['captcha_provider'] = 'recaptcha';		//取值 recaptcha | geetest(极验)

$_ENV['recaptcha_sitekey'] = '';
$_ENV['recaptcha_secret'] = '';

$_ENV['geetest_id'] = '';
$_ENV['geetest_key'] = '';

$_ENV['enable_reg_captcha'] = false;		//启用注册验证码
$_ENV['enable_login_captcha'] = false;	//启用登录验证码
$_ENV['enable_checkin_captcha'] = false;	//启用签到验证码


//支付系统设置----------------------------------------------------------------------------------------
#取值 none | codepay | f2fpay | chenAlipay | paymentwall | spay |tomatopay | payjs | yftpay
$_ENV['payment_system'] = 'none';

#yft支付设置
$_ENV['yft_secret'] = '';
$_ENV['yft_accesskey'] = '';

#codepay码支付
#wiki地址:https://goo.gl/dRwRDi  http://t.cn/RnsWjtB
$_ENV['codepay_id'] = '';					//码支付ID
$_ENV['codepay_key'] = '';					//码支付通信密钥

#alipay,f2fpay
$_ENV['f2fpay_app_id'] = '';
$_ENV['f2fpay_p_id'] = '';
$_ENV['alipay_public_key'] = '';
$_ENV['merchant_private_key'] = '';
$_ENV['f2fNotifyUrl'] = null;                  //自定义当面付回调地址

#PaymentWall
$_ENV['pmw_publickey'] = '';
$_ENV['pmw_privatekey'] = '';
$_ENV['pmw_widget'] = 'm2_1';
$_ENV['pmw_height'] = '350px';

#alipay,spay
$_ENV['alipay_id'] = '';
$_ENV['alipay_key'] = '';
$_ENV['amount'] = [2, 23, 233, 2333, 23333];		//充值金额选项设定

#alipay,zfbjk.com
$_ENV['zfbjk_pid'] = '';
$_ENV['zfbjk_key'] = '';
$_ENV['zfbjk_qrcodeurl'] = '';

# BitPay 数字货币支付（比特币、以太坊、EOS等） 商户后台获取授权码 https://merchants.mugglepay.com/
#   客服和技术 24x7 在线支持： https://t.me/joinchat/GLKSKhUnE4GvEAPgqtChAQ
$_ENV['bitpay_secret'] = '';

#PayJs
$_ENV['payjs_mchid'] = '';
$_ENV['payjs_key'] = '';

$_ENV['idtpay']=[
    'partner'=>"", //商户号
    'key' =>"", //商户key
    'sign_type'=>strtoupper('MD5'),
    'input_charset'=>strtolower('utf-8'),
    'subjects'=>["内存",'手抓饼'], //商品名称
    'transport'=>'https' ,//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
    'apiurl'=>'https://pay.idt.xyz/', //http开头
    'appname'=>$_ENV['appName'],

];

#tomatopay番茄云支付
#使用教程:https://swapidc.fanqieui.com/?t/329.html  tg群 https://t.me/fanqiepay
$_ENV['tomatopay'] = [
        'wxpay'=>[
            'mchid' => '',   // 商户号
            'account' => '', //您在番茄云支付的登录邮箱
            'token' => "" // 安全验证码
        ],
        'alipay'=>[
            'mchid' => '',   // 商户号
            'account' => '', //您在番茄云支付的登录邮箱
            'token' => "" // 安全验证码
        ],
 ];


//其他面板显示设置------------------------------------------------------------------------------------------

#用户文档
$_ENV['enable_documents'] = false;	    //是否允许未登陆用户查看文档中心
$_ENV['documents_name'] = $_ENV['appName'] . ' 文档中心';	    //文档中心名称
$_ENV['remote_documents'] = true;	    //是否从远程加载文档中心，否的话请执行 php xcat initdocuments
$_ENV['documents_source'] = 'https://raw.githubusercontent.com/GeekQu/PANEL_DOC/master/GeekQu';	    //远程文档加载地址

#后台商品列表 销量统计
$_ENV['sales_period'] = 30;	//统计指定周期内的销量，值为【expire/任意大于0的整数】

#国旗
$_ENV['enable_flag'] = true;			//启用该项之前务必先仔细阅读教程
$_ENV['flag_regex'] = '/.*?(?=\s)/';		//从站点全名中匹配【国家/地区】的正则表达式(php版)

#捐赠
$_ENV['enable_donate'] = true;	//是否显示用户捐赠（所有收入将被公开）

#iOS账户显示
$_ENV['display_ios_class'] = -1;	//至少等级为多少的用户可以看见，小于0时关闭此功能
$_ENV['display_ios_topup'] = 0;	//满足等级要求后，累计充值高于多少的用户可以看见
$_ENV['ios_account'] = '';			//iOS账户
$_ENV['ios_password'] = '';			//iOS密码


//节点检测-----------------------------------------------------------------------------------------------
#GFW检测，请通过crontab进行【开启/关闭】
$_ENV['detect_gfw_interval'] = 3600;										//检测间隔，单位：秒，低于推荐值会爆炸
$_ENV['detect_gfw_port'] = 22;												//所有节点服务器都打开的TCP端口，常用的为22（SSH端口）
$_ENV['detect_gfw_url'] = 'https://cn-qz-tcping.torch.njs.app/{ip}/{port}';	//检测节点是否被gfw墙了的API的URL
$_ENV['detect_gfw_judge'] = '$json_tcping[\'status\']=="true"';				//判断是否被墙的依据，json_tcping为上方URL返回的json数组
$_ENV['detect_gfw_count'] = '3';												//尝试次数

#离线检测
$_ENV['enable_detect_offline'] = true;
#离线检测是否推送到Server酱 请配置好上文的Server配置
$_ENV['enable_detect_offline_useScFtqq'] = false;


//V2Ray相关设置------------------------------------------------------------------------------------------
$_ENV['v2ray_port'] = 443;					//V2Ray端口
$_ENV['v2ray_protocol'] = 'HTTP/2 + TLS';	//V2Ray协议
$_ENV['v2ray_alter_id'] = 32;
$_ENV['v2ray_level'] = '0';

//以下所有均为高级设置（一般用不上，不用改---------------------------------------------------------------------
#杂项
$_ENV['enable_login_bind_ip'] = true;		//是否将登陆线程和IP绑定
$_ENV['rememberMeDuration'] = 7;           //登录时记住账号时长天数
$_ENV['authDriver'] = 'cookie';			//不能更改此项
$_ENV['pwdMethod'] = 'md5';				//密码加密 可选 md5, sha256, bcrypt, argon2i, argon2id（argon2i需要至少php7.2）
$_ENV['salt'] = '';						//推荐配合 md5/sha256， bcrypt/argon2i/argon2id 会忽略此项
$_ENV['sessionDriver'] = 'cookie';			//可选: cookie,redis
$_ENV['cacheDriver'] = 'cookie';			//可选: cookie,redis
$_ENV['tokenDriver'] = 'db';				//可选: db,redis
$_ENV['jump_delay'] = 1200;				//跳转延时，单位ms，不建议太长
$_ENV['theme'] = 'Cool';			//主题
$_ENV['pacp_offset'] = -20000;				//VPN 端口偏移
$_ENV['pacpp_offset'] = -20000;
$_ENV['Speedtest_duration'] = 6;			//显示多长时间的测速记录
$_ENV['login_warn'] = true;				//异地登陆提示
$_ENV['timeZone'] = 'PRC';					//PRC 天朝时间  UTC 格林时间
$_ENV['db_charset'] = 'utf8';
$_ENV['db_collation'] = 'utf8_general_ci';
$_ENV['db_prefix'] = '';
$_ENV['muKeyList'] = [];                //多 key 列表
$_ENV['subscribe_client'] = false;        //下载协议客户端时附带节点和订阅信息
$_ENV['subscribe_client_url'] = '';       //使用独立的服务器提供附带节点和订阅信息的协议客户端下载，为空表示不使用

$_ENV['mu_port_migration'] = false;       //为后端直接下发偏移后的端口
$_ENV['add_emoji_to_node_name'] = false;  //为部分订阅中默认添加 emoji
$_ENV['add_appName_to_ss_uri'] = true;    //为 SS 节点名称中添加站点名
$_ENV['enable_sub_cache'] = false;        //订阅信息缓存
$_ENV['sub_cache_time'] = 360;            //订阅信息缓存有效时间 (分钟)
$_ENV['sub_cache_max_quantity'] = 15;     //每个用户订阅缓存最大数量，请基于磁盘空间考虑

// 审计自动封禁开关
$_ENV['enable_auto_detect_ban'] = false;

// 管理员不受审计限制
$_ENV['auto_detect_ban_allow_admin'] = true;

// 审计封禁的例外用户 ID
$_ENV['auto_detect_ban_allow_users'] = [];

// 审计封禁判断类型：
//   - 1 = 仁慈模式，每触碰多少次封禁一次
//   - 2 = 疯狂模式，累计触碰次数按阶梯进行不同时长的封禁
$_ENV['auto_detect_ban_type'] = 1;

// 仁慈模式每次执行封禁所需的触发次数
$_ENV['auto_detect_ban_number'] = 30;

// 仁慈模式每次封禁的时长 (分钟)
$_ENV['auto_detect_ban_time'] = 60;

// 疯狂模式阶梯
// key 为触发次数
//   - type：可选 time 按时间 或 kill 删号
//   - time：时间，单位分钟
$_ENV['auto_detect_ban'] = [
    100 => [
        'type' => 'time',
        'time' => 120
    ],
    300 => [
        'type' => 'time',
        'time' => 720
    ],
    600 => [
        'type' => 'time',
        'time' => 4320
    ],
    1000 => [
        'type' => 'kill',
        'time' => 0
    ]
];

#aws
$_ENV['aws_access_key_id'] = '';
$_ENV['aws_secret_access_key'] = '';

#redis
$_ENV['redis_scheme'] = 'tcp';
$_ENV['redis_host'] = '127.0.0.1';
$_ENV['redis_port'] = 6379;
$_ENV['redis_database'] = '';
$_ENV['redis_password']= '';

#Radius设置
$_ENV['enable_radius'] = false;			//是否开启Radius
$_ENV['radius_db_host'] = '';				//以下4项为Radius数据库设置
$_ENV['radius_db_database'] = '';
$_ENV['radius_db_user'] = '';
$_ENV['radius_db_password'] = '';
$_ENV['radius_secret'] = '';					//Radius连接密钥

#Cloudflare
$_ENV['cloudflare_enable'] = false;										//是否开启 Cloudflare 解析
$_ENV['cloudflare_email'] = 'user@example.com';								//Cloudflare 邮箱地址
$_ENV['cloudflare_key'] = 'c2547eb745079dac9320b638f5e225cf483cc5cfdda41';	//Cloudflare API Key
$_ENV['cloudflare_name'] = 'example.com';									//域名

#不安全中转模式，这个开启之后使用除了 auth_aes128_md5 或者 auth_aes128_sha1 以外的协议地用户也可以设置和使用中转
$_ENV['relay_insecure_mode'] = false;		//强烈推荐不开启

#是否夹带统计代码，自己在 resources/views/{主题名} 下创建一个 analytics.tpl ，如果有必要就用 literal 界定符
$_ENV['enable_analytics_code'] = false;
$_ENV['sspanelAnalysis'] = true;


#在套了CDN之后获取用户真实ip，如果您不知道这是什么，请不要乱动
if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
$list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
$_SERVER['REMOTE_ADDR'] = $list[0];
}


// make replace _ENV with env
function findKeyName($name) {
    global $_ENV;
    foreach($_ENV as $configKey => $configValue) {
        if (strtoupper($configKey) == $name) {
            return $configKey;
        }
    }

    return NULL;
}

foreach(getenv() as $envKey => $envValue) {
    global $_ENV;
    $envUpKey = strtoupper($envKey);
    // Key starts with UIM_
    if (substr($envUpKey, 0 , 4) == "UIM_") {
        // Vaild env key, set to _ENV
        $configKey = substr($envUpKey, 4);
        $realKey = findKeyName($configKey);
        if ($realKey != NULL) {
            $_ENV[$realKey] = $envValue;
        }
    }
}
