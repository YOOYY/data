<?php

class Util {

    /**
     * get_ip 获得用户的IP,未知ip返回0.0.0.0
     * 
     * @access public
     * @param $islong 是否返回浮点型数字
     *
     * @return string
     * @author RD
     */
    
    public static function getip($islong = false) {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), '0.0.0.0'))
            $onlineip = getenv('HTTP_CLIENT_IP');
        elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), '0.0.0.0'))
            $onlineip = getenv('HTTP_X_FORWARDED_FOR');
        elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), '0.0.0.0'))
            $onlineip = getenv('REMOTE_ADDR');
        elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], '0.0.0.0'))
            $onlineip = $_SERVER['REMOTE_ADDR'];

        preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
        $onlineip = !empty($onlineipmatches[0]) ? $onlineipmatches[0] : '0.0.0.0';

        if ($islong)
            $onlineip = sprintf('%u', ip2long($onlineip));

        return $onlineip;
    }

    /**
     * 邮件发送
     *
     * @param string $mail_content 邮件内容
     * @param string $mail_title 邮件标题
     * @param string $charset 邮件字符编码
     * @param string $mail_to 收件人email
     * @param string $mail_to_nick 收件人昵称
     * @return boole 邮件是否发送成功
     */
    public static function sentmail($mail_content = '', $mail_title = '', $mail_to = '', $mail_to_nick = '', $charset = 'utf-8') {
        $error = '';
        require_once 'Zend/Mail.php';
        require_once 'Zend/Mail/Transport/Smtp.php';
        try {
            if ($mail_content == '' || $mail_to == '' || $mail_to_nick == '' || $mail_title == '') {
                throw new Exception('发送邮件需要的信息不完整!');
            }
            $mail = new Zend_Mail($charset);
            $config = array(
                'auth' => 'login',
                'username' => "luleilei@52y.com",
                'password' => "47hDpybufGVEeRfd",
                'port' => "465",
                'ssl' => "ssl"
            );
            $transport = new Zend_Mail_Transport_Smtp('smtp.exmail.qq.com', $config);
            $mail->setDefaultTransport($transport);
            $mail->setBodyHtml($mail_content);
            $mail->setFrom('luleilei@52y.com', 'error');
            $mail->addTo($mail_to, $mail_to_nick);
            $mail->setSubject("=?{$charset}?B?" . base64_encode($mail_title) . "?=");
            $mail->send();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        if ($error != '') {
            return false; //邮件发送失败,请重试!
        } else {
            return true; //发送成功
        }
    }

    /**
     * curl访问url
     *
     * @param str $url_str
     * @return int
     */
    public static function call_remote_by_curl($url, $data) {
//        $host = 'http://10.1.3.250/game_platform/web/';
        //正式
        $host = 'http://services.578w.com/web/';
        //测试
//        $host = 'http://services3.578w.com/web/';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $host . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $return = curl_exec($ch);
        // 检查是否有错误发生
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
            die;
        }
        curl_close($ch);
        $resultArr = json_decode($return, true);
        return $resultArr;
    }

    function err($e){
        //echo $e->getMessage();
        // switch ($e->type)
        // {
        //     // 404 error -- controller or action not found
        //     case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
        //     case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        //     $res = '页面未找到';
        //     break;

        //     //系统或框架抛出的异常
        //     case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
        //     $res = '系统异常';
        //     break;
		// }
		
		// if($e instanceof ArrayObject)
		// $e = $e->exception;
		
		//记录日志
		$content = date('Y-m-d H:i:s').":\t";
		$content.= $e->getMessage()."\t";
		$content.= $e->getFile()." on line ".$e->getLine()."\t";
		$content.= $e->getTraceAsString()."\t";
		require_once APPLICATION_ROOT_PATH . 'models/Logs.php';
		Logs::write('exception', $content);

        $res = new stdClass();
        switch ($e->getMessage()) {
			case 10001:
				$res->error = "10001";
				$res->data = "系统错误";
				break;
			case 10002:
				$res->error = "10002";
				$res->data = "验证码不正确";
				break;
			case 10003:
				$res->error = "10003";
				$res->data = "用户已存在";
				break;
			case 10004:
				$res->error = "10004";
				$res->data = "帐号或者密码不正确;或该帐号已绑定手机或微信";
				break;
			case 10005:
				$res->error = "10005";
				$res->data = "token验证失败";
				break;
			case 10006:
				$res->error = "10006";
				$res->data = "请先绑定微信或者手机号";
				break;
			case 10007:
				$res->error = "10007";
				$res->data = "旧密码不正确";
				break;
			case 10008:
				$res->error = "10008";
				$res->data = "请填写必要参数";
				break;
			case 10009:
				$res->error = "10009";
				$res->data = "帐号与身份证号对应不上";
				break;
			case 10010:
				$res->error = "10010";
				$res->data = "该帐号已经绑定过手机";
				break;
			case 10011:
				$res->error = "10011";
				$res->data = "两次密码不一致";
				break;
			case 10012:
				$res->error = "10012";
				$res->data = "用户被锁定";
				break;
			case 10013:
				$res->error = "10013";
				$res->data = "机器码不一致";
				break;
			case 10014:
				$res->error = "10014";
				$res->data = "该帐号未绑定过手机";
				break;
			case 10015:
				$res->error = "10015";
				$res->data = "帐号已重复";
				break;
			case 10016:
				$res->error = "10016";
				$res->data = "昵称已重复";
				break;
			case 10017:
				$res->error = "10017";
				$res->data = "卡号或者密码错误";
				break;
			case 10018:
				$res->error = "10018";
				$res->data = "订单号不存在";
				break;
			case 10019:
				$res->error = "10019";
				$res->data = "昵称格式不正确";
				break;
			case 10020:
				$res->error = "10020";
				$res->data = "非法昵称";
				break;
			case 10021:
				$res->error = "10021";
				$res->data = "身份证号码验证失败";
				break;
			case 10022:
				$res->error = "10022";
				$res->data = "该手机号已绑定其他帐号";
				break;
			case 10025:
				$res->error = "10025";
				$res->data = "该微信号已绑定过帐号";
				break;
			case 10026:
				$res->error = "10026";
				$res->data = "该帐号已绑定过微信号";
				break;
			case 10027:
				$res->error = "10027";
				$res->data = "已经绑定过机器";
				break;
			case 10028:
				$res->error = "10028";
				$res->data = "未绑定任何机器";
				break;
			case 10029:
				$res->error = "10029";
				$res->data = "请下载官方包进行游戏";
				break;
			case 10030:
				$res->error = "10030";
				$res->data = "用户不存在";
				break;
			case 10031:
				$res->error = "10031";
				$res->data = "密码错误";
				break;
			case 10032:
				$res->error = "10032";
				$res->data = "房卡数量不足";
				break;
			case 10033:
				$res->error = "10033";
				$res->data = "用户不在线";
				break;
			case 10034:
				$res->error = "10034";
				$res->data = "本日充值已达上限";
				break;
			case 10035:
				$res->error = "10035";
				$res->data = "vip经验不足";
				break;
			case 10036:
				$res->error = "10036";
				$res->data = "ios充值超出单日限额";
				break;
			case 10040:
				$res->error = "10040";
				$res->data = "不能修改成原密码";
				break;
			default:
				$res->error = "10001";
				$res->data = "系统错误";
        }
        return $res;
	}

	public function _Get($url,$params = false) {
        $options = array('http' => array('method' => "GET", 'timeout' => 10, 'header' => 'qiutao:leilei'));
		//二维数组：$options里包含着http，http包含数组method' => "GET", 'timeout' => 10, 'header' => 'qiutao:leilei'
        if($params) $url = $url."?".http_build_query($params);
        return file_get_contents($url, false, stream_context_create($options));
		//file_get_contents读取文件
    }
}
