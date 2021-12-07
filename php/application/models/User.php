<?php
require_once APPLICATION_ROOT_PATH . 'models/Base.php';

class user extends Base
{

    public function newdata()
    {
        $col = 'from_unixtime( z.date, "%Y-%m-%d" ) AS date,
		IFNULL ( sum( user ), "-" ) AS user,
		IFNULL ( sum( machine ), "-" ) AS machine,
		IFNULL ( sum( activeuser ), "-" ) AS activeuser,
		CONCAT(
			IF
				(
					!ISNULL(sum( machine )) || !ISNULL(sum( activeuser )),
					CEIL( sum( activeuser ) / sum( machine ) * 100 ),
					"-"
				),
			"%" 
		) AS rate';
        $sql = $this->buildSql('user', $col);
        return $this->_db->fetchAll($sql);
    }
    public function balance()
    {
        $this->select->from('balance', array('date', 'gametype AS type', 'sum(balance) AS balance'));
        $this->select->group('date');
        $this->select->group('gametype');
        $result = $this->_db->fetchAll($this->select);
        $sql = 'select * from gametype ORDER BY id';
        $types = $this->_db->fetchAll($sql);

        $res = $this->timeArr;

        foreach ($res as &$sval) {
            foreach ($types as $val) {
                $sval[$val['id']] = 0;
            }
        }

        foreach ($result as $val) {
            foreach ($res as &$sval) {
                if ($val['date'] == $sval['date']) {
                    $sval[($val['type'])] = $val['balance'];
                    break;
                }
            }
        }

        foreach ($res as &$val) {
            $val['date'] = date("Y-m-d", $val['date']);
        }
        return $res;
    }
    public function iosdata()
    {
        $this->select->from('ios', array('date', 'sum(user) AS user', 'sum(recharge) AS recharge'));
        $this->select->group('date');
        $result = $this->_db->fetchAll($this->select);
        $result = $this->formatData($result);
        for ($i = 0; $i < $this->timeLength; $i++) {
            if (!isset($result[$i]['recharge'])) {
                $result[$i]['recharge'] = 0;
            } else {
                $result[$i]['recharge'] = $result[$i]['recharge'] / 100;
            }
            if (!isset($result[$i]['user'])) {
                $result[$i]['user'] = 0;
            }
        }
        return $result;
    }

    public function todayPay()
    {
        $url = '#';
        $param = array('platform' => implode(',', $this->channels));
        // $res = $this->_Get($url,$param);
        // $res = json_decode($res,true);
        // $result = $res['data']['rechargelist'];
        $result = array();
        $where = $this->_db->quoteInto('channel IN(?)', $this->channels);
        $arr = $this->_db->fetchAll('select channel,note from channel where ' . $where . ' ORDER BY channel ASC');
        foreach ($result as &$val) {
            foreach ($arr as $sindex => $sval) {
                if ($val['platform'] == $sval['channel']) {
                    $val['platform'] = $sval['note'];
                }
            }
            $val['amount'] = $val['amount'] / 100;
        }
        return $result;
    }

    public function regtime()
    {
        $regtime = array('money' => [], 'num' => []);
        $selects = $this->_db->select();
        //渠道
        $selects->Where('channel IN(?)', $this->channels);

        for ($i = 0; $i < $this->timeLength; $i++) {
            $select = clone $selects;
            $time = $this->timeArr[$i]['date'];
            $select->where('regdays = ?', $time);
            $money = array("regdays" => date("Y-m-d", $time));
            $num = array("regdays" => date("Y-m-d", $time));
            for ($k = 0; $k < 30; $k++) {
                $selectalone = clone $select;
                $selectalone->where('date = ?', $time + $k * 86400);
                $selectalone->from('paymentbyday', array('sum(t_amount) AS amount', 'sum(num) AS num'));
                $val = $this->_db->fetchRow($selectalone);
                if ($val['amount']) {
                    $money[$k] = $val['amount'] / 100;
                } else {
                    $money[$k] = 0;
                }
                if ($val['num']) {
                    $num[$k] = $val['num'];
                } else {
                    $num[$k] = 0;
                }
            }
            array_push($regtime['money'], $money);
            array_push($regtime['num'], $num);
        }
        return $regtime;
    }

    public function link()
    {
        $sql = $this->buildSql("link", "from_unixtime(z.date,'%Y-%m-%d') as date,COALESCE ( sum(useropen), 0 ) AS useropen,COALESCE ( sum(userstay3), 0 ) AS userstay3,            CONCAT(
                IF
                    (
                        COALESCE ( sum(useropen), 0 ) != 0,
                        FLOOR(sum(userstay3)/sum(useropen)*100),
                        '-'
                    ),
                    '%'
        ) AS rate");
        $result = $this->_db->fetchAll($sql);
        return $result;
    }

    public function retention()
    {
        $res = array();
        $date = $this->times[0];
        $data = $this->_db->fetchAll("SELECT id,COALESCE ( num, 0 ) as num,COALESCE ( daunum, '-' ) as daunum,
        CONCAT(
                IF
                    (
                        (COALESCE ( dauuser, 0 ) != 0) && (COALESCE ( daunum, '-' ) != '-'),
                        FLOOR(daunum/dauuser*100),
                        '-'
                    ),
                    '%'
        ) AS daurate
        FROM t1000 LEFT JOIN (SELECT sum(num) as num,sum(dau_num) as daunum,CEIL((date - regdays)/86400) as retenday FROM retention WHERE platform IN ('" . implode("','", $this->channels) . "')" . " AND regdays = {$date} GROUP BY date) v1 ON v1.retenday = t1000.id LEFT JOIN (SELECT count(*) as dauuser, CEIL((day - ${date})/86400) as date FROM gamelog t1 left join account t2 on t2.id = t1.playerid where t1.day >= {$date} AND t1.day <= ({$date}+30*86400) and t2.platform IN ('" . implode("','", $this->channels) . "') GROUP BY date) v2 ON v2.date = t1000.id WHERE t1000.id<=30");
        $user = $this->_db->fetchOne("SELECT sum(user) from user where date = {$date} AND channel IN ('" . implode("','", $this->channels) . "')");
        $dau = array();
        $add = array();
        $daurate = array();
        $rate = array();
        foreach ($data as $val) {
            $i = $val['id'];
            $add[$i] = $val['num'];
            $dau[$i] = $val['daunum'];
            $daurate[$i] = $val['daurate'];
            if ($user != 0) {
                $rate[$i] = ceil($val['num'] / $user * 100) . '%';
            } else {
                $rate[$i] = '-';
            }
        }
        $add['title'] = '新增账号';
        $add['date'] = date('Y-m-d', $date);
        $dau['title'] = '活跃账号';
        $dau['date'] = date('Y-m-d', $date);
        $rate['title'] = '新增留存率';
        $rate['date'] = date('Y-m-d', $date);
        $daurate['title'] = '活跃留存率';
        $daurate['date'] = date('Y-m-d', $date);
        array_push($res, $add, $rate, $dau, $daurate);
        return $res;
    }

    public function _Get($url, $params = false)
    {
        $options = array('http' => array('method' => "GET", 'timeout' => 10, 'header' => 'qiutao:leilei'));
        //二维数组：$options里包含着http，http包含数组method' => "GET", 'timeout' => 10, 'header' => 'qiutao:leilei'
        if ($params) $url = $url . "?" . http_build_query($params);
        return file_get_contents($url, false, stream_context_create($options));
        //file_get_contents读取文件
    }
}
