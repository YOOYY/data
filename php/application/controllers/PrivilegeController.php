<?php
require_once APPLICATION_ROOT_PATH . 'models/Privilege.php';
require_once APPLICATION_ROOT_PATH . 'models/Util.php';

class PrivilegeController extends Admin_Action
{

    public function preDispatch()
    {
        $this->_db = Zend_Registry::get('db');
        $this->util = new Util();
        $this->privilege = new Privilege($this->_db);
    }

    //获取权限组
    public function getlistAction()
    {
        try {
            $res = $this->privilege->getList();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    //获取权限组
    public function getallgroupAction()
    {
        try {
            $res = $this->privilege->getAllGroup();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    //新增权限组
    public function addgroupAction()
    {
        $res = json_decode(file_get_contents('php://input'));
        $name = $res->name;
        $privilege = $res->privilege;
        $note = $res->note;
        try {
            $res = $this->privilege->addGroup($name, $privilege, $note);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    //删除权限组
    public function delgroupAction()
    {
        $id = json_decode(file_get_contents('php://input'))->id;
        try {
            $res = $this->privilege->delGroup($id);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    //修改权限组
    public function updategroupAction()
    {
        // $id = $this->_getParam('id');
        // $name = $this->_getParam('name');
        // $privilege = $this->_getParam('privilege');
        // $note = $this->_getParam('note');
        $opt = json_decode(file_get_contents('php://input'), true);
        try {
            $res = $this->privilege->updateGroup($opt);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }
}
