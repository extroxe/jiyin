<div class="head"><img src="<?php echo site_url('source/img/u2.png') ?>" alt=""></div>
<div class="bg-img-wrapper"></div>
<div class="article-wrapper">
    <div class="title">
        <span>Health</span>
        <hr>
        <span>健康讲座</span>
    </div>
    <div class="content" id="article_list">
        <div class="no_data">
            <img src="<?=site_url('source/img/nodata.jpg'); ?>">
            <span>暂无相关文章</span>
        </div>
    </div>
    <div class="loading">
        加载中···
    </div>
   <!-- <div class="load_done">
        加载完成
    </div>-->

   <!-- <script type="text/html" id="article_list_tpl">
        <% for (var i = 0; i < data.length; i++) { %>
        <div class="item" data-article-id="<%:= data[i].id %>">
            <img src="<?php /*echo site_url('<%:=data[i].thumbnail_path%>') */?>" alt="">
            <div class="item-title"><%:=data[i].title%></div>
            <div class="item-description"><%:=data[i].abstract%></div>
            <div class="item-date"><%:=data[i].update_time%></div>
        </div>
        <% } %>
    </script>-->
</div>