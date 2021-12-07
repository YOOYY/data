<?php
require_once APPLICATION_ROOT_PATH . 'models/Logs.php';

/**
 * 充值统计
 * @package admin
 */
require_once APPLICATION_ROOT_PATH . 'models/Link.php';

class LinkController extends Admin_Action {
    public function preDispatch() {
        $this->_db = Zend_Registry::get('db');
        $this->_link = new Link($this->_db);
    }

    public function useropenAction() {
        $this->_helper->viewRenderer->SetNoRender(true);
        $channel = $_GET['channel'];
        $date = $_GET['date'];
        Logs::Write('link', $channel . $date);
        $this->_link->useropen($channel, $date);
        echo '';
    }

    public function userstayAction()
    {
        $this->_helper->viewRenderer->SetNoRender(true);
        $channel = $_GET['channel'];
        $date = $_GET['date'];
        Logs::Write('link', $channel . $date);
        $this->_link->userstay($channel, $date);
        echo '';
    }
}