<?php
use yii\helpers\Url;
use mobile\controllers\Service;
?>

<?= $this->render('@app/views/layouts/header') ?>

<style>
    .person_foot{display: none;}
    .mescroll {
        position: fixed;
        top: 40px;
        bottom: 2.6rem;
        height: auto;
    }
</style>

<div class="mescroll" id="mescroll">
    <div class="replyListMain">
<!-- 主评论部分 start-->
        <div class="replayMain">
        <div class="posterAuthorLeft">
            <div class="posterAuthorTx">
                <img src="<?= Service::getUserAvatar($model['member']['pic']) ?>">
            </div>

            <div class="posterAuthorInfo">
                <div class="flexVip">
                    <h1><?= $model['member']['nickname'] ?></h1>
                    <i class="vip0<?= $model['member']['grade']?>">
                        <?= Service::getMemberType($model['member']['grade'], $model['member']['end_time'], $model['member']['id']) ?>
                    </i>
                </div>

                <p><?= $model['create_time'] ?></p>
            </div>
        </div>

        <div class="replyMainTxt">
            <?= htmlspecialchars_decode($model['content']) ?>
        </div>
    </div>
<!-- 主评论部分 end-->

<!--回复列表部分 start-->
        <div class="replyListBox">
        <ul id="data-list">
            <!--回复内容-->
        </ul>
    </div>
<!--回复列表部分 end -->
    </div>
</div>

<!--固定底部的评论按钮 start-->
<div class="emptyDiv"></div>
<div class="bottomReplyBox" onclick="openSaysFun()">
    <div>大家都停下，听我讲</div>
</div>
<!--固定底部的评论按钮 end  -->

<!--评论的富文本编辑器部分 start-->
<div class="editBox">
    <div class="closeArea"></div>
    <div class="editBoxInner replayInner">
        <textarea id="demo" placeholder="文明上网 理性发言"></textarea>
        <div class="editBtn">评论</div>
    </div>
</div>

