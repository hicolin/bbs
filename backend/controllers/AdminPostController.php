<?php
/**
 * User: Colin
 * Date: 2019/5/30
 * Time: 19:53
 */

namespace backend\controllers;

use backend\models\AdminDiscuss;
use backend\models\AdminMember;
use backend\models\AdminPost;
use backend\models\AdminReply;
use Yii;
use yii\data\Pagination;

class AdminPostController extends BaseController
{
    public $layout = "lte_main";
    public $enableCsrfValidation=false;

    public function actionIndex()
    {
        $query = AdminPost::find()->joinWith('member');
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
            $query->andWhere(['like', 'admin_post.content', $search['content']]);
        }
        if (isset($search['type']) && $search['type']) {
            $query->andWhere(['=', 'admin_post.type', $search['type']]);
        }
        if (isset($search['is_stick']) && $search['is_stick']) {
            $query->andWhere(['=', 'admin_post.is_stick', $search['is_stick']]);
        }
        if (isset($search['status']) && $search['status']) {
            $query->andWhere(['=', 'admin_post.status', $search['status']]);
        }
        if (isset($search['b_time']) && $search['b_time']) {
            $query->andWhere(['>', 'admin_post.create_time', $search['b_time']]);
        }
        if (isset($search['e_time']) && $search['e_time']) {
            $query->andWhere(['<', 'admin_post.create_time', $search['e_time']]);
        }
        return $query;
    }

    public function actionAdd()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $member = AdminMember::findOne(['tel' => $post['tel']]);
            if (!$member) return $this->json(100, '手机号对应的用户不存在');
            $model = new AdminPost();
            $model->user_id = $member->id;
            $model->pageview = $post['pageview'];
            $model->thumb_up = $post['thumb_up'];
            $model->type = $post['type'];
            $model->is_stick = $post['is_stick'];
            $model->status = $post['status'];
            $model->title = $post['title'];
            $model->content = htmlspecialchars($post['content']);
            if (!$model->save()) return $this->json(100, '保存失败');
            return $this->json(200, '保存成功');
        }
        return $this->render('add');
    }

    public function actionUpdate()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model = AdminPost::findOne($post['id']);
            $model->pageview = $post['pageview'];
            $model->thumb_up = $post['thumb_up'];
            $model->type = $post['type'];
            $model->is_stick = $post['is_stick'];
            $model->status = $post['status'];
            $model->title = $post['title'];
            $model->content = htmlspecialchars($post['content']);
            if (!$model->save()) {
                return $this->json(100, '保存失败');
            }
            return $this->json(200, '保存成功');
        }
        $id = (int)Yii::$app->request->get('id');
        $model = AdminPost::find()->joinWith('member')
            ->where(['admin_post.id' => $id])
            ->one();
        $data = compact('model');
        return $this->render('update', $data);
    }

    public function actionDel()
    {
        $id = (int)Yii::$app->request->post('id');
        $model = AdminPost::findOne($id);
        $trans = Yii::$app->db->beginTransaction();
        try{
            $model->delete();  // 帖子表
            AdminDiscuss::deleteAll(['post_id' => $id]); // 评论表
            AdminReply::deleteAll(['post_id' => $id]); // 回复表
            $trans->commit();
            return $this->json(200, '删除成功');
        }catch (\Exception $e) {
            $trans->rollBack();
            return $this->json(100, '删除失败');
        }
    }

    public function actionBatchDel()
    {
        $ids = Yii::$app->request->post('ids');
        $trans = Yii::$app->db->beginTransaction();
        try {
            AdminPost::deleteAll(['in', 'id', $ids]);
            AdminDiscuss::deleteAll(['in', 'post_id', $ids]);
            AdminReply::deleteAll(['in', 'post_id', $ids]);
            $trans->commit();
            return $this->json(200, '批量删除成功');
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->json(100, '批量删除失败');
        }
    }



}