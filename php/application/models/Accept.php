<?php
/**
 * 在线数据类
 */
class Accept {

    private $_db;

    public function __construct($db) {
        $this->_db = $db;
        $this->_select = $this->_db->select();
    }

    //接受巨量引擎发送的统计数据
    public function acceptqihoo($param){
        return $this->_db->insert('acceptqihoo', $param);
    }

    //多玩注册信息查询接口
    public function xwregister($param){
        try{
            // $this->_select
            // ->from('acceptxianwan',array('gamecount','gold','userid'))
            // ->where('ptype = ?', $param['ptype'])
            // ->where('deviceid = ?', $param['deviceid'])
            // ->where('xwid = ?', $param['xwid'])
            // ->where('channel = ?', $param['channel'])
            // ->where('keycode = ?', $param['keycode']);
            $this->_select
            ->from('recharge',array('sum(amount) as amount','account_id as userid'))
            ->where('account_id = ?', $param['account_id'])
            ->where('platform = ?', $param['platform']);
            $res = $this->_db->fetchRow($this->_select);
        }catch(Exception $e){
            $res = false;
        }
        return $res;
    }

    //多玩游戏信息查询接口
    public function xwgameinfo($param){
        try{
            // $this->_select
            //     ->from('acceptxianwan',array('gamecount','gold','userid'))
            //     ->where('userid = ?', $param['userid'])
            //     ->where('channel = ?', $param['channel'])
            //     ->where('keycode = ?', $param['keycode']);
            $this->_select
            ->from('recharge',array('sum(amount) as amount','account_id as userid'))
            ->where('account_id = ?', $param['account_id'])
            ->where('platform = ?', $param['platform']);
            $res = $this->_db->fetchRow($this->_select);
        }catch(Exception $e){
            $res = false;
        }
        return $res;
    }
}
