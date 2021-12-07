<?php
require_once APPLICATION_ROOT_PATH . 'models/Logs.php';

class Link
{

    public function __construct() {
        $this->_db = Zend_Registry::get('db');
    }

    public function useropen($channel, $date)
    {
        $issave = $this->_db->fetchOne('select COUNT(*) from link where channel=:channel AND date=:date', array('channel'=>$channel,'date'=>$date));
        if($issave>0){
            $channel = $this->_db->quote($channel);
            $date = $this->_db->quote($date);
            $this->_db->query("UPDATE link SET useropen = `useropen`+1 where channel = {$channel} AND date = {$date}");
        }else{
            $this->_db->insert('link', array(
                'useropen' => 1,
                'channel' => $channel,
                'date' => $date
            ));
        }
    }

    public function userstay($channel, $date)
    {
        $issave = $this->_db->fetchOne('select COUNT(*) from link where channel=:channel AND date=:date', array('channel' => $channel, 'date' => $date));
        if ($issave > 0) {
            $channel = $this->_db->quote($channel);
            $date = $this->_db->quote($date);
            $this->_db->query("UPDATE link SET userstay3 = `userstay3`+1 where channel = {$channel} AND date = {$date}");
        } else {
            $this->_db->insert('link', array(
                'userstay3' => 1,
                'channel' => $channel,
                'date' => $date
            ));
        }
    }
}
