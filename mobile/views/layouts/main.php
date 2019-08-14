<?php

/* @var $this \yii\web\View */
/* @var $content string */

use mobile\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use common\controllers\PublicController;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) . ' - ' . PublicController::getSysInfo(25)?></title>
    <?php $this->head() ?>
    <link href="<?= Url::base()?>/mobile/web/css/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://at.alicdn.com/t/font_468103_139ugzs21zmjwcdi.css">
    <link rel="stylesheet" type="text/css" href="https://at.alicdn.com/t/font_782999_pbze9tnagg.css">
    <link rel="stylesheet" href="<?= Url::base()?>/mobile/web/nprogress/nprogress.css">
    <link rel="stylesheet" href="<?= Url::base()?>/mobile/web/css/swiper.min.css">
    <link rel="stylesheet" href="<?= Url::base()?>/mobile/web/css/animate.css">
    <?php if(isset($this->blocks['header']) == true):?>
        <?= $this->blocks['header'] ?>
    <?php endif;?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="container">
        <?= $content ?>
</div>


<?php

if( Yii::$app->getSession()->hasFlash('error') ) { ?>
    <script>
        var error = "<?=Yii::$app->getSession()->getFlash('error')?>";
        layer.msg(error,{icon:2,time:2000})
    </script>
<?php }

if(Yii::$app->getSession()->hasFlash('success')) { ?>
    <script>
        var success = "<?=Yii::$app->getSession()->getFlash('success')?>";
        layer.msg(success,{icon:1,time:2000})
    </script>
<?php }

if(Yii::$app->getSession()->hasFlash('jump')) { ?>
    <script>
        var jump = "<?=Yii::$app->getSession()->getFlash('jump')?>";
        var url = "<?=Yii::$app->getSession()->getFlash('url')?>";
        layer.confirm(jump, {
            btn: ['确定'] //按钮
        }, function(){
            window.location.href=url;
        }, function(e){
            layer.close(e);
            return false;
        });
    </script>
<?php }
?>

    <style type="text/css">

        @media screen and (min-width: 600px) {
            body,.person_foot{max-width:600px;margin:0 auto;font-size:28px;}
            html{font-size:28px;}
            .person_foot{left: auto!important;}
            .person_foot li a.pf_mid{width: 2.8rem!important;margin-left: 20px;}
            .person_foot li i{padding-top:0; padding-bottom:0;}
            .person_foot li a.pf_mid i{margin-top: 0;}
            .index_main_con ul{margin-bottom:20px;}
        }
        .person_foot li a.pf_mid i.icon-qiandai{color:#3af;}
        .person_foot li a.pf_mid{background:none;top:-0.5rem}
        .layui-layer-btn .layui-layer-btn0{font-size:0.6rem;}
    </style>
    <div class="person_foot">
        <ul>
            <li>
                <a href="<?= Url::to(['post/index', 'type' => 1]) ?>">
                    <i class="iconfont icon-handle-card-empty"></i>
                    <span>曝光区</span>
                </a>
            </li>
            <li>
                <a href="<?= Url::to(['post/index', 'type' => 2]) ?>">
                    <i class="iconfont icon-baodian"></i>
                    <span>信息街</span>
                </a>
            </li>
            <li style="height: 2.5rem;">
                <a href="<?=Url::toRoute(['member/article'])?>" class="">
                    <i class="iconfont icon-heikeji"></i>
                    <span>黑科技</span>
                </a>
            </li>
            <!--<li style="height: 2.5rem;">
                <a href="<?/*=Url::toRoute(['index/index'])*/?>" class="">
                    <i class="iconfont icon-qiandai"></i>
                    <span>产品代理</span>
                </a>
            </li>-->
            <li>
                <a href="<?=Url::toRoute('member/index')?>">
                    <i class="iconfont icon-rengezhongxin"></i>
                    <span>个人中心</span>
                </a>
            </li>
        </ul>
    </div>

<script src="<?= Url::base()?>/mobile/web/js/clipboard.min.js"></script>
<script src="<?= Url::base()?>/mobile/web/js/mui.min.js"></script>
<script src="<?= Url::base()?>/mobile/web/nprogress/nprogress.js"></script>
<script src="<?= Url::base()?>/mobile/web/js/lazyload.js"></script>
<script src="<?= Url::base()?>/mobile/web/aui/script/aui-scroll.js"></script>
<script src="<?= Url::base()?>/mobile/web/js/common.js"></script>
<script>
    // nprogress
    bindNprogress();

    // tabBar
    switchTabBar();


    // 返回键处理 需要mui.js版本
    document.addEventListener('plusready', function() {
        var webview = plus.webview.currentWebview();
        plus.key.addEventListener('backbutton', function() {
            webview.canBack(function(e) {
                if(e.canBack) {
                    webview.back();
                } else {
                    //webview.close(); //hide,quit
                    //plus.runtime.quit();
                    mui.plusReady(function() {
                        //首页返回键处理
                        //处理逻辑：1秒内，连续两次按返回键，则退出应用；
                        var first = null;
                        plus.key.addEventListener('backbutton', function() {
                            //首次按键，提示‘再按一次退出应用’
                            if(!first) {
                                first = new Date().getTime();
                                mui.toast('再按一次退出应用');
                                setTimeout(function() {
                                    first = null;
                                }, 1000);
                            } else {
                                if(new Date().getTime() - first < 1500) {
                                    plus.runtime.quit();
                                }
                            }
                        }, false);
                    });
                }
            })
        });
    });
</script>
<?php $this->endBody() ?>
</body>
<?php if(isset($this->blocks['footer']) == true):?>
    <?= $this->blocks['footer'] ?>
<?php endif;?>
</html>
<?php $this->endPage() ?>


