<?php
/**
 * User: Colin
 * Date: 2019/6/7
 * Time: 7:33
 */

namespace mobile\controllers;


use yii\helpers\VarDumper;
use yii\web\Controller;
use Yii;

class SelfController extends Controller
{
    // 检查登陆状态
    public function checkLoginStatus()
    {
        $userId = Yii::$app->session->get('user_id');
        if (!$userId) {
            return $this->redirect(['index/login']);
        }
    }

    // 返回json数据
    public function json($status, $msg, $data = '')
    {
        if (!$data) {
            return json_encode(['status' => $status, 'msg' => $msg]);
        }
        return json_encode(['status' => $status, 'msg' => $msg, 'data' => $data]);
    }

    // 调试函数
    public static function dd()
    {
        $params = func_get_args();
        foreach ($params as $v) {
            VarDumper::dump($v, 10, true);
        }
        exit(1);
    }
}