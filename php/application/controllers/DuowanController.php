<?php

require_once APPLICATION_ROOT_PATH . 'models/Accept.php';
require_once APPLICATION_ROOT_PATH . 'models/Logs.php';

class DuowanController extends Admin_Action {
     
    public function preDispatch() {
        $this->_db = Zend_Registry::get('db');
        //后台管理员对象
        $this->_accept = new Accept($this->_db);
        $param = $this->_getAllParams();
        
        foreach($param as $k=>$v){
            logs::write('debug',$k.$v);
        }
	    $arr = ['controller'=>'','action'=>'','module'=>''];
        $this->param=array_diff_key($param,$arr);
    }

    //接受巨量引擎发送的统计数据
    public function indexAction(){
        print_r($this->param);
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
}
