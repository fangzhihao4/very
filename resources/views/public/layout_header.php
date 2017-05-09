<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>后台系统</title>
    <!-- Bootstrap -->
    <link href="/css/bootstrap.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/css/font-awesome.css" rel="stylesheet">
    <link href="/css/green.css" rel="stylesheet">
    <link href="/css/dataTables.bootstrap.css" rel="stylesheet">

    <link href="/css/dropzone.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="/css/daterangepicker.css" rel="stylesheet">
    <link href="/css/custom.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="/js/jquery.js"></script>
    <!-- Bootstrap -->
    <script src="/js/bootstrap.js"></script>
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                    <a href="index.html" class="site_title"><i class="fa fa-paw"></i> <span>系统后台</span></a>
                </div>

                <div class="clearfix"></div>

                <!-- menu profile quick info -->
                <div class="profile clearfix">
                    <div class="profile_pic">
                        <img src="/img/logo.jpg" alt="..." class="img-circle profile_img">
                    </div>
                    <div class="profile_info">
                        <span>Welcome,</span>
                        <h2>User</h2>
                    </div>
                </div>
                <!-- /menu profile quick info -->

                <br />
                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <ul class="nav side-menu">
                            <li><a href="javascript:void(0)"><i class="fa fa-laptop"></i> 首页 </a></li>
                            <li><a><i class="fa fa-desktop"></i> 品牌 <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="/shop/brand/index" relation="<?php echo url('shop/brand/edit');?>">品牌列表</a></li>
                                    <li><a href="/shop/brand/add">新增品牌</a></li>
                                </ul>
                            </li>

                            <li><a><i class="fa fa-user"></i> 用户 <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="/user/user/index" relation="<?php echo url('user/user/edit');?>,<?php echo url('user/auth/relation');?>">用户列表</a></li>
                                    <li><a href="/user/user/add">添加用户</a></li>
                                    <li><a href="/user/role/index" relation="<?php echo url('user/roleAuth/relation');?>,<?php echo url('user/role/edit');?>">角色列表</a></li>
<!--                                    <li><a href="/user/role/add">添加角色</a></li>-->
                                    <li><a href="/user/auth/index" relation="<?php echo url('user/auth/editauth');?>">用户权限</a></li>
                                    <li><a href="/user/auth/addauth">新增权限</a></li>
                                </ul>
                            </li>

                            <li><a><i class="fa fa-bar-chart-o"></i> 门店 <span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="/shop/store/index" relation="<?php echo url('shop/store/edit');?>">门店列表</a></li>
                                    <li><a href="/shop/store/add">新增门店</a></li>
                                </ul>
                            </li>

                            <li><a href="/shop/store/storeItem"><i class="fa fa-clone"></i> 对应项 </a></li>
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
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <img src="/img/logo.jpg" alt="">User
                                <span class=" fa fa-angle-down"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                <li><a href="javascript:;"> Profile</a></li>
                                <li>
                                    <a href="javascript:;">
                                        <span class="badge bg-red pull-right">50%</span>
                                        <span>Settings</span>
                                    </a>
                                </li>
                                <li><a href="javascript:;">Help</a></li>
                                <li>
                                    <a href="#"><i class="fa fa-sign-out pull-right"></i> Log Out</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- /top navigation -->
        <!-- page content -->

        <!-- /page content -->

        <!-- footer content -->
