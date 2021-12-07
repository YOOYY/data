<?php

require_once APPLICATION_ROOT_PATH . 'models/Accept.php';
require_once APPLICATION_ROOT_PATH . 'models/Logs.php';

class AcceptController extends Admin_Action {
     
    public function preDispatch() {
        $this->_db = Zend_Registry::get('db');
        //后台管理员对象
        $this->_accept = new Accept($this->_db);
        $this->param = $this->_getallParams();

        foreach ($this->param as $k => $v) {
            Logs::Write('Kalipay', 'Type:Kalipay, ' . $k . ':' . ($v) . ', Error:参数');
        }
	    $arr = ['controller'=>'','action'=>'','module'=>''];
        $this->param=array_diff_key($this->param,$arr);
    }

    public function indexAction(){
        require_once APPLICATION_ROOT_PATH . 'models/KalipayBase.php';
        // $_POST = array(
        //     "amount"=>"100.00",
        //     "payment_reference"=>"40003408",
        //     "provider_reference"=>"WPPk2k6byenejlkcD",
        //     "reference"=>"ANR20191104164005207D",
        //     "status"=>"OK",
        //     "sign"=>"e948520fd9321887ba20666b93ce5464",
        //     "product_id"=>"5"
        // );
        $key = "3cb32ab8a4ae732ac831295aab219cf0";
        $sign = $_POST["sign"];
        $Kalipay = new kalipayBase($this->_db);
        $mysign = $Kalipay->buildSign($_POST,$key);
        $isSign = false;
        Logs::Write('Kalipay', 'Type:Kalipay,验证签名:我的签名:  ' . $mysign . '回调签名：'.$sign);
        if ($mysign == $sign) {
            $isSign = true;
        } else {
            $isSign = false;
        }

        if ($isSign) {
            $msg = array("success" => true,"message" => "更新成功");
            echo json_encode($msg);
        } else {
            $msg = array("success" => false,"message" => "更新成功");
            echo json_encode($msg);
        }
    }

    public function xwregisterAction(){
        $data = $this->_accept->xwregister($this->param);
        if($data){
            $array = array("success"=>1,"message"=>"查询成功","data"=>$data);
        }else{
            $array = array("success"=>0,"message"=>"查询失败","data"=>array());
        }
        echo json_encode($array);
    }

    public function xwgameinfoAction(){
        $data = $this->_accept->xwgameinfo($this->param);
        if($data){
            $array = array("success"=>1,"message"=>"查询成功","data"=>$data);
        }else{
            $array = array("success"=>0,"message"=>"查询失败","data"=>array());
        }
        echo json_encode($array);
    }

    //记录巨量引擎统计数据
    public function qihooAction(){
        $res = $this->_accept->acceptqihoo($this->param);
    }
}
