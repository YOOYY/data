<?php
require_once APPLICATION_ROOT_PATH . 'models/Payment.php';
require_once APPLICATION_ROOT_PATH . 'models/Util.php';

class PaymentController extends Admin_Action
{

    public function preDispatch()
    {
        $req = json_decode(file_get_contents('php://input'));
        $timeLine = $req->timeLine;
        $times = $req->times;
        $channels = $req->channels;
        // $timeLine = $this->_getParam('timeLine');
        // $channels = $this->_getParam('channels');
        // $times = $this->_getParam('times');
        $requireTime = true;
        $require = true;
        $action = $this->_getParam('action');
        $this->_payment = new Payment($timeLine, $channels, $times, $action, $require, $requireTime);
        $this->util = new Util();
    }

    public function paydataAction()
    {
        try {
            $res = $this->_payment->paiddata();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }

    public function newpaydataAction()
    {
        try {
            $req = json_decode(file_get_contents('php://input'));
            $channelTagFlag = $req->channelTagFlag;
            $res = $this->_payment->newpaydata($channelTagFlag);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $e;
            // $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }

    public function paytratioAction()
    {
        try {
            $res = $this->_payment->paymentratio();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }
    public function totalcostAction()
    {
        try {
            $res = $this->_payment->totalcost();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }
    public function userlostAction()
    {
        try {
            $res = $this->_payment->userlost();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }

    public function userliveAction()
    {
        try {
            $res = $this->_payment->userlive();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }
}
