<?php

require_once APPLICATION_ROOT_PATH . 'models/User.php';
require_once APPLICATION_ROOT_PATH . 'models/Util.php';

class UserController extends Admin_Action
{

    public function preDispatch()
    {
        // $timeLine = $this->_getParam('timeLine');
        // $channels = $this->_getParam('channels');
        // $times = $this->_getParam('times');
        $req = json_decode(file_get_contents('php://input'));
        $timeLine = $req->timeLine;
        $times = $req->times;
        $channels = $req->channels;
        // $timeLine = true;
        // $channels = 'fishworld';
        // $times = [1527782400000,1533052800000];

        $requireTime = true;
        $require = true;
        $action = $this->_getParam('action');
        if ($action == 'iosdata') {
            $require = false;
        }
        if ($action == 'monthpayment') {
            return;
        }
        $this->_user = new User($timeLine, $channels, $times, $action, $require, $requireTime);
        $this->util = new Util();
    }

    public function balanceAction()
    {
        try {
            $res = $this->_user->balance();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }

    public function adddataAction()
    {
        try {
            $res = $this->_user->newdata();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }

    public function linkAction()
    {
        try {
            $res = $this->_user->link();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }

    public function iosdataAction()
    {
        try {
            $res = $this->_user->iosdata();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }

    public function daypayAction()
    {
        try {
            $res = $this->_user->regtime();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }

    public function retentionAction()
    {
        try {
            $res = $this->_user->retention();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }

    public function todaypayAction()
    {
        try {
            $res = $this->_user->todayPay();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }

    public function monthpaymentAction()
    {
        require_once APPLICATION_ROOT_PATH . 'models/MonthPayment.php';
        $req = json_decode(file_get_contents('php://input'));
        $timeLine = $req->timeLine;
        $regTimes = $req->regTimes;
        $regTimeLine = $req->regTimeLine;
        $times = $req->times;
        $channels = $req->channels;
        $monthPayment = new MonthPayment($timeLine, $channels, $times, $regTimes, $regTimeLine);
        try {
            $res = $monthPayment->monthPayment();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }
}
