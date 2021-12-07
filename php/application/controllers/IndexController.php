<?php
require_once APPLICATION_ROOT_PATH . 'models/Index.php';
require_once APPLICATION_ROOT_PATH . 'models/Util.php';
/**
 * 后台管理主页
 * @package admin
 */
class IndexController extends Admin_Action
{

    public function preDispatch()
    {
        $this->_index = new Index();
        $this->util = new Util();
    }

    /**
     * 加载框架页
     */
    public function indexAction()
    {
        //加载默认视图
        $this->render('index');
    }

    //管理员登陆后台
    public function loginAction()
    {
        $req = json_decode(file_get_contents('php://input'));
        $name = $req->name;
        $password = $req->password;
        try {
            $res = $this->_index->login($name, $password);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }

    //获取登陆信息
    public function getlogininfoAction()
    {
        try {
            $res = $this->_index->getLoginInfo();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }

    //查询权限
    public function getplistAction()
    {
        // $id = (int) $this->_getParam('id', 1);
        $req = json_decode(file_get_contents('php://input'));
        $id = $req->id;
        try {
            $res = $this->_index->getPlist($id);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    //查询权限组
    public function getpgroupAction()
    {
        $id = (int) $this->_getParam('id');
        try {
            $res = $this->_index->getPgroup($id);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    //查询权限组
    public function getcgroupAction()
    {
        $id = (int) $this->_getParam('id');
        try {
            $res = $this->_index->getCgroup($id);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $e;
            // $res = $this->util->err($e);
        }
        echo json_encode($res);
    }
}
