<?php

class Channel{

    protected $_db; 

    public function __construct() {
        $this->_db = Zend_Registry::get('db');
        $this->select = $this->_db->select();
        $this->CHANNEL = $_SESSION['ADMIN_CHANNEL'];
        $this->CHANNELFLAG = $_SESSION['ADMIN_CHANNELFLAG'];
        $this->somechannel = array();
    }

    //查询渠道
	function getList($id){
        if($this->CHANNELFLAG){
            // $res = $this->_db->fetchAll('select channel,note from channel');
            $channelArr = $this->_db->fetchCol('select channel from channel');
        }else{
            $channelArr = explode(',',$this->CHANNEL);
            // $where = $this->_db->quoteInto('channel= ?', $channelArr);
            // $res = $this->_db->fetchAll('select channel,note from channel where '.$where);
        }
        $_SESSION['ADMIN_CHANNEL'] = $channelArr;
        return $channelArr;
    }

    //检查渠道是否存在
    function checkList($channel){
        $oldListArr = $this->_db->fetchCol('select channel from channel');
        $channelArr = explode(',', $channel);
        $error=array_intersect($oldListArr,$channelArr);
        if($error){
            throw new Exception(10003);
        }
        return $oldListArr;
    }

    //查询渠道组
    function getAllGroup() {
        $result = [];
        $arr = $this->_db->fetchAll('select channel,note from channel ORDER BY channel ASC');
        $res = $this->_db->fetchAll('select id, name, channel from channelgroup where id != 1');
        foreach($res as $index => &$val){
            $channels = explode(',',$val['channel']);
            $group = array();
            foreach($arr as $sindex => &$sval){
                if(in_array($sval['channel'],$channels)){
                    array_push($group,$sval);
                    unset($arr[$sindex]);
                }
            }
            $val['channel'] = $group;
            if(!$val['channel']){
                unset($res[$index]);
            }
        }
        if($arr){
            $arr = array_values($arr);
            array_push($res,['name' =>'未分组渠道','id' => '-1','channel'=>$arr]);
        }
        $res = array_values($res);
        // $children = $res['children'];
        // //查找子渠道
        // if($children){
        //     $childrenArr = explode(';', $children);
        //     array_pop($childrenArr);
        //     foreach($childrenArr as $value){
        //         $ares = $this->getRole($value);
        //         if(!is_array($res['children'])){
        //             $res['children'] = [];
        //         }
        //         array_push($res['children'],$ares);
        //     }
        // }else{
        //     $res['children'] = [];
        // }
        return $res;        
    }

    //新增渠道
	function addList($channel,$note){
        $oldList = $this->checkList($channel);
        if($this->CHANNELFLAG){
            array_push($oldList,$channel);
        }
        return $this->_db->insert('channel',Array('channel' => $channel,'note' => $note));
    }

    function updateList($channel,$note){
        $where = $this->_db->quoteInto('channel= ?', $channel);
        $opt = array('note'=>$note);
        return $this->_db->update('channel',$opt,$where);
    }
    
    //删除渠道
	function delList($channel){
        if($this->CHANNELFLAG){
            $index = array_search($channel,$this->CHANNEL);
            if($index){
                array_splice($this->CHANNEL,$index,1);
            }
        };
        $where = $this->_db->quoteInto('channel= ?', $channel);
        $this->_db->delete('channel',$where);
        $this->select->from('channelgroup', array('channel', 'id'));
        $updateArr = $this->_db->fetchAll($this->select);
        foreach ($updateArr as $value){
            $id = $value['id'];
            $oldList = $value['channel'];
            $newList = $this->changeHandle($oldList,$channel,false);
            $where = $this->_db->quoteInto('id= ?', $id);
            $this->_db->update('channelgroup',Array('channel' => $newList),$where);
        }
    }

    function changeHandle($str,$channel,$update){
        $arr = explode(',',$str);
        if(!is_array($channel)){
            $channel = array($channel);
        }
        foreach ($channel as $val){
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

    //新增渠道组
    function addGroup($name,$channel,$note,$parent = 0) {
        if(is_array($channel)){
            $channel = implode(',', $channel).',';
        }
        $param = Array(
            'name' => trim($name),
            'channel' => trim($channel),
            'note' => $note,
            'parent' => $parent,
        );
        $this->_db->insert('channelgroup', $param);
        $last_insert_id = $this->_db->lastInsertId();
        return $last_insert_id;
    }

    //删除渠道组
    function delGroup($id) {
        $where = $this->_db->quoteInto('id= ?', $id);
        $this->_db->delete('channelgroup', $where);
        return '删除成功!';
    }

    //修改渠道组
    function updateGroup($id,$name,$channel,$note = '') {
        //取得删除的渠道名数组
        $where = $this->_db->quoteInto('id= ?', $id);
        $oldChannel = $this->_db->fetchOne('select channel from channelgroup where '.$where);
        //删除渠道
        if(!isset($channel)){
            $sresult = false;
        }else{
            if(!is_null($oldChannel)){
                $oldArr = explode(',',$oldChannel);
                array_pop($oldArr);
                //交集
                $sresult = array_intersect($oldArr,$channel);
                //没有需要删除的原有渠道
                if(is_null($sresult)){
                    $sresult = false;
                }else{
                    $sresult = $sresult;
                }
            }else{
                $sresult = false;
            }
        }
        //更新本渠道组
        if(is_array($channel)){
            $channel = implode(',',$channel).',';
        }
        $opt = Array(
            'name' => trim($name),
            'channel' => trim($channel),
            'note' => $note
        );
        $this->_db->update('channelgroup',$opt,$where);
        //更新子渠道组
        return '修改成功!';
    }
}