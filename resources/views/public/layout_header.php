<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>管理系统</title>
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
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                    <a href="index.html" class="site_title"><i class="fa fa-paw"></i> <span>管理系统</span></a>
                </div>

                <div class="clearfix"></div>

                <!-- menu profile quick info -->
                <div class="profile clearfix">
<!--                    <div class="profile_pic">-->
<!--                        <img src="/img/logo.jpg" alt="..." class="img-circle profile_img">-->
<!--                    </div>-->
                    <div class="profile_info">
                        <span>Welcome,</span>
                        <h2><?php $user_info = session("user_info", false);
                            echo $user_info ? $user_info['username'] : '未登录'; ?></h2>
                    </div>
                </div>
                <!-- /menu profile quick info -->

                <br/>
                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <ul class="nav side-menu">
                            <li><a href="/common/index"><i class="fa fa-laptop"></i> 首页 </a></li>
                            <?php if (!empty($user_info) && ($user_info["type"] == 1)): ?>
                            <li><a href="/user/index"><i class="fa fa-users"></i> 用户管理 </a></li>
                            <?php endif;?>
                            <li><a href="/goods/index"><i class="fa fa-pinterest"></i> 商品信息 </a></li>
                            <li><a href="/head/index"><i class="fa fa-desktop"></i> ERP </a></li>
                            <li><a href="/hang/index"><i class="fa fa-leaf"></i> 小红帽 </a></li>
                            <li><a href="/wei/index"><i class="fa fa-fire"></i> 微店 </a></li>
<!--                            <li><a href="/tuan/index"><i class="fa fa-eye"></i> 团长 </a></li>-->
                            <li><a href="/tuanKun/index"><i class="fa fa-random"></i> 团长线下 </a></li>
                            <li><a href="/tuanMao/index"><i class="fa fa-comment"></i> 团长快团 </a></li>
                        </ul>
                    </div>
                </div>

                <!-- /sidebar menu -->

                <!-- /menu footer buttons -->
                <!-- /menu footer buttons -->
            </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
            <div class="nav_menu">
                <nav>
                    <div class="nav toggle">
                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                    </div>

                    <ul class="nav navbar-nav navbar-right">
                        <li class="">
                            <a href="/login/logout"><i class="fa fa-sign-out pull-right"></i>退出登录</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- /top navigation -->
        <!-- page content -->

        <!-- /page content -->

        <!-- footer content -->
