<?php
/**
 * User: Colin
 * Date: 2019/5/30
 * Time: 19:57
 */

namespace backend\controllers;


use backend\models\AdminDiscuss;
use Yii;
use yii\data\Pagination;

class AdminDiscussController extends BaseController
{
    public $layout = "lte_main";
    public $enableCsrfValidation=false;

    public function actionIndex()
    {
        $query = AdminDiscuss::find()->joinWith('member')->joinWith('user');
        $search = Yii::$app->request->get('search');
        $query = $this->condition($query, $search);
        $pageSize = (int)abs(\common\controllers\PublicController::getSysInfo(36));
        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => $pageSize,
        ]);
        $models = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('id desc')
            ->all();
        $data = compact('search', 'pagination', 'models');
        return $this->render('index', $data);
    }

    public function condition($query, $search)
    {
        if (isset($search['nickname']) && $search['nickname']) {
            $query->andWhere(['like', 'admin_member.nickname', $search['nickname']]);
        }
        if (isset($search['tel']) && $search['tel']) {
            $query->andWhere(['like', 'admin_member.tel', $search['tel']]);
        }
        if (isset($search['content']) && $search['content']) {
            $query->andWhere(['like', 'admin_discuss.content', $search['content']]);
        }
        if (isset($search['status']) && $search['status']) {
            $query->andWhere(['=', 'admin_discuss.status', $search['status']]);
        }
        if (isset($search['b_time']) && $search['b_time']) {
            $query->andWhere(['>', 'admin_discuss.create_time', $search['b_time']]);
        }
        if (isset($search['e_time']) && $search['e_time']) {
            $query->andWhere(['<', 'admin_discuss.create_time', $search['e_time']]);
        }
        return $query;
    }

    public function actionUpdate()
    {

    }

    public function actionChangeStatus()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $status = $post['status'] == 1 ? 2 : 1;
            $model = AdminDiscuss::findOne($post['id']);
            $model->status = $status;
            if (!$model->save(false)) {
                return $this->json(100, '更改失败');
            }
            return $this->json(200, '更改成功');
        }

    }

    public function actionDel()
    {
        $id = (int)Yii::$app->request->post('id');
        $model = AdminDiscuss::findOne($id);
        if (!$model->delete()){
            return $this->json(100, '删除失败');
        }
        return $this->json(200, '删除成功');
    }

    public function actionBatchDel()
    {
        $ids = Yii::$app->request->post('ids');
        $res = AdminDiscuss::deleteAll(['in', 'id', $ids]);
        if (!$res) {
            return $this->json(100, '批量删除失败');
        }
        return $this->json(200, '批量删除成功');
    }
}