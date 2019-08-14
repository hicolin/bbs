<?php
use yii\helpers\Url;
use mobile\controllers\Service;
?>

<div class="m-header">
    <a href="javascript:history.back()" class="back">
        <i class="fa fa-angle-left" style="font-size: 30px;line-height: 37px;color: #333;"></i>
    </a>
    <span class="title"><?= $this->title ?></span>
    <a class="iconShare" style="visibility: hidden"><i class="iconfont icon-fenxiang1"> </i></a>
</div>

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

<div class="mescroll" id="mescroll" style="max-width: 600px">
    <div class="postDetailsMain">
        <h1 class="posterTitle"><?= $model['title'] ?></h1>
        <div class="posterInfo">
            <p>评论<i class="discuss_num"><?= $model['discuss_num'] ?></i></p>
            <p>阅读<i><?= $model['pageview'] ?></i></p>
        </div>

        <div class="posterAuthor">
            <a class="posterAuthorLeft" href="<?=Url::toRoute(['post/poster-man', 'id' => $model['user_id']])?>">
                <div class="posterAuthorTx">
                    <img src="<?= Service::getUserAvatar($model['member']['pic']) ?>">
                </div>

                <div class="posterAuthorInfo">
                    <div>
                        <h1><?= $model['member']['nickname'] ?></h1>
                            <i class="vip0<?= $model['member']['grade']?>">
                                <?= Service::getMemberType($model['member']['grade'], $model['member']['end_time'], $model['member']['id']) ?>
                            </i>
                    </div>

                    <p><?= $model['create_time'] ?></p>
                </div>
            </a>

            <div class="posterAuthorRight" onclick="attention('<?= $isAttention ?>', '<?= $model['user_id'] ?>')">
                <?php if ($isAttention == 0): ?>
                +<i>关注</i>
                <?php else: ?>
                <i>取消关注</i>
                <?php endif; ?>
            </div>
        </div>

        <div class="posterCons">
            <div><?= $model['content'] ?></div>
        </div>
    </div>

    <div class="posterSays">
        <div class="posterSaysTop">
            <h1>全部评论</h1>
            <p>(<span class="discuss_num"><?= $model['discuss_num'] ?></span>)</p>
        </div>

        <ul class="posterSaysList" id="data-list">
            <!--评论-->
        </ul>
    </div>
</div>

<div class="fixedSaysBox" style="max-width: 600px;">
    <div class="fixedSaysLeft" onclick="openSaysFun('<?= $model['id']?>', 0, 0, '#data-list', 0, '')">
        <i class="iconfont icon-bianji"></i>
        写评论
    </div>

    <div class="fixedSaysRight">
        <div>
            <i class="iconfont icon-pinglun3"></i>
            <span class="discuss_num"><?= $model['discuss_num'] ?></span>
        </div>

        <div>
            <i class="iconfont icon-dianzan"></i>
            <span id="total-thumb"><?= $model['thumb_up'] ?></span>
        </div>
    </div>
</div>
<!--固定底部的评论按钮 end  -->

<!--评论的富文本编辑器部分 start-->

<!--<div class="editBox">-->
<!--    <div class="closeArea"></div>-->
<!--    <div class="editBoxInner editBoxInnerPl">-->
<!--        <textarea class="replyTextArea" style="" placeholder="文明上网 理性发言"></textarea>-->
<!--        <div class="editBtn">评论</div>-->
<!--    </div>-->
<!--</div>-->


<div class="privateEditBox" style="display: none;">
    <div class="privateEditCons" id="privateEditCons">

        <div class="btnsBox">
            <span onclick="closeEditBox()">取消</span>
            <div class="replyBtn03" id="privateEditBtn">评论</div>
        </div>
        <textarea placeholder="文明上网 理性发言" id="textarea"></textarea>

    </div>
</div>

<!--评论的富文本编辑器部分 end  -->

