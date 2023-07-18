<?php echo view('public/layout_header'); ?>

<meta name="csrf-token" content="<?php echo csrf_token(); ?>">
<div class="right_col" role="main" role="main" style="min-height: 1162px;">
    <div class="x_panel">
        <div class="x_title">
            <h2>商品管理</h2>
            <div style=" float: right">
                <button type="button" onclick="javascript:location.href='/goods/download'"
                        class="btn btn-info"><i class="fa fa-file-excel-o"></i> 导出商品信息
                </button>
            </div>
            <div style=" float: right">
                <form action="<?php echo url('/goods/index'); ?>" class="form-inline" method="get">
                    <ul class="nav nav-pills">
                        <li>
                        <li>
                            <input type="file"  onchange="batchUploadStoreInfo(this,'/goods/batchUploadStoreInfo')" style="position: absolute;opacity: 0;cursor: pointer;width: 100px;height: 35px;">
                            <button type="button" class="btn btn-danger"><i class="fa fa-upload"></i>导入商品信息</button>
                        </li>
                        </li>
                    </ul>
                </form>
            </div>

            <div class="">
                <a href="/goods/detail">
                    <input type="button" style="float: right" class="col-sm-1 btn btn-success" value="新增商品">
                </a>
            </div>

            <div class="clearfix"></div>
        </div>
        <br>
        <div class="x_content">

            <div class="table-responsive">
                <div class="row">
                    <form action="<?php echo url('/goods/index');?>" class="form-inline form-horizontal" method="get" >
                        <?php echo csrf_field();?>
                        <div class="form-group">

                            <label>&nbsp;&nbsp;&nbsp;&nbsp;店铺选择:&nbsp;</label>
                            <select name="store_type" style="width: 300px;height: 34px;">
                                <option value="" <?php if(!isset($_GET['store_type']) || empty($_GET["store_type"]) ){echo ' selected';}?> >全部</option>
                                <option value="1" <?php if(isset($_GET['store_type']) && $_GET['store_type'] == 1 ){echo ' selected';}?> >小红帽</option>
                                <option value="2" <?php if(isset($_GET['store_type']) && ($_GET['store_type'] == 2)){echo ' selected';}?> >微店</option>
                                <option value="3" <?php if(isset($_GET['store_type']) && ($_GET['store_type'] == 3)){echo ' selected';}?> >团长</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success" style="margin-bottom:0px;margin-right:0px;" ><i class="fa fa-search"></i> 搜索</button>

                        <a onclick="delGoodsAll(this);" style="float: right;margin-right: 15px" href="javascript:;" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>删除全部商品 </a>
                    </form>
                </div>

                <br>
                <table id="datatable-checkbox" class="table table-striped table-bordered bulk_action">
                    <thead>
                    <th>*</th>
                    <th>商品名称</th>
                    <th>商品编码</th>
                    <th>店铺</th>
                    <th>商品价格</th>
                    <th>更新时间</th>
                    <th>操作</th>
                    </thead>

                    <tbody>
                    <?php foreach ($list as $k => $v) { ?>
                        <tr>
                            <td><?php echo !empty($v->id) ? $v->id : ''; ?></td>
                            <td><?php echo !empty($v->goods_name) ? $v->goods_name : ''; ?></td>
                            <td><?php echo !empty($v->goods_sku) ? $v->goods_sku : ''; ?></td>
                            <td><?php echo (!empty($v->type) && !empty($type) && !empty($type[$v->type])) ? $type[$v->type] : '其他'; ?></td>
                            <td><?php echo !empty($v->price) ? $v->price : ''; ?></td>
                            <td><?php echo !empty($v->update_time) ? $v->update_time : ''; ?></td>
                            <td><a class="btn btn-info btn-sm"
                                   href="/goods/detail?id=<?php echo $v->id; ?>">修改</a>
                                <a onclick="delGoods(this);" href="javascript:;" data-sku="<?php echo $v->goods_sku;?>"  data-id="<?php echo $v->id;?>" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>删除</a>
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
            alertPrompt(res.msg, 6, '/goods/index');
        } else {
            alertPrompt(res.msg, 5, '/goods/index');
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


    function delGoods(obj){
        var sku =$(obj).data('sku');
        var msg = "确认删除商品编码  " + sku  + " 吗";
        layer.confirm(msg,{
            title:'提示',
            btn:['确定','取消'],
        },function(){
            var url = '/goods/del';
            var id =$(obj).data('id');
            var data = {
                'id':id,
            };
            sendAjax(url,data);
        });
    }

    function delGoodsAll(obj){
        var msg = "是否确认删除所有商品数据?";
        layer.confirm(msg,{
            title:'提示',
            btn:['确定','取消'],
        },function(){
            var url = '/goods/delAll';
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
                        window.location.href = '/goods/index';
                    });
                }
            }
        });
    }
</script>
