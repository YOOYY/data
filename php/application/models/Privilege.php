<?php

class Privilege{

    protected $_db; 

    public function __construct() {
        $this->_db = Zend_Registry::get('db');
        $this->select = $this->_db->select();
        $this->PRIVILEGEID = $_SESSION['ADMIN_PRIVILEGEID'];
        $this->PRIVILEGE = $_SESSION['ADMIN_PRIVILEGE'];
    }

    function getList(){
        $res = $this->_db->fetchOne('select privilege from privilegegroup where id = 1');
        $res = explode(',',$res);
        sort($res);
        array_shift($res);
        return $res;
    }

    function getAllGroup(){
        $res = $this->_db->fetchAll('select name,id,note,privilege from privilegegroup');
        foreach($res as &$val){
            $val['privilege'] = explode(',',$val['privilege']);
            sort($val['privilege']);
            array_shift($val['privilege']);
        }
        return $res;
    }

    //新增权限组
    function addGroup($name,$privilege,$note) {
        if(is_array($privilege)){
            $privilege = implode(',', $privilege).',';
        }
        $param = Array(
            'name' => trim($name),
            'privilege' => trim($privilege),
            'note' => $note,
            'parent' => '',
        );
        $this->_db->insert('privilegegroup', $param);
        $last_insert_id = $this->_db->lastInsertId();
        return $last_insert_id;
    }

    //删除权限组
    function delGroup($id) {
        $where = $this->_db->quoteInto('id= ?', $id);
        $res = $this->_db->delete('privilegegroup', $where);
        $where = $this->_db->quoteInto('privilege in (?)', $id);
        $opt = array('privilege'=> '');
        $this->_db->update('adminuser', $opt, $where);
        return $res;
    }

    //修改权限组
    function updateGroup($opt) {
        //取得删除的权限名数组
        $where = $this->_db->quoteInto('id= ?', $opt['id']);
        unset($opt['id']);
        $opt['privilege'] = implode(',',$opt['privilege']).',';
        return $this->_db->update('privilegegroup', $opt, $where);
    }
    
    function updateprivilege($parent,$arr){
        $where = $this->_db->quoteInto('parent= ?', $parent);
        $children = $this->_db->fetchAll('select id,privilege from privilegegroup where '.$where);
        if(!is_null($children)){
            foreach($children as $val){
                if($arr == false){
                    $data = ['privilege'=>''];
                }else{
                    $newChildren = $this->changeHandle($val['privilege'],$arr,false);
                    $data = ['privilege'=>$newChildren];
                }
                $where = $this->_db->quoteInto('id= ?', $val['id']);
                $this->_db->update('privilegegroup',$data,$where);
                $this->updateprivilege($val['id'],$arr);
            }
        }
    }

    function changeHandle($str,$privilege,$update){
        $arr = explode(',',$str);
        if(!is_array($privilege)){
            $privilege = array($privilege);
        }
        foreach ($privilege as $val){
            $index = array_search($val,$arr);
            if($index){
                if($update == false){
                    array_splice($arr,$index,1);            
                }else{
                    array_splice($arr,$index,1,$update);
                }
            }
        }
        $str = implode(',',$arr);
        return $str;
    }
}