<!--评论的富文本编辑器部分 end  -->
<script>
    var user_id = '<?= Yii::$app->session->get('user_id') ?>';
    var to_user_id = '<?= $model['member']['id']?>';
    var id = '<?= $model['id'] ?>';
    var post_id = '<?= $model['post_id'] ?>';
    var request_time = '<?= time() ?>';
    var mescroll = new MeScroll('mescroll', {
        down: {
            auto: false,
            callback: downCallback
        },
        up: {
            callback: upCallBack,
            toTop: {
                src: "<?= Url::base()?>/mobile/web/plugins/mescroll/img/mescroll-totop.png",
                offset: 1000
            },
            empty: {
                warpId:	"mescroll",
                icon: "<?= Url::base()?>/mobile/web/plugins/mescroll/img/mescroll-empty.png",
                tip: "暂无相关数据~"
            },
        }
    });

    // 下拉
    function downCallback() {
        var transferData = {id: id, request_time: request_time, pull_type: 'down'};
        $.post('<?= Url::to(['post/all-reply']) ?>', transferData, function (res) {
            if (res.status === 200) {
                var data = res.data;
                if (data.discuss.length != 0) {
                    var html = '';
                    data.discuss.forEach(function (list) {
                        html = jointHtml(html, list);
                    })
                    $('#data-list').prepend(html);
                    request_time = res.requestTime;
                }
                mescroll.endSuccess();
            } else {
                mescroll.endErr();
            }
        }, 'json');
    }

    // 上拉
    function upCallBack(page) {
        var transferData = {id: id, pull_type: 'up', num: page.num, size: page.size};
        $.post('<?= Url::to(['post/all-reply'])?>', transferData, function (res) {
            if (res.status === 200) {
                var data = res.data;
                var curPageData = data.discuss;
                var totalSize = data.total;
                var html = '';
                curPageData.forEach(function (list) {
                    html = jointHtml(html, list);
                });
                $('#data-list').append(html);
                // 自己已经点赞的高亮显示
                $('.icon_zan').each(function (index, val) {
                    var status = $(this).attr('data-status');
                    if (status == 1) {
                        $(this).addClass("rubberBand").css("color","red");
                    }
                });
                mescroll.endBySize(curPageData.length, totalSize);
            } else {
                mescroll.endErr();
            }
        }, 'json');
    }

    // 拼接html
    function jointHtml(html, list) {
        var vip = 'vip0' + list.member.grade;
        html += `<li class="replyItem">
                <div class="replyItemTop">
                    <div class="replyItemLeft">
                        <div class="replyItemTx">
                            <img src="${list.avatar}">
                        </div>
                        <div class="replyItemMan">
                            <div class="flexVip">
                              <h1 class="saysAuthor">${list.member.nickname}</h1>
                              <i class="${vip}">${list.grade_name}</i>
                            </div>

                            <p>${list.create_time}</p>
                        </div>
                    </div>

                    <div class="replyItemRight">
                        <i class="iconfont icon-dianzan animated icon_zan" data-status="${list.is_thumbed}"
                        onclick="thumbUp(this, ${list.post_id}, ${list.id}, ${list.member.id})"></i>
                        <em>${list.thumb_up}</em>
                    </div>
                </div>

                <div class="replyItemBottom">${list.content}</div>
            </li>`;
        return html;
    }

    $(function () {
        //点赞效果
        $(".icon_zan").click(function () {
            var _status = $(this).attr("data-status");//0-》点赞  1-》取消点赞
            if(_status == 0){
                $(this).addClass("rubberBand").css("color","red");
                $(this).attr("data-status",'1');
            }else{
                $(this).removeClass("rubberBand").css("color","");
                $(this).attr("data-status",'0');
            }
        });
    });

    // 点赞/取消点赞
    function thumbUp(_this, postId, discussId, toUserId) {
        if (!user_id) {
            location.href = '<?= Url::to(['index/login']) ?>';
            return;
        }
        var status = $(_this).attr('data-status');
        // 点赞效果
        if(status == 0){
            $(_this).addClass("rubberBand").css("color","red");
            $(_this).attr("data-status",'1');
        }else{
            $(_this).removeClass("rubberBand").css("color","");
            $(_this).attr("data-status",'0');
        }
        var transferData = {userId: user_id, postId: postId, discussId: discussId, status: status, toUserId: toUserId};
        $.post('<?= Url::to(['post/thumb-up']) ?>', transferData, function (res) {
            if (res.status === 200 ) {
                var thumbNum;
                if (status == 0) { // 点赞
                    thumbNum = parseInt($(_this).next('em').text());
                    $(_this).next('em').text(thumbNum + 1);
                } else { // 取消点赞
                    thumbNum = parseInt($(_this).next('em').text());
                    $(_this).next('em').text(thumbNum - 1);
                }
            } else {
                layer.msg(res.msg)
            }
        }, 'json')
    }
</script>

<script>
    //点击评论框以外部分关闭评论区域
    $(".closeArea").click(function () {
        $(".editBox").hide();
    });

    //点击评论按钮
    $(".editBtn").click(function () {
        var content = $('textarea').val();
        if (!$.trim(content)) {
            layer.msg('评论内容不能为空', {time: 1500});
            return;
        }
        layer.load(3);
        var transferData = {postId: post_id, discussId: id, toUserId: to_user_id, content: content};
        $.post('<?= Url::to(['post/add-discuss'])?>', transferData, function (res) {
            layer.closeAll();
            $(".editBox").hide();
            var data = res.data;
            if (res.status === 200) {
                layer.msg(res.msg, {time: 1500}, function () {
                    var html = '';
                    html = jointHtml(html, data.model);
                    $('#data-list').prepend(html);
                    // 去除暂无数据占位
                    if ($('.mescroll-empty').html != '') {
                        $('.mescroll-empty').remove();
                    }
                });
            } else {
                layer.msg(res.msg, {time: 1500});
            }
        }, 'json');
    });

    //弹出回复框的方法
    function openSaysFun() {
        if (!user_id) {
            location.href = '<?= Url::to(['index/login']) ?>';
            return;
        }
        $('textarea').val('');
        $(".editBox").show();
    }
</script>
