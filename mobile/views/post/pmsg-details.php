<?php
use yii\helpers\Url;
?>

<?= $this->render('@app/views/layouts/header') ?>

<style>
    .person_foot{display: none;}
    .fixedPostBox .btnPost{width: 100%;}
    .privateZzc{width: 100%;height: calc(100% - 8rem);}
    .privateEditCons{padding: 0.6rem 0.6rem 0.4rem;background: #fff;}
    .privateEditCons textarea{width: 100%;height: 6rem;padding: 0.4rem;resize: none;border: 1px solid #eee;background: rgb(250, 250, 250);border-radius: 4px;}
    .privateNewItemMan span{margin-left: .4rem;color: #999;}
    .mescroll {
        position: fixed;
        top: 40px;
        bottom: 2.6rem;
        height: auto;
    }
    .privateNewsList{background:rgb(239, 237, 238);padding: 0.6rem;}
    .fixedSaysLeft{width: 100%;}
    .fixHeight{width: 100%;height: 2.6rem;}
    #privateEditCons .btnsBox{display: flex;align-items: center;justify-content: space-between;padding-bottom: 0.6rem;}
    .btnsBox .replyBtn03{color: #2dacfc;}
    .privateEditBox{display: flex;align-items: center;justify-content: center;}
    .privateEditCons{position: static;width: 90%;border-radius: 4px;}
</style>

<!--私信列表部分 start-->
<div class="privateNewsList mescroll" id="mescroll">
    <ul id="data-list">
        <?php foreach ($models as $model): ?>
            <?php if ($model['is_me'] == 1): ?>
                <li class="pmsgItem pmsgItem02">
                    <p class="pmsgTime"><?= $model['create_time'] ?></p>
                    <p class="pmsgTime"></p>
                    <div class="psgCon">
                        <div class="pmsgTxt pmsgTxt02">
                            <?= $model['content'] ?>
                        </div>
                        <div class="pmsgTx">
                            <img src="<?= $model['member']['avatar'] ?>">
                        </div>
                    </div>
                </li>
            <?php else: ?>
                <li class="pmsgItem pmsgItem01">
                    <p class="pmsgTime"><?= $model['create_time'] ?></p>
                    <div class="psgCon">
                        <div class="pmsgTx">
                            <img src="<?= $model['member']['avatar']?>">
                        </div>
                        <div class="pmsgTxt pmsgTxt01">
                            <?= $model['content'] ?>
                        </div>
                    </div>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>

    <div class="fixHeight"></div>
    <div class="fixedSaysBox">
        <div class="fixedSaysLeft" onclick="openPop()">
            <i class="iconfont icon-bianji"></i>

        </div>
    </div>
</div>
<!--私信列表部分 end  -->

<!-- start-->
<div class="privateEditBox" style="display: none;">
    <div class="privateEditCons" id="privateEditCons">

        <div class="btnsBox">
            <span onclick="closePop()">取消</span>
            <div class="replyBtn03" id="privateEditBtn">回复</div>
        </div>
        <textarea placeholder="内容支持输入法表情" id="textarea"></textarea>

    </div>
</div>
<!-- end  -->

<script>

    var page = 2;
    var page_size = 6;
    var user_id = '<?= $userId ?>';
    var mescroll = new MeScroll('mescroll', {
        down: {
            auto: false,
            callback: downCallback
        }
    });

    // 下拉
    function downCallback() {
        var transferData = {page: page, page_size: page_size, user_id: user_id, pull_type: 'down'};
        $.post('<?= Url::to(['post/pmsg-details']) ?>', transferData, function (res) {
            if (res.status === 200) {
                var data = res.data;
                if (data.length != 0) {
                    var html = '';
                    data.forEach(function (list) {
                        html = jointHtml(html, list);
                    })
                    $('#data-list').prepend(html);
                    page++;
                }
                mescroll.endSuccess();
            } else {
                mescroll.endErr();
            }
        }, 'json');
    }

    // 拼接html
    function jointHtml(html, list) {
        if (list.is_me == 1) {
            html += `
                <li class="pmsgItem pmsgItem02">
                    <p class="pmsgTime">${list.create_time}</p>
                    <p class="pmsgTime"></p>
                    <div class="psgCon">
                        <div class="pmsgTxt pmsgTxt02">
                            ${list.content}
                        </div>
                        <div class="pmsgTx">
                            <img src="${list.member.avatar}">
                        </div>
                    </div>
                </li>
            `;
        } else {
            html += `
                <li class="pmsgItem pmsgItem01">
                    <p class="pmsgTime">${list.create_time}</p>
                    <div class="psgCon">
                        <div class="pmsgTx">
                            <img src="${list.member.avatar}">
                        </div>
                        <div class="pmsgTxt pmsgTxt01">
                            ${list.content}
                        </div>
                    </div>
                </li>
            `;
        }
        return html;
    }

    //弹出输入框
    function openPop() {
        $(".privateEditBox").show();
    }

    //关闭输入框
    function closePop() {
        $(".privateEditBox").hide();
    }

    $('.replyBtn03').click(function () {
        var content = $('textarea').val();
        if (!content) {
            layer.msg('私信内容不能为空', {time: 1500});
            return;
        }
        layer.load(3);
        $.post('<?= Url::to(['post/private-message']) ?>', {id: user_id, content: content}, function (res) {
            layer.closeAll();

            closePop();
            $("#textarea").val('');
            if (res.status === 200) {
                var data = res.data;
                layer.msg(res.msg, {time: 1500}, function () {
                    if (data.length != 0) {
                        var html = '';
                        data.forEach(function (list) {
                            html = jointHtml(html, list);
                        });
                        $('#data-list').append(html);
                    }
                })


            } else {
                layer.msg(res.msg, {time: 1500})
            }
        }, 'json')
    })
</script>

