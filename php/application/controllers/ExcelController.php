<?php

require_once APPLICATION_ROOT_PATH . 'models/Excel.php';
require_once APPLICATION_ROOT_PATH . 'models/Util.php';

class ExcelController extends Admin_Action
{

    public function preDispatch()
    {
        $req = json_decode(file_get_contents('php://input'));
        $timeLine = $req->timeLine;
        $times = $req->times;
        $channels = $req->channels;

        // $timeLine = $this->_getParam('timeLine');
        // $times = $this->_getParam('times');
        // $channels = $this->_getParam('channels');
        $requireTime = true;
        $require = true;
        $action = $this->_getParam('action');
        $this->Excel = new Excel($timeLine, $channels, $times, $action, $require, $requireTime);
        $this->util = new Util();
    }

    public function indexAction()
    {
        try {
            $res = $this->Excel->outExcel();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $e;
            //$res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }
}
