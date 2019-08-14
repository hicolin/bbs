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

<div class="myFocusMain mescroll" id="mescroll">
  <ul class="myFOcusLists" id="data-list">
        <!--关注列表-->
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
        $.post('<?= Url::to(['post/my-focus']) ?>', transferData, function (res) {
            if (res.status === 200) {
                var data = res.data;
                if (data.attention.length != 0) {
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
        var transferData = {pull_type: 'up', num: page.num, size: page.size};
        $.post('<?= Url::to(['post/my-focus'])?>', transferData, function (res) {
            if (res.status === 200) {
                var data = res.data;
                var curPageData = data.attention;
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
        html += `<li class="myFocusItem" id="li-${list.user.id}" onclick="location.href = '${list.url}'">
          <div class="myFocusItemLeft">
              <div>
                  <img src="${list.avatar}">
              </div>
              <h1>${list.user.nickname}</h1>
          </div>
          <div class="myFocusItemRight" data-status="1" onclick="cancelFocus(${list.user.id})">取消关注</div>
      </li>`;
        return html;
    }

    // 取消关注
    function cancelFocus(toUserId) {
        layer.load(3);
        $.post('<?= Url::to(['post/cancel-focus'])?>', {toUserId: toUserId}, function (res) {
            layer.closeAll();
            if (res.status === 200) {
                layer.msg(res.msg, {time: 1500}, function () {
                    $('#li-' + toUserId).remove();
                })
            } else {
                layer.msg(res.msg, {time: 1500})
            }
        }, 'json');
        event.stopPropagation();
    }

</script>


