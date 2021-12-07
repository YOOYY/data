<?php

/**
 * 后台专用控制器
 * 在这里实现权限认证，根据controller和action的名称来控制
 * 设置默认的视图目录,设置默认视图扩展名为php
 * 默认编码为utf-8
 * @package admin
 */
/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

class Admin_Action extends Zend_Controller_Action
{

    /**
     * 重载控制器
     * 定义默认的视图路径，文件编码，视图扩展名
     * 并调用权限认证
     */
    public function __construct($req, $resp, $args)
    {
        parent::__construct($req, $resp, $args);
    }

    public function init()
    {
        $this->view->setBasePath(APPLICATION_ROOT_PATH . "views")
            ->setEncoding("utf-8");
        $this->getHelper('viewRenderer')
            ->setViewSuffix("php");
        //不自动输出模板内容
        $this->_helper->viewRenderer->setNoRender(true);

        //设置后台根目录的相对路径
        $this->view->domain = isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] . '/' : 'http://admin.test.com/';
        $this->view->base_url = str_replace(strrchr($_SERVER['SCRIPT_NAME'], '/'), '', $_SERVER['SCRIPT_NAME']) . '/'; //base url
        $this->view->url_root_path = substr($this->view->domain, 0, -1) . $this->view->base_url; //http://domain+baseurl,..doman...'/'..

        $this->view->baseUrl = $this->view->url_root_path;
        $controllerName = $this->_getParam('controller');
        $actionName = $this->_getParam('action');
        $moduleName = $this->_getParam('module');
        //$this->view->baseUrl = $this->getFrontController ()->getBas();
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Origin: http://localhost:8080");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header("Access-Control-Allow-Credentials: true");
        //记录用户操作日志
        $this->_logUserActions();

        /**
         * 调用权限认证函数
         */
        $this->_authenticate($controllerName, $actionName, $moduleName);
    }

    /**
     * _logUserActions 记录用户操作的日志
     * 
     * @access private
     * @return void
     * @author CJ <keepwatch@gmail.com>
     * @throws void
     */
    private function _logUserActions()
    {
        require_once APPLICATION_ROOT_PATH . '/models/Index.php';
        require_once APPLICATION_ROOT_PATH . '/models/Admin_Log.php';

        $index = new Index();
        $AdminLog = new Admin_Log(Zend_Registry::get('db'));

        $admininfo = $index->getLoginInfo();
        $name = empty($admininfo['name']) ? 'guest' : $admininfo['name'];
        $ext = '';
        //获取所有参数信息
        $params = $this->_getAllParams();
        $c = $params['controller'];
        $a = $params['action'];

        //记录参数
        $ext .= empty($params) ? '用户参数:无' . "\r\n\r\n" : '参数:' . print_r($params, true) . "\r\n\r\n";

        //写入日志
        $AdminLog->addLog($c, $a, $name, $ext);
    }

    /**
     * 权限认证，根据modelName, controllerName,actionName来控制用户的权限
     * @param controllerName 控制器名称
     * @param actionName 动作名称
     * @param modelName 模块名称，这里默认为admin
     */
    private function _authenticate($controllerName, $actionName, $modelName = 'admin')
    {
        //登录&验证码模块不用验证
        $un_auth = array('index', 'error', 'sitestat', 'accept', 'helper','link');
        if (in_array($controllerName, $un_auth))
            return;
        //do Auth
        require_once APPLICATION_ROOT_PATH . '/models/Index.php';
        $admin = new Index();
        if ($admin->checkLogin()) { //已登陆
            if (!$admin->checkRight($controllerName, $actionName)) {
                $res = ['error' => 1, 'data' => '1'];
                exit(json_encode($res));
            };
        } else { //未登陆
            $res = ['error' => 1, 'data' => '未登录'];
            exit(json_encode($res));
        }
    }
}
