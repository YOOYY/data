<?php
require_once APPLICATION_ROOT_PATH . 'models/Base2.php';

class HelperController extends Admin_Action
{
    public function preDispatch()
    {
        $this->helper = new Base2();
        $this->util = new Util();
    }
    public function typelistAction()
    {
        try {
            $res = $this->helper->typelist();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }

    public function gettypeAction()
    {
        try {
            $res = $this->helper->gettype();
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }

    public function addtypeAction()
    {
        try {
            $res = json_decode(file_get_contents('php://input'));
            $name = $res->name;
            $id = $res->id;
            $res = $this->helper->addType($name, $id);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            // $res = $this->util->err(new Exception('10041'));
            $res = ['error' => 1, 'data' => 'gametype重复'];
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }

    public function updatetypeAction()
    {
        try {
            $opt = json_decode(file_get_contents('php://input'), true);
            $res = $this->helper->updateType($opt);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = ['error' => 1, 'data' => 'gametype重复'];
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }

    public function deltypeAction()
    {
        try {
            $res = json_decode(file_get_contents('php://input'));
            $id = $res->id;
            $res = $this->helper->delType($id);
            $res = ['error' => 0, 'data' => $res];
        } catch (Exception $e) {
            $res = $this->util->err($e);
        }
        echo json_encode($res, JSON_NUMERIC_CHECK);
    }
}
