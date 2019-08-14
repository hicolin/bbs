<?php

namespace backend\models;

use mobile\controllers\Helper;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "admin_post".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $content
 * @property string $thumbnail
 * @property integer $pageview
 * @property integer $thumb_up
 * @property integer $discuss_num
 * @property integer $type
 * @property integer $status
 * @property integer $is_stick
 * @property integer $create_time
 */
class AdminPost extends BaseModel
{
    public static $postType = [
        1 => '同行曝光',
        2 => '产品曝光',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'pageview', 'thumb_up', 'discuss_num', 'type', 'status', 'is_stick', 'create_time'], 'integer'],
            [['content'], 'string'],
            [['thumbnail', 'title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户名',
            'title' => '标题',
            'content' => '内容',
            'thumbnail' => '缩略图',
            'pageview' => '浏览量',
            'thumb_up' => '点赞数',
            'discuss_num' => '评论数',
            'type' => '类型',
            'status' => '状态',
            'create_time' => '创建时间',
        ];
    }

    public function getMember()
    {
        return $this->hasOne(AdminMember::className(), ['id' => 'user_id']);
    }

    // 格式化数据
    public static function mFormatData($data)
    {
        foreach ($data as &$list) {
            if ($list['thumbnail']) {
                $list['thumbnail'] = Url::base() . $list['thumbnail'];
            }
            $list['content'] = htmlspecialchars_decode($list['content']);
            $list['url'] = Url::to(['post/details', 'id' => $list['id']]);
            $list['create_time'] = Helper::HumanTime($list['create_time']);
        }
        return $data;
    }

    // 保存帖子
    public function mSavePost($userId, $title, $content, $thumbnail, $type)
    {
        $this->user_id = $userId;
        $this->title = $title;
        $this->content = $content;
        $this->thumbnail = $thumbnail;
        $this->type = $type;
        $this->create_time = time();
        if ($type == 2) {
            $this->status = 2;
        } else {
            $this->type = 1;
        }
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return ['status' => 100, 'msg' => $error];
        }
        return ['status' => 200, 'msg' => '保存成功'];
    }

}
