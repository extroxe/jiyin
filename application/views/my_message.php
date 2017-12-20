<div class="message-wrapper">
    <div class="nav">
        <ul>
            <li class="nav-item active">
                <a href="#inbox" data-toggle="tab">
                    <span>收件箱</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#outbox" data-toggle="tab">
                    <span>发件箱</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#follow" data-toggle="tab">
                    <span>关注的人</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="inbox">
        </div>
        <div class="no-inbox">暂无关注的用户</div>
        <div class="tab-pane" id="outbox">
        </div>
        <div class="no-outbox">暂无关注的用户</div>
        <div class="tab-pane" id="follow">
        </div>
        <div class="no-follow">暂无关注的用户</div>
    </div>
    <div class="message-content" id="msg"></div>
    <div class="send-content" id="send"></div>
    <div class="post-page" id="page_content"></div>
</div>

<script id="inbox_list" type="text/html">
    <ul>
        <% for(var i = 0; i < list.length; i++){ %>
        <li>
            <div class="item">
                <div class="content-wrapper" data-id="<%:=list[i].id%>">
                    收到了来自
                    <a href="#">【<%:=list[i].send_user_username%>】</a>
                    的信息
                    <a href="#"><%:=list[i].content%></a>
                </div>
                <div class="operated"><%:=list[i].status_name%></div>
                <div class="item-date"><%:=list[i].create_time%></div>
            </div>
        </li>
        <% } %>
    </ul>
</script>

<script id="outbox_list" type="text/html">
    <ul>
        <% for(var i = 0; i < list.length; i++){ %>
        <li>
            <div class="item">
                <div class="content-wrapper" data-id="<%:=list[i].id%>">
                    向
                    <a href="#">【<%:=list[i].receive_user_username%>】</a>
                    发送了一条信息
                    <a href="#"><%:=list[i].content%></a>
                </div>
                <div class="item-date"><%:=list[i].create_time%></div>
            </div>
        </li>
        <% } %>
    </ul>
</script>

<script id="follow_list" type="text/html">
    <% for(var i = 0; i < list.length; i++){ %>
    <div class="follow-item">
        <div class="follow-avatar">
            <img src="<?=site_url(); ?><%:=list[i].avatar_path%>" alt="">
        </div>
        <div>
            <ul>
                <li class="name">
                    <span class="username"><%:=list[i].username%></span>
                    <span class="send-message" data-id="<%:=list[i].focus_id%>"><i class="icon-envelope"></i></span>
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

<script id="msg_wrapper" type="text/html">
    <div class="content"><%:=data.content%></div>
    <div class="signature">
    <% if(data.status_type == 1){ %>
        <span>接收者：<%:=data.receive_user_username%></span>
    <% }else{ %>
        <span>发送者：<%:=data.send_user_username%></span>
    <% } %>
        <span class="time"><%:=data.create_time%></span>
        <span class="back"><返回</span>
    </div>
</script>
<script id="send_wrapper" type="text/html">
    <div class="receive-name">接受者：<%:=data.username%></div>
    <div class="message-text">
        <textarea id="message_content" placeholder="此处填写将要发送的信息..."></textarea>
    </div>
    <div class="send-footer">
        <div class="back">返回</div>
        <div class="send-btn" data-id="<%:=data.id%>">发送</div>
    </div>
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