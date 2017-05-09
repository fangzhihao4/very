<?php echo view('public/layout_header');?>
    <div class="right_col" role="main">
        <div class="container body">
            <div class="main_container">
                <!-- page content -->
                <div class="col-md-12">
                    <div class="col-middle" style="margin-top:10%;">
                        <div class="text-center">
                            <h1 class="error-number"><i class="fa fa-check-circle-o" style="color:#26B99A;"></i></h1>
                            <h2><?php echo $message;?></h2>
                            <p>
                                <span class="jump_second">3</span>秒后返回，或点击<a href="<?php echo $url;?>">立即跳转</a>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- /page content -->
            </div>
        </div>
    </div>

    <script>
        function countDown(s){
            $(".jump_second").html(s);
            if( s == 0 ){
                // 跳转
                location.href = '<?php echo $url;?>';
                s = 3;
            }else{
                s--;
                timmer = setTimeout(function(){
                    countDown(s);
                }, 1000)
            }
        }

        $(function(){
            countDown(3)
        });
    </script>

<?php echo view('public/layout_footer');?>