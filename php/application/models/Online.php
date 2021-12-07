<?php
require_once APPLICATION_ROOT_PATH . 'models/Base.php';

class Online extends Base{
	public function onlinetime(){
		$this->select->from('online', array('hour', 'max(total) AS max', 'avg(total) AS avg'));
		$this->select->group('hour');
		$result = $this->_db->fetchAll($this->select);

		//取整
		foreach($result as &$v){
			$v['avg'] = intval($v['avg']);
		}

		//格式化
		$arr = [];
		for($i=0;$i<24;$i++){
			$arr[$i]['hour'] = $i;
		}
		foreach($arr as $i=>&$v){
			foreach($result as $k=>$a){
				if($v['hour'] == $a['hour']){
					$v = $a;
					break;
				}
			}
			if(!isset($v['avg'])){$v['avg'] = 0;}
			if(!isset($v['max'])){$v['max'] = 0;}
		}
		return $arr;
	}
}
?>