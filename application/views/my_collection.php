<div class="collection-wrapper">
    <div class="content">
        <ul id="collection_content">
        </ul>
    </div>
    <div class="collection-page" id="page_content">
    </div>
    <div class="no-collection">您还没有收藏的帖子...</div>
</div>

<script id="collection_list" type="text/html">
    <% for(var i = 0; i < list.length; i++){ %>
    <li>
        <div class="collection-item">
            收藏了来自
            <a href="<?php echo site_url('my_city/post'); ?>/<%:=list[i].post_bar_id%>"><%:=list[i].post_bar_name%></a>
            的帖子
            <a href="<?php echo site_url('my_city/view_post'); ?>/<%:=list[i].post_bar_id%>/<%:=list[i].post_id%>"><%:=list[i].title%></a>
            <div class="operated" data-id="<%:=list[i].post_id%>">取消收藏</div>
            <div class="item-date"><%:=list[0].create_time%></div>
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