<script>
    var type= '<?= Yii::$app->request->get('type') ?>';
    !type && type == 1;
    var user_id = '<?= Yii::$app->session->get('user_id') ?>';
    var id = '<?= $model['id'] ?>';
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
        var transferData = {id: id, request_time: request_time,pull_type: 'down'};
        $.post('<?= Url::to(['post/details']) ?>', transferData, function (res) {
            if (res.status === 200) {
                var data = res.data;
                if (data.oneLevelDiscuss.length != 0) {
                    var html = '';
                    data.oneLevelDiscuss.forEach(function (list) {
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
        $.post('<?= Url::to(['post/details'])?>', transferData, function (res) {
             if (res.status === 200) {
                 var data = res.data;
                 var curPageData = data.oneLevelDiscuss;
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
                 mescroll.endBySize(curPageData.length, totalSize)
             } else {
                 mescroll.endErr();
             }
        }, 'json');
    }

    function jointHtml(html, list) {
        var vip = 'vip0' + list.member.grade;
        html += `<li class="posterSaysItem" id="discuss_${list.id}">
                <div class="saysMansTx">
                    <img src="${list.avatar}">
                </div>
                <div class="saysCons">
                    <div class="saysAuthorBox">
                        <h1 class="saysAuthor">${list.member.nickname}</h1>
                        <i class="${vip}">${list.grade_name}</i>
                    </div>

                    <p class="saysTxt">${list.content}</p>`;
        if (list.secondLevelDiscuss.length > 0) {
            html += `<div class="saysReplyBox">
                        <ul class="saysReplyList">`;
            list.secondLevelDiscuss.forEach(function (value) {
                html += `<li class="saysReplyItem">
                                <i>${value.member.nickname}：</i>
                                <em>${value.content}</em>
                            </li>`;
            });
            html += `</ul>
                        <a class="lookMoreSays" href="${list.url}">查看全部评论</a>
                    </div>`;
        }
            html += `<div class="saysBottom">
                            <p class="saysTime">${list.create_time}</p>
                            <div class="saysGn">
                                <i class="iconfont icon-pinglun3"
                                onclick="openSaysFun(${id}, ${list.id}, ${list.member.id}, '#discuss_${list.id}', ${list.secondLevelDiscuss.length}, '${list.url}')"></i>
                                <i class="iconfont icon-dianzan animated icon_zan" data-status="${list.is_thumbed}" onclick="thumbUp(this, ${id}, ${list.id}, ${list.member.id})"></i>
                                <span>${list.thumb_up}</span>
                            </div>
                        </div>
                    </div>
                </li>`;
        return html;
    }

    function jointSecondLevelDiscuss(secondDataLength, html, data, oneDataUrl) {
        if (secondDataLength == 0) {
            html = `<div class="saysReplyBox">
                        <ul class="saysReplyList">
                            <li class="saysReplyItem">
                                <i>${data.member.nickname}：</i>
                                <em>${data.content}</em>
                            </li>
                        </ul>
                        <a class="lookMoreSays" href="${oneDataUrl}">查看全部评论</a>
                    </div>`
        } else {
            html = `<li class="saysReplyItem">
                                <i>${data.member.nickname}：</i>
                                <em>${data.content}</em>
                    </li>`;
        }
        return html;
    }

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
                var totalThumbObj = $('#total-thumb');
                var thumbNum,totalThumb;
                if (status == 0) { // 点赞
                    thumbNum = parseInt($(_this).next('span').text());
                    $(_this).next('span').text(thumbNum + 1);
                    totalThumb = parseInt(totalThumbObj.text());
                    totalThumbObj.text(totalThumb + 1);
                } else { // 取消点赞
                    thumbNum = parseInt($(_this).next('span').text());
                    $(_this).next('span').text(thumbNum - 1);
                    totalThumb = parseInt(totalThumbObj.text());
                    totalThumbObj.text(totalThumb - 1);
                }
            } else {
                layer.msg(res.msg)
            }
        }, 'json')
    }
</script>

<script>
    //点击评论框以外部分关闭评论区域
    function closeEditBox() {
        $(".editBox").hide();
    }


    var postId = 0;
    var discussId = 0;
    var toUserId = 0;
    var selectorId = 0;
    var secondDataLength = 0;
    var oneDataUrl = '';
    //弹出回复框的方法
    function openSaysFun(_postId, _discussId, _toUserId, _selectorId, _secondDataLength, _oneDataUrl) {
        if (!user_id) {
            location.href = '<?= Url::to(['index/login']) ?>';
            return;
        }
        // 清空富文本编辑前内容
        $('textarea').val('');

        $(".privateEditBox").show();

        postId = _postId;
        discussId = _discussId;
        toUserId = _toUserId;
        selectorId = _selectorId;
        secondDataLength = _secondDataLength;
        oneDataUrl = _oneDataUrl;

    }

    //点击评论按钮
    $(".replyBtn03").click(function () {
        var content = $('textarea').val();
        if (!$.trim(content)) {
            layer.msg('评论内容不能为空', {time: 1500});
            return;
        }
        layer.load(3);
        var transferData = {postId: postId, discussId: discussId, toUserId: toUserId, content: content};
        $.post('<?= Url::to(['post/add-discuss'])?>', transferData, function (res) {
            layer.closeAll();
            $(".privateEditBox").hide();
            var data = res.data;
            if (res.status === 200) {
                layer.msg(res.msg, {time: 1500}, function () {
                    var html = '';
                    if (discussId == 0) {
                        html = jointHtml(html, data.model);
                        $(selectorId).prepend(html);
                        // 去除暂无数据占位
                        if ($('.mescroll-empty').html != '') {
                            $('.mescroll-empty').remove();
                        }
                        // location.href = selectorId;
                    } else {
                        html = jointSecondLevelDiscuss(secondDataLength, html, data.model, oneDataUrl);
                        if (secondDataLength == 0) {
                            $(selectorId + ' .saysCons .saysBottom').before(html);
                        } else {
                            $(selectorId + ' ul').prepend(html);
                        }
                    }
                    // 评论数 + 1
                    $('.discuss_num').each(function () {
                        var num = parseInt($(this).text()) + 1;
                        $(this).text(num);
                    })
                });
            } else {
                layer.msg(res.msg, {time: 1500});
            }
        }, 'json');
    })

    // 关注、取消关注
    function attention(isAttention, toUserId) {
        if (!user_id) {
            location.href = '<?= Url::to(['index/login']) ?>';
            return;
        }
        layer.load(3);
        $.get('<?= Url::to(['post/attention']) ?>', {isAttention: isAttention, toUserId: toUserId}, function (res) {
            layer.closeAll();
            if (res.status === 200) {
                layer.msg(res.msg, {time: 1500}, function () {
                    location.reload();
                })
            } else {
                layer.msg(res.msg, {time: 1500})
            }
        }, 'json')
    }
</script>

<script>
    // 微信分享
    var shares = null;
    var sweixin = null;
    var buttons = [
        {title:'我的好友',extra:{scene:'WXSceneSession'}},
        {title:'朋友圈',extra:{scene:'WXSceneTimeline'}},
        {title:'我的收藏',extra:{scene:'WXSceneFavorite'}}
    ];

    // HTML5+
    if (window.plus) {
        $('.iconShare').css({'visibility': 'visible'});
        plusReady();
    } else {
        document.addEventListener('plusready', plusReady, false);
    }
    function plusReady() {
        updateServices();
        $('.iconShare').click(function () {
            shareWeb();
        });
    }

    // 更新分享服务
    function updateServices(){
        plus.share.getServices(function(s){
            shares={};
            for(var i in s){
                var t=s[i];
                shares[t.id]=t;
            }
            sweixin=shares['weixin'];
        }, function(e){
            console.log('获取分享服务列表失败：'+e.message);
        });
    }

    // 分享网页
    function shareWeb() {
        var thumb = '<?= Url::base() . $list['thumbnail'] ?>';
        var msg = {type: 'web', thumbs: [thumb]};
        msg.href = location.href;
        msg.title = '<?= $model['title'] ?>';
        msg.content = '<?= $model['content'] ?>';
        if (sweixin) {
            plus.nativeUI.actionSheet({title: '分享网页到微信', cancel: '取消', buttons: buttons}, function (e) {
                if (e.index > 0) {
                    share(sweixin, msg, buttons[e.index - 1]);
                }
            })
        } else {
            plus.nativeUI.alert('当前环境不支持微信分享操作!');
        }
    }

    // 分享
    function share(srv, msg, button) {
        console.log('分享操作：');
        if (!srv) {
            console.log('无效的分享服务！');
            return;
        }
        button && (msg.extra = button.extra);
        // 发送分享
        if (srv.authenticated) {
            console.log('---已授权---');
            doShare(srv, msg);
        } else {
            console.log('---未授权---');
            srv.authorize(function () {
                doShare(srv, msg);
            }, function (e) {
                console.log('认证授权失败：' + JSON.stringify(e));
            });
        }
    }

    // 发送分享
    function doShare(srv, msg){
        console.log(JSON.stringify(msg));
        srv.send(msg, function(){
            console.log('分享到"'+srv.description+'"成功！');
        }, function(e){
            console.log('分享到"'+srv.description+'"失败: '+JSON.stringify(e));
        });
    }
</script>

