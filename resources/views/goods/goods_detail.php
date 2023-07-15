<?php echo view('public/layout_header'); ?>
<div class="right_col" role="main" style="min-height: 1162px;">
    <div class="">

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2><?php  echo  !empty($data) && !empty($data->id) ? '修改商品': '新增商品'; ?></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <form id="demo-form" data-parsley-validate="" class="form-horizontal form-label-left">
                            <input type="hidden" name="id" class="form-control title"
                                   value="<?php if (!empty($_GET['id'])) {
                                       echo $_GET['id'];
                                   } ?>">
                            <?php echo csrf_field();?>
                            <div class="campaigns">


                                <!-- 商品名称 -->
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">商品名称(不超过200字符)<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" name="goods_name" class="form-control col-md-3 col-xs-12" value="<?php echo !empty($data) && !empty($data->goods_name) ? $data->goods_name: "" ?>" placeholder="商品名称" required="required">
                                    </div>
                                </div>

                                <!-- 商品sku -->
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">商品编码(不超过45字符)<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" name="goods_sku" class="form-control col-md-3 col-xs-12" value="<?php echo !empty($data) && !empty($data->goods_sku) ? $data->goods_sku: "" ?>" placeholder="商品编码" required="required">
                                    </div>
                                </div>

                                <!-- 商品价格 -->
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">商品价格<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" name="price" class="form-control col-md-3 col-xs-12" value="<?php echo !empty($data) && !empty($data->price) ? $data->price: "" ?>" placeholder="商品名称" required="required">
                                    </div>
                                </div>

                                <!-- 产品类型 -->
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">门店选择<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select class="form-control" name="type" id="product_type" required>
                                            <option value="">请选择</option>
                                            <option value="1" <?php echo isset($data->type) ? (($data->type == 1) ? 'selected' : '') : '' ?>>小红帽</option>
                                            <option value="2" <?php echo isset($data->type) ? (($data->type == 2) ? 'selected' : '') : '' ?>>微店</option>
                                            <option value="3" <?php echo isset($data->type) ? (($data->type == 3) ? 'selected' : '') : '' ?>>团长</option>
                                        </select>
                                    </div>
                                </div>



                            </div>
                    <label for="card_title_remark" class="col-sm-3 control-label"> </label>
                    <div class="col-sm-6 save" style="margin-top: 5px">
                        <a  href="/goods/index" type="button" class="col-sm-3 btn btn-primary" >返回</a>
                        <button type="button" class="col-sm-3 btn btn-success" onclick="buttonFrom()">保存</button>
                    </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php echo view('public/layout_footer'); ?>
<script>


    function buttonFrom() {
        if (!validator.checkAll($('#demo-form'))) {
            return false;
        }
        layer.confirm('您确定要提交吗？', {
            title: '提示',
            btn: ['确定', '取消'],
        }, function () {
            var url = '/goods/buttonDetail';
            sendAjax(url, $("#demo-form").serialize());
        });
    }

    function sendAjax(url, data) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            method: 'POST',
            dataType: 'json',
            data: data,
            success: function (res) {
                if (res.retcode != 200) {
                    layer.alert(res.msg, {title: '提示', icon: 5});
                    return false;
                } else {
                    layer.alert(res.msg, {
                        title: '提交成功',
                        icon: 1
                    }, function () {
                        layer.msg('加载中', {
                            icon: 16,
                            shade: [0.5, '#000000']
                        });
                        window.location.href = '/goods/index';
                    });
                }
            },
        });
    }

</script>
