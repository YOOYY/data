<?php
require_once APPLICATION_ROOT_PATH . 'models/Admin.php';
require_once APPLICATION_ROOT_PATH . 'models/Util.php';

class AdminController extends Admin_Action
{

    public function preDispatch()
    {
        $this->_db = Zend_Registry::get('db');
        //后台管理员对象
        $this->_admin = new Admin($this->_db);
        $this->util = new Util();
    }

    //添加用户
    public function adduserAction()
    {
        try {
            $req = json_decode(file_get_contents('php://input'));
            $name = $req->name;
            $password = $req->password;
            $nickname = $req->nickname;
            $privilege = $req->privilege;
            $channel = $req->channel;
            $channel = implode($channel, ',');
            $note = $this->_getParam('note');
            $param = array(
                'name' => trim($name),
                'password' => trim($password),
                'nickname' => trim($nickname),
                'privilege' => trim($privilege),
                'channel' => trim($channel),
                'tag' => 1,
                'last_login_date' => date("Y-m-d H:i:s"),
                'last_login_ip' => util::getip(),
                'note' => trim($note),
                'userimg' => '/static/img/user.jpg'
            );
            $res = $this->_admin->addAdminUser($param);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    //冻结用户
    public function useduserAction()
    {
        $id = json_decode(file_get_contents('php://input'))->id;
        try {
            $res = $this->_admin->usedAdminUser($id);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    //修改本账户密码
    public function updatemypasswordAction()
    {
        $oldpassword = $this->_getParam('oldpassword');
        $newpassword = $this->_getParam('password');
        $repassword = $this->_getParam('repassword');
        $name = $this->_getParam('name');
        $id = $this->_getParam('id');
        try {
            $res = $this->_admin->updateMyPassword($oldpassword, $newpassword, $repassword, $name, $id);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    // //修改权限
    // public function changecaAction() {
    //     $id = $this->_getParam('id');
    //     $privilege = $this->_getParam('privilege');
    //     $reprivilege = $this->_getParam('reprivilege');
    //     try {
    //         $result = $this->_admin->changeCa($id, $privilege, $reprivilege);
    //         $res = ['error'=>0,'data'=>$result];
    //     } catch (Exception $e) {
    //         $res = ['error'=>1,'data'=>'修改出错'];
    //     }
    //     echo json_encode($res);
    // }

    // //修改渠道
    // public function changechannelAction() {
    //     $id = $this->_getParam('id');
    //     $channel = $this->_getParam('channel');
    //     $rechannel = $this->_getParam('rechannel');
    //     try {
    //         $result = $this->_admin->changeChannel($id, $channel,$rechannel);
    //         $res = ['error'=>0,'data'=>$result];
    //     } catch (Exception $e) {
    //         $res = ['error'=>1,'data'=>'修改出错'];
    //     }
    //     echo json_encode($res);
    // }

    //获取账号列表
    public function getlistAction()
    {
        $req = json_decode(file_get_contents('php://input'));
        $id = $req->id;
        try {
            $result = $this->_admin->getList($id);
            $res = ['error' => 0, 'data' => $result];
        } catch (Exception $e) {
            $res = $e;
        }
        echo json_encode($res);
    }

    //更新用户
    public function updateuserAction()
    {
        // $adminid = $this->_getParam('adminid');
        // $id = $this->_getParam('id');
        // $password = $this->_getParam('password');
        // $nickname = $this->_getParam('nickname');
        // $privilege = $this->_getParam('privilege');
        // $channel = $this->_getParam('channel');
        // $note = $this->_getParam('note');
        // $opt = Array(
        //     'password' => trim($password),
        //     'nickname' => trim($nickname),
        //     'privilege' => trim($privilege),
        //     'channel' => trim($channel),
        //     'note' => trim($note),
        // );
        try {
            $opt = json_decode(file_get_contents('php://input'), true);
            $list = array('controller' => '1', 'action' => '1', 'module' => '1', 'adminid' => '0', 'id' => '0');
            $opts = array_diff_key($opt, $list);
            if (count($opts) == 0) {
                throw new Exception(10008);
            }
            $result = $this->_admin->updateAdminUser($opt['id'], $opts);
            $result = $this->_admin->getList($opt['adminid']);
            $res = ['error' => 0, 'data' => $result];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    // //查询渠道
    // public function getclistAction() {
    //     $channelId = $this->_getParam('channelId');
    //     try {
    //         $result = $this->_admin->getCList($channelId);
    //         $res = ['error'=>0,'data'=>$result];
    //     } catch (Exception $e) {
    //         $res = $this->util->err($e);
    //     }
    //     echo json_encode($res);
    // }

    //查询权限
    public function getplistAction()
    {
        $privilegeId = $this->_getParam('privilegeId');
        try {
            $result = $this->_admin->getPList($privilegeId);
            $res = ['error' => 0, 'data' => $result];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    //用户退出登陆
    public function loginoutAction()
    {
        try {
            $result = $this->_admin->loginOut();
            $res = ['error' => 0, 'data' => $result];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }
}
