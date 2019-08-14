<?php

namespace backend\models;

use common\controllers\PublicController;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "admin_article".
 *
 * @property integer $art_id
 * @property string $title
 * @property string $img
 * @property integer $permission
 * @property integer $is_recom
 * @property string $detail
 * @property string $source
 * @property integer $create_time
 */
class AdminArticle extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_article';
    }

    /*联表查询*/

    public function getArt_name(){
        return $this->hasOne(AdminCategory::className(),['id'=>'cat_id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title','cat_id'], 'required',],
            [['permission', 'is_recom','grade'], 'integer'],
            [['img','permission','is_recom','create_time','detail'],'safe'],
            [['title'], 'string', 'max' => 255],
            [['source'], 'string', 'max' => 50],

        ];
    }

    public function upload()
    {
        if($this->validate()) {
            $this->img->saveAs('backend/web/plugins/uploads/'.$this->img->baseNames.'.'.$this->img->extension);
            return true;
        }else{
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'art_id' => 'ID',
            'cat_id'=>'分类名称',
            'title' => '文章标题',
            'img' => '文章图片',
            'permission' => '代理商是否可见',
            'is_recom' => '是否推荐',
            'detail' => '详细内容',
            'source' => '来源',
            'create_time' => '创建时间',
            'grade' =>'会员权限',
        ];
    }

    public static function mFormatData($data)
    {
        foreach ($data as &$list) {
            $list['url'] = Url::to(['list/detail', 'id' => $list['art_id']]);
            $list['img'] = $list['img'] ? : Url::base() . '/mobile/web/images/kouzi.png';
            $list['title'] = PublicController::subStr($list['title'], 40);
            $list['create_time'] = date('Y-m-d');
        }
        return $data;
    }

}
