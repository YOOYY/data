<?php
class MonthPayment {
    protected $_db,$privilege; //接口服务器
    public function __construct($timeLine,$channels,$times,$regTimes,$regTimeLine) {
		$this->_db = Zend_Registry::get('db');

     	// 获取登陆信息
		$this->_admininfo = Array(
			'id' => empty($_SESSION['ADMIN_ID']) ? '' : $_SESSION['ADMIN_ID'],
			'name' => empty($_SESSION['ADMIN_NAME']) ? '' : $_SESSION['ADMIN_NAME'],
			'password' => empty($_SESSION['ADMIN_PASSWORD']) ? '' : $_SESSION['ADMIN_PASSWORD'],
			'privilege' => empty($_SESSION['ADMIN_PRIVILEGE']) ? '' : $_SESSION['ADMIN_PRIVILEGE'],
			'channel' => empty($_SESSION['ADMIN_CHANNEL']) ? '' : $_SESSION['ADMIN_CHANNEL'],
		);
		//通用权限
		$this->baseprivilege = ['index','sitestat','log','error'];
		//权限
		$this->privilege = $this->_admininfo['privilege'];
		//渠道
		$this->channelAll = $this->_admininfo['channel'];

		//查询渠道
		$this->channels = $channels;
        //查询时间
        if($timeLine){
		foreach($times as $index => $value){
			$dateStr = date('Y-m-d', date(floor($value/1000)));
			$times[$index] = strtotime($dateStr);
		};
        }else{
            $times = $times/1000;
        }
        if($regTimeLine){
		foreach($regTimes as $index => $value){
			$dateStr = date('Y-m-d', date(floor($value/1000)));
			$regTimes[$index] = strtotime($dateStr);
        };
        }else{
            $regTimes = $regTimes/1000;
        }

        
		//验证权限
        $this->checkChannel();
        $this->regtimes = $this->selectTime($regTimes,$regTimeLine,'t1');
        $this->times = $this->selectTime($times,$timeLine,'t2');
	}

	public function selectTime($times,$line,$type){
        if($line){
            return "$type.createtime >= $times[0] and $type.createtime <= $times[1]";
        }else{
            return "$type.createtime >= $times";
        }
	}
	
	/**
	 * 过滤渠道
	 * @param Boolean 是否将渠道添加到数据库查询条件
	 * @return 
	 */
	function checkChannel($require = true) {
		if($require){
			$this->channels = array_intersect($this->channels,$this->channelAll);
		}
    }
    
    public function monthPayment(){
        $channels = "'".implode("','",$this->channels)."'";
        $sql = "select FROM_UNIXTIME(t1.createtime, '%Y-%m') AS createdate,FROM_UNIXTIME(t2.createtime, '%Y-%m') AS rechargedate,t1.platform AS channel,sum(t2.amount)/100 AS amount from account t1 left join recharge t2 on t2.account_id = t1.id  where $this->regtimes and $this->times and t1.platform in ($channels) group by FROM_UNIXTIME(t1.createtime, '%Y-%m'),FROM_UNIXTIME(t2.createtime, '%Y-%m'),t1.platform";
        $result = $this->_db->fetchAll($sql);
        return $result;
    }
}
?>
