<?php

use common\controllers\PublicController;
use mobile\controllers\Service;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$modelLabel = new \backend\models\AdminMember()
?>
<?php $this->beginBlock('header'); ?>
<!-- <head></head>中代码块 -->
<?php $this->endBlock(); ?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <a id="create_btn" href="<?= Url::toRoute([$this->context->id . '/index']) ?>"
                               class="btn btn-xs btn-primary">返回列表</a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->

                <div class="box-body">
                    <?php $form = ActiveForm::begin([
                        'fieldConfig' => [
                            'template' => '<div class="span12 field-box">{input}</div>{error}',
                        ],
                        'options' => [
                            'class' => 'new_user_form inline-input',
                        ],
                        'id' => 'form',
                    ])
                    ?>
                    <div class="tab-content">
                        <div class="form-group">
                            <label for="nickname"
                                   class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("nickname") ?></label>
                            <div class="col-sm-8">
                                <?php echo $form->field($model, 'nickname')->textInput(["class" => "form-control","value"=>PublicController::filter_decode($model->nickname), "placeholder" => $modelLabel->getAttributeLabel("nickname"), "id" => 'nickname']) ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="form-group">
                            <label for="real_name"
                                   class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("real_name") ?></label>
                            <div class="col-sm-8">
                                <?php echo $form->field($model, 'real_name')->textInput(["class" => "form-control","value"=>PublicController::filter_decode($model->real_name), "placeholder" => $modelLabel->getAttributeLabel("real_name"), "id" => 'real_name']) ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="form-group">
                            <label for="image"
                                   class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("pic") ?></label>
                            <div class="col-sm-8">
                                <img class="change_img" id="thumb" width="80" height="90" src="<?=$model->pic?:'/backend/web/images/default.jpg'?>">
                                <?php echo $form->field($model, 'pic')->hiddenInput(["class" => "form-control", "placeholder" => $modelLabel->getAttributeLabel("pic"), "id" => 'pic']) ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="form-group">
                            <label for="tel"
                                   class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("tel") ?></label>
                            <div class="col-sm-8">
                                <?php echo $form->field($model, 'tel')->textInput(["class" => "form-control", "value"=>PublicController::filter_decode($model->tel),"placeholder" => $modelLabel->getAttributeLabel("tel"), "id" => 'tel']) ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="form-group">
                            <label for="account_number"
                                   class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("account_number") ?></label>
                            <div class="col-sm-8">
                                <?php echo $form->field($model, 'account_number')->textInput(["class" => "form-control", "value"=>PublicController::filter_decode($model->account_number),"placeholder" => $modelLabel->getAttributeLabel("account_number"), "id" => 'account_number']) ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="form-group">
                            <label for="account_name"
                                   class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("account_name") ?></label>
                            <div class="col-sm-8">
                                <?php echo $form->field($model, 'account_name')->textInput(["class" => "form-control", "value"=>PublicController::filter_decode($model->account_name),"placeholder" => $modelLabel->getAttributeLabel("account_name"), "id" => 'account_name']) ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="form-group">
                            <label for="agent"
                                   class="col-sm-2 control-label">会员等级</label>
                            <div class="col-sm-2">
                            <select name="AdminMember[grade]" id="grade" class="form-control">
                                     <option value="1" <?= $model->grade == 1 ? 'selected' : '' ?>> 普通会员 </option>
                                     <option value="2" <?= $model->grade == 2 ? 'selected' : '' ?>> <?= Service::getMemberType(2) ?> </option>
                                     <option value="3" <?= $model->grade == 3 ? 'selected' : '' ?>> <?= Service::getMemberType(3) ?> </option>
                            </select>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="form-group">
                            <label for="is_partner"
                                   class="col-sm-2 control-label">合伙人</label>
                            <div class="col-sm-2">
                                <select name="AdminMember[is_partner]" id="is_partner" class="form-control">
                                    <option value="1" <?=  $model->is_partner == 1 ? 'selected' : ''?>> 否 </option>
                                    <option value="2" <?=  $model->is_partner == 2 ? 'selected' : ''?>> 是 </option>
                                </select>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="form-group">
                            <label for="agent"
                                   class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("promotion_commission") ?></label>
                            <div class="col-sm-8">
                                <?php echo $form->field($model, 'promotion_commission')->textInput(["class" => "form-control", "placeholder" => $modelLabel->getAttributeLabel("promotion_commission"), "id" => 'promotion_commission']) ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="form-group">
                            <label for="agent"
                                   class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("dai_commission") ?></label>
                            <div class="col-sm-8">
                                <?php echo $form->field($model, 'dai_commission')->textInput(["class" => "form-control", "placeholder" => $modelLabel->getAttributeLabel("dai_commission"), "id" => 'dai_commission']) ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="form-group">
                            <label for="agent"
                                   class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("available_money") ?></label>
                            <div class="col-sm-8">
                                <?php echo $form->field($model, 'available_money')->textInput(["class" => "form-control", "placeholder" => $modelLabel->getAttributeLabel("available_money"), "id" => 'available_money']) ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="form-group">
                            <label for="resource" class="col-sm-2 control-label">&nbsp;</label>
                            <div class="col-sm-8">
                                <?php echo Html::submitButton('保存', ['class' => "btn btn-primary",'id'=>"put_up"]); ?>
                                <span>&nbsp;</span>
                                <?php echo Html::resetButton('重置', ['class' => "btn btn-primary"]); ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<div style="display: none">
    <input type="file" name="file" class="file" id="file" onchange="document.getElementById('textfield').value=this.value" />
    <span onclick="UploadFile()" class="mybtn">上传</span>
</div>
<script>
    $('.change_img').on('click',function(){
        $('#file').click();
    })
    var head_img = document.getElementById("file");
    head_img.onchange=function () {
        $('.mybtn').click()
    }
</script>
<script type="text/javascript">
    var xhr;
    function createXMLHttpRequest()
    {
        if(window.ActiveXObject)
        {
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        }
        else if(window.XMLHttpRequest)
        {
            xhr = new XMLHttpRequest();
        }
    }

    function UploadFile()
    {
        var fileObj = document.getElementById("file").files[0];
        //服务器端的路径
        var FileController = "<?=Url::toRoute('public/file')?>";
        var form = new FormData();
        //file可更改，在服务器端获取$_FILES['file']
        form.append("file", fileObj);
        createXMLHttpRequest();
        xhr.onreadystatechange = deal;
        xhr.open("post", FileController, true);
        xhr.send(form);
    }

    function deal()
    {
        if(xhr.readyState == 4)
        {
            if (xhr.status == 200 || xhr.status == 0)
            {
                var result = xhr.responseText;
                $('.change_img').attr('src',result)
                $('#pic').val(result)
            }
        }
    }
        $('#put_up').click(function(){
            var tel = $('#tel').val();
            if(!tel){
                layer.msg('请输入手机号码！');
                return false;
            }else{
                var str = /^1[3,4,5,7,8]\d{9}$/;
            if(!str.test(tel)){
                layer.msg('手机号码不正确！');
                return false;
            }else{
                $('#form').submit();
            }
            }
    })

    $("#end_time").ECalendar({
        type:"time",   //模式，time: 带时间选择; date: 不带时间选择;
        stamp : true,   //是否转成时间戳，默认true;
        offset:[0,2],   //弹框手动偏移量;
        format:"yyyy-mm-dd hh:ii",   //格式 默认 yyyy-mm-dd hh:ii;
        skin:2,   //皮肤颜色，默认随机，可选值：0-8,或者直接标注颜色值;
        step:10,   //选择时间分钟的精确度;
    });
</script>
