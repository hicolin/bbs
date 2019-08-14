<?php

namespace backend\models;

use mobile\controllers\Helper;
use mobile\controllers\Service;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "admin_reply".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $content
 * @property integer $to_user_id
 * @property integer $post_id
 * @property integer $is_read
 * @property integer $create_time
 */
class AdminReply extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_reply';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'to_user_id', 'post_id', 'is_read', 'create_time'], 'integer'],
            [['content'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'content' => '内容',
            'to_user_id' => '被回复用户ID',
            'post_id' => '帖子ID',
            'is_read' => '状态',
            'create_time' => '创建时间',
        ];
    }

    public function addReply($userId, $content, $toUserId, $postId)
    {
        $this->user_id = $userId;
        $this->content = $content;
        $this->to_user_id = $toUserId;
        $this->post_id = $postId;
        $this->is_read = 1;
        $this->create_time = time();
        $this->save(false);
    }

    public function getMember()
    {
        return $this->hasOne(AdminMember::className(), ['id' => 'user_id']);
    }

    public function getPost()
    {
        return $this->hasOne(AdminPost::className(), ['id' => 'post_id']);
    }

    public static function mFormatData($data)
    {
        foreach ($data as &$list) {
            $list['avatar'] = Service::getUserAvatar($list['member']['pic']);
            $list['create_time'] = Helper::HumanTime($list['create_time']);
            $list['url'] = Url::to(['post/details', 'id' => $list['post']['id']]);
        }
        return $data;
    }


}
