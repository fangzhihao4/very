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
                            <li><a href="/" target="_blank"><i class="fa fa-laptop"></i> 首页</a>
                            </li>
                            <?php if( session("user_info")['menu_list'] ):?>
                                <?php $appKey = env('OPEN_KEY');$userId = session('user_info')['user']['user_id'];?>
                                <?php foreach( session("user_info")['menu_list'] as $k=>$v ):?>
                                    <?php if( $v['sub'] ):?>
                                        <li><a><i class="fa <?php echo $v['icon'];?>"></i> <?php echo $v['name'];?> <span class="fa fa-chevron-down"></span></a >
                                            <ul class="nav child_menu">
                                                <?php foreach( $v['sub'] as $key=>$val ):?>
                                                    <?php
                                                    if (strpos($val['link'],'http') !== false || strpos($val['link'],'https') !== false) {
                                                        $link = $val['link'] . "?app_key={$appKey}&uid={$userId}";
                                                    } else {
                                                        $link = $val['link'][0]=='/'?$val['link']:'/'.$val['link'];
                                                    }
                                                    ?>
                                                    <li><a href="<?php echo $link?>"><?php echo $val['name'];?></a ></li>
                                                <?php endforeach;?>
                                            </ul>
                                        </li>
                                    <?php else:?>
                                        <li><a href="<?php echo $v['link'];?>"><i class="fa <?php echo $v['icon'];?>"></i> <?php echo $v['name'];?> </a ></li>
                                    <?php endif;?>
                                <?php endforeach;?>
                            <?php endif;?>
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
