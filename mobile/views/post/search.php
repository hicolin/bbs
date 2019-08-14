<?php
use yii\helpers\Url;
?>
<?= $this->render('@app/views/layouts/header') ?>

<style>
    .person_foot{display: none;}
    .searchResults li{padding: .3rem 0;}
</style>

<!--主体部分 start-->
<div class="searchMain">
 <div class="searchTop">
     <input type="text" name="keyword" placeholder="帖子标题"/>
     <input type="button" name="search" value="搜索"/>
 </div>

    <!--搜索有结果时 start-->
    <div class="searchResultBox">
        <ul class="searchResults" id="data-list">
        </ul>
    </div>

    <!--搜索有结果时 end  -->

    <!--没有数据时 start-->
    <div class="nodataBox">
        <img src="<?= Url::base()?>/mobile/web/images/img_noData.png">
    </div>
    <!--没有数据时 end  -->
</div>
<!--主体部分 end  -->

<script>
    var type = '<?= $type ?>';
    $('input[name="keyword"]').keyup(function () {
        search();
    });
    $('input[name="search"]').click(function () {
        search();
    });
    function search() {
        var keyword = $('input[name="keyword"]').val();
        if (keyword) {
            $.post('<?= Url::to(['post/search'])?>' ,{keyword: keyword, type: type}, function (res) {
                var listObj = $('#data-list');
                listObj.empty();
                if (res.status === 200) {
                    var html = '';
                    var data = res.data;
                    data.forEach(function (list) {
                        html += `
                        <li class="searchResultItem">
                            <a class="posterItem_a" href="${list.url}">${list.title}</a>
                        </li>
                    `;
                    });
                    $('#data-list').append(html);
                    $('.nodataBox').fadeOut();
                } else {
                    $('.nodataBox').fadeIn();
                }
            }, 'json')
        }
    }
</script>

