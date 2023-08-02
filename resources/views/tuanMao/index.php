<?php echo view('public/layout_header'); ?>

<meta name="csrf-token" content="<?php echo csrf_token(); ?>">
<div class="right_col" role="main">
    <div class="x_panel">
        <div class="x_title">
            <h2>团长快团</h2>
            <div class="clearfix"></div>
        </div>

        <div>
            <form action="<?php echo url('/tuanMao/index'); ?>" class="form-inline" method="get">
                <ul class="nav nav-pills">
                    <li>
                        <input type="file" onchange="batchUploadStoreInfo(this,'/tuanMao/batchUploadStoreInfo')"
                               style="position: absolute;opacity: 0;cursor: pointer;width: 100px;height: 35px;">
                        <button type="button" class="btn btn-danger"><i class="fa fa-upload"></i>批量导入</button>
                    </li>
                </ul>
            </form>
        </div>
        <br>
        <div class="x_content">
            <div class="table-responsive">
                <table id="datatable-checkbox" class="table table-striped table-bordered bulk_action">
                    <thead>
                    <th>*</th>
                    <th>文件名称</th>
                    <th>状态</th>
                    <th>上传时间</th>
                    <th>更新时间</th>
                    <th>操作</th>
                    </thead>

                    <tbody>
                    <?php foreach ($list as $k => $v) { ?>
                        <tr>
                            <td><?php echo !empty($v->id) ? $v->id : ''; ?></td>
                            <td><?php echo !empty($v->name) ? $v->name : ''; ?>
                            <td><?php echo !empty($v->status) ? (!empty($status[$v->status]) ? $status[$v->status] : $v->status) : ''; ?></td>
                            <td><?php echo !empty($v->create_time) ? $v->create_time : ''; ?></td>
                            <td><?php echo !empty($v->update_time) ? $v->update_time : ''; ?></td>
                            <td>
                                <button type="button" onclick="javascript:location.href='/tuanMao/download?upload_id=' + <?php echo !empty($v->id) ? $v->id : ''; ?>"
                                        class="btn btn-info"><i class="fa fa-file-excel-o"></i> 导出excel
                                </button>
                                <a onclick="delUpload(this);" href="javascript:;" data-id="<?php echo $v->id;?>" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>删除</a>

                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php echo view('public/paginator_size', ['data' => $list]); ?>
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
            alertPrompt(res.msg, 6, '/tuanMao/index');
        } else {
            alertPrompt(res.msg, 5, '/tuanMao/index');
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

    function delUpload(obj){
        var msg = "是否确认删除这次上传的商品数据?";
        layer.confirm(msg,{
            title:'提示',
            btn:['确定','取消'],
        },function(){
            var url = '/head/delUpload';
            var id =$(obj).data('id');
            var data = {
                'id':id,
            };
            sendAjax(url,data);
        });
    }

    function sendAjax(url,data){
        var load = layer.load(1, {
            shade: [0.5,'#fff'] //0.1透明度的白色背景
        });
        $.ajax({
            url:url,
            method:'post',
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:data,
            success:function(res){
                layer.close(load);
                if(res.retcode!=200){
                    layer.alert(res.msg, {title:'提示',icon: 5});
                    return false;
                }else{
                    layer.alert(res.msg, {
                        title: '提交成功',
                        icon: 1
                    }, function(){
                        layer.msg('加载中', {
                            icon: 16,
                            shade: [0.5,'#000000']
                        });
                        window.location.href = '/tuanMao/index';
                    });
                }
            }
        });
    }

</script>
