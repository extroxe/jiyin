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
                <input type="text" class="input-medium " id="key_words" placeholder="请输入关键字搜索" value="">
                <a class="btn btn-link btn-3" id="search-btn-post" target="_blank">搜索贴吧</a>
            </div>
        </div>
    </div>
</header>

<div id="page-content" style="min-height: 200px; " class="home-page">
    <div class="container">
        <div class="focus_lists">
            <ul id="focus_lists">
            </ul>
            <div class="pagination pull-right">
                <ul>
                    <li><a href="javascript:void(0)" id="Prev_page">上一页</a></li>
                    <li>共<span id="total_pages" style="color: #117d94"></span>页 &nbsp;
                        第<input id="page_num" value="1" type="text">页
                        <a href="javascript:void(0)" style="cursor: pointer" id="jump_to_page">跳转</a>
                    </li>
                    <li><a href="javascript:void(0)" id="Next_page">下一页</a></li>
                </ul>
            </div>
        </div>

        <div class="personal_info">
            <span class="head">个人信息</span>
            <div class="data">
                <?php if (isset($user_info['avatar_path'])) : ?>
                    <img class="portrait" src="<?=site_url($user_info['avatar_path']); ?>">
                <?php else: ?>
                    <img class="portrait" src="<?=site_url('source/img/05.jpg'); ?>">
                <?php endif; ?>
                <div class="info_list">
                    <span class="nickname"><a href="javascript:void(0)"><?php echo isset($user_info['nickname']) ? $user_info['nickname'] : ''; ?></a></span>
                    <span class="sex"><?php echo isset($user_info['gender']) && $user_info['gender'] == 1 ? '男' : '女'; ?></span>
                    <span class="follows">粉丝：<span>0</span></span>
                    <span class="focus">关注：<span>0</span></span>
                </div>
                <div style="clear: both"></div>
            </div>
        </div>
        <div style="clear: both"></div>
    </div>

    <script type="text/html" id="focus_lists_tpl">
        <% for(var i = 0; i < data.length; i++) {%>
        <li>
            <img class="portrait" style="width: 50px" src="<?=site_url('<%:=data[i].avatar_path%>'); ?>">
            <a class="name" href="<?php echo site_url('my_city/visit')?>/<%:=data[i].fans_user_id%>"><%:=data[i].nickname%></a>
            <span class="follows">粉丝：<span><%:=data[i].fans_lists%></span></span>
            <span>关注：<span><%:=data[i].focus_lists%></span></span>
            <% if(data[i].is_focused && !data[i].is_me) {%>
            <a class="btn btn-link add_focus focused_btn" data-focus-id="<%:=data[i].fans_user_id%>">
                <i class="fa fa-check" aria-hidden="true"></i>
                已关注
            </a>
            <% } else if(data[i].is_me) { %>
            <span class="btn btn-link add_focus focus_btn">
                <i class="fa" aria-hidden="true"></i>
                我
            </span>
            <% } else { %>
            <a class="btn btn-link add_focus focus_btn" data-focus-id="<%:=data[i].fans_user_id%>">
                <i class="fa fa-plus" aria-hidden="true"></i>
                加关注
            </a>
            <% } %>
        </li>
        <% } %>
    </script>
</div>