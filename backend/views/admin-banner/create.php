<?php
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\models\AdminBanner;

$modelLabel = new \backend\models\AdminBanner()
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
                        <div class="form-group" style="display: none;">
                            <label for="id"
                                   class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("id") ?></label>
                            <div class="col-sm-8">
                                <?php echo $form->field($model, 'id')->textInput(["class" => "form-control", "placeholder" => $modelLabel->getAttributeLabel("id"), "id" => 'id']) ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="form-group">
                            <label for="title"
                                   class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("title") ?></label>
                            <div class="col-sm-8">
                                <?php echo $form->field($model, 'title')->textInput(["class" => "form-control", "placeholder" => $modelLabel->getAttributeLabel("title"), "id" => 'title']) ?>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="form-group" >
                            <label for="img"
                                   class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("img") ?></label>
                            <div class="col-sm-8">
                                <img class="change_img" id="thumb" width="120" height="136" src="/backend/web/images/default.jpg">
                                <?php echo $form->field($model, 'img')->hiddenInput(["class" => "form-control", "placeholder" => $modelLabel->getAttributeLabel("img"), "id" => 'img','style'=>'border:none;']) ?>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="form-group">
                            <label for="link"
                                   class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("link") ?></label>
                            <div class="col-sm-8">
                                <?php echo $form->field($model, 'link')->textInput(["class" => "form-control", "placeholder" => $modelLabel->getAttributeLabel("link"), "id" => 'link']) ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="link"
                                   class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("type") ?></label>
                            <div class="col-sm-8">
                                <?php echo $form->field($model, 'type')->dropDownList([1 => '产品', 2 => '同行曝光', 3 => '产品曝光', 4 => '黑科技'],["class" => "form-control", "id" => 'type']) ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="form-group" style="display: none;">
                            <label for="create_time"
                                   class="col-sm-2 control-label"><?php echo $modelLabel->getAttributeLabel("create_time") ?></label>
                            <div class="col-sm-8">

                                <?php echo $form->field($model, 'create_time')->textInput(["value"=>time(),"class" => "form-control", "placeholder" => $modelLabel->getAttributeLabel("create_time"), "id" => 'create_time',]) ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="form-group">
                            <label for="resource" class="col-sm-2 control-label">&nbsp;</label>
                            <div class="col-sm-8">
                                <?php echo Html::submitButton('保存', ['class' => "btn btn-primary"]); ?>
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
    <span onclick="UpladFile('deal')" class="mybtn">上传</span>
</div>

<script>
    $('.change_img').on('click',function(){
        $('#file').click();
    })
    var img = document.getElementById("file");
    img.onchange=function () {
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

    function UpladFile()
    {
        var fileObj = document.getElementById("file").files[0];
        //服务器端的路径
        var FileController = "<?=Url::toRoute('/public/file')?>";
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
                $('#img').val(result)
                //alert(result)
                /*var json = eval("(" + result + ")");
                 alert('图片链接:\n'+json.file);*/
            }
        }
    }
</script>
<?php $this->beginBlock('footer'); ?>
<?php $this->endBlock(); ?>
