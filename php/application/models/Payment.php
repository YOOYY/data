<?php
require_once APPLICATION_ROOT_PATH . 'models/Base.php';

class Payment extends Base{
	public function paiddata() {
		$result = [];
		$platform = $this->platform();
		$paydata = $this->paydata();
		$firsttime = $this->firsttime();
		for($i=0;$i<$this->timeLength;$i++){
			$result[$i] = array_merge($platform[$i],$paydata[$i],$firsttime[$i]);
				if(!isset($result[$i]['paynum'])){$result[$i]['paynum'] = 0;}
				if(!isset($result[$i]['payuser'])){$result[$i]['payuser'] = 0;}
				if(!isset($result[$i]['fnum'])){$result[$i]['fnum'] = 0;}
				if(!isset($result[$i]['fuser'])){$result[$i]['fuser'] = 0;}
				if(!isset($result[$i]['num'])){$result[$i]['num'] = 0;}
				if(!isset($result[$i]['user'])){$result[$i]['user'] = 0;}

				if(isset($result[$i]['payamount'])){
					$result[$i]['payamount']=$result[$i]['payamount']/100;
				}else{
					$result[$i]['payamount'] = 0;
				}

				if(isset($result[$i]['famount'])){
					$result[$i]['famount']=$result[$i]['famount']/100;
				}else{
					$result[$i]['famount'] = 0;
				}

				if(isset($result[$i]['amount'])){
					$result[$i]['amount']=$result[$i]['amount']/100;
				}else{
					$result[$i]['amount'] = 0;
				}
		}
		return $result;
    }

    public function newpaydata($channelTagFlag)
    {
        $where = '';
        if ($this->timeLine == 'true') {
            $where = 't1.createtime >= ' . $this->times[0] . ' and t1.createtime < ' . ($this->times[1] + 86400);
        } else {
            foreach ($this->times as $time) {
                $where .= '(t1.createtime >= ' . $time . ' and t1.createtime < ' . ($time + 86400) . ') OR ';
            }
            $where = substr($where, 0, -3);
            $where = '(' . $where . ')';
        }
        if ($channelTagFlag == "true") {
            $sql = 'select FROM_UNIXTIME(t1.createtime,"%Y-%m-%d") as date, sum(amount) as num,count(t2.id) as account,t2.lost_status from recharge t1 left join account t2 on t2.id= t1.account_id where ' . $where . '  and t2.lost_status = 0  and t2.platform in("' . implode('","', $this->channels) . '") group by date';
            $result = $this->_db->fetchAll($sql);
            $sql2 = 'select FROM_UNIXTIME(t1.createtime,"%Y-%m-%d") as date, sum(amount) as fnum, t2.lost_status from recharge t1 left join account t2 on t2.id= t1.account_id where ' . $where . '  and t2.lost_status = 0  and t2.platform in("' . implode('","', $this->channels) . '") and (t1.createtime - t2.createtime < 86400) group by date';
            $result2 = $this->_db->fetchAll($sql2);
            $result = $this->formatData2($result, $result2, true);
        } else {
            $sql = 'select FROM_UNIXTIME(t1.createtime,"%Y-%m-%d") as date, sum(amount) as num,count(t2.id) as account, t2.platform as channel,t3.note,t2.lost_status from recharge t1
			 left join account t2 on t2.id= t1.account_id 
			 left join channel t3 on t2.platform= t3.channel 
			 where ' . $where . '  and t2.lost_status = 0 and t2.platform in("' . implode('","', $this->channels) . '") group by t2.platform,date';
            $result = $this->_db->fetchAll($sql);
            $sql2 = 'select FROM_UNIXTIME(t1.createtime,"%Y-%m-%d") as date, sum(amount) as fnum, t2.platform as channel,t3.note,t2.lost_status from recharge t1
			 left join account t2 on t2.id= t1.account_id 
			 left join channel t3 on t2.platform= t3.channel 
			 where ' . $where . '  and t2.lost_status = 0 and t2.platform in("' . implode('","', $this->channels) . '") and (t1.createtime - t2.createtime < 86400) group by t2.platform,date';
            $result2 = $this->_db->fetchAll($sql2);
            $result = $this->formatData2($result, $result2);
        }

        return $result;
    }

