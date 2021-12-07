<?php
/**
 * 在线数据类
 */
class Sitestat {

    private $_db;

    public function __construct($db) {
        $this->_db = $db;
        $this->_cgiurl = Zend_Registry::get('config')['cgi_url'];//'api_url' => 'http://services.578w.com:8080/web/'
        $this->_apiurl = Zend_Registry::get('config')['api_url'];//'cgi_url' => 'http://services.578w.com:8090/'
    }

    public function addonline(){
        $url = $this->_cgiurl.'online';//$url=http://services.578w.com:8090/online
        $result = $this->_Get($url);//请求数据
        $result_json = substr($result,0,strripos($result,',')).']}}';
        $result_arr = json_decode($result_json);//json_decode - 对JSON 格式的字符串进行解码
        $today = strtotime(date("Y-m-d"),time());//获得今天零点时间戳
        $hour = date("H");//获取现在小时
        $param = array('date'=>$today,'hour'=>$hour,'total'=>$result_arr->total);//
        $this->_db->insert('online',$param);
    }
    public function addnewuser(){
        $mixid = $this->_getmaxid();//获取数据库最大ID
        $url = $this->_apiurl.'data/user?mixid='.$mixid;
        $result = $this->_Get($url);
        $result_arr = json_decode($result,true);
        $user = array();
        $today = strtotime(date("Y-m-d"),time());
        foreach($result_arr['data']['accountlist'] as $v){
            $this->_db->insert('account',$v);
            $user[$v['platform']]['user'] = isset($user[$v['platform']]['user'])?$user[$v['platform']]['user'] +1 :1;
            if($this->_uniquemachinecode($v['machine_code']) > 0){
                $user[$v['platform']]['machine'] = isset($user[$v['platform']]['machine'])?$user[$v['platform']]['machine'] +1 :1;
                if($v['status'] == 1){
                    $user[$v['platform']]['activeuser'] = isset($user[$v['platform']]['activeuser'])?$user[$v['platform']]['activeuser'] +1 :1;
                } 
            }
        }
        foreach($user as $k=>$v){
            $v['channel'] = $k;
            $v['date'] = $today;
            $v['machine'] = isset($v['machine'])?$v['machine']:0;
            $v['activeuser'] = isset($v['activeuser'])?$v['activeuser']:0;
            $this->_db->query("insert into user values (".$v['date'].",'".$v['channel']."',".$v['user'].",".$v['machine'].",".$v['activeuser'].") on duplicate key update user=user+".$v['user'].",machine=machine+".$v['machine'].",activeuser=activeuser+".$v['activeuser']);
        }
    }
    public function addrecharge(){
        $url = $this->_apiurl.'data/recharge';
        $result = $this->_Get($url);
        $result_arr = json_decode($result,true);
        foreach($result_arr['data']['rechargelist'] as $v){
            $this->_db->insert('recharge',$v);
        }        
    }
    public function addrechargebyplatform(){
        $url = $this->_apiurl.'data/rechargebyplatform';
        $result = $this->_Get($url);
        $result_arr = json_decode($result,true);
        foreach($result_arr['data']['rechargelist'] as $v){
            $this->_db->insert('rechargebyplatform',$v);
        } 
    }

    public function addgamelog(){
        $url = $this->_apiurl.'data/addgamelog';
        $result = $this->_Get($url);
        $result_arr = json_decode($result,true);
        $this->_db->query("truncate gamelog");
        foreach($result_arr['data']['gamelog'] as $v){
            $this->_db->insert('gamelog',$v);
        } 
    }
    public function addpayment(){
        $today = strtotime(date("Y-m-d"),time());
        $yestoday =  $today - 86401;
        $result= $this->_db->fetchAll('select platform as channel ,sum(amount) as t_amount,count(id) as num,count(distinct account_id) as t_user from recharge where createtime > ? and createtime < ? group by platform',array($yestoday,$today));
        foreach($result as $v){
            $v['date'] = $yestoday + 1;
            $this->_db->insert('payment',$v);
        }
    }
    public function addpaymentbyplatform(){
        $today = strtotime(date("Y-m-d"),time());
        $yestoday =  $today - 86401;
        $result= $this->_db->fetchAll('select platform as channel ,sum(amount) as t_amount,count(id) as num,count(distinct account_id) as t_user from rechargebyplatform where createtime > ? and createtime < ? group by platform',array($yestoday,$today));
        foreach($result as $v){
            $v['date'] = $yestoday + 1;
            $this->_db->insert('paymentbyplatform',$v);
        }
    }
	
	public function linkcount(){
        $today = strtotime(date("Y-m-d"),time());
        $yestoday =  $today - 86401;
        $result= $this->_db->fetchAll('select channel,sum(flag) as down,count(channel) as visit from linkcount where createtime > ? and createtime < ? group by channel',array($yestoday,$today));
        foreach($result as $v){
            $v['date'] = $yestoday + 1;
            $this->_db->insert('olinkcount',$v);
        }
    }

