<?php
/**
 * User: Colin
 * Date: 2019/5/28
 * Time: 20:09
 */

namespace mobile\controllers;

use backend\models\AdminAttention;
use backend\models\AdminBanner;
use backend\models\AdminDiscuss;
use backend\models\AdminMember;
use backend\models\AdminPost;
use backend\models\AdminPrivateMessage;
use backend\models\AdminReply;
use backend\models\AdminThumbUp;
use Yii;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class PostController extends SelfController
{
    public $enableCsrfValidation = false;

    // 同行曝光、产品曝光
    public function actionIndex()
    {
        $type = (int)Yii::$app->request->get('type');
        if ($type == 1) {
            $this->getView()->title = '曝光区';
        } else {
            $this->getView()->title = '信息街';
        }
        if (!in_array($type, [1, 2])) {
            $type = 1;
        }
        $bannerType = $type + 1;
        if (Yii::$app->request->isAjax) {
            $pullType = Yii::$app->request->get('pull_type');
            if ($pullType == 'up') {
                $pageNum = Yii::$app->request->get('num');
                $pageSize = Yii::$app->request->get('size');
                $offset = ($pageNum - 1) * $pageSize;
                $query = AdminPost::find()->joinWith('member')
                    ->where(['type' => $type, 'status' => 1]);
                $total = $query->count();
                $posts = $query->orderBy(['is_stick' => SORT_DESC, 'create_time' => SORT_DESC])
                    ->offset($offset)->limit($pageSize)
                    ->asArray()->all();
                $posts = AdminPost::mFormatData($posts);
                return $this->json(200, '获取成功', compact('total', 'posts'));
            } else {
                $requestTime = Yii::$app->request->get('request_time');
                $posts = AdminPost::find()->joinWith('member')
                    ->where(['type' => $type])->andWhere(['>', 'create_time', $requestTime])
                    ->andWhere(['status' => 1])
                    ->orderBy('create_time desc')
                    ->asArray()->all();
                $posts = AdminPost::mFormatData($posts);
                $requestTime = time();
                return $this->json(200, '获取成功', compact('posts', 'requestTime'));
            }
        }
        $banners = AdminBanner::find()->where(['type' => $bannerType])->orderBy('create_time desc')
            ->limit(5)->all();
        return $this->render('index', compact('banners', 'type'));
    }

    // 同行曝光、产品曝光 对应的详情页
    public function actionDetails()
    {
        $id = (int)Yii::$app->request->get('id');
        $userId = Yii::$app->session->get('user_id');

        if (Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('id');
            $pullType = Yii::$app->request->post('pull_type');
            if ($pullType == 'up') {
                $pageNum = Yii::$app->request->post('num');
                $pageSize = Yii::$app->request->post('size');
                $offset = ($pageNum - 1) * $pageSize;
                $query = AdminDiscuss::find()->joinWith('member')
                    ->where(['status' => 1, 'post_id' => $id])
                    ->andWhere(['to_user_id' => 0]);
                $total = $query->count();
                $oneLevelDiscuss = $query->orderBy(['thumb_up' => SORT_DESC, 'create_time' => SORT_DESC])
                    ->offset($offset)->limit($pageSize)
                    ->asArray()->all();
                $oneLevelDiscuss = AdminDiscuss::mFormatData($oneLevelDiscuss, $userId);
                $oneLevelDiscuss = AdminDiscuss::getSecondLevelData($oneLevelDiscuss);
                return $this->json(200, '获取成功', compact('total', 'oneLevelDiscuss'));
            } else {
                $requestTime = Yii::$app->request->get('request_time');
                $oneLevelDiscuss = AdminDiscuss::find()->joinWith('member')
                    ->where(['status' => 1, 'post_id' => $id])
                    ->andWhere(['to_user_id' => 0])
                    ->andWhere(['>', 'create_time', $requestTime])
                    ->orderBy(['thumb_up' => SORT_DESC, 'create_time' => SORT_DESC])
                    ->asArray()->all();
                $oneLevelDiscuss = AdminDiscuss::mFormatData($oneLevelDiscuss, $userId);
                $oneLevelDiscuss = AdminDiscuss::getSecondLevelData($oneLevelDiscuss);
                $requestTime = time();
                return $this->json(200, '获取成功', compact('oneLevelDiscuss', 'requestTime'));
            }
        }

        $postModel = AdminPost::findOne($id);
        if (!$postModel) {
            return '参数错误';
        }
        $postModel->pageview += 1;
        $postModel->save(false);

        $models = AdminPost::find()->joinWith('member')
            ->where(['admin_post.id' => $id, 'admin_post.status' => 1])
            ->asArray()->all();
        $models = AdminPost::mFormatData($models);
        $model = $models[0];
        $this->getView()->title = $model['title'];
        $isAttention = 0;
        $attention = AdminAttention::find()->where(['user_id' => $userId, 'to_user_id' => $model['user_id']])
            ->one();
        $attention && $isAttention = 1;
        return $this->render('details', compact('model', 'isAttention'));
    }

    // 添加评论
    public function actionAddDiscuss()
    {
        $this->checkLoginStatus();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $userId = Yii::$app->session->get('user_id');
            $discuss = new AdminDiscuss();
            $discuss->pid = $post['discussId'];
            $discuss->post_id = $post['postId'];
            $discuss->user_id = $userId;
            $discuss->content = htmlspecialchars($post['content']);
            $discuss->to_user_id = $post['toUserId'];
            $discuss->create_time = time();
            if (!$discuss->save(false)) {
                return $this->json(100, '评论失败');
            }
            $data = AdminDiscuss::find()->joinWith('member')
                ->where(['admin_discuss.id' => $discuss->id, 'admin_discuss.status' => 1])
                ->asArray()->all();
            $data = AdminDiscuss::mFormatData($data, $userId);
            $model = $data[0];
            $model['secondLevelDiscuss'] = [];
            $postModel = AdminPost::findOne($post['postId']);
            $postModel->discuss_num += 1;
            $postModel->save(false);
            if ($post['toUserId'] != 0) { // 不是直接回复贴子的评论
                $toUserId = $post['toUserId'];
                $content = '回复了您的评论';
            }else {
                $toUserId = $postModel->user_id;
                $content = '评论了您的帖子';
            }
            $reply = new AdminReply();
            $reply->addReply($userId, $content, $toUserId, $post['postId']);
            return $this->json(200, '发布成功', compact('model'));
        }
    }

    // 点赞、取消点赞
    public function actionThumbUp()
    {
        $post = Yii::$app->request->post();
        if ($post['status'] == 0) { // 点赞
            $postModel = AdminPost::findOne($post['postId']);
            $postModel->thumb_up += 1;
            $postModel->save(false);
            $discuss = AdminDiscuss::findOne($post['discussId']);
            $discuss->thumb_up += 1;
            $discuss->save(false);
            $thumbUp = new AdminThumbUp();
            $res = $thumbUp->addThumbUp($post['userId'], 2, $post['discussId']);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            $reply = new AdminReply();
            $reply->addReply($post['userId'], '点赞了您的评论', $post['toUserId'], $post['postId']);
            return $this->json(200, $res['msg']);
        } else { // 取消点赞
            $postModel = AdminPost::findOne($post['postId']);
            $postModel->thumb_up -= 1;
            $postModel->save(false);
            $discuss = AdminDiscuss::findOne($post['discussId']);
            $discuss->thumb_up -= 1;
            $discuss->save(false);
            $thumbUp = AdminThumbUp::find()
                ->where(['user_id' => $post['userId'], 'type' => 2, 'content_id' => $post['discussId']])
                ->one();
            $res = $thumbUp->delete();
            if (!$res) {
                $this->json(100, '取消失败');
            }
            $reply = new AdminReply();
            $reply->addReply($post['userId'], '取消点赞了您的评论', $post['toUserId'], $post['postId']);
            return $this->json(200, '取消成功');
        }
    }

    // 关注、取消关注
    public function actionAttention()
    {
        $this->checkLoginStatus();
        if (Yii::$app->request->isAjax) {
            $get = Yii::$app->request->get();
            $userId = Yii::$app->session->get('user_id');
            if ($get['isAttention'] == 0) { // 关注
                $attention = new AdminAttention();
                $res = $attention->addAttention($userId, $get['toUserId']);
                if ($res['status'] != 200) {
                    return $this->json(100, '关注失败');
                }
                return $this->json(200, '关注成功');
            } else { // 取消关注
                $attention = AdminAttention::find()->where(['user_id' => $userId, 'to_user_id' => $get['toUserId']])
                    ->one();
                if (!$attention->delete()) {
                    return $this->json(100, '取消关注失败');
                }
                return $this->json(200, '取消关注成功');
            }
        }
    }

    // 查看全部回复的页面
    public function actionAllReply()
    {
        $this->getView()->title = '全部评论';
        $id = Yii::$app->request->get('id');
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('id');
            $pullType = Yii::$app->request->post('pull_type');
            $userId = Yii::$app->session->get('user_id');
            if ($pullType == 'up') {
                $pageNum = Yii::$app->request->post('num');
                $pageSize = Yii::$app->request->post('size');
                $offset = ($pageNum - 1) * $pageSize;
                $query = AdminDiscuss::find()->joinWith('member')
                    ->where(['pid' => $id, 'status' => 1]);
                $total = $query->count();
                $discuss = $query->orderBy(['thumb_up' => SORT_DESC, 'create_time' => SORT_DESC])
                    ->offset($offset)->limit($pageSize)
                    ->asArray()->all();
                $discuss = AdminDiscuss::mFormatData($discuss, $userId);
                return $this->json(200, '获取成功', compact('total', 'discuss'));
            } else {
                $requestTime = Yii::$app->request->get('request_time');
                $discuss = AdminDiscuss::find()->joinWith('member')
                    ->where(['status' => 1, 'pid' => $id])
                    ->andWhere(['>', 'create_time', $requestTime])
                    ->orderBy(['thumb_up' => SORT_DESC, 'create_time' => SORT_DESC])
                    ->asArray()->all();
                $discuss = AdminDiscuss::mFormatData($discuss, $userId);
                $requestTime = time();
                return $this->json(200, '获取成功', compact('discuss', 'requestTime'));
            }
        }
        $model = AdminDiscuss::find()->joinWith('member')
            ->where(['admin_discuss.id' => $id])
            ->one();
        $model['create_time'] = Helper::HumanTime($model['create_time']);
        return $this->render('all-reply', compact('model'));
    }

    // 发帖页面
    public function actionPost()
    {
        $this->checkLoginStatus();
        $this->getView()->title = '我要发帖';
        $type = Yii::$app->request->get('type');
        return $this->render('post', compact('type'));
    }

    // 帖子图片上传
    public function uploadPostImg($files)
    {
        $data = [];
        $nameArr = $files['name'];
        $tmpNameArr = $files['tmp_name'];
        for ($i = 0; $i< count($nameArr); $i++) {
            $data[$i]['name'] = $nameArr[$i];
            $data[$i]['ext'] = pathinfo($nameArr[$i], PATHINFO_EXTENSION);
            $data[$i]['tmp_name'] = $tmpNameArr[$i];
        }
        foreach ($data as &$list) {
            if ($list['tmp_name']) {
                $dir = 'uploads/images/'.date('Ymd');
                if(!is_dir($dir)){
                    mkdir($dir,0755,true);
                }
                $fileName = $dir . '/' . uniqid() . '.' . $list['ext'];
                if (move_uploaded_file($list['tmp_name'], $fileName)) {
                    $list['path'] = '/' . $fileName;
                } else {
                    $list['path'] = '';
                }
            } else {
                $list['path'] = '';
            }
        }
        return $data;
    }

    // 保存发帖
    public function actionAddPost()
    {
        $this->checkLoginStatus();
        $userId = Yii::$app->session->get('user_id');
        $type = (int)Yii::$app->request->post('type');
        in_array($type, [1, 2]) || $type = 1;
        $title = htmlspecialchars(Yii::$app->request->post('title'));
        $contentArr = Yii::$app->request->post('content');
        $member = AdminMember::findOne($userId);
        if ($type == 1 && $member->grade == 1) {
            Yii::$app->session->setFlash('error', '发布失败: 在曝光区，普通会员不能发帖');
            return $this->redirect(Yii::$app->request->referrer);
        }
        $files = $_FILES['pic'];
        foreach ($files['type'] as $list) {
            if (!empty($list)) {
                if (!in_array($list, ['image/png', 'image/jpeg', 'image/gif'])) {
                    Yii::$app->session->setFlash('error', '只能上传图片文件');
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }
        }
        $files = $this->uploadPostImg($files);
        $content = '';
        for ($i = 0; $i < count($contentArr); $i++) {
            $content .= Helper::formatUrlsInText($contentArr[$i]) . '<br/>';
            if ($files[$i]['path']) {
                $content .= '<img src="'. Url::base() . $files[$i]['path'] .'" style="width: 100%; height: 100%">' . '<br/>';
            }
        }
        $content = htmlspecialchars($content);
        $thumbnail = '';
        if ($files[0]['path']) {
            $thumbnail = $files[0]['path'];
        }

        $post = new AdminPost();
        $res = $post->mSavePost($userId, $title, $content, $thumbnail, $type);
        if ($res['status'] != 200) {
            Yii::$app->session->setFlash('error', '发布失败: ' . $res['msg']);
            return $this->redirect(Yii::$app->request->referrer);
        }
        $msg = '发布成功';
        if ($type == 2) { // 信息街发帖需要后台审核
            $msg = '提交成功,等待管理员审核中';
        }
        Yii::$app->session->setFlash('success', $msg);
        $url = Url::to(['post/index', 'type' => $type]);
        return $this->redirect($url);
    }

    // 发帖人的信息页面
    public function actionPosterMan()
    {
        $this->getView()->title = '个人动态';
        $userId = Yii::$app->session->get('user_id');
        $id = Yii::$app->request->get('id');
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('id');
            $pullType = Yii::$app->request->post('pull_type');
            if ($pullType == 'up') {
                $pageNum = Yii::$app->request->post('num');
                $pageSize = Yii::$app->request->post('size');
                $offset = ($pageNum - 1) * $pageSize;
                $query = AdminPost::find()->joinWith('member')
                    ->where(['user_id' => $id, 'status' => 1]);
                $total = $query->count();
                $data = $query->orderBy(['admin_post.is_stick' => SORT_DESC, 'admin_post.create_time' => SORT_DESC])
                    ->offset($offset)->limit($pageSize)
                    ->asArray()->all();
                $data = AdminPost::mFormatData($data);
                return $this->json(200, '获取成功', compact('total', 'data'));
            } else {
                $requestTime = Yii::$app->request->get('request_time');
                $data = AdminPost::find()->joinWith('member')
                    ->where(['user_id' => $id, 'status' => 1])
                    ->andWhere(['>', 'admin_post.create_time', $requestTime])
                    ->orderBy(['admin_post.is_stick' => SORT_DESC, 'admin_post.create_time' => SORT_DESC])
                    ->asArray()->all();
                $data = AdminPost::mFormatData($data);
                $requestTime = time();
                return $this->json(200, '获取成功', compact('data', 'requestTime'));
            }
        }
        $isSelf = 0;
        $id == $userId && $isSelf = 1;
        $message = AdminPrivateMessage::find()->where(['is_read' => 1])->one();
        $member = AdminMember::findOne($id);
        $res = AdminAttention::find()->where(['user_id' => $userId, 'to_user_id' => $id])
            ->one();
        $isAttention = 0;
        $res && $isAttention = 1;
        $data = compact('member', 'id', 'isAttention', 'isSelf', 'message', 'userId');
        return $this->render('poster-man', $data);
    }

    // 私信页面
    public function actionPrivateMessage()
    {
        $this->checkLoginStatus();
        $this->getView()->title = '发私信';
        $id = Yii::$app->request->get('id');
        $myUserId = Yii::$app->session->get('user_id');
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('id');
            $content = htmlspecialchars(Yii::$app->request->post('content'));
            $userId = Yii::$app->session->get('user_id');
            $privateMessage = new AdminPrivateMessage();
            $res = $privateMessage->mAddMessage($userId, $id, $content);
            if ($res['status'] != 200) {
                return $this->json(100, $res['msg']);
            }
            $data = AdminPrivateMessage::find()->joinWith('member')->joinWith('user')
                ->where(['admin_private_message.id' => $privateMessage->id])
                ->asArray()->all();
            foreach ($data as &$list) {
                $list['is_me'] = 0;
                if ($list['user_id'] == $myUserId) $list['is_me'] = 1;
            }
            $data = AdminPrivateMessage::mPmsgData($data);
            return $this->json(200, '发送成功', $data);
        }
        return $this->render('private-message', compact('id'));
    }

    // 私信消息列表页面
    public function actionPrivateMessageList()
    {
        $this->getView()->title = '我的私信';
        $userId = Yii::$app->session->get('user_id');
        if (Yii::$app->request->isAjax) {
            $pullType = Yii::$app->request->post('pull_type');
            if ($pullType == 'up') {
                $pageNum = Yii::$app->request->post('num');
                $pageSize = Yii::$app->request->post('size');
                $offset = ($pageNum - 1) * $pageSize;
                $subQuery = AdminPrivateMessage::find()
                    ->where(['to_user_id' => $userId])
                    ->orderBy('create_time desc');
                $query = (new Query())
                    ->select(['admin_private_message.content', 'admin_private_message.create_time', 'admin_private_message.user_id',
                        'admin_private_message.is_read', 'member.pic', 'member.nickname'])
                    ->from(['admin_private_message' => $subQuery])
                    ->leftJoin(['member' => AdminMember::tableName()], 'member.id = admin_private_message.user_id')
                    ->groupBy('admin_private_message.user_id');
                $total = $query->count();
                $data = $query->orderBy(['admin_private_message.create_time' => SORT_DESC])
                    ->offset($offset)->limit($pageSize)
                    ->all();
                $data = AdminPrivateMessage::mFormatData($data);
                return $this->json(200, '获取成功', compact('total', 'data'));
            } else {
                $requestTime = Yii::$app->request->get('request_time');
                $subQuery = AdminPrivateMessage::find()
                    ->where(['to_user_id' => $userId])
                    ->orderBy('create_time desc');
                $data = (new Query())
                    ->select(['admin_private_message.content', 'admin_private_message.create_time', 'admin_private_message.user_id',
                        'admin_private_message.is_read', 'member.pic', 'member.nickname'])
                    ->from(['admin_private_message' => $subQuery])
                    ->leftJoin(['member' => AdminMember::tableName()], 'member.id = admin_private_message.user_id')
                    ->groupBy('admin_private_message.user_id')
                    ->andWhere(['>', 'admin_private_message.create_time', $requestTime])
                    ->all();
                $data = AdminPrivateMessage::mFormatData($data);
                $requestTime = time();
                return $this->json(200, '获取成功', compact('data', 'requestTime'));
            }
        }
        return $this->render('private-message-list');
    }
    
    //私信详情页
    public function actionPmsgDetails(){
        $this->checkLoginStatus();
        $myUserId = Yii::$app->session->get('user_id');
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            list($page, $pageSize, $userId) = [$post['page'], $post['page_size'], $post['user_id']];
            $data = $this->getPsmgPageData($page, $pageSize, $myUserId, $userId);
            if (empty($data)) {
                return $this->json(100, 'no data');
            }
            return $this->json(200, 'ok', $data);
        }
        $userId = (int)Yii::$app->request->get('user_id');
        $member = AdminMember::findOne($userId);
        if (empty($member)) return '用户不存在或已删除';
        $this->getView()->title = $member->nickname;
        $models = $this->getPsmgPageData(1, 6 , $myUserId, $userId);
        // 将未读的私信标记为已读
        $privateMessage = AdminPrivateMessage::find()->where(['user_id' => $userId, 'is_read' => 1])
            ->asArray()->all();
        if ($privateMessage) {
            $ids = array_column($privateMessage, 'id');
            AdminPrivateMessage::updateAll(['is_read' => 2], ['id' => $ids]);
        }
        return $this->render('pmsg-details', compact('models', 'userId'));
    }

    // 获取私信分页数据
    protected function getPsmgPageData($page, $pageSize, $myUserId, $userId)
    {
        $offset = ($page - 1) * $pageSize;
        $models = AdminPrivateMessage::find()->joinWith('member')->joinWith('user')
            ->where(['to_user_id' => $myUserId, 'admin_private_message.user_id' => $userId])
            ->orWhere(['to_user_id' => $userId, 'admin_private_message.user_id' => $myUserId])
            ->orderBy('admin_private_message.create_time desc')
            ->offset($offset)->limit($pageSize)
            ->asArray()->all();
        foreach ($models as &$list) {
            $list['is_me'] = 0;
            if ($list['user_id'] == $myUserId) $list['is_me'] = 1;
        }
        return array_reverse(AdminPrivateMessage::mPmsgData($models));
    }

    // 我的关注
    public function actionMyFocus()
    {
        $this->checkLoginStatus();
        $this->getView()->title = '我的关注';
        if (Yii::$app->request->isAjax) {
            $pullType = Yii::$app->request->post('pull_type');
            $userId = Yii::$app->session->get('user_id');
            if ($pullType == 'up') {
                $pageNum = Yii::$app->request->post('num');
                $pageSize = Yii::$app->request->post('size');
                $offset = ($pageNum - 1) * $pageSize;
                $query = AdminAttention::find()->joinWith('user')
                    ->where(['user_id' => $userId]);
                $total = $query->count();
                $attention = $query->orderBy(['admin_attention.create_time' => SORT_DESC])
                    ->offset($offset)->limit($pageSize)
                    ->asArray()->all();
                $attention = AdminAttention::mFormatData($attention);
                return $this->json(200, '获取成功', compact('total', 'attention'));
            } else {
                $requestTime = Yii::$app->request->get('request_time');
                $attention = AdminAttention::find()->joinWith('user')
                    ->where(['user_id' => $userId])
                    ->andWhere(['>', 'create_time', $requestTime])
                    ->orderBy(['create_time' => SORT_DESC])
                    ->asArray()->all();
                $attention = AdminAttention::mFormatData($attention);
                $requestTime = time();
                return $this->json(200, '获取成功', compact('attention', 'requestTime'));
            }
        }
        return $this->render('my-focus');
    }

    //我的回复
    public function actionMyReply(){
        $this->checkLoginStatus();
        $this->getView()->title = '我的回复';
        $userId = Yii::$app->session->get('user_id');
        if (Yii::$app->request->isAjax) {
            $pullType = Yii::$app->request->post('pull_type');
            if ($pullType == 'up') {
                $pageNum = Yii::$app->request->post('num');
                $pageSize = Yii::$app->request->post('size');
                $offset = ($pageNum - 1) * $pageSize;
                $query = AdminReply::find()->joinWith('member')->joinWith('post')
                    ->where(['to_user_id' => $userId]);
                $total = $query->count();
                $data = $query->orderBy(['admin_reply.create_time' => SORT_DESC])
                    ->offset($offset)->limit($pageSize)
                    ->asArray()->all();
                $data = AdminReply::mFormatData($data);
                return $this->json(200, '获取成功', compact('total', 'data'));
            } else {
                $requestTime = Yii::$app->request->get('request_time');
                $data = AdminReply::find()->joinWith('member')->joinWith('post')
                    ->where(['to_user_id' => $userId])
                    ->andWhere(['>', 'admin_reply.create_time', $requestTime])
                    ->orderBy(['admin_reply.create_time' => SORT_DESC])
                    ->asArray()->all();
                $data = AdminReply::mFormatData($data);
                $requestTime = time();
                return $this->json(200, '获取成功', compact('data', 'requestTime'));
            }
        }
        // 将未读的回复标记为已读
        $models = AdminReply::find()->where(['to_user_id' => $userId, 'is_read' => 1])
            ->asArray()->all();
        if ($models) {
            $ids = array_column($models, 'id');
            AdminReply::updateAll(['is_read' => 2], ['id' => $ids]);
        }
        return $this->render('my-reply');
    }

    // 取消关注
    public function actionCancelFocus()
    {
        if (Yii::$app->request->isAjax) {
            $userId = Yii::$app->session->get('user_id');
            $toUserId = Yii::$app->request->post('toUserId');
            $model = AdminAttention::find()->where(['user_id' => $userId, 'to_user_id' => $toUserId])
                ->one();
            if (!$model->delete()) {
                return $this->json(100, '取消关注失败');
            }
            return $this->json(200, '取消关注成功');
        }
    }

    // 搜索页面
    public function actionSearch()
    {
        $this->getView()->title = '搜索';
        $type = Yii::$app->request->get('type');
        if (Yii::$app->request->isAjax) {
            $keyword = Yii::$app->request->post('keyword');
            $type = Yii::$app->request->post('type');
            if (!in_array($type, [1, 2])) {
                $type = 1;
            }
            $data = AdminPost::find()
                ->where(['like', 'title', $keyword])
                ->andWhere(['type' => $type])
                ->asArray()->all();
            $data = AdminPost::mFormatData($data);
            if (!$data) {
                return $this->json(100, '没有数据');
            }
            return $this->json(200, 'ok', $data);
        }
        return $this->render('search', compact('type'));
    }

    /**
     * 删帖
     * @return mixed|string
     * @throws \yii\db\Exception
     */
    public function actionDelPost()
    {
        $this->checkLoginStatus();
        if (Yii::$app->request->isAjax) {
            $userId = Yii::$app->session->get('user_id');
            $postId = (int)Yii::$app->request->post('id');
            $postModel = AdminPost::findOne($postId);
            if (!$postModel) return $this->json(100, '帖子不存在');
            if ($userId != $postModel->user_id) {
                return $this->json(100, '只能删除自己的发帖');
            }
            $trans = Yii::$app->db->beginTransaction();
            try {
                $postModel->delete();
                AdminDiscuss::deleteAll(['post_id' => $postId]); // 评论表
                AdminReply::deleteAll(['post_id' => $postId]); // 回复表
                $trans->commit();
                return $this->json(200, '删除成功');
            } catch (\Exception $e) {
                $trans->rollBack();
                return $this->json(100, '删除失败');
            }
        }
    }



}