    public function formatData2($data, $data2, $flag = false)
    {
        $arr = $this->timeArr;
        $where = $this->_db->quoteInto('channel IN(?)', $this->channels);
        $channel = $this->_db->fetchAll('select channel,note from channel where ' . $where . ' ORDER BY channel ASC');
        $table = array();
        if ($flag) {
            foreach ($arr as $i) {
                array_push($table, array('date' => date("Y-m-d", $i['date']), 'channel' => '累加渠道', 'num' => 0, 'account' => 0, 'fnum' => 0));
            }
            if ($data) {
                foreach ($table as &$v) {
                    foreach ($data as $a) {
                        if ($v['date'] == $a['date']) {
                            $v = $a;
                            $v['num'] = $v['num'] / 100;
                            $v['channel'] = '累加渠道';
                        }
                    }
                    foreach ($data2 as $a2) {
                        if ($v['date'] == $a2['date']) {
                            $v['fnum'] = $a2['fnum'] / 100;
                        }
                    }
                }
            } else {
                return $table;
            }
        } else {
            foreach ($arr as $i) {
                foreach ($channel as $k) {
                    array_push($table, array('date' => date("Y-m-d", $i['date']), 'channel' => $k['note'], 'num' => 0, 'account' => 0, 'fnum' => 0));
                }
            }
            if ($data) {
                foreach ($table as &$v) {
                    foreach ($data as $a) {
                        if (($v['date'] == $a['date']) && ($v['channel'] == $a['note'])) {
                            $v = $a;
                            $v['num'] = $v['num'] / 100;
                            $v['channel'] = $v['note'];
                        }
                    }

                    foreach ($data2 as $a2) {
                        if (($v['date'] == $a2['date']) && ($v['channel'] == $a2['note'])) {
                            $v['fnum'] = $a2['fnum'] / 100;
                        }
                    }
                }
            }
        }

        return $table;
    }
	
	public function paymentratio() {
		$result = [];
		$user = $this->user();
		$num = $this->num();
		for($i=0;$i<$this->timeLength;$i++){
			$result[$i] = array_merge($user[$i],$num[$i]);
			if(isset($num[$i]['dnum']) && isset($user[$i]['adduser'])){
				$result[$i]['drate'] = intval(($num[$i]['dnum']/$user[$i]['adduser'])*100).' %';
			}else{
				$result[$i]['drate'] = 0;
			}

			if(isset($num[$i]['wnum']) && isset($user[$i]['adduser'])){
				$result[$i]['wrate'] = intval(($num[$i]['wnum']/$user[$i]['adduser'])*100).' %';
			}else{
				$result[$i]['wrate'] = 0;
			}

			if(isset($num[$i]['mnum']) && isset($user[$i]['adduser'])){			
				$result[$i]['mrate'] = intval(($num[$i]['mnum']/$user[$i]['adduser'])*100).' %';
			}else{
				$result[$i]['mrate'] = 0;
			}
			if(!isset($result[$i]['dnum'])){$result[$i]['dnum'] = 0;}
			if(!isset($result[$i]['adduser'])){$result[$i]['adduser'] = 0;}
			if(!isset($result[$i]['wnum'])){$result[$i]['wnum'] = 0;}
			if(!isset($result[$i]['mnum'])){$result[$i]['mnum'] = 0;}
		}
		return $result;
	}
	
	public function platform() {
		$select = clone $this->select;
		$select->from('paymentbyplatform', array('date', 'sum(num) AS num', 'sum(t_user) AS user','sum(t_amount) AS amount'));
		$select->group('date');
		$result = $this->_db->fetchAll($select);
		$result = $this->formatData($result);
		// print_r($result);
		// die();
        return $result;
	}

	public function paydata() {
		$select = clone $this->select;
		$select->from('payment', array('date', 'sum(num) AS paynum', 'sum(t_user) AS payuser','sum(t_amount) AS payamount'));
		$select->group('date');
		$result = $this->_db->fetchAll($select);
		$result = $this->formatData($result);
		// print_r($result);
		// die();
        return $result;
	}

	public function firsttime() {
		$select = clone $this->select;
		$select->from('firstpayment', array('date', 'sum(num) AS fnum', 'sum(t_user) AS fuser','sum(t_amount) AS famount'));
		$select->group('date');
		$result = $this->_db->fetchAll($select);
		$result = $this->formatData($result);
		//print_r($result);
		return $result;
    }

	public function user() {
		$select = clone $this->select;
		$select->from('user', array('date', 'sum(user) AS adduser'));
		$select->group('date');
		$result = $this->_db->fetchAll($select);
		$result = $this->formatData($result);
        return $result;
	}
	
	public function num() {
		$select = clone $this->select;
		$select->from('paymentratio', array('date', 'sum(d_num) AS dnum', 'sum(w_num) AS wnum','sum(m_num) AS mnum'));
		$select->group('date');
		$result = $this->_db->fetchAll($select);
		$result = $this->formatData($result);
        return $result;
    }

