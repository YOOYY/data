<?php
require_once APPLICATION_ROOT_PATH . 'models/Base.php';

class Index{
    public function __construct() {
        $this->_db = Zend_Registry::get('db');
             	// 获取登陆信息
		$this->privilege = empty($_SESSION['ADMIN_PRIVILEGE']) ? '' : $_SESSION['ADMIN_PRIVILEGE'];
		//通用权限
		$this->baseprivilege = ['index','sitestat','log','error'];
		//权限
    }
    
    //管理员登陆后台
    public function login($name, $password) {
        if ($this->checkAdminUser(trim($name), $this->_encrypt(trim($password)))) {
            $result = $this->_db->fetchRow('select * from adminuser where name=?', array($name));
            $this->_db->update('adminuser', array('last_login_ip' => Util::getip(), 'last_login_date' => date("Y-m-d H:i:s")), ' name= \'' . $name . '\'');
            $this->_db->insert('recordadminlogin', array('login_ip' => Util::getip(), 'login_date' => date("Y-m-d H:i:s"), 'name' => $name));
            $_SESSION['ADMIN_NAME'] = $result['name'];
            $_SESSION['ADMIN_PASSWORD'] = $result['password'];
            $_SESSION['ADMIN_ID'] = $result['auid'];
            $_SESSION['ADMIN_NICKNAME'] = $result['nickname'];
            $_SESSION['ADMIN_PRIVILEGEID'] = $result['privilege'];
            $_SESSION['ADMIN_PRIVILEGE'] = $this->getPList($result['privilege']);
            $_SESSION['ADMIN_CHANNELFLAG'] = $result['channeltag']=='*'?true:false;
            if($_SESSION['ADMIN_CHANNELFLAG']){
                $channelArr = $this->_db->fetchCol('select channel from channel');
            }else{
                $channelArr = explode(',',$result['channel']);
            }
            $_SESSION['ADMIN_CHANNEL'] = $channelArr;
            $_SESSION['ADMIN_IMG'] = $result['userimg'];
            return true;
        } else {
            return false;
        }
    }

    /**
	 * 权限验证
	 * @param Array 
	 * @return Array
	 */
	function checkRight($controller,$action) {
        $privilegeArr = array_merge($this->privilege,$this->baseprivilege);
        $controllerArr = ['channel','privilege','admin'];
        if(!in_array($controller,$controllerArr)){
            $controller = '';
        }
        if(in_array($controller,$privilegeArr) || in_array($action,$privilegeArr)){
            return true;
        }else{
            return false;
        }
    }

        /**
     * 判断管理员是否登陆
     * @return bool false 未登陆，true 已登陆
     */
    public function checkLogin() {
        if (
            empty($_SESSION['ADMIN_ID']) ||
            empty($_SESSION['ADMIN_NAME']) ||
            empty($_SESSION['ADMIN_PASSWORD']) ||
            !$this->checkAdminUser($_SESSION['ADMIN_NAME'], $_SESSION['ADMIN_PASSWORD'])
        ) {
            return false;
        } else {
            return true;
        }
    }

    //登录验证
    public function checkAdminUser($name, $password) {
        $getpassword = $this->_db->fetchOne('select password from adminuser where name=? and is_used = ? and tag = 1', array($name, 0));
        if (empty($getpassword) || $getpassword != $password)
            return false;
        else
            return true;
    }

    //密码加密
    private function _encrypt($str) {
        return md5(md5($str));
    }

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
     * 获取登陆信息
     * @return Array (id=>,name=>, password=> privilege=>)
     */
    public static function getLoginInfo() {
        return Array(
            'adminID' => empty($_SESSION['ADMIN_ID']) ? '' : $_SESSION['ADMIN_ID'],
            'name' => empty($_SESSION['ADMIN_NAME']) ? '' : $_SESSION['ADMIN_NAME'],
            'nickName' => empty($_SESSION['ADMIN_NICKNAME']) ? '' : $_SESSION['ADMIN_NICKNAME'],
            'privilegeID' => empty($_SESSION['ADMIN_PRIVILEGEID']) ? 0 : $_SESSION['ADMIN_PRIVILEGEID'],
            'privilege' => empty($_SESSION['ADMIN_PRIVILEGE']) ? '' : $_SESSION['ADMIN_PRIVILEGE'],
            'userImg' => empty($_SESSION['ADMIN_IMG']) ? '' : $_SESSION['ADMIN_IMG'] ,
            'channels' => empty($_SESSION['ADMIN_CHANNEL']) ? '' : $_SESSION['ADMIN_CHANNEL'] ,
            'channelFlag' => empty($_SESSION['ADMIN_CHANNELFLAG']) ? '' : $_SESSION['ADMIN_CHANNELFLAG'] ,
        );
    }

    //查询渠道组
    function getCgroup() {
        $result = [];
        $where = $this->_db->quoteInto('channel IN(?)', $_SESSION['ADMIN_CHANNEL']);
        $arr = $this->_db->fetchAll('select channel,note from channel where '.$where.' ORDER BY channel ASC');
        $res = $this->_db->fetchAll('select id, name, channel from channelgroup where id != 1');
        foreach($res as $index => &$val){
            $channels = explode(',',$val['channel']);
            $group = array();
            foreach($arr as $sindex => &$sval){
                if(in_array($sval['channel'],$channels)){
                    array_push($group,$sval);
                    unset($arr[$sindex]);
                }
            }
            $val['channel'] = $group;
            if(!$val['channel']){
                unset($res[$index]);
            }
        }
        if($arr){
            $arr = array_values($arr);
            array_push($res,['name' =>'未分组渠道','id' => '-1','channel'=>$arr]);
        }
        $res = array_values($res);
        return $res;        
    }

    //查询权限组
    function getPgroup($id) {
        $where = $this->_db->quoteInto('parent= ?', $id);
        $res = $this->_db->fetchAll('select name,id,note,privilege from privilegegroup where '.$where);
        foreach($res as &$val){
            $val['privilege'] = explode(',',$val['privilege']);
            sort($val['privilege']);
            array_shift($val['privilege']);
        }
        return $res;        
    }
}
