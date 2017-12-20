<div class="follow-wrapper">
    <div class="nav">
        <ul>
            <li class="nav-item active">
                <a href="#follow_person_view" data-toggle="tab">
                    <span>关注的人</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#follow_post_view" data-toggle="tab">
                    <span>关注的贴吧</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="follow_person_view">
            <div id="follow_person"></div>
            <div class="no-follow-person">暂无关注的用户...</div>
        </div>
        <div class="tab-pane" id="follow_post_view">
            <div id="follow_post">
                <ul id="follow_post_content"></ul>
            </div>
            <div class="no-follow-post">暂无关注的吧...</div>
        </div>

    </div>
    <div class="post-page" id="page_content">
    </div>
</div>

<script id="follow_person_list" type="text/html">
    <% for(var i = 0; i < list.length; i++){ %>
    <div class="follow-item" data-user-id = "<%:=list[i].focus_id%>">
        <div class="follow-avatar">
            <img src="<?=site_url(); ?><%:=list[i].avatar_path%>" alt="">
        </div>
        <div>
            <ul>
                <li class="name">
                    <span class="username"><%:=list[i].username%></span>
                    <span class="cancel" data-id="<%:=list[i].focus_id%>">取消关注</span>
                </li>
                <li class="autograph">
                    <span>昵称：</span>
                    <% if (list[i].nickname == '' || list[i].nickname == null){ %>
                    <span class="nickname"><%:=list[i].username%></span>
                    <% }else{ %>
                    <span class="nickname"><%:=list[i].nickname%></span>
                    <% } %>
                </li>
            </ul>
        </div>
    </div>
    <% } %>
</script>

<script id="follow_post_list" type="text/html">
    <% for(var i = 0; i < list.length; i++){ %>
    <li>
        <div class="follow-post-item">
            关注了贴吧
            <a href="<?php echo site_url('my_city/post'); ?>/<%:=list[i].post_bar_id%>"><%:=list[i].name%></a>
            <div class="operated" data-id="<%:=list[i].post_bar_id%>">取消关注</div>
            <div class="item-date"><%:=list[i].create_time%></div>
        </div>
    </li>
    <% } %>
</script>

<script id="page_list" type="text/html">
    <div class="page-wrapper">
        <% if (list.total_page != 1){ %>
            <% if (list.current_page != 1){ %>
            <div class="home">首页</div>
            <% } %>
            <% if (list.current_page > 1){ %>
            <div class="prev-page" data-page="<%:=list.current_page%>"><上一页</div>
            <% } %>
            <% if (list.current_page < list.total_page){ %>
            <div class="next-page" data-page="<%:=list.current_page%>">下一页></div>
            <% } %>
        <% } %>
    </div>
</script>