    public function totalcost()
    {
        $date = $this->times[0];
        $sql = "SELECT from_unixtime(date,'%Y-%m-%d') as date,sum(amount)/100 as amount,sum(r_user) as ruser,sum(p_user) as puser,CEIL(sum(amount)/sum(r_user))/100 AS r_cost,CEIL(sum(amount)/sum(p_user))/100 AS p_cost FROM totalcost WHERE channel IN ('" . implode("','", $this->channels) . "') AND date >= ({$date} -86400) AND date <= {$date} group by date order by date desc";
        $result = $this->_db->fetchAll($sql);
        if (isset($result[1])) {
            foreach ($result[1] as $key => &$val) {
                if (isset($val) && isset($result[0][$key]) && $key != 'date') {
                    $val = intval(($result[0][$key] - $val)*100)/100;
                } else {
                    $val = '-';
                }
            }
            $result[1]['date'] = '数据变化';
        }
        return $result;
    }

    public function userlive()
    {
        $res = array();
        $date = $this->times[0];
        $data = $this->_db->fetchAll("SELECT id, COALESCE ( dau, '-' ) as dau,COALESCE ( user, '-' ) as user, CONCAT(
                IF
                    (
                        (COALESCE ( user, '-' ) != '-') && (COALESCE ( dau, '-' ) != '-'),
                        FLOOR(dau/user*1000),
                        '-' 
                    ),
                    '‰'
            ) AS rate FROM t1000 LEFT JOIN (SELECT sum(dau) as dau,sum(r_user) as user,CEIL((date - {$date})/86400) as date FROM totalcost WHERE channel IN ('" . implode("','", $this->channels) . "')" . " AND date > {$date} AND date <= ({$date} +86400*30) GROUP BY date) v1 ON v1.date = t1000.id WHERE t1000.id<=30");
        $dau = array();
        $user = array();
        $rate = array();
        $sum_dau = 0;
        $sum_user = 0;
        $sum_rate = 0;
        $num_dau = 0;
        $num_user = 0;
        $num_rate = 0;
        foreach ($data as $index => $val) {
            $dau[$index+1] = $val['dau'];
            $user[$index+1] = $val['user'];
            $rate[$index + 1] = $val['rate'];

            if ($val['dau'] != '-') {
                $sum_dau += $val['dau'];
                $num_dau += 1;
            }

            if ($val['user'] != '-') {
                $sum_user += $val['user'];
                $num_user += 1;
            }

            if ($val['rate'] != '-‰') {
                $sum_rate += $val['rate'];
                $num_rate +=1;
            }
            if($index == 7){
                $dau['avg7'] = ($num_dau!=0)? floor($sum_dau/ $num_dau) :'-';
                $user['avg7'] = ($num_user != 0) ? floor($sum_user / $num_user) : '-';
                $rate['avg7'] = (($num_rate != 0) ? floor($sum_rate / $num_rate*100)/100 : '-').'‰';
            }
            $dau['avg30'] = ($num_dau != 0) ? floor($sum_dau / $num_dau) : '-';
            $user['avg30'] = ($num_user != 0) ? floor($sum_user / $num_user) : '-';
            $rate['avg30'] = (($num_rate != 0) ? floor($sum_rate / $num_rate * 100) / 100 : '-') . '‰';
        }
        $dau['date'] = $user['date'] = $rate['date'] = date('Y-m-d', $date);
        $dau['title'] = '日活数';
        $user['title'] = '总注册数';
        $rate['title'] = '日活率';
        array_push($res, $dau, $user, $rate);
        return $res;
    }

    public function userlost()
    {
        $sql = $this->buildSql("churnratio", "from_unixtime(z.date,'%Y-%m-%d') as date,
            CONCAT(
                IF
                    (
                        COALESCE ( sum(user_2), 0 ) != 0,
                        FLOOR(sum(churn_2)/sum(user_2)*100),
                        '-' 
                    ),
                    '%'
            ) AS lost2,
            CONCAT(
                IF
                    (
                        COALESCE ( sum(user_3), 0 ) != 0,
                        FLOOR(sum(churn_3)/sum(user_3)*100),
                        0 
                    ),
                    '%'
            ) AS lost3,
            CONCAT(
                IF
                    (
                        COALESCE ( sum(user_7), 0 ) != 0,
                        FLOOR(sum(churn_7)/sum(user_7)*100),
                        0 
                    ),
                    '%'
            ) AS lost7,
            CONCAT(
                IF
                    (
                        COALESCE ( sum(user_30), 0 ) != 0,
                        FLOOR(sum(churn_30)/sum(user_30)*100),
                        0 
                    ),
                    '%'
            ) AS lost30");
        $result = $this->_db->fetchAll($sql);
        return $result;
    }
}
?>