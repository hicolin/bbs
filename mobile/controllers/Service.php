<?php
/**
 * User: Colin
 * Time: 2019/3/6 10:16
 */

namespace mobile\controllers;


use backend\models\AdminMember;
use yii\helpers\Url;

class Service
{

    // 获取用户头像
    public static function getUserAvatar($avatar)
    {
        if ($avatar) {
            $path = Url::base() . $avatar;
        } else {
            $path = Url::base() . '/mobile/web/images/avatar.png';
        }
        return $path;
    }

    /**
     * 处理交易人用户名或手机号（脱敏）
     * @param $dyUserInfo
     * @return mixed
     */
    public static function dealTrader($dyUserInfo)
    {
        if (!$dyUserInfo) {
            return $dyUserInfo;
        }
        $begin = mb_substr($dyUserInfo, 0, 1);
        if (is_numeric($begin)) {
            return substr_replace($dyUserInfo, '****', 3, 4);
        } else {
            return $dyUserInfo;
        }
    }

    /**
     * 获取轮播的类型
     * @param $type
     * @return mixed|string
     */
    public static function getBannerTypeName($type)
    {
        $types = [1 => '产品', 2 => '曝光区', 3 => '信息街', 4 => '黑科技'];
        if (in_array($type, array_keys($types))) {
            return $types[$type];
        }
        return '';
    }

    /**
     * 获取会员类型
     * @param $grade
     * @param $endTime
     * @param $userId
     * @return string
     */
    public static function getMemberType($grade, $endTime = '', $userId = '') // 为了兼容之前的代码，参数暂未去除
    {
        $types = [1 => '普通会员', 2 => 'VIP会员', 3 => '合伙人'];
        return $types[$grade];
    }

}