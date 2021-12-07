<?php

/**
 * 充值统计
 * @package admin
 */
require_once APPLICATION_ROOT_PATH . 'models/Sitestat.php';

class SitestatController extends Admin_Action {

    // const KEYWORD = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ23456789';

    /**
     * 初始化,获取db对象，创建分类对象,设置baseUrl
     */
    public function preDispatch() {
        $this->_db = Zend_Registry::get('db');
        $this->_sitestat = new Sitestat($this->_db);
    }

    /*
     * 在线写入
     */

    public function onlineAction() {
        $this->_helper->viewRenderer->SetNoRender(true);
        $num = 0;
        $msg = '';
        while($num < 3){
            try{
                $this->_sitestat->addonline();
                $num = 4;
            }catch(Exception $e){
                $num ++;
                $msg .= $e->getMessage();
            }
        }
        if($num < 4){
            $this->_sitestat->sendmail($msg,'在线写入错误');
        }
    }

    /*
     * 新增写入
     */

    public function userAction() {
        set_time_limit(100);
        $this->_helper->viewRenderer->SetNoRender(true);
        $num = 0;
        $msg = '';
        while($num < 3){
            try{
                $this->_sitestat->addnewuser();
                $num = 4;
            }catch(Exception $e){
                $num ++;
                $msg .= $e->getMessage();
            }
        }
        if($num < 4){
            $this->_sitestat->sendmail($msg,'新增写入错误');
        }
    }


    /*
    *充值写入
    */
    public function rechargeAction(){
        set_time_limit(300);
        $this->_helper->viewRenderer->SetNoRender(true);
        $num = 0;
        $msg = '';
        while($num < 4){
            try{
                $this->_sitestat->addrecharge();
                $num = 4;
            }catch(Exception $e){
                $num ++;
                $msg .= $e->getMessage();
            }
        }
        if($num < 4){
            $this->_sitestat->sendmail($msg,'充值写入错误');
        }
    }
        /*
    *充值写入
    */
    public function rechargebyplatformAction(){
        set_time_limit(300);
        $this->_helper->viewRenderer->SetNoRender(true);
        $num = 0;
        $msg = '';
        while($num < 4){
            try{
                $this->_sitestat->addrechargebyplatform();
                $num = 4;
            }catch(Exception $e){
                $num ++;
                $msg .= $e->getMessage();
            }
        }
        if($num < 4){
            $this->_sitestat->sendmail($msg,'充值写入错误platform');
        }
    }
    
    /*
    *充值统计
    */
    public function paymentAction(){
        set_time_limit(300);
        $this->_helper->viewRenderer->SetNoRender(true);
        $num = 0;
        $msg = '';
        while($num < 3){
            try{
                $this->_sitestat->addpayment();
                $num = 4;
            }catch(Exception $e){
                $num ++;
                $msg .= $e->getMessage();
            }
        }
        if($num < 4){
            $this->_sitestat->sendmail($msg,'充值统计错误');
        }
    }

    /*
    *充值统计
    */
    public function paymentbyplatformAction(){
        set_time_limit(300);
        $this->_helper->viewRenderer->SetNoRender(true);
        $num = 0;
        $msg = '';
        while($num < 3){
            try{
                $this->_sitestat->addpaymentbyplatform();
                $num = 4;
            }catch(Exception $e){
                $num ++;
                $msg .= $e->getMessage();
            }
        }
        if($num < 4){
            $this->_sitestat->sendmail($msg,'充值统计错误platform');
        }
    }

         /*
     *首次充值玩家统计
     */ 

    public function firstpaymentAction(){
        set_time_limit(300);
        $this->_helper->viewRenderer->SetNoRender(true);
        $num = 0;
        $msg = '';
        while($num < 3){
            try{
                $this->_sitestat->addfirstpayment();
                $num = 4;
            }catch(Exception $e){
                $num ++;
                $msg .= $e->getMessage();
            }
        }
        echo($msg);
        if($num < 4){
            $this->_sitestat->sendmail($msg,'首次付费统计错误');
        }
     }

     /*
     *付费率统计
     */ 

    public function paymentratioAction(){
        set_time_limit(300);
        $this->_helper->viewRenderer->SetNoRender(true);
        $num = 0;
        $msg = '';
        while($num < 3){
            try{
                $this->_sitestat->addpaymentratio();
                $num = 4;
            }catch(Exception $e){
                $num ++;
                $msg .= $e->getMessage();
            }
        }
        echo($msg);
        if($num < 4){
            $this->_sitestat->sendmail($msg,'首日付费统计错误');
        }
     }

	public function linkcountAction(){
        set_time_limit(300);
        $this->_helper->viewRenderer->SetNoRender(true);
        $num = 0;
        $msg = '';
        while($num < 3){
            try{
                $this->_sitestat->linkcount();
                $num = 4;
            }catch(Exception $e){
                $num ++;
                $msg .= $e->getMessage();
            }
        }
        echo($msg);
        if($num < 4){
            $this->_sitestat->sendmail($msg,'推广数据统计错误');
        }
     }

    public function paymentbydayAction(){
        set_time_limit(300);
        $this->_helper->viewRenderer->SetNoRender(true);
        $num = 0;
        $msg = '';
        while($num < 3){
            try{
                $this->_sitestat->addpaymentbyday();
                $num = 4;
            }catch(Exception $e){
                $num ++;
                $msg .= $e->getMessage();
            }
        }
        echo($msg);
        if($num < 4){
            $this->_sitestat->sendmail($msg,'首日付费统计错误');
        }
    }

    public function sendmailAction(){
        $this->_helper->viewRenderer->SetNoRender(true);
       $this->_sitestat->sendmail();
	}
	
	public function docountAction(){
		$flag = $this->_getParam("flag",0);
		$url = $this->_getParam("url",0);
		$ip = $this->_getParam("ip",0);
		$time = time();
		$params = array('createtime'=>$time,'channel'=>$url,'ip'=>$ip,'flag'=>$flag);
		$this->_db->insert('linkcount',$params);
		header('Content-type: application/js');
    }
    
    public function gamelogAction(){
        set_time_limit(300);
        $this->_helper->viewRenderer->SetNoRender(true);
        $num = 0;
        $msg = '';
        while($num < 3){
            try{
                $this->_sitestat->addgamelog();
                $num = 4;
            }catch(Exception $e){
                $num ++;
                $msg .= $e->getMessage();
            }
        }
        echo($msg);
        if($num < 4){
            $this->_sitestat->sendmail($msg,'游戏日志ID统计');
        }
    }

    public function retentionAction(){
        set_time_limit(300);
        $this->_helper->viewRenderer->SetNoRender(true);
        $num = 0;
        $msg = '';
        while($num < 3){
            try{
                $this->_sitestat->addretention();
                $num = 4;
            }catch(Exception $e){
                $num ++;
                $msg .= $e->getMessage();
            }            
        }
        echo($msg);
        if($num < 4){
            $this->_sitestat->sendmail($msg,'游戏留存统计');
        }
    }

}

/*Controller_remark:资产管理*/
/**
 * Action_remark:
 * 生成渠道账号
 * 生成渠道账号操作
 * 渠道账号列表
 * 重置渠道账号密码
 */