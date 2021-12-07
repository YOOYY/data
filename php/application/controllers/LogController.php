<?php

/**
 * 后台日志管理控制器
 * @package admin
 */

class LogController extends Admin_Action
{

    /**
     * 初始化,获取db对象，创建分类对象,设置baseUrl
     */

    public function preDispatch()
    {
        $this->_db           = Zend_Registry::get ('db');
        //$this->view->baseUrl = $this->_config->admin_baseurl;
    }
    

    /**
     * 后台操作日志搜索页面
     * @author RD
     */
    public function searchAction()
    {
        //直接显示视图
    }
    
    
    /**
     * 后台日志搜索结果查看
     * @author RD
     */
    public function resultAction ()
    {
        //每页显示多少数量
        $per_page              = 22;
        
        //搜索条件
        $param = array ();
        $search                = $this->getRequest ()->getPost ('search');
        $param['username']     = $this->getRequest ()->getPost ('username');
        $param['controller']   = $this->getRequest ()->getPost ('controller');
        $param['action']       = $this->getRequest ()->getPost ('action');
        $param['ip']           = $this->getRequest ()->getPost ('ip');
        $param['stime']        = (int)$this->getRequest ()->getPost ('stime');
        $param['etime']        = (int)$this->getRequest ()->getPost ('etime');
        $param['ext']          = $this->getRequest ()->getPost ('ext');
        if (!empty ($search))
            $_SESSION ['LOG_SEARCH'] = $param;
        elseif (!empty ($_SESSION ['LOG_SEARCH'])){
            $param             = $_SESSION ['LOG_SEARCH'];
        }else
            exit ('No Value');
        
        require_once APPLICATION_ROOT_PATH . '/models/Admin_Log.php';
        $AdminLog              = new Admin_Log ($this->_db);
        $nowp                  = (int)$this->_getParam ('nowp', 1);
        
        $result                = $AdminLog->searchLog ($param, $per_page, $nowp);
        $this->view->list      = $result['list'];
        $this->view->page      = $result['page'];
        $this->view->pagenum   = Util::pageNumStyle ($result['page'], 15);
        $this->view->nowp      = $nowp;
    }
    
    
    /**
     * 查看后台日志的详细信息
     * @author RD
     */
    public function showAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $alid                  = (int)$this->_getParam ('alid');
        require_once APPLICATION_ROOT_PATH . '/models/Admin_Log.php';
        $AdminLog              = new Admin_Log ($this->_db);
        $detail                = $AdminLog->showExt ($alid);
        header("Content-Type:text/html; charset=gb2312");
        echo nl2br($detail);
    }
    
    
    /**
     * 后台日志批量删除
     * @author RD
     */
    public function deleteAction ()
    {
        $alid                  = $this->getRequest ()->getPost ('alid');
        require_once APPLICATION_ROOT_PATH . '/models/Admin_Log.php';
        $AdminLog              = new Admin_Log ($this->_db);
        $affected_rows         = $AdminLog->delLog ($alid);
        $this->view->url       = $this->view->baseUrl . '/log/result/nowp/' . $this->getRequest ()->getPost ('nowp');
        $this->renderScript ('showmessage.php');
    }


    /**
     * 前台错误日志搜索页面
     * @author ydl
     */
    public function frontsearchAction()
    {
        $this->render('frontsearch');
    }


    /**
     * 前台错误日志搜索结果查看
     * @author ydl
     */
    public function frontresultAction ()
    {
        
        //搜索时间
        $stime        = (int)$this->getRequest ()->getPost ('etime');
        $date   =  date("Y-m-d",$stime);
        $address = SITEDATA_ROOT_PATH;
        $address = str_replace('admin','front',$address);
        $content = @file_get_contents($address.'log/'.$date.'.log');

        if($content){
            $this->view->exist = 'ok';
            $this->view->content = $content;
        };
        $this->view->sdate = $date;
        $this->render('frontresult');
    }

}