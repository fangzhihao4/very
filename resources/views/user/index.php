<?php echo view('public/layout_header'); ?>

<meta name="csrf-token" content="<?php echo csrf_token(); ?>">
<div class="right_col" role="main" role="main" style="min-height: 1162px;">
    <div class="x_panel">
        <div class="x_title">
            <h2>用户管理</h2>

            <div class="">
                <a href="/user/detail">
                    <input type="button" style="float: right" class=" col-sm-1 btn btn-success" value="新增用户">
                </a>
            </div>
            <div class="clearfix"></div>
        </div>
        <br>
        <div class="x_content">
            <div class="table-responsive">
                <table id="datatable-checkbox" class="table table-striped table-bordered bulk_action">
                    <thead>
                    <th>*</th>
                    <th>用户名</th>
                    <th>状态</th>
                    <th>更新时间</th>
                    <th>操作</th>
                    </thead>

                    <tbody>
                    <?php foreach ($list as $k => $v) { ?>
                        <tr>
                            <td><?php echo !empty($v->id) ? $v->id : ''; ?></td>
                            <td><?php echo !empty($v->username) ? $v->username : ''; ?></td>
                            <td><?php echo !empty($v->status) && ($v->status == 1) ? '使用中' : '已停用'; ?></td>
                            <td><?php echo !empty($v->update_time) ? $v->update_time : ''; ?></td>
                            <td><a class="btn btn-xs btn-primary"
                                   href="/user/detail?id=<?php echo $v->id; ?>">修改</a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php echo view('public/paginator', ['data' => $list]); ?>
            </div>
        </div>
    </div>
</div>


<?php echo view('public/layout_footer'); ?>

<script>
    function batchUploadStoreInfo(obj, $url) {
        if (!$url) {
            return false;
        }
        var url = $url;
        var parent = $(obj).parent();
        var upload_file_obj = null;
        upload_file_obj = obj;

        if ($(obj).val() == "") {
            return false;
        }

        var myform = document.createElement("form");
        myform.action = url;
        myform.method = "post";
        myform.enctype = "multipart/form-data";

        //创建表单后一定要加上这句否则得到的form不能上传。document后要加上body,否则火狐下不行。
        document.body.appendChild(myform);
        var form = $(myform);
        var fu = $(obj).clone(true);
        parent.prepend(fu);
        $(obj).appendTo(form);
        form.find('input[type=file]').attr('name', 'storeFile');
        form.ajaxSubmit({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                loading('导入中，请勿刷新页面');
            },
            success: function (res) {
                upload_file_obj = null;
                form.remove();
                handleAjaxResponse(res);
            }
        });
    }

    function handleAjaxResponse(res) {
        if (res.retcode == 200) {
            alertPrompt(res.msg, 6, '/hang/index');
        } else {
            alertPrompt(res.msg, 5, '/hang/index');
        }
    }

    function loading(msg) {
        layer.msg(msg, {
            icon: 16,
            shade: [0.5, '#000000'],
            time: false
        });
    }

    function alertPrompt(msg, level, url = '') {
        layer.alert(msg, {icon: level, title: '提示'}, function (index) {
            if (url) {
                window.location.href = url;
            }
            layer.close(index);
        });
    }

</script>
