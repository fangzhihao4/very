<?php echo view('public/layout_header'); ?>
<div class="right_col" role="main" style="min-height: 1162px;">
    <div class="">

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2><?php  echo  !empty($data) && !empty($data->id) ? '修改用户': '新增用户'; ?></h2>
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

                                <!-- 账号 -->
                                <div class="item form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">账号 <span
                                                class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text"  required="required" class="form-control theme_name"
                                               data-parsley-trigger="keyup" data-parsley-minlength="2"
                                               data-parsley-maxlength="20"
                                               data-parsley-minlength-message="账号名称最长为10个中文/20个英文"
                                               data-parsley-validation-threshold="5" name="username"
                                               placeholder="账号 账号名称最长为10个中文/20个英文"
                                               value="<?php echo !empty($data) && !empty($data->username) ? $data->username: "" ?>">
                                    </div>
                                </div>

                                <!-- 密码 -->
                                <div class="item form-group">
                                    <label for="card_title_remark" class="col-sm-3  form-group control-label">密码
                                        </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" required="required" name="password"
                                               class="form-control title" placeholder="密码 密码最长为20个字符"
                                               value="<?php echo !empty($data) && !empty($data->password) ? $data->password: "" ?>">
                                    </div>
                                </div>



                            <!-- 状态 -->
                            <label for="card_title_remark" class="col-sm-3  form-group control-label">状态 *</label>
                            <div class="col-sm-9  form-group control-label" style="text-align:left">
                                <p>
                                    使用:
                                    <input type="radio" class="flat" name="status" id="genderM" value="1" checked
                                           required/>
                                    停用:
                                    <input type="radio" class="flat" name="status" id="genderF"
                                           value="2" <?php echo !empty($data) && ($data->status == 2) ? 'checked' : ''; ?>/>
                                </p>
                            </div>

                    </div>
                    <label for="card_title_remark" class="col-sm-3 control-label"> </label>
                    <div class="col-sm-6 save" style="margin-top: 5px">
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
            var url = '/user/buttonDetail';
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
                        window.location.href = '/user/index';
                    });
                }
            },
        });
    }

</script>
