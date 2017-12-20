<div class="post-wrapper">
    <div class="content">
        <ul id="post_content">
        </ul>
    </div>
    <div class="post-page" id="page_content">
    </div>
</div>

<script id="post_list" type="text/html">
    <% for(var i = 0; i < list.length; i++){ %>
    <li>
        <div class="post-item">
            在
            <a href="<?php echo site_url('my_city/post'); ?>/<%:=list[i].post_bar_id%>"><%:=list[i].post_bar_name%></a>
            发帖
            <a href="<?php echo site_url('my_city/view_post'); ?>/<%:=list[i].post_bar_id%>/<%:=list[i].id%>"><%:=list[i].title%></a>
            <div class="item-date"><%:=list[i].create_time%></div>
            <% if (list[i].status_id == 1) { %>
            <div class="post-status-draft"><%:=list[i].status_name%></div>
            <% }else if (list[i].status_id == 2) { %>
            <div class="post-status-published"><%:=list[i].status_name%></div>
            <% } %>
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