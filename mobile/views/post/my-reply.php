<?php
use yii\helpers\Url;
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

<div class="myReplyMain mescroll" id="mescroll">
    <ul id="data-list">
    </ul>
</div>

<script>
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
        var transferData = {request_time: request_time, pull_type: 'down'};
        $.post('<?= Url::to(['post/my-reply']) ?>', transferData, function (res) {
            if (res.status === 200) {
                var data = res.data;
                if (data.data.length != 0) {
                    var html = '';
                    data.data.forEach(function (list) {
                        html = jointHtml(html, list);
                    });
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
        var transferData = {pull_type: 'up', num: page.num, size: page.size};
        $.post('<?= Url::to(['post/my-reply'])?>', transferData, function (res) {
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
            <li class="myReplyItem">
                <a href="${list.url}">
                    <div class="myReplyItemLeft">
                        <img src="${list.avatar}">
                    </div>
                    <div class="myReplyItemRight">
                        <h1>${list.member.nickname} ${list.content} <i>“${list.post.title}”</i></h1>
                        <p>${list.create_time}</p>
                    </div>
                </a>
            </li>
        `;
        return html;
    }
</script>

