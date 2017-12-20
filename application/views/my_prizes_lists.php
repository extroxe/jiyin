<div id="page-content" class="home-page">
    <div class="container" style="background-color: #fff;">
        <div class="top">
            <p class="rotate">我参与的活动记录</p>
        </div>
        <div class="activity-record">
            <ul id="activity-record"></ul>
        </div>
    </div>
    <script type="text/html" id="activity-record-tpl">
        <% if(data == null){%>
        <li>
            <div>暂无纪录</div>
        </li>
        <% }else{%>
        <% for(var i = 0; i<data.length; i++){ %>
        <li>
            <img class="activity-img" src="<?=site_url('<%:=data[i].commodity_path%>'); ?>">
            <div>

                <% if(data[i].commodity_id == null){%>
                <p class="activity-name"><%:=data[i].prize_name%></p>
                <% }else{%>
                <p class="activity-name"><%:=data[i].commodity_name%></p>
                <% } %>
                <span class="activity-time">参与时间：<%:=data[i].create_time%></span>
            </div>
            <% if(data[i].commodity_id == null || data[i].status == 2){ %>
            <a class="btn btn-default accept-prize disabled">已领取</a>
            <%}else if(data[i].status == 1){ %>
            <a class="btn btn-primary accept-prize" data-id="<%:=data[i].sweepstakes_commodity_id%>" data-result-id="<%:=data[i].result_id%>">去领奖</a>
            <% }else if(data[i].status == 0){ %>
            <a class="btn btn-info accept-prize disabled">结果尚未公布</a>
            <% } %>
            <p></p>
        </li>
        <%}}%>
    </script>
</div>

