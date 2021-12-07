<?php

class Admin {

    private $_db;

    function __construct($db) {
        $this->_db = $db;
        $this->select = $this->_db->select();
    }

    //添加用户
    public function addAdminUser(Array $param) {
        $names = $this->_db->fetchAll('select name from adminuser where is_used != -1');
        if($param['name'] == ''||$param['password']==''){
            throw new Exception(10008);
        }
        if(array_search(['name' => $param['name']],$names) !== false){
            throw new Exception(10003);
        }
        $param['password'] = $this->_encrypt($param['password']);
        return $this->_db->insert('adminuser', $param);
    }

    //冻结用户
    public function usedAdminUser($id) {
        $info = $this->_getInfoByid($id);
        $opt = $info['is_used'] == '0' ? Array('is_used' => '-1') : Array('is_used' => '0');
        return $this->updateAdminUser($id, $opt);
    }
    
    //修改本账户密码
    public function updateMyPassword($oldpassword,$newpassword, $repassword, $name,$id) {
        if ($newpassword != $repassword) {
            throw new Exception(10011);
        }
        if (!$this->checkAdminUser(trim($name), $this->_encrypt(trim($oldpassword)))) {
            throw new Exception(10031); 
        }
        if ($oldpassword == $newpassword) {
            throw new Exception(10040);
        }
        $opt = Array('password' => $password);
        $_SESSION['ADMIN_PASSWORD'] = $this->_encrypt($opt['password']);
        $this->updateAdminUser($id, $opt);
        return $this->getList($id);
        }

    // //修改密码
    // public function updatePassword($newpassword, $id) {
    //     $password = $this->_encrypt(trim($newpassword));
    //     $opt = Array('password' => $password);
    //     $this->updateAdminUser($id, $opt);
    //     return $this->getList($id);
    // }

    // //修改权限
    // public function changeCa($id, $ca, $reca) {
    //     $opt = Array('privilege' => $ca);
    //     $this->updateAdminUser($id, $opt);
    //     return $this->getList($id);
    // }

    // //修改渠道
    // public function changeChannel($id, $channel,$rechannel) {
    //     $opt = Array('channel' => $channel);
    //     $this->updateAdminUser($id, $opt);
    //     return $this->getList($id);
    // }

    //获取账号信息
    public function _getInfoByid($id) {
        return $this->_db->fetchRow('select * from adminuser where auid = ?', $id);
    }

    //获取账号列表
    public function getList($id) {
        $sql = 'SELECT a.auid,a.name,a.nickname,a.last_login_ip,a.channeltag,a.last_login_date,a.privilege,a.note,a.channel,a.userimg,b.name AS privilegename FROM adminuser a LEFT JOIN privilegegroup b ON b.id = a.privilege WHERE a.is_used = 0 AND a.tag = 1';
        $res = $this->_db->fetchAll($sql);
        $channelAll = $this->_db->fetchAll('select channel,note from channel ORDER BY channel ASC');
        foreach($res as &$val){
            if($val['channeltag'] == '*'){
                $val['channel'] = $channelAll;
            }else{
                $channelArr = explode(',',$val['channel']);
                $result = array();
                foreach($channelAll as $chan){
                    if(in_array($chan['channel'] ,$channelArr)){
                        array_push($result,$chan);
                    }
                }
                $val['channel'] = $result;
                array_shift($val['channel']);
            }
        }
        return $res;
    }
    
    //更新用户
    public function updateAdminUser($id, $opt) {
        $where = $this->_db->quoteInto('auid= ?', $id);
        if(isset($opt['password'])){
            $opt['password'] = $this->_encrypt($opt['password']);
        }
        if(isset($opt['channel'])){
            $opt['channel'] = implode($opt['channel'],',').',';
        }
        $this->_db->update('adminuser', $opt, $where);
        
        // if(isset($opt['channel'])){
        //     $opt['channel'] = implode($opt['channel'],',');
        //     $this->updateHandle($id,'channel');
        // }
        // if(isset($opt['privilege'])){
        //     $this->updateHandle($id,'privilege');
        // }
        // if(isset($opt['is_used'])){
        //     $this->updateHandle($id,'is_used');
        // }
        return;
    }
    //更新辅助函数,渠道或权限变动时,子用户渠道或权限置空
    public function updateHandle($parent,$note){
        $where = $this->_db->quoteInto('parent= ?', $parent);
        $children = $this->_db->fetchAll('select auid , '.$note.' from adminuser where '.$where);
            foreach($children as $val){
                if($note == 'is_used'){
                    $data = [$note=>'-1'];
                }else{
                    $data = [$note=>''];
                }
                $where = $this->_db->quoteInto('auid= ?', $val['auid']);
                $this->_db->update('adminuser',$data,$where);
                $this->updateHandle($val['auid'],$note);
            }
    }

    //查询渠道
	// public function getCList($channelId){
    //     if($channelId == 0) return;
    //     $select = $this->_db->select();
    //     $select->Where('id IN(?)', $channelId);
    //     $select->from('channelgroup', 'channel');
    //     $result = $this->_db->fetchAll($select);
    //     $res = '';
    //     foreach($result as $val){
    //         $res .= $val['channel'];
    //     }
    //     $res = explode(',',$res);
    //     sort($res);
    //     array_shift($res);
    //     return $res;
    // }

    //查询权限
	public function getPList($privilegeId){
        if($privilegeId == 0) return;
        $select = $this->_db->select();
        $select->Where('id = ?', $privilegeId);
        $select->from('privilegegroup', 'privilege');
        $res = $this->_db->fetchOne($select);
        $res = explode(',',$res);
        sort($res);
        array_shift($res);
        return $res;
    }

    /**
     * 用户退出登陆
     */
    public static function loginOut() {
        unset(
            $_SESSION['ADMIN_NAME'],
            $_SESSION['ADMIN_PASSWORD'],
            $_SESSION['ADMIN_ID'],
            $_SESSION['ADMIN_NICKNAME'],
            $_SESSION['ADMIN_PRIVILEGEID'],
            $_SESSION['ADMIN_PRIVILEGE'],
            $_SESSION['ADMIN_CHANNELFLAG'],
            $_SESSION['ADMIN_CHANNEL'],
            $_SESSION['ADMIN_IMG']
        );
    }
    
    //密码加密
    private function _encrypt($str) {
        return md5(md5($str));
    }
    
    //登录验证
    public function checkAdminUser($name, $password) {
        $getpassword = $this->_db->fetchOne('select password from adminuser where name=? and is_used = ?', array($name, 0));
        if (empty($getpassword) || $getpassword != $password)
            return false;
        else
            return true;
    }

}
