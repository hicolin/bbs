<?php
use yii\helpers\Url;
?>

<?php $this->beginBlock('header') ?>
<style>
    .person_foot {
        display: none;
    }
</style>
<?php $this->endBlock() ?>

<?= $this->render('@app/views/layouts/header') ?>
<?php if ($type == 1): ?>
    <p style="padding: 10px;color: #999;font-size: 12px">注：在曝光区，普通会员不能发帖,请升级会员后再来。</p>
<?php endif;?>
<?php if ($type == 2): ?>
    <p style="padding: 10px;color: #999;font-size: 12px">注：信息街的发帖，需要管理员审核通过后，才会显示。</p>
<?php endif;?>
<!--评论的富文本编辑器部分 start-->
<form action="<?= Url::to(['post/add-post']) ?>" method="post" enctype="multipart/form-data" onsubmit="return checkData()">
    <div class="postEditBox">
        <input type="hidden" name="type" value="<?= $type ?>">
        <input type="text" name="title" placeholder="请输入标题" class="titleInput"/>
        <div class="editBoxInner">
            <i class="iconfont icon-close48 closeEditBtn"></i>
            <textarea placeholder="内容" name="content[]" class="conTextArea"></textarea>
            <div class="conIgBox">
                <img src="">
                <input type="file" name="pic[]" onchange="viewImg(this)"/>
            </div>
        </div>
    </div>
    <!--评论的富文本编辑器部分 end  -->

    <div class="emptyDiv"></div>
    <!--固定底部的评论按钮 start-->
    <div class="fixedPostBox">
        <div class="fixedPostBoxLeft">
            <i class="iconfont icon-tinajia"></i>
            <em>添加图文</em>
        </div>
        <input type="submit" value="发布" class="btnPost"/>
    </div>
</form>

<!--固定底部的评论按钮 end  -->

<script>
    function checkData() {
        var title = $('input[name="title"]').val();
        var content = $('textarea[name="content[]"]').val();
        if (!title) {
            layer.msg('标题不能为空');
            return false;
        }
        if (!content) {
            layer.msg('发帖内容不能为空');
            return false;
        }
    }

    // 图片预览
    function viewImg(obj) {
        var reads = new FileReader();
        f = obj.files[0];
        reads.readAsDataURL(f);
        reads.onload = function (e) {
            $(obj).parent().find('img').attr('src', this.result);
        }
    }
</script>

<script>
    //点击添加图文按钮
    var _addHtml =  "<div class='editBoxInner editBoxInnerAdd'>";
        _addHtml += "<i class='iconfont icon-close48 closeEditBtn' onclick='deleteAddArea(this)'></i>";
        _addHtml += "<textarea placeholder='内容' name='content[]' class='conTextArea'></textarea>";
        _addHtml += "<div class='conIgBox'>";
        _addHtml += "<img src=''>";
        _addHtml += "<input type='file' name='pic[]' onchange='viewImg(this)'/>";
        _addHtml += "</div></div>";

   $(".fixedPostBoxLeft").click(function () {
       $(".postEditBox").append(_addHtml);
   });

   //删除对于的图文添加区域
    function deleteAddArea(obj) {
        $(obj).parent().remove();
    }
</script>
