<?php
require_once APPLICATION_ROOT_PATH . 'models/Channel.php';
require_once APPLICATION_ROOT_PATH . 'models/Util.php';

class ChannelController extends Admin_Action
{

    public function preDispatch()
    {
        $this->_db = Zend_Registry::get('db');
        $this->util = new Util();
        $this->_channel = new Channel($this->_db);
    }

    //查询渠道
    public function getlistAction()
    {
        // $id = (array) $this->_getParam('id', 1);
        $req = json_decode(file_get_contents('php://input'));
        $id = array($req->id);
        try {
            $res = $this->_channel->getList($id);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    //新增渠道
    public function addlistAction()
    {
        // $arr = $this->_getParam('channel');
        $req = json_decode(file_get_contents('php://input'));
        $channel = $req->channel;
        $note = $req->note;
        try {
            $res = $this->_channel->addList($channel, $note);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    //修改渠道
    public function updatelistAction()
    {
        // $channel = $this->_getParam('channel');
        // $reChannel = $this->_getParam('reChannel');
        // $oldChannel = $this->_getParam('oldChannel');
        $req = json_decode(file_get_contents('php://input'));
        $channel = $req->channel;
        $note = $req->note;
        try {
            $result = $this->_channel->updateList($channel, $note);
            $res = ['error' => 0, 'data' => $result];
        } catch (Exception $e) {
            $res = ['error' => 1, 'data' => '修改出错'];
        }
        echo json_encode($res);
    }

    //删除渠道
    public function dellistAction()
    {
        // $arr = $this->_getParam('channel');
        $req = json_decode(file_get_contents('php://input'));
        $channel = $req->channel;
        try {
            $res = $this->_channel->delList($channel);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    //查询渠道组
    public function getgroupAction()
    {
        require_once APPLICATION_ROOT_PATH . 'models/Index.php';
        try {
            $index = new Index();
            $res = $index->getCgroup();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    //查询渠道组
    public function getallgroupAction()
    {
        try {
            $res = $this->_channel->getAllGroup();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    //新增渠道组
    public function addgroupAction()
    {
        // $name = $this->_getParam('name','新建分组');
        // $channel = $this->_getParam('channel','');
        // $note = $this->_getParam('note','');
        // $parent = $this->_getParam('parent',0);
        $req = json_decode(file_get_contents('php://input'));
        $name = $req->name;
        $channel = $req->channel;
        $note = $req->note;
        try {
            $res = $this->_channel->addGroup($name, $channel, $note);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    //删除渠道组
    public function delgroupAction()
    {
        // $id = $this->_getParam('id');
        $req = json_decode(file_get_contents('php://input'));
        $id = $req->id;
        try {
            $res = $this->_channel->delGroup($id);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }

    //修改渠道组
    public function updategroupAction()
    {
        // $id = $this->_getParam('id');
        // $name = $this->_getParam('name');
        // $channel = $this->_getParam('channel');
        // $note = $this->_getParam('note');
        $req = json_decode(file_get_contents('php://input'));
        $id = $req->id;
        $name = $req->name;
        $channel = $req->channel;
        try {
            $res = $this->_channel->updateGroup($id, $name, $channel);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res);
    }
}
