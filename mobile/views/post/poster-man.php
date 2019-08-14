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

<!--主体部分 start-->
<div class="posterManMain mescroll" id="mescroll">
    <div class="posterManTop">
        <div class="posterManInner">
            <div class="posterManTx">
                <img src="<?= Service::getUserAvatar($member['pic']) ?>">
            </div>
            <div class="flexVipMan">
                <h1 class="posterManName"><?= $member['nickname'] ?></h1>
                <i class="vip0<?= $member['grade']?>">
                    <?= Service::getMemberType($member['grade'], $member['end_time'], $member['id']) ?>
                </i>
            </div>

            <div class="posterManGn">
                <?php if ($isSelf): ?>
                <a href="<?=Url::toRoute (['post/private-message-list'])?>">
                <?php else: ?>
                <a href="<?=Url::toRoute (['post/private-message', 'id' => $member['id']])?>">
                <?php endif; ?>
                    <!--有私信消息时显示这个小红点，其余不显示-->
                    <i class="iconfont icon-sixin"></i>
                    私信
                </a>
                <a data-status="0" class="icon_focus">
                    <i class="iconfont icon-guanzhu animated"></i>
                    <em>关注</em>
                </a>
            </div>
        </div>
    </div>

    <!--列表部分 start-->
    <div class="posterManBottom">
        <div class="posterManBottomTitle">TA的帖子</div>
        <div class="postersBox">
            <ul id="data-list">
                <!--帖子列表-->
            </ul>
        </div>
    </div>
    <!--列表部分 end  -->
</div>
<!--主体部分  end -->

<script>
    var isAttention = '<?= $isAttention ?>';
    var id = '<?= $id ?>';
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
        var transferData = {request_time: request_time, pull_type: 'down', id: id};
        $.post('<?= Url::to(['post/poster-man']) ?>', transferData, function (res) {
            if (res.status === 200) {
                var data = res.data;
                if (data.data.length != 0) {
                    var html = '';
                    data.data.forEach(function (list) {
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
        var transferData = {pull_type: 'up', num: page.num, size: page.size, id: id};
        $.post('<?= Url::to(['post/poster-man'])?>', transferData, function (res) {
            if (res.status === 200) {
                var data = res.data;
                var curPageData = data.data;
                var totalSize = data.total;
                var html = '';
                curPageData.forEach(function (list) {
                    html = jointHtml(html, list);
                });
                $('#data-list').append(html);
                mescroll.endBySize(curPageData.length, totalSize);
            } else {
                mescroll.endErr();
            }
        }, 'json');
    }

    // 拼接html
    function jointHtml(html, list) {
        html += `
            <li class="posterItem">
                    <a class="posterItem_a">
                        <div class="posterItemTop" onclick="location.href='${list.url}'">
                            <h1>${list.title}</h1>
        `;
        if (list.thumbnail) {
            html += `
                            <div>
                                <img src="${list.thumbnail}">
                            </div>
            `;
        }
        html += `
                        </div>
                        <div class="posterItemDown">
                            <p>${list.create_time}</p>
                            <div>
                                <div>评论<i>${list.discuss_num}</i></div>
                                <div>阅读<i>${list.pageview}</i></div>
                                <div><em class="iconfont icon-delete" onclick="del_post('${list.id}')"></em></div>
                            </div>
                        </div>
                    </a>
                </li>
        `;
        return html;
    }

    $(function () {
        // 关注初始化状态
        if (isAttention) {
            var focusObj = $('.icon_focus');
            focusObj.find("i").addClass("rubberBand").css("color","red");
            focusObj.attr("data-status",'1');
            focusObj.find("em").html("取消关注");
        }

        //关注效果
        $(".icon_focus").click(function () {
            var _this = this;
            var _status = $(_this).attr("data-status");//0-》关注  1-》取消关注
            if(_status == 0){
                layer.load(3);
                $.get('<?= Url::to(['post/attention']) ?>', {isAttention: 0, toUserId: id}, function (res) {
                    layer.closeAll();
                   if (res.status === 200) {
                       layer.msg(res.msg, {time: 1500}, function () {
                           $(_this).find("i").addClass("rubberBand").css("color","red");
                           $(_this).attr("data-status",'1');
                           $(_this).find("em").html("取消关注");
                       })
                   } else {
                       layer.msg(res.msg, {time: 1500});
                   }
                }, 'json');
            }else{
                layer.load(3);
                $.get('<?= Url::to(['post/attention']) ?>', {isAttention: 1, toUserId: id}, function (res) {
                    layer.closeAll();
                    if (res.status === 200) {
                        layer.msg(res.msg, {time: 1500}, function () {
                            $(_this).find("i").removeClass("rubberBand").css("color","");
                            $(_this).attr("data-status",'0');
                            $(_this).find("em").html("关注");
                        })
                    } else {
                        layer.msg(res.msg, {time: 1500});
                    }
                }, 'json');
            }
        });
    });

    function del_post(id) {
        layer.confirm('确定要删除吗? 删除帖子，会同时删除与此贴有关的所有评论和回复等信息！', {icon: 0}, function () {
            // layer.load(3);
            $.post('<?= Url::to(['post/del-post'])?>', {id: id}, function (res) {
                console.log(res);
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {time: 1500}, function () {
                        location.reload()
                    })
                } else {
                    layer.msg(res.msg)
                }
             }, 'json')
        })
    }
</script>

