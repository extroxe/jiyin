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
                <input type="text" class="input-medium search-query" id="key_words" placeholder="请输入关键字搜索" value="">
                <a class="btn btn-link btn-3" id="search-btn-post" target="_blank">搜索贴吧</a>
            </div>
        </div>
    </div>
</header>

<div id="page-content" style="min-height: 200px;" class="home-page">
    <div class="container">
        <div class="head_background"></div>
        <div class="personal_home">
            <div class="row-fluid">
                <?php if (isset($user_info['avatar_path'])) : ?>
                    <img class="portrait" src="<?=site_url($user_info['avatar_path']); ?>">
                <?php else: ?>
                    <img class="portrait" src="<?=site_url('source/img/05.jpg'); ?>">
                <?php endif; ?>
                <div class="personal_data">
                    <?php if (isset($is_focused) && $is_focused == TRUE) : ?>
                        <a class="btn btn-link focused" id="focused">取消关注</a>
                    <?php else : ?>
                        <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && $_SESSION['role_id'] == Jys_system_code::ROLE_USER): ?>
                        <a class="btn btn-link focus" id="focus">关注</a>
                        <?php else: ?>
                        <a href="<?php echo site_url('index/sign_in'); ?>" class="btn btn-link focus">关注</a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && $_SESSION['role_id'] == Jys_system_code::ROLE_USER): ?>
                    <a href="#myModal" role="button" class="btn btn-link message" data-toggle="modal">站内信</a>
                    <?php else: ?>
                    <a href="<?php echo site_url('index/sign_in'); ?>" role="button" class="btn btn-link message">站内信</a>
                    <?php endif; ?>
<!--                    <a class="btn btn-link message">站内信</a>-->
                    <span class="nickname"><a href="javascript:void(0)"><?php echo isset($user_info['nickname']) ? $user_info['nickname'] : ''; ?></a></span>
                    <span class="sex"><?php echo isset($user_info['gender']) && $user_info['gender'] == 1 ? '男' : '女'; ?></span>
                    <span>发帖：<span class="post_nums"></span></span>
                </div>
            </div>
        </div>
        <div class="social">
            <div class="focus_post" style="width: 886px;height: auto; background-color: #ddd">
                <ul class="breadcrumb">
                    <li>
                        <a href="javascript:void(0)"><i class="fa fa-pencil" style="    margin-right: 6px;" aria-hidden="true"></i>他的帖子</a>
                    </li>
                </ul>
                <table>
                    <thead>
                    <tr>
                        <th style="width: 50%;text-align: center;">主题</th>
                        <th style="">评论数</th>
                        <th>浏览量</th>
                        <th>发表时间</th>
                    </tr>
                    </thead>
                    <tbody id="post_lists">
                    </tbody>
                </table>
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
            <div class="my_focus_follows">
                <ul class="focus_list focus_lists">
                    <li>他的关注<span><a class="focus_num" href="javascript:void(0)">(0)</a></span></li>
                </ul>
                <ul class="focus_list follow_lists">
                    <li>他的粉丝<span><a class="fallows_num" href="javascript:void(0)">(0)</a></span></li>
                </ul>
            </div>

            <div style="clear:both"></div>

        </div>

    </div>

    <script type="text/html" id="post_list_tpl">
    <% for(var i = 0; i < post_list.length; i++) {%>
        <tr>

            <td>
                <a class="post_head" href="<?php echo site_url('my_city/view_post')?>/<%:=post_list[i].post_bar_id%>/<%:=post_list[i].id%>"><%:=post_list[i].title%></a>
                <span class="post_content">贴吧：
                    <span>
                        <a class="post_bar" href="<?php echo site_url('my_city/post_bar')?>/<%:=post_list[i].post_bar_id%>">
                        <%:=post_list[i].bar_name_post%>
                        </a>
                    </span>
                </span>
            </td>
            <td>
                <span class="post_replay_num"><%:=post_list[i].comment_count%></span>
            </td>
            <td>
                <span class="post_view_num"><%:=post_list[i].page_view%></span>
            </td>
            <td>
                <span class="post_create_time"><%:=post_list[i].publish_time%></span>
            </td>
        </tr>
    <% } %>
    </script>
</div>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">站内信</h3>
    </div>
    <div class="modal-body">
        <p>接受者: <span id="to_user_letter"></span></p>
        <textarea placeholder="请输入您要发送的内容"></textarea>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
        <button class="btn btn-primary send_msg">发送</button>
    </div>
</div>