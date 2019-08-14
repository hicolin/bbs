<?php
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use yii\helpers\Url;
?>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row">
                            <div class="col-sm-12">
                                <?php ActiveForm::begin(['id' => 'admin-search-form', 'method' => 'get', 'options' => ['class' => 'form-inline'], 'action' => '']); ?>
                                <div class="form-group" style="margin: 5px;">
                                    <input type="text" class="form-control" id="search[nickname]" name="search[nickname]" placeholder="昵称" value="<?= isset($search["nickname"]) ? $search["nickname"] : "" ?>">
                                </div>
                                <div class="form-group" style="margin: 5px;">
                                    <input type="text" class="form-control" id="search[tel]" name="search[tel]" placeholder="手机号" value="<?= isset($search["tel"]) ? $search["tel"] : "" ?>">
                                </div>
                                <div class="form-group" style="margin: 5px;">
                                    <input type="text" class="form-control" id="search[content]" name="search[content]" placeholder="评论" value="<?= isset($search["content"]) ? $search["content"] : "" ?>">
                                </div>
                                <div class="form-group" style="margin: 5px">
                                    <select name="search[status]" id="search[status]" class="form-control">
                                        <option value="">状态</option>
                                        <option value="1" <?= $search['status'] == 1 ? 'selected' : '' ?>>显示</option>
                                        <option value="2" <?= $search['status'] == 2 ? 'selected' : '' ?> >隐藏</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin: 5px;">
                                    <input type="text" class="form-control ECalendar" id="b_time" name="search[b_time]" placeholder="开始时间" value="<?= $search["b_time"] ? date('Y-m-d H:i',$search["b_time"]) : "" ?>"> -
                                    <input type="text" class="form-control ECalendar" id="e_time" name="search[e_time]" placeholder="结束时间" value="<?= $search["e_time"] ? date('Y-m-d H:i',$search["e_time"]) : "" ?>">
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-zoom-in icon-white"></i>搜索</button>
                                    <a class="btn btn-primary btn-sm" href="<?= Url::toRoute([$this->context->id . '/'.Yii::$app->controller->action->id]) ?>"> <i class="glyphicon glyphicon-zoom-in icon-white"></i>清空</a>
                                </div>
                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-sm-12">
                                <button id="delete_btn" type="button" class="btn btn-xs btn-danger">批量删除</button>&nbsp;&nbsp;&nbsp;
                                <table id="data_table" class="table table-bordered table-striped dataTable" role="grid"
                                       aria-describedby="data_table_info">
                                    <thead>
                                    <tr role="row">
                                        <th><input id="data_table_check" type="checkbox"></th>
                                        <th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1"
                                            aria-sort="ascending">ID</th>
                                        <th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1"
                                            aria-sort="ascending">昵称</th>
                                        <th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1"
                                            aria-sort="ascending">手机号</th>
                                        <th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1"
                                            aria-sort="ascending">帖子ID</th>
                                        <th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1"
                                            aria-sort="ascending">评论</th>
                                        <th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1"
                                            aria-sort="ascending">回复用户昵称</th>
                                        <th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1"
                                            aria-sort="ascending">回复用户手机号</th>
                                        <th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1"
                                            aria-sort="ascending">点赞数</th>
                                        <th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1"
                                            aria-sort="ascending">状态</th>
                                        <th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1"
                                            aria-sort="ascending">创建时间</th>
                                        <th tabindex="0" aria-controls="data_table" rowspan="1" colspan="1"
                                            aria-sort="ascending">操作
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($models as $list): ?>
                                        <tr id="rowid_$list->id">
                                            <td><label><input type="checkbox" value="<?= $list['id'] ?>"></label></td>
                                            <td><?= $list['id'] ?></td>
                                            <td><?= $list['member']['nickname'] ?></td>
                                            <td><?= $list['member']['tel'] ?></td>
                                            <td><?= $list['post_id'] ?></td>
                                            <td style="width: 10%; overflow: hidden; white-space: nowrap; text-overflow: ellipsis">
                                                <?= $list['content'] ?>
                                            </td>
                                            <td><?= $list['user']['nickname'] ?></td>
                                            <td><?= $list['user']['tel'] ?></td>
                                            <td><?= $list['thumb_up'] ?></td>
                                            <td>
                                                <span class="label <?= $list['status'] == 1 ? 'bg-green': 'bg-red' ?>"
                                                      onclick="changeStatus('<?= $list['id'] ?>', '<?= $list['status'] ?>')"
                                                style="cursor: pointer">
                                                    <i class="glyphicon glyphicon-edit icon-white"></i>&nbsp;
                                                    <?= $list['status'] == 1 ? '显示' : '隐藏' ?>
                                                </span>
                                            </td>
                                            <td><?= date('Y-m-d H:i:s', $list['create_time']) ?></td>
                                            <td class="center">
                                                <a id="delete_btn" onclick="del('<?= $list['id'] ?>')"
                                                   class="btn btn-danger btn-sm" href="javascript:;">
                                                    <i class="glyphicon glyphicon-trash icon-white"></i>删除</a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- row end -->

                        <!-- row start -->
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="data_table_info" role="status" aria-live="polite">
                                    <div class="infos">
                                        从<?= $pagination->getPage() * $pagination->getPageSize() + 1 ?>
                                        到 <?= ($pageCount = ($pagination->getPage() + 1) * $pagination->getPageSize()) < $pagination->totalCount ? $pageCount : $pagination->totalCount ?>
                                        共 <?= $pagination->totalCount ?> 条记录
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="data_table_paginate"
                                     style="text-align: right;padding-right: 50px;">
                                    <?= LinkPager::widget([
                                        'pagination' => $pagination,
                                        'nextPageLabel' => '下一页',
                                        'prevPageLabel' => '上一页',
                                        'firstPageLabel' => '首页',
                                        'lastPageLabel' => '尾页',
                                    ]); ?>

                                </div>
                            </div>
                        </div>
                        <!-- row end -->
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>

