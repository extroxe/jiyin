<div class="fans-wrapper">
    <div class="content">
        <div id="fans">
        </div>
    </div>
    <div class="fans-page" id="page_content">
    </div>
    <div class="no-fans">您还没有粉丝...</div>
</div>

<script id="fans_list" type="text/html">
    <% for(var i = 0; i < list.length; i++){ %>
    <div class="fans-item" data-follows-id="<%:=list[i].user_id%>">
        <div class="fans-avatar">
            <img src="<?=site_url(); ?><%:=list[i].avatar_path%>" alt="">
        </div>
        <div>
            <ul>
                <li class="name">
                    <span class="username"><%:=list[i].username%></span>
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