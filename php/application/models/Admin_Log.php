<?php
/**
 * 记录后台操作日志
 * 若sql调试开关打开，则同时记录sql语句
 * @package admin
 * @author RD
 */

class Admin_Log{

    private $_db;
    public function __construct ($db) {
        $this->_db = $db;
    }
    
    
    /**
     * 添加日志
     * 
     */
    public function addLog ($controller, $action, $name, $ext='') {
        $this->_db->insert ('adminlog', array ('controller' => $controller,
                                                'action'     => $action,
                                                'name'   => $name,
                                                'ip'         => Util::getip(),
                                                'ext'        => $ext
                                                ));
    }


    /**
     * 搜索日志
     * @param array $param 参数列表
     * @param Int per_page 每页显示数量
     * @param Int nowp     当前页
     * @return array ('list'=>list, 'page'=>pageinfo)
     */
    public function searchLog (array $param, $per_page = 15, $nowp = 1) {
        $where = ' where 1=1 ';
        if (!empty ($param ['username'])) {
            $where .= ' and ' . $this->_db->quoteInto('username=?', $param ['username']);
        }
        if (!empty ($param ['controller'])) {
            $where .= ' and ' . $this->_db->quoteInto('controller=?', $param ['controller']);
        }
        if (!empty ($param ['action'])) {
            $where .= ' and ' . $this->_db->quoteInto('action=?', $param ['action']);
        }
        if (!empty ($param ['ip'])) {
            $where .= ' and ' . $this->_db->quoteInto('ip=?', ip2long($param ['ip']));
        }
        if (!empty ($param ['stime'])) {
            $where .= ' and ' . $this->_db->quoteInto('time>?', $param ['stime']);
        }
        if (!empty ($param ['etime'])) {
            $where .= ' and ' . $this->_db->quoteInto('time<?', $param ['etime']);
        }
        if (!empty ($param ['ext'])) {
            $where .= ' and ext like ' . $this->_db->quoteInto('?', '%' . $param ['ext'] . '%');
        }
        
        $totalNum = $this->_db->fetchOne ('select count(*) from DWSiteDB.dbo.AdminLog' . $where);
        
        $pageInfo = Util::page ($totalNum, $per_page, $nowp);
        $start    = $pageInfo ['start'];
		$end      = $start + $per_page;
		$list     = $this->_db->fetchAll ("SELECT * FROM (SELECT row_number() over (ORDER BY alid DESC) row,alid,controller,action,username,ip,time FROM DWSiteDB.dbo.AdminLog ".$where.") sfaf WHERE row>'".$start."' and row <='".$end."'");

        return Array ('page' => $pageInfo['page'], 'list' => $list);
    }


    /**
     * 查看具体的日志的扩展信息
     * @param Int alid 主键
     * @return String
     * @author RD
     */
    public function showExt ($alid) {
        return $this->_db->fetchOne ('select ext from DWSiteDB.dbo.AdminLog where alid=?', array ($alid));
    }
    
    
    /**
     * 删除日志信息
     * @param array $alid 删除的id列表
     * @return affected_rows
     */
    public function delLog (array $alid) {
        return $this->_db->delete ('DWSiteDB.dbo.AdminLog', $this->_db->quoteInto ('alid in (?)', $alid));
    }
    
    
    /**
     * 获取ip地址
     * @return String ip
     */
    public static function getIP () {
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) 
            $onlineip = getenv('HTTP_CLIENT_IP');
        elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'))
            $onlineip = getenv('HTTP_X_FORWARDED_FOR');
        elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown'))
            $onlineip = getenv('REMOTE_ADDR');
        elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
            $onlineip = $_SERVER['REMOTE_ADDR'];

        preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
        $onlineip = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
        if ($onlineip == 'unknown') $onlineip = '0.0.0.0';
        return $onlineip;
    }
}
