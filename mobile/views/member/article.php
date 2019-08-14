<?php
use yii\helpers\Url;
?>

<style>
    .mescroll {
        position: fixed;
        top: 246px;
        bottom: 2.6rem;
        height: auto;
    }
</style>
<!--main-->
<div class="jindu_main">
    <!-- 轮播部分 start-->
    <div>
        <div class="swiper-container postSwiper" style="max-height: 8rem">
            <div class="swiper-wrapper">
                <?php foreach ($banners as $list): ?>
                    <a href="<?= $list['link'] ?>" class="swiper-slide postSwiperItem">
                        <img src="<?= Url::base() . $list['img'] ?>">
                    </a>
                <?php endforeach; ?>
            </div>
            <!-- 如果需要分页器 -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
    <!-- 轮播部分 end -->
</div>

<div class="nkm_search">
    <form method="get" action="">
        <input type="hidden" name="r" value="member/withdraw-money">
        <input type="text" class="search_con fl" value="<?= $keywords ?>" name="keywords" placeholder="请输入关键词查询">
        <input type="hidden" name="cat_ids" value="<?= $cat_id ?>">
        <button type="submit" class="fl"><i class="iconfont icon-search1"></i></button>
        <div class="clear"></div>
    </form>
</div>
<div class="nkm_con" style="height: inherit">
    <div class="nkm_con_head">
        <ul>
            <?php foreach ($category as $key => $value): ?>
                <li class="<?= $cat_ids && ($cat_ids == $value->id) ? 'curr' : '11' ?>">
                    <a href="<?= Url::toRoute(['member/article', 'cat_ids' => $value->id]) ?>"><?= $value->name ?></a>
                </li>
            <?php endforeach ?>
            <div class="clear"></div>
        </ul>
    </div>
    <div class="jindu_main mescroll" id="mescroll">
            <div class="nkm_con_list" style="background-color: #fff">
                <ul id="data-list">
                </ul>
            </div>
    </div>
</div>

<!--main end-->
<script>
    var user_grade = '<?= $user_info['grade'] ?>';
    var cat_ids = '<?= $cat_ids ?>';
    var keywords = '<?= $keywords ?>';
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
        var transferData = {cat_ids: cat_ids, request_time: request_time, pull_type: 'down'};
        $.post('<?= Url::to(['member/article']) ?>', transferData, function (res) {
            if (res.status === 200) {
                var data = res.url;
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
        var transferData = {cat_ids:cat_ids, keywords: keywords, pull_type: 'up', num: page.num, size: page.size};
        $.post('<?= Url::to(['member/article'])?>', transferData, function (res) {
            if (res.status === 200) {
                var data = res.url;
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
            <li>
                <a onclick="see(${list.permission},'${list.url}',${list.grade},${user_grade})">
                    <img src="${list.img}" class="fl"/>
                    <p>${list.title}</p>
                    <span>${list.create_time}</span>
                    <div class="clear"></div>
                </a>
            </li>
        `;
        return html;
    }

    $(function () {
        var mySwiper = new Swiper('.postSwiper', {
            loop: true, // 循环模式选项
            // 如果需要分页器
            pagination: {
                el: '.swiper-pagination',
            },
        });
    });

    // 用户权限
    function see(permission, url, grade, usergrade) {
        var gradeName = '钻石会员';
        if (!usergrade) {
                window.location.href = '<?= Url::to(['index/login']) ?>';
                return;
            }
            if (grade == 2) gradeName = '金牌会员';
            if (grade > usergrade) {
                return layer.alert(gradeName + '专享内容，请升级会员后查看');
            }
            window.location.href = url;
    }
</script>
