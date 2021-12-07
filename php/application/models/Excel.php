<?php
require_once APPLICATION_ROOT_PATH . 'models/Base.php';

class Excel extends Base{
	public function user() {
		$results = [];		
		$selects = $this->_db->select();
		$selects->Where('channel IN(?)', $this->channels);
		for($i=0;$i<$this->timeLength;$i++){
			$select = clone $selects;
			$time = $this->timeArr[$i]['date'];
			$select->where('date = ?',$time);
			$select->from('user', array('date','channel','sum(user) AS adduser'));
			$select->group('channel');
			$result = $this->_db->fetchAll($select);
			if($result){
				array_push($results,$result);
			}
		}
        return $results;
	}

	public function todayPay() {
		$results = [];
		$selects = $this->_db->select();
		$selects->Where('channel IN(?)', $this->channels);
		for($i=0;$i<$this->timeLength;$i++){
			$select = clone $selects;
			$time = $this->timeArr[$i]['date'];
			$select->where('date = ?',$time);
			$select->from('paymentbyday', array('date','channel','sum(t_amount) AS todaypayment', 'sum(num) AS todaypaycount'));
			$select->group('channel');
			$result = $this->_db->fetchAll($select);
			if($result){
				array_push($results,$result);
			}
		}
        return $results;
	}

	public function channelPay() {
		$results = [];
		$selects = $this->_db->select();
		$selects->Where('channel IN(?)', $this->channels);
		for($i=0;$i<$this->timeLength;$i++){
			$select = clone $selects;
			$time = $this->timeArr[$i]['date'];
			$select->where('date = ?',$time);
			$select->from('paymentbyplatform', array('date','channel','sum(t_amount) AS channelpayment'));
			$select->group('channel');
			$result = $this->_db->fetchAll($select);
			if($result){
				array_push($results,$result);
			}
		}
        return $results;
	}

	public function retention() {
		$result=array();
		$results=array();
		$selects = $this->_db->select();
		//渠道
		$selects->Where('platform IN(?)', $this->channels);

		for($i=0;$i<$this->timeLength;$i++){
			$select = clone $selects;
			$time = $this->timeArr[$i]['date'];
			$select->where('regdays = ?',$time);
			//$result = array("date"=>date("Y-m-d",$time),"twolive"=>0,"sevenlive"=>0);
			//取得留存量
			for($k=0;$k<2;$k++){
				$selectalone = clone $select;
				$selectalone->where('date = ?', $time+(($k*6+1)*86400));
				if($k == 0){
					$selectalone->from('retention', array('regdays AS date','platform AS channel','sum(num) AS twolive'));
				}else{
					$selectalone->from('retention', array('regdays AS date','platform AS channel','sum(num) AS sevenlive'));
				}
				$selectalone->group('platform');
				$val = $this->_db->fetchAll($selectalone);
				if($val && $k == 0){
					$result['twoliveArr'] = $val;
				}else if($val && $k==1){
					$result['sevenliveArr'] = $val;
				}else{
					break;
				}
			}
			//取得留存累加量
			$selectSum = clone $select;
			$selectSum->where('date >= ?', $time);
			$selectSum->where('date <= ?', $time+6*86400);
			$selectSum->from('retention', array('regdays AS date','platform AS channel','sum(num) AS sevenlivesum'));
			$selectSum->group('platform');
			$val = $this->_db->fetchAll($selectSum);
			if($val){
				$result['sevenliveCountArr'] = $val;
			}
			if($result){
				array_push($results,$result);
			}
		}
		return $results;
	}

	public function outExcel(){
		// $tableData= [[
		// 	'date' => '2016-05-02',
		// 	'channel' =>  'fishworld',
		// 	'show'=> 285,
		// 	'click'=> 396,
		// 	'payment'=> 321,
		// 	'adduser'=>869,
		// 	'clickrate'=> '0%',
		// 	'avgclick'=> 0,
		// 	'tratiorate'=> '0%',
		// 	'usercost'=> 0,
		// 	'todaypayment'=> '12335',
		// 	'tpdaypaycount'=> '1234',
		// 	'channelpayment'=> '33.584',
		// 	'twolive'=> '123',
		// 	'twoliverate'=> '123',
		// 	'sevenlive'=> '24',
		// 	'sevenliverate'=> '24',
		// 	'twocost'=> 0,
		// 	'sevencost'=> 0,
		// 	'hehe'=>'adfss'
		// 	]];
		$user = $this->user();
		$todayPay = $this->todayPay();
		$channelPay = $this->channelPay();
		$retention = $this->retention();
		$where = $this->_db->quoteInto('channel IN(?)', $this->channels);
        $channel = $this->_db->fetchAll('select channel,note from channel where '.$where.' ORDER BY channel ASC');
		$arr = [];
		foreach($this->timeArr as $v){
			foreach($this->channels as $i){
			$obj = [
				'date'=>$v['date'],
				'channel'=>$i,
				'show'=> '',
				'click'=> '',
				'payment'=> '',
				'clickrate'=> '0%',
				'avgclick'=> 0,
				'tratiorate'=> '0%',
				'usercost'=> 0,
				'adduser'=>0,
				'todaypayment'=>0,
				'todaypaycount'=>0,
				'channelpayment'=>0,
				'sevenlivesum' => 0,
				'sevenlive'=>0,
				'sevenliverate'=> '0%',
				'twolive'=>0,
				'twoliverate'=> '0%',
				'twocost'=> 0,
				'sevencost'=> 0
			];
				array_push($arr,$obj);
			}
		}
		$arr = $this->cycle($arr,$user,['adduser']);
		$arr = $this->cycle($arr,$todayPay,['todaypayment','todaypaycount']);
		$arr = $this->cycle($arr,$channelPay,['channelpayment']);
		$arr = $this->cycle2($arr,$retention,['sevenlivesum','sevenlive','twolive']);
		foreach($arr as &$v){
			$v['date'] = date("Y-m-d",$v['date']);
			foreach($channel as $channelvalue){
				if($v['channel'] == $channelvalue['channel']){
					$v['channel'] = $channelvalue['note'];
				}
			}
		}
		return $arr;
	}

	public function cycle($arr,$data,$attrs){
		if($data){
			foreach($arr as &$val){
				foreach($data as $a){
					if($a){
						if($val['date'] == $a[0]['date']){
							foreach($a as $v){
								if($v['channel'] == $val['channel']){
									foreach($attrs as $attr){
										if($attr == 'todaypayment' || $attr == 'channelpayment'){
											$val[$attr] = $v[$attr]/100;											
										}else{
											$val[$attr] = $v[$attr];
										}
									}
									break;
								}
							}
						}
					}
				}
			}
		}
		return $arr;
	}

	public function cycle2($arr,$data,$attrs){
		if($data){
			foreach($arr as &$val){
				foreach($data as $a){
					foreach($a as $aval){
						if($val['date'] == $aval[0]['date']){
							foreach($aval as $v){
								if($v['channel'] == $val['channel']){
									foreach($attrs as $attr){
										if(isset($v[$attr])){
											$val[$attr] = $v[$attr];
										}
									}
									break;
								}
							}
						}
					}
				}
			}
		}
		return $arr;
	}
}
?>