    public function addfirstpayment(){
        $today = strtotime(date("Y-m-d"),time());
        $result = $this->_db->fetchAll('select sum(t1.amount) as t_amount,count(t1.id) as num,t1.platform as channel,count(distinct t1.account_id) as t_user from recharge t1 where (select count(*) from account t2 where t2.id = t1.account_id and t2.is_pay = 0 ) = 1 group by platform ');
        $result_account = $this->_db->fetchAll('select account_id from recharge t1 where ( select count(*) from account t2 where t2.id = t1.account_id and t2.is_pay = 0 ) = 1 group by account_id');
        foreach($result as $v){
            $v['date'] = $today - 172800;
            $this->_db->insert('firstpayment',$v);
        }
        foreach($result_account as $v){
            $set = array('is_pay'=> '1');
            $where = $this->_db->quoteInto('id=?',$v['account_id']);
            $this->_db->update('account',$set,$where);
        }
    }

    public function addpaymentratio(){
        // $result = $this->_db->fetchPairs('select count(id),platform from account where is_pay= 0 and DATEDIFF(from_unixtime(createtime),now()) = -2 group by platform');
        $result_d = $this->_db->fetchAll('select platform,count(id) as num from account where is_pay= 1 and DATEDIFF(from_unixtime(createtime),now()) = -2 group by platform');
        $result_w = $this->_db->fetchAll('select platform,count(id) as num from account where is_pay= 1 and DATEDIFF(from_unixtime(createtime),now()) = -9 group by platform');
        $result_m = $this->_db->fetchAll('select platform,count(id) as num from account where is_pay= 1 and DATEDIFF(from_unixtime(createtime),now()) = -32 group by platform');
        // $result = array_merge($result_d,$result_w,$result_m);
        foreach($result_d as $v){
            $today = strtotime(date("Y-m-d"),time());
            $date = $today - 172800;
            $params = array('date'=>$date,'channel'=>$v['platform'],'d_num'=>$v['num']);
            $this->_db->insert('paymentratio',$params);
        }
        foreach($result_w as $v){
            $today = strtotime(date("Y-m-d"),time());
            $date = $today - 777600;
            $this->_db->query("insert into paymentratio values (".$date.",'".$v['platform']."',0,".$v['num'].",0) on duplicate key update w_num=".$v['num']);

            // $this->_db->update('paymentratio',$set,$where);
        }
        foreach($result_m as $v){
            $today = strtotime(date("Y-m-d"),time());
            $date = $today - 2764800;
            $this->_db->query("insert into paymentratio values (".$date.",'".$v['platform']."',0,0,".$v['num'].") on duplicate key update m_num=".$v['num']);
            // $this->_db->update('paymentratio',$set,$where);
        }
    }

    public function addpaymentbyday(){
        $today = strtotime(date("Y-m-d"),time());
        $yestoday = $today - 86401;
        $thirty_day = $today - 86400*30;
        $result = $this->_db->fetchAll("select sum(t1.amount) as t_amount,count(t1.id) as num,UNIX_TIMESTAMP(FROM_UNIXTIME(t2.createtime,'%Y-%m-%d')) as regdays,t2.platform as channel from recharge t1 left join account t2 on t1.account_id = t2.id where t1.createtime > ? and t2.createtime > ? and t2.createtime <?  group by t2.platform,regdays",array($yestoday,$thirty_day,$today));
        foreach($result as $v){
            $v['date'] = $yestoday + 1;
            $this->_db->insert('paymentbyday',$v);
        }
    }
    public function addretention(){
        $result = $this->_db->fetchAll("select count(t2.playerid) as num,t1.platform, UNIX_TIMESTAMP(FROM_UNIXTIME(t1.createtime,'%Y-%m-%d')) as regdays from account t1 left join gamelog t2 on t2.playerid = t1.id where DATEDIFF(from_unixtime(createtime),now()) > -32 and DATEDIFF(from_unixtime(createtime),now()) < -1 and t2.playerid is not NULL group by t1.platform,DATEDIFF(from_unixtime(createtime),now());");
        $today = strtotime(date("Y-m-d"),time());
        $yestoday = $today-86400;
        foreach($result as $v){
            $v['date'] = $yestoday;
            $this->_db->insert('retention',$v);
        }
    }

    private function _getmaxid(){
        return $this->_db->fetchOne('select id from account order by id desc limit 1');
    }
    private function _uniquemachinecode($machine_code){
        return $this->_db->fetchOne('select count(*) from account where machine_code=?', array($machine_code));
    }
    public function sendmail($mail_content, $mail_title){
        Util::sentmail($mail_content,$mail_title,'luleilei@52y.com','213');
    }

 
    
   /*
    * 从接口获取数据
    */

    private function _Get($url,$params = false) {
        $options = array('http' => array('method' => "GET", 'timeout' => 10, 'header' => 'qiutao:leilei'));
		//二维数组：$options里包含着http，http包含数组method' => "GET", 'timeout' => 10, 'header' => 'qiutao:leilei'
        if($params) $url = $url."?".http_build_query($params);
        return file_get_contents($url, false, stream_context_create($options));
		//file_get_contents读取文件
    }

   private function _Post($url,$data){
        $headers = array('qiutao:leilei');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $this->_url . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $return = curl_exec($ch);
        // 检查是否有错误发生
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
            die;
        }
        curl_close($ch);
        return $return;
   }
}
