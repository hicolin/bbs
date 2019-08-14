<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "admin_thumb_up".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $type
 * @property integer $content_id
 * @property integer $create_time
 */
class AdminThumbUp extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_thumb_up';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'content_id', 'create_time'], 'integer']
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
            'type' => '类型',
            'content_id' => '内容ID',
            'create_time' => '创建时间',
        ];
    }

    // 添加点赞
    public function addThumbUp($userId, $type, $contentId)
    {
        $this->user_id = $userId;
        $this->type = $type;
        $this->content_id = $contentId;
        $this->create_time = time();
        if (!$this->save(false)) {
            return $this->arrData(100, '添加失败');
        }
        return $this->arrData(200, '添加成功');
    }
}
