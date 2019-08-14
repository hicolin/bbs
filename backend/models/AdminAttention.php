<?php

namespace backend\models;

use mobile\controllers\Service;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "admin_attention".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $to_user_id
 * @property integer $create_time
 */
class AdminAttention extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_attention';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'to_user_id', 'create_time'], 'integer']
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
            'to_user_id' => '关注用户ID',
            'create_time' => '创建时间',
        ];
    }

    public function getMember()
    {
        return $this->hasOne(AdminMember::className(), ['id' => 'user_id']);
    }

    // 关注的用户
    public function getUser()
    {
        return $this->hasOne(AdminMember::className(), ['id' => 'to_user_id'])
            ->from(AdminMember::tableName())->alias('user');
    }

    // 添加关注
    public function addAttention($userId, $toUserId)
    {
        $this->user_id = $userId;
        $this->to_user_id = $toUserId;
        $this->create_time = time();
        if (!$this->save(false)) {
            return $this->arrData(100, '添加失败');
        }
        return $this->arrData(200, '添加成功');
    }

    // 格式化数据
    public static function mFormatData($data)
    {
        foreach ($data as &$list) {
            $list['avatar'] = Service::getUserAvatar($list['user']['pic']);
            $list['url'] = Url::to(['post/poster-man', 'id' => $list['to_user_id']]);
        }
        return $data;
    }


}
