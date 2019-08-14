<?php

namespace backend\models;

use mobile\controllers\Helper;
use mobile\controllers\Service;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "admin_discuss".
 *
 * @property integer $id
 * @property integer $pid
 * @property integer $post_id
 * @property integer $user_id
 * @property string $content
 * @property integer $to_user_id
 * @property integer $thumb_up
 * @property integer $status
 * @property integer $create_time
 */
class AdminDiscuss extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_discuss';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'post_id', 'user_id', 'to_user_id', 'thumb_up', 'status', 'create_time'], 'integer'],
            [['content'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => '父级ID',
            'user_id' => '用户ID',
            'content' => '内容',
            'to_user_id' => '回复的用户ID',
            'thumb_up' => '点赞数',
            'status' => '状态',
            'create_time' => '创建时间',
        ];
    }

    public function getMember()
    {
        return $this->hasOne(AdminMember::className(), ['id' => 'user_id']);
    }

    // 回复的用户
    public function getUser()
    {
        return $this->hasOne(AdminMember::className(), ['id' => 'to_user_id'])
            ->from(AdminMember::tableName())->alias('user');
    }

    // 获取添加二级评论的数据
    public static function getSecondLevelData($oneLevelData)
    {
        foreach ($oneLevelData as &$list) {
            $data = AdminDiscuss::find()->joinWith('member')
                ->where(['status' => 1, 'post_id' => $list['post_id']])
                ->andWhere(['pid' => $list['id']])
                ->orderBy(['thumb_up' => SORT_DESC, 'create_time' => SORT_DESC])
                ->limit(3)->asArray()->all();
            $list['secondLevelDiscuss'] = self::mFormatData($data, 0);
        }
        return $oneLevelData;
    }

    // 格式化数据
    public static function mFormatData($data, $userId)
    {
        foreach ($data as &$list) {
            $list['avatar'] = Service::getUserAvatar($list['member']['pic']);
            $list['grade_name'] = Service::getMemberType($list['member']['grade'], $list['member']['end_time'], $list['member']['id']);
            $list['create_time'] = Helper::HumanTime($list['create_time']);
            $list['url'] = Url::to(['post/all-reply', 'id' => $list['id']]);
            $list['is_thumbed'] = 0;
            if ($userId) {
                $res = AdminThumbUp::find()
                    ->where(['user_id' => $userId, 'type' => 2, 'content_id' => $list['id']])
                    ->one();
                $res && $list['is_thumbed'] = 1;
            }
        }
        return $data;
    }


}
