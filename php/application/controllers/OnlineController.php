<?php

require_once APPLICATION_ROOT_PATH . 'models/Online.php';

class OnlineController extends Admin_Action
{

    public function preDispatch()
    {
        $req = json_decode(file_get_contents('php://input'));
        $timeLine = $req->timeLine;
        $times = $req->times;

        $channels = [];
        $requireTime = true;
        $require = false;
        $action = $this->_getParam('action');
        $this->_online = new Online($timeLine, $channels, $times, $action, $require, $requireTime);
    }

    public function onlinetimeAction()
    {
        try {
            $res = $this->_online->onlinetime();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }
}
