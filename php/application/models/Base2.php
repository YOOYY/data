<?php
class Base2
{
    protected $_db; //接口服务器
    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
    }

    public function gettype()
    {
        $sql = 'select id as value,name as label from gametype';
        $result = $this->_db->fetchAll($sql);
        return $result;
    }

    public function typelist()
    {
        $sql = 'select id,name from gametype';
        $result = $this->_db->fetchAll($sql);
        return $result;
    }

    //新增权限组
    function addType($name, $id)
    {
        $param = array(
            'id' => trim($id),
            'name' => trim($name),
        );
        $this->_db->insert('gametype', $param);
        return true;
    }

    //删除权限组
    function delType($id)
    {
        $where = $this->_db->quoteInto('id= ?', $id);
        $res = $this->_db->delete('gametype', $where);
        return $res;
    }

    //修改权限组
    function updateType($opt)
    {
        $where = $this->_db->quoteInto('id= ?', $opt['id']);
        return $this->_db->update('gametype', $opt, $where);
    }
}
