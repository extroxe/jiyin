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
                        <li class="active post_bar_name"></li>
                    </ul>
                </div>
                <div class="announcement">
                    <p id="des_content"></p>
                </div>
                <?php if (isset($_SESSION['user_id'])) : ?>
                <a class="btn btn-primary publish_post_btn" href="javascript:void(0)">发帖</a>
                <?php else : ?>
                <a class="btn btn-default btn-lg" href="<?php echo site_url('index/sign_in'); ?>">发帖请登录</a>
                <?php endif;?>
                <table>
                    <thead>
                    <tr>
                        <th></th>
                        <th>浏览量</th>
                        <th style="width: 51%">主题</th>
                        <th>作者</th>
                        <th>回复/查看</th>
                        <th>发表时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="empty-post">没有相关帖子</div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <div class="pagination pull-right">
                    <ul>
                        <li><a href="javascript:void(0)" id="Prev_page">上一页</a></li>
                        <li>共<span id="total_pages" style="color: #117d94">1</span>页 &nbsp;
                            第<input id="page_num" value="1" type="text">页
                            <a href="javascript:void(0)" style="cursor: pointer" id="jump_to_page">跳转</a>
                        </li>
                        <li><a href="javascript:void(0)" id="Next_page">下一页</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div id="" class="publish">

    </div>
</div>
<script type="text/html" id="post_lists_tpl">
    <% for(var i = 0; i<data.length; i++){%>
    <tr>
        <% if(i == 0 && data[i].is_stickied == 1){%>
        <td><span>置顶</span></td>
        <% }else{ %>
        <td></td>
        <%}%>
        <td>
            <span class="post_view_num"><%:=data[i].page_view%></span>
        </td>
        <td>
            <a class="post_head" data-post-id = "<%:=data[i].id%>" href="javascript:void(0)"><%:=data[i].title%></a>
            <span class="post_content" data-id = "<%:=data[i].id%>"></span>
        </td>
        <td>
            <span class="post_user_name"><%:=data[i].nickname%></span>
        </td>
        <td>
            <span class="post_replay_num"><%:=data[i].comment_count%></span>
        </td>
        <td>
            <span class="post_create_time"><%:=data[i].create_time%></span>
        </td>
    </tr>
    <%}%>
</script>