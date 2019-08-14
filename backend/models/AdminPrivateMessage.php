<?php

namespace backend\models;

use mobile\controllers\Helper;
use mobile\controllers\Service;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "admin_private_message".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $to_user_id
 * @property string $content
 * @property integer $is_read
 * @property integer $create_time
 */
class AdminPrivateMessage extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_private_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'to_user_id', 'create_time', 'is_read'], 'integer'],
            [['content'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '发信人ID',
            'to_user_id' => '收信人ID',
            'content' => '内容',
            'is_read' => '是否已读',
            'create_time' => '创建时间',
        ];
    }

    public function getMember()
    {
        return $this->hasOne(AdminMember::className(), ['id' => 'user_id']);
    }

    public function getUser()
    {
        return $this->hasOne(AdminMember::className(), ['id' => 'to_user_id'])
            ->from(AdminMember::tableName())->alias('user');
    }

    // 添加私信
    public function mAddMessage($userId, $toUserId, $content)
    {
        $this->user_id = $userId;
        $this->to_user_id = $toUserId;
        $this->content = htmlspecialchars($content);
        $this->is_read = 1;
        $this->create_time = time();
        if (!$this->save()) {
            $error = array_values($this->getFirstErrors())[0];
            return $this->arrData(100, $error);
        }
        return $this->arrData(200, '添加成功');
    }

    // 格式化数据
    public static function mFormatData($data)
    {
        foreach ($data as &$list) {
            $list['url'] = Url::to(['post/pmsg-details', 'user_id' => $list['user_id']]);
            $list['member']['avatar'] = Service::getUserAvatar($list['pic']);
            $list['member']['nickname'] = $list['nickname'];
            $list['create_time'] = Helper::HumanTime($list['create_time']);
            $list['content'] = htmlspecialchars_decode($list['content']);
        }
        return $data;
    }

    // 格式化数据
    public static function mPmsgData($data)
    {
        foreach ($data as &$list) {
            $list['member']['avatar'] = Service::getUserAvatar($list['member']['pic']);
            $list['user']['avatar'] = Service::getUserAvatar($list['user']['pic']);
            $list['create_time'] = Helper::HumanTime($list['create_time']);
            $list['content'] = htmlspecialchars_decode($list['content']);
        }
        return $data;
    }
}
