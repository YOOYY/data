<?php
/**
 * ErrorController 统一的异常处理
 * 
 * @uses Fucai
 * @uses _Controller_Action
 * @package 
 * @version 0.1 2008-02-28 15:16:28
 * @copyright 
 * @author CJ <keepwatch@gmail.com> 
 * @license 
 */
class ErrorController extends Zend_Controller_Action
{

    public function errorAction()/*{{{*/
    {
        $e = $this->_getParam('error_handler');

        switch ($e->type)
        {
            // 404 error -- controller or action not found
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                self::show404();
            break;

            //系统或框架抛出的异常
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
                self::showException($e);
            break;
        }
    }/*}}}*/

    /**
     * show404 显示404错误页面:请求的页面找不到
     * 
     * @static
     * @access public
     * @return void
     * @author CJ <keepwatch@gmail.com>
     * @throws void
     */
    public static function show404() /*{{{*/
    {
        Header('HTTP/1.1 404 Not Found');
        echo file_get_contents(APPLICATION_ROOT_PATH.'views/scripts/error/404.html');
    }/*}}}*/



    /**
     * showException 处理所有异常
     * 
     * @param Exception $error 
     * @static
     * @access public
     * @return void
     * @author CJ <keepwatch@gmail.com>
     * @throws void
     */
    public static function showException($error) /*{{{*/
    {
        //写日志
        ErrorController::writeExceptionLog($error);
        echo file_get_contents(APPLICATION_ROOT_PATH.'views/scripts/error/error.html');
    }/*}}}*/


    /**
     * writeExceptionLog 写异常的log
     * 
     * @param Exception $e 
     * @static
     * @access private
     * @return void
     * @author CJ <keepwatch@gmail.com>
     * @throws bool
     */
    private static function writeExceptionLog($e) /*{{{*/
    {
        //zf抛出这样的异常:arrayobject=>(exception=>(),type=>(),request=>())
        //其它异常直接引用
        if($e instanceof ArrayObject)
            $e = $e->exception;
        //记录日志
        $content = date('Y-m-d H:i:s').":\t";
        $content.= $e->getMessage()."\t";
        $content.= $e->getFile()." on line ".$e->getLine()."\t";
        $content.= $e->getTraceAsString()."\t";
        require_once APPLICATION_ROOT_PATH . 'models/Logs.php';
        return Logs::write('exception', $content);
    }/*}}}*/

}
/*Controller_remark:异常处理*/
/**
 * Action_remark:
 * 异常处理方法
 */