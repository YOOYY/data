<?php
/**
 * 从平台获取数据
 */
class Base {

    protected $_db,$privilege; //接口服务器

    /**
     * 初始化
     */

    public function __construct($timeLine,$channels,$times,$action,$require,$requireTime) {
		$this->_db = Zend_Registry::get('db');
		$this->select = $this->_db->select();

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
		//时间段?
		$this->timeLine = $timeLine;
		//时间数组
		$this->timeArr = [];
		//时间数组长度
		$this->timeLength = '';
		//查询渠道
		$this->channels = $channels;
		//查询时间
		foreach($times as $index => $value){
			$dateStr = date('Y-m-d', date(floor($value/1000)));
			$times[$index] = strtotime($dateStr);
		};
		$this->times = $times;

		//验证权限
			$this->checkChannel($require);
			$this->selectTime($requireTime);
	}

	public function selectTime($requireTime){
		if($requireTime){
			if($this->timeLine == 'true'){
				$this->select->where('date >= ?', $this->times[0]);
				$this->select->where('date <= ?', $this->times[1]);
				//注册时间数组
				$length = (($this->times[1]-$this->times[0])/86400)+1;
				for($i=0;$i<$length;$i++){
					array_push($this->timeArr,['date'=>$this->times[0]+86400*$i]);
				}
			}else{
				$this->select->Where('date IN(?)', $this->times);
				foreach($this->times as $k){
					array_push($this->timeArr,['date'=>$k]);
				}
			}
			$this->timeLength = count($this->timeArr);
		}else{
			return;
		}

	}

	//格式化输出
	public function formatData($data){
		$arr = $this->timeArr;
		if($data){
			foreach($arr as $i=>&$v){
				foreach($data as $k=>$a){
					if($v['date'] == $a['date']){
						$v = $a;
						break;
					}
				}
			}
		}
		foreach($arr as &$v){
			$v['date'] = date("Y-m-d",$v['date']);
		}
		return $arr;
	}
	
	/**
	 * 过滤渠道
	 * @param Boolean 是否将渠道添加到数据库查询条件
	 * @return 
	 */
	function checkChannel($require = true) {
		if($require){
			$this->channels = array_intersect($this->channels,$this->channelAll);
			$this->select->Where('channel IN(?)', $this->channels);
		}
	}

    function buildSql($table, $col)
    {
        $channels = $this->_db->quote($this->channels);
        $channels = $this->channels ? "AND channel IN($channels)" : "";
        $dateTable = $this->buildTimeTable();

        return "select $col
		from($dateTable)z left join $table
		on (z.date = $table.date $channels)
		group by z.date
		order by 1";
    }

    // 补全时间线，查询时间长度最多1000天
    // SELECT
    // 	1587225600+ ( ( t1000.id - 1 ) * 86400 ) date 
    // FROM
    // 	t1000 
    // WHERE
    // 	1587225600+ ( ( t1000.id - 1 ) * 86400 ) <= 1587312000 
    function buildTimeTable($formatDate = false)
    {
        $times = $this->times;
        $timeLine = $this->timeLine;

        if ($times) {
            $startTime = $this->_db->quote($times[0]);
            if ($timeLine == 'true') {
                $endTime = $this->_db->quote($times[1]);
                $timeSql = "<= $endTime";
            } else {
                $times = $this->_db->quote($times);
                $timeSql = "IN($times)";
            }
        }

        if ($formatDate) {
            $date = "from_unixtime($startTime+ (( t1000.id - 1 ) * 86400 ), '%Y-%m-%d' ) AS date";
        } else {
            $date = "$startTime+ ( ( t1000.id - 1 ) * 86400 ) AS date";
        }

        return "SELECT $date FROM t1000 
		WHERE
		$startTime+ ( ( t1000.id - 1 ) * 86400 ) $timeSql";
    }
}
?>