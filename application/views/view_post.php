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
<div id="page-content" class="home-page">
    <div class="container body">
        <div class="header">
            <div class="head_img" style="display: none">
                <img src="<?=site_url('source/img/default-user.jpg'); ?>">
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

        <div class="post_detail">
            <div class="row-fluid title_line" style="background-color: #fff">
                <h5 class="post_title" style="display: inline-block"></h5>
                <a class="btn btn-link post_collect" style="color: #d9534f;"><i class="fa fa-plus" aria-hidden="true" style="margin-right: 5px;"></i>收藏</a>
                <a class="btn btn-link post_collected"><i class="fa fa-check" aria-hidden="true" style="margin-right: 5px;"></i>取消收藏</a>
            </div>
            <div class="row-fluid" style="position:relative;">
<!--                <p class="delt_comment">提示：该内容已被作者删除</p>-->
                <table>
                    <tbody>
                    <tr>
                        <td style="width: 20%">
                            <img class="land_host" width="100px" src="<?=site_url('source/img/default-user.jpg'); ?>">
                            <span class="post_detail_name"></span>
                            <img class="landower" src="<?=site_url('source/img/landower.png'); ?>">
                        </td>
                        <td>
                            <div>
                                <p class="post_detail_content"></p>
                                <div class="land_footer" style=" padding-right: 59px;">
                                    <span>1楼</span>
                                    <span class="post_detail_create_time"></span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                    <tbody id="comment_cotent">
                    </tbody>
                </table>
           </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
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
        </div>
        <div class="row-fluid">
            <p style="    padding-left: 59px;font-size: 20px;">发表评论</p>
            <textarea class="publish_comment"></textarea>
            <?php if (isset($_SESSION['user_id'])) : ?>
                <a class="btn btn-primary publish_comment_btn" style="margin: 20px 0 20px 59px;">发表评论</a>
            <?php else : ?>
                <a class="btn btn-default btn-lg" href="<?php echo site_url('index/sign_in'); ?>" style="margin: 20px 0 20px 59px;">发表评论请登录</a>
            <?php endif;?>
        </div>
    </div>


    <script type="text/html" id="post_comment_tpl">
        <% for(var i = 0; i < comment_data.length; i++) { %>
        <tr>
            <td>
                <img class="check_user" style="height: 100px" data-user-id="<%:=comment_data[i].publisher_id%>" width="100px" src="<?=site_url('<%:=comment_data[i].avatar_path ? comment_data[i].avatar_path : "source/img/default-user.png" %>'); ?>">
                <span class="check_user" data-user-id="<%:=comment_data[i].publisher_id%>"><%:=comment_data[i].nickname ? comment_data[i].nickname : '管理员' %></span>
                <% if(comment_data[i].land_icon) {%>
                <img class="landower" src="<?=site_url('source/img/landower.png'); ?>">
                <% } %>
            </td>
            <td>
                <div>
                    <p><%:=comment_data[i].content%></p>
                    <div class="land_footer">
                        <span><%:=parseInt(i+2)%>楼</span>
                        <span><%:=comment_data[i].create_time%></span>
                        <a class="btn btn-link accordion-toggle replay_btn" data-toggle="collapse" data-parent="#accordion2" style="    padding-top: 0;">收起回复</a>
                        <div class="accordion-body collapse in collapseOne">
                            <div class="accordion-inner">
                                <ul class="replay_comment_list" data-id="<%:=comment_data[i].id%>" data-publisher-id="<%:=comment_data[i].publisher_id%>">
                                    <% if(comment_data[i].replies){ %>
                                    <% for(var j = 0; j < comment_data[i].replies.length; j++) { %>
                                    <li>
                                        <div class="replay_box">
                                            <img src="<?=site_url('<%:=comment_data[i].replies[j].avatar_path ? comment_data[i].replies[j].avatar_path : "source/img/default-user.png"%>'); ?>">
                                            <a class="btn btn-link user_name" href="<?php echo site_url('my_city/visit')?>/<%:=comment_data[i].replies[j].publisher_id%>"><%:=comment_data[i].replies[j].nickname%></a>
                                            <span>回复 <%:=comment_data[i].replies[j].to_user_nickname%>：</span>
                                            <p class="replay_content"><%:=comment_data[i].replies[j].content%></p>
                                            <div class="replay">
                                                <span><%:=comment_data[i].replies[j].create_time%></span>
                                                <a class="btn btn-link accordion-toggle reply_comment" data-comment-id="<%:=comment_data[i].replies[j].id%>" data-to-user-id="<%:=comment_data[i].replies[j].publisher_id%>" data-toggle="collapse" data-parent="#accordion2">回复</a>
                                            </div>
                                        </div>
                                        <p class="replay_content_del"></p>
                                    </li>
                                    <% } %>
                                    <% } %>
                                    <li style="overflow: hidden;zoom: 1;">
                                        <a class="btn btn-link accordion-toggle my_say" data-toggle="collapse" data-parent="#accordion2">我也说一句</a>
                                    </li>
                                    <li style="border-bottom: none">
                                        <div class="accordion-body collapse collapseTwo">
                                            <textarea></textarea>
                                            <?php if (isset($_SESSION['user_id'])) : ?>
                                            <a class="btn btn-primary publish_replay publish_comment_to_comment">发表评论</a>
                                            <a class="btn btn-primary publish_replay publish_comment_to_replay">发表回复</a>
                                            <?php else : ?>
                                            <a class="btn btn-default publish_replay publish_comment_to_comment" href="<?php echo site_url('index/sign_in');?>">发表评论请先登录</a>
                                            <a class="btn btn-default publish_replay publish_comment_to_replay" href="<?php echo site_url('index/sign_in');?>">发表回复请先登录</a>
                                            <?php endif;?>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <% } %>
    </script>
</div>