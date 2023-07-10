<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>管理系统</title>

    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">

    <!-- Bootstrap -->
    <link href="/css/bootstrap.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/css/font-awesome.css" rel="stylesheet">
    <link href="/css/green.css" rel="stylesheet">
    <link href="/css/dataTables.bootstrap.css" rel="stylesheet">

    <link href="/css/dropzone.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="/css/daterangepicker.css" rel="stylesheet">
    <link href="/css/switchery.min.css" rel="stylesheet">
    <link href="/css/custom.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="/js/jquery.js"></script>
    <!-- Bootstrap -->
    <script src="/js/bootstrap.js"></script>
    <script src="/js/jquery.form.min.js"></script>

    <script src="/js/jquery.inputmask.bundle.min.js"></script>
    <!-- iCheck -->
    <script src="/js/icheck.js"></script>
    <!-- Custom Theme Scripts -->
    <script src="/js/moment.min.js"></script>
    <script src="/js/daterangepicker.js"></script>
    <script src="/js/validator.js"></script>
    <!-- layer -->
    <script src="/plugin/layer/layer.js"></script>
    <script src="/plugin/laydate/laydate.js"></script>
    <script src="/js/custom.js"></script>
    <!-- select2 -->
    <script src="/js/select2.min.js"></script>

    <script src="/js/public.js?v=1048"></script>
</head>
<body class="login" style="height:auto;">
<div class="login_wrapper">
    <div class="animate form">
        <!--        <h1 style="display:flex;">-->
        <!--               管理平台-->
        <!--        </h1>-->
        <section class="login_content">
            <div class="replace_login">
                <form method="POST" class="login_zm" onsubmit="return submitLogin();" autocomplete="off">
                    <?php echo csrf_field(); ?>
                    <div>
                        <input name="username" type="text" class="form-control" placeholder="username" required=""
                               autocomplete="off">
                    </div>
                    <div>
                        <input name="password" type="password" class="form-control" placeholder="Password" required=""
                               autocomplete="off">
                    </div>
                    <div>
                        <button class="btn btn-default submit form-control" onclick="buttonFrom()">登录</button>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>


        </section>
    </div>
</div>
<div class="clearfix"></div>


<script>

    function submitLogin(){
        var index = layer.load(1, {
            shade: [0.5, '#000000']
        });
        $.ajax({
            url : '/login/buttonLogin',
            data : $(".login_zm").serializeArray(),
            type : 'POST',
            dataType : 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            complete: function(){
                layer.close(index);
            },
            success : function (ret) {
                if(ret.retcode == 200){
                    window.location.href = '/common/index';
                }else{
                    $("#verification_code").val("");
                    layer.msg(ret.msg, {icon:2, time:1500});
                }
            },
            error: function(){
                layer.msg("网络错误,请重试", {icon:2, time:1500});
            }
        });

        return false;
    }




</script>

</body>
</html>