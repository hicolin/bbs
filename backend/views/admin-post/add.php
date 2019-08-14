<?php
use yii\helpers\Url;
?>
<?php $this->beginBlock('header'); ?>
<!-- <head></head>中代码块 -->
<?php $this->endBlock(); ?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="form-group">
                        <label for="title" class="col-sm-2 control-label">手机号</label>
                        <div class="col-sm-8">
                            <input class="form-control" name="tel" type="tel" value="">
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="form-group">
                        <label for="title" class="col-sm-2 control-label">标题</label>
                        <div class="col-sm-8">
                            <input class="form-control" name="title" type="text" value="">
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="form-group">
                        <label for="pageview" class="col-sm-2 control-label">浏览量</label>
                        <div class="col-sm-8">
                            <input class="form-control" name="pageview" type="number" value="0">
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="form-group">
                        <label for="thumb_up" class="col-sm-2 control-label">点赞数</label>
                        <div class="col-sm-8">
                            <input class="form-control" name="thumb_up" type="number" value="0">
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">类型</label>
                        <div class="col-sm-8">
                            <select name="type" id="type" class="form-control">
                                <option value="1">曝光区</option>
                                <option value="2">信息街</option>
                            </select>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="form-group">
                        <label for="is_stick" class="col-sm-2 control-label">置顶</label>
                        <div class="col-sm-8">
                            <select name="is_stick" id="is_stick" class="form-control">
                                <option value="1">否</option>
                                <option value="2">是</option>
                            </select>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="form-group">
                        <label for="status" class="col-sm-2 control-label">状态</label>
                        <div class="col-sm-8">
                            <select name="status" id="status" class="form-control">
                                <option value="1">显示</option>
                                <option value="2">隐藏</option>
                            </select>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="form-group">
                        <label for="content" class="col-sm-2 control-label">内容</label>
                        <div class="col-sm-8">
                            <textarea name="content"  id="content"  style="float:left;width:100%; height:300px;border:1px;"></textarea>
                            <script type="text/javascript">
                                var ue = UE.getEditor("content");
                            </script>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="form-group">
                        <label for="resource" class="col-sm-2 control-label">&nbsp;</label>
                        <div class="col-sm-8">
                            <button class="btn btn-primary" id="save_btn">保存</button>
                            <button class="btn" style="margin-left: 10px" onclick="history.back();">返回</button>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<?php $this->beginBlock('footer'); ?>
<script>
    $('#save_btn').click(function () {
        var tel = $('input[name="tel"]').val();
        var pageview = $('input[name="pageview"]').val();
        var thumb_up = $('input[name="thumb_up"]').val();
        var type = $('select[name="type"]').val();
        var is_stick = $('select[name="is_stick"]').val();
        var status = $('select[name="status"]').val();
        var title = $('input[name="title"]').val();
        var content = ue.getContent();
        var data = {tel: tel, pageview: pageview, thumb_up: thumb_up, type: type, is_stick: is_stick, status: status,
        title: title, content: content};
        layer.load(3);
        $.post('<?= Url::to([$this->context->id . '/add']) ?>', data, function (res) {
            layer.closeAll();
            if (res.status === 200) {
                layer.msg(res.msg, {icon: 1, time: 1500}, function () {
                    location.href = '<?= Yii::$app->request->referrer ?>';
                })
            } else {
                layer.msg(res.msg, {icon: 2, time: 1500});
            }
        }, 'json')
    })
</script>
<?php $this->endBlock(); ?>
