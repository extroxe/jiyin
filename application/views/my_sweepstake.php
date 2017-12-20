<div id="my_sweepstake"  class="model_box">
    <div class="control-group" style=" border: none">
        <h5>我的抽奖</h5>
    </div>
    <div class="control-group"  id="my_indiana_container">
    </div>
</div>
<script id="my_indiana_tpl" type="text/html">
    <label>我的抽奖详情</label>
    <table class="table table-hover">
        <thead style="border: 1px solid #eee;background-color: #f2f7ff;">
        <tr>
            <th>商品名称</th>
<!--            <th>是否中奖</th>-->
            <th>状态</th>
            <th>参与时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <% for (var i = 0; i < list.length; i++) { %>
        <tr>
            <% if(list[i].commodity_id == null) {%>
            <td class="name"><%:=list[i].prize_name%></td>
            <% }else{ %>
            <td class="name"><%:=list[i].commodity_name%></td>
            <% } %>
            <% if (list[i].status == 1) { %>
            <td>已领取</td>
            <% }else{ %>
            <td>未领取</td>
            <% } %>
            <td><%:=list[i].create_time%></td>
            <% if (list[i].status == 0) { %>
            <td><a href="<?php echo site_url('order/receive_prize'); ?>/<%:=list[i].sweepstakes_commodity_id%>/<%:=list[i].result_id%>/0">去领取</a></td>
            <% }else{ %>
            <td></td>
            <% } %>
        </tr>
        <% } %>
        </tbody>
    </table>
</script>