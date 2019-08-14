<?php
use yii\helpers\Url;
?>

<?php $this->beginBlock('header') ?>
<style>
    .mescroll {
        position: fixed;
        top: 0;
        bottom: 2.5rem;
        height: auto; /*如设置bottom:50px,则需height:auto才能生效*/
    }
</style>
<?php $this->endBlock() ?>

<div class="postsMain mescroll" id="mescroll" style="max-width: 600px">
    <!-- 轮播部分 start-->
    <div>
        <div class="swiper-container postSwiper">
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

    <!--搜索部分 start-->
    <div class="searchBox">
        <a href="<?= Url::toRoute(['post/search', 'type' => $type]) ?>">
            <i class="iconfont icon-ss"></i>
            <em>搜索</em>
        </a>
    </div>
    <!--搜索部分 end  -->

    <!--列表部分 start-->
    <div class="postersBox">
        <ul id="data-list">
        <!--帖子列表-->
        </ul>
    </div>

    <!--列表部分 end  -->

    <!-- 悬浮的我要发帖的按钮 start-->
        <a class="goPostBtn" href="<?= Url::toRoute(['post/post', 'type' => Yii::$app->request->get('type')]) ?>">我要发帖</a>
    <!-- 悬浮的我要发帖的按钮 end  -->

</div>

<script>
    var type = '<?= \Yii::$app->request->get('type') ?>';
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
                //列表第一页无任何数据时,显示的空提示布局; 需配置warpId才显示
                warpId:	"mescroll", //父布局的id (1.3.5版本支持传入dom元素)
                icon: "<?= Url::base()?>/mobile/web/plugins/mescroll/img/mescroll-empty.png", //图标,默认null,支持网络图
                tip: "暂无相关数据~" //提示
            },
        }
    });

    // 下拉
    function downCallback() {
        $.get('<?= Url::to(['post/index']) ?>', {type: type, request_time: request_time,pull_type: 'down'}, function (res) {
            if (res.status === 200) {
                var data = res.data;
                if (data.posts.length != 0) {
                    var html = '';
                    data.posts.forEach(function (list) {
                        html = jointHtml(html, list,type);
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
        var transferData = {type: type, num: page.num, size: page.size, pull_type: 'up'};
        $.get('<?= Url::to(['post/index']) ?>', transferData, function (res) {
            if (res.status === 200) {
                var data = res.data;
                var curPageData = data.posts;
                var totalSize = data.total;
                var html = '';
                curPageData.forEach(function (list) {
                    html = jointHtml(html, list,type); // JS变量提升
                });
                $('#data-list').append(html);
                mescroll.endBySize(curPageData.length, totalSize)
            } else {
                mescroll.endErr();
            }
        }, 'json')
    }

    function jointHtml(html, list,type) {
        if (list.thumbnail && type == 2) {
            html += `            <li class="posterItem">
                <a class="posterItem_a" href="${list.url}">
                    <div class="posterItemTop posterItemTopLeft">
                        <div>
                            <img src="${list.thumbnail}">
                        </div>
                        <h1>${list.title}</h1>

                    </div>

                    <div class="posterItemDown">
                        <div>
                            <i>${list.member.nickname}</i>
                            <em>${list.pageview}阅读</em>
                        </div>
                        <p>${list.create_time}</p>
                    </div>
                </a>
            </li>
`;
        }else if(list.thumbnail && type == 1){
            html += `            <li class="posterItem">
                <a class="posterItem_a" href="${list.url}">
                    <div class="posterItemTop">

                        <h1>${list.title}</h1>

                        <div>
                            <img src="${list.thumbnail}">
                        </div>
                    </div>

                    <div class="posterItemDown">
                        <div>
                            <i>${list.member.nickname}</i>
                            <em>${list.pageview}阅读</em>
                        </div>
                        <p>${list.create_time}</p>
                    </div>
                </a>
            </li>
`;
        } else {
            html += `            <li class="posterItem">
                <a class="posterItem_a posterItem_a_noImg" href="${list.url}">
                    <div class="posterItemTop">
                        <h1>${list.title}</h1>
                    </div>

                    <div class="posterItemDown">
                        <div>
                            <i>${list.member.nickname}</i>
                            <em>${list.pageview}阅读</em>
                        </div>
                        <p>${list.create_time}</p>
                    </div>
                </a>
            </li>
`;
        }
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

    //页面滚动一定距离后，搜索部分固定
    $(window).scroll(function () {
        var scrollPos = $(window).scrollTop();

        if (scrollPos > 100) {
            $(".searchBox").addClass("on");
        } else {
            $(".searchBox").removeClass("on");
        }
    });

</script>