<?php $this->beginBlock('footer'); ?>
<script>
    function del(id) {
        layer.confirm('确定要删除吗？', function () {
            layer.load(3);
            $.post('<?= Url::to([$this->context->id . '/del'])?>', {id: id}, function (res) {
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1, time: 1500}, function () {
                        location.reload()
                    })
                } else {
                    layer.msg(res.msg, {icon: 2, time: 1500});
                }
            }, 'json');
        })
    }

    $('#delete_btn').click(function () {
        var ids = [];
        var checkBoxs = $('tbody input[type="checkbox"]:checked');
        for(i = 0; i < checkBoxs.size(); i++) {
            var val = checkBoxs.eq(i).val();
            ids.push(val);
        }
        if (ids.length == 0) {
            layer.msg('请选择你要批量删除的数据');return;
        }
        layer.confirm('确定要批量删除吗？', function () {
            layer.load(3);
            $.post('<?= Url::to([$this->context->id . '/batch-del'])?>', {ids: ids}, function (res) {
                layer.closeAll();
                if (res.status === 200) {
                    layer.msg(res.msg, {icon: 1, time: 1500}, function () {
                        location.reload();
                    })
                } else {
                    layer.msg(res.msg, {icon: 2, time: 1500})
                }
            }, 'json')
        });
    })

    function changeStatus(id, status) {
        layer.load(3);
        $.post('<?= Url::to([$this->context->id . '/change-status'])?>', {id: id, status: status}, function (res) {
            layer.closeAll();
            if (res.status === 200) {
                layer.msg(res.msg, {icon: 1, time: 1500}, function () {
                    location.reload();
                })
            } else {
                layer.msg(res.msg, {icon: 2, time: 1500});
            }
        }, 'json')
    }

</script>

<script>
    $("#b_time").ECalendar({
        type:"time",   //模式，time: 带时间选择; date: 不带时间选择;
        stamp : true,   //是否转成时间戳，默认true;
        offset:[0,2],   //弹框手动偏移量;
        format:"yyyy-mm-dd",   //格式 默认 yyyy-mm-dd hh:ii;
        skin:2,   //皮肤颜色，默认随机，可选值：0-8,或者直接标注颜色值;
        step:10,   //选择时间分钟的精确度;
        callback:function(v,e){

        } //回调函数
    });

    $("#e_time").ECalendar({
        type:"time",   //模式，time: 带时间选择; date: 不带时间选择;
        stamp : true,   //是否转成时间戳，默认true;
        offset:[0,2],   //弹框手动偏移量;
        format:"yyyy-mm-dd",   //格式 默认 yyyy-mm-dd hh:ii;
        skin:2,   //皮肤颜色，默认随机，可选值：0-8,或者直接标注颜色值;
        step:10,   //选择时间分钟的精确度;
        callback:function(v,e){

        } //回调函数
    });
</script>
<?php $this->endBlock(); ?>
