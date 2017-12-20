
<header class="container">
    <div class="row-fluid">
        <div style="height: 100px; float: left;">
            <div id="logo">
                <h2>
                    <a href="<?php echo site_url(); ?>" style="text-decoration: none;">
                        <img style="width: 251px" src="<?=site_url('source/img/u2.png'); ?>">
                    </a>
                </h2>
            </div>
        </div>
        <div class="input_search">
            <div class="form-search">
                <input type="text" id="key_words" class="input-medium" placeholder="请输入关键字搜索" value="">
                <a class="btn btn-link btn-3" id="search-btn-post" target="_blank">搜索贴吧</a>
            </div>
        </div>
    </div>
</header>

<div id="page-content" class="home-page">
    <div class="container body">
        <div class="header">
            <div class="head_img" style=" display: none">
                <img src="<?=site_url('source/img/05.jpg'); ?>">
            </div>
            <a href="javascript:void(0)" class="post_name"></a>
            <a class="btn btn-link focus" id="focus_post_bar">
                <i class="fa fa-plus" aria-hidden="true" style="margin-right: 10px"></i>关注
            </a>
            <a class="btn btn-link focus" id="cancel_focus_post_bar" style="background-color: #117d94">
                <i class="fa fa-check" aria-hidden="true"></i>
                取消关注
            </a>
            <span class="focus_num">关注：<span>0</span></span>
            <span class="post_num">帖子：<span>0</span></span>
        </div>

        <div class="row-fluid" style="background-color: #fff;">
            <div class="personal_info">
                <div class="personal_box" style="padding: 10px 12px">
                    <span>我在贴吧</span>
                    <div class="head_portrait" style="margin-top: 10px">
                        <?php if (isset($_SESSION['avatar_path']) && isset($_SESSION['nickname'])): ?>
                            <img src="<?=site_url($_SESSION['avatar_path']); ?>">
                            <span style="margin-left: 10px"><?php echo $_SESSION['nickname'];?></span>
                        <?php else: ?>
                            <img src="<?=site_url('source/img/05.jpg'); ?>">
                            <span style="margin-left: 10px">游客</span>
                        <?php endif;?>
                    </div>
                </div>
                <?php if (!isset($_SESSION['avatar_path']) && !isset($_SESSION['username'])): ?>
                    <span class="tourist">游客请先<a class="sign_in" href="<?php echo site_url('index/sign_in'); ?>">登录</a></span>
                <?php endif;?>
                <div class="hot_post" style="padding: 5px 12px">
                    <span style="font-family: '黑体'; font-weight: bold">热门贴吧</span>
                    <ul>
                    </ul>
                    <a class="btn btn-link exchange" ><i class="fa fa-refresh" aria-hidden="true" style="margin-right: 10px"></i>换一批</a>
                </div>
            </div>
            <div class="post_list_box">
                <div class="nav">
                    <ul class="breadcrumb">
                        <li><a href="<?=site_url('my_city/post_bar') ?>">论坛首页</a> <span class="divider">/</span></li>
                        <li><a href="javascript:void(0)" class="post_bar_name"></a><span class="divider">/</span></li>
                        <li class="active">发表帖子</li>
                    </ul>
                </div>
                <!--<ul class="nav nav-tabs">
                    <li class="active">
                        <a href="javascript:void(0)">发表帖子</a>
                    </li>
                </ul>-->
                <div class="post_content">
                    <div class="head" style="    margin: 20px 0 10px;">
                        <span>标题</span>
                        <input class="post_title" style="width: 835px; margin-left: 15px" placeholder="标题" type="text">
                    </div>
                    <div style="margin-top: 15px">
                        <script id="myEditor" type="text/plain" style="width:1024px;height:500px;"></script>
                    </div>
                    <a class="btn btn-primary publish_post_btn" style="margin-top: 20px;">发表帖子</a>
                </div>


            </div>
        </div>
    </div>
    <div id="" class="publish">

    </div>
</div>