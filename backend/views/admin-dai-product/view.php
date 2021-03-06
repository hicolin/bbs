<?php
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\models\AdminDaiProduct;
$modelLabel=new \backend\models\AdminDaiProduct();
?>
<?php  $this->beginBlock('header');  ?>
<!-- <head></head>中代码块 -->
<?php  $this->endBlock(); ?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <a id="create_btn" href="<?=Url::toRoute([$this->context->id.'/index'])?>" class="btn btn-xs btn-primary">admin-dai-products列表</a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->

                <div class="box-body">

                    <div class="tab-content">
                        <div class="form-group">
   <label for="id" class="col-sm-2 control-label" ><?php echo $modelLabel->getAttributeLabel("id")?></label>
   <div class="col-sm-8">
<div class="form-control" style="height: auto;min-height: 34px;"><?=$model->id?></div>   </div>
</div>
 <div class="clear"></div>
<div class="form-group">
   <label for="logo" class="col-sm-2 control-label" ><?php echo $modelLabel->getAttributeLabel("logo")?></label>
   <div class="col-sm-8">
<div class="form-control" style="height: auto;min-height: 34px;"><?=$model->logo?></div>   </div>
</div>
 <div class="clear"></div>
<div class="form-group">
   <label for="title" class="col-sm-2 control-label" ><?php echo $modelLabel->getAttributeLabel("title")?></label>
   <div class="col-sm-8">
<div class="form-control" style="height: auto;min-height: 34px;"><?=$model->title?></div>   </div>
</div>
 <div class="clear"></div>
<div class="form-group">
   <label for="fy_info" class="col-sm-2 control-label" ><?php echo $modelLabel->getAttributeLabel("fy_info")?></label>
   <div class="col-sm-8">
<div class="form-control" style="height: auto;min-height: 34px;"><?=$model->fy_info?></div>   </div>
</div>
 <div class="clear"></div>
<div class="form-group">
   <label for="titile_info" class="col-sm-2 control-label" ><?php echo $modelLabel->getAttributeLabel("titile_info")?></label>
   <div class="col-sm-8">
<div class="form-control" style="height: auto;min-height: 34px;"><?=$model->titile_info?></div>   </div>
</div>
 <div class="clear"></div>
<div class="form-group">
   <label for="detail" class="col-sm-2 control-label" ><?php echo $modelLabel->getAttributeLabel("detail")?></label>
   <div class="col-sm-8">
<div class="form-control" style="height: auto;min-height: 34px;"><?=$model->detail?></div>   </div>
</div>
 <div class="clear"></div>
<div class="form-group">
   <label for="pic" class="col-sm-2 control-label" ><?php echo $modelLabel->getAttributeLabel("pic")?></label>
   <div class="col-sm-8">
<div class="form-control" style="height: auto;min-height: 34px;"><?=$model->pic?></div>   </div>
</div>
 <div class="clear"></div>
                        <div class="form-group">
                            <label for="logo" class="col-sm-2 control-label" >&nbsp;</label>
                            <div class="col-sm-8">
                                <div class="form-control" style="height: auto;min-height: 34px;border: none;">
                                    <a href="javascript:history.back(-1)" class="btn btn-primary"> 返&nbsp;回</a>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<?php  $this->beginBlock('footer');  ?>
<?php  $this->endBlock(); ?>
