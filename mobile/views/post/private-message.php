<?php
use yii\helpers\Url;
?>
<?= $this->render('@app/views/layouts/header') ?>


<style>
    .person_foot{display: none;}
    .privateMain{background: #fff;height:calc(100vh - 40px - 2.4rem);}
    .privateMain textarea{width: 100%;height: 8rem;resize: none;border: 1px solid #eee;padding: 0.4rem;}
</style>

<!--评论的富文本编辑器部分 start-->

<div class="privateMain">
    <textarea placeholder="私信给他"></textarea>
</div>


<!--评论的富文本编辑器部分 end  -->

<!--固定底部的评论按钮 start-->

<div class="fixedPostBox fixedPostBox_private">
    <input type="button" value="私信" class="btnPost"/>
</div>
<!--固定底部的评论按钮 end  -->

<script>
    var id = '<?= $id ?>';
    $('.btnPost').click(function () {
        var content = $('textarea').val();
        if (!content) {
            layer.msg('私信内容不能为空', {time: 1500});
            return;
        }
        layer.load(3);
        $.post('<?= Url::to(['post/private-message']) ?>', {id: id, content: content}, function (res) {
            layer.closeAll();
            if (res.status === 200) {
                layer.msg(res.msg, {time: 1500}, function () {
                    history.back();
                })
            } else {
                layer.msg(res.msg, {time: 1500})
            }
        }, 'json')
    })
</script>

