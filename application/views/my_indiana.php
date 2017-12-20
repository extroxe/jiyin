<div id="my_indiana"  class="model_box">
    <div class="control-group" style=" border: none">
        <h5>我的夺宝</h5>
    </div>
    <div class="control-group"  id="my_indiana_container">
    </div>
</div>
<script id="my_indiana_tpl" type="text/html">
    <label>我的夺宝详情</label>
    <table class="table table-hover">
        <thead style="border: 1px solid #eee;background-color: #f2f7ff;">
        <tr>
            <th>商品名称</th>
            <th>投注积分</th>
            <th>夺宝次数</th>
            <th>积分使用总数</th>
            <th>是否中奖</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
            <% for (var i = 0; i < list.length; i++) { %>
            <tr>
                <td class="name"><%:=list[i].commodity_name%></td>
                <td class="sex"><%:=list[i].amount_bet%></td>
                <td class="phone"><%:=list[i].count_bet%></td>
                <td class="identity_card"><%:=list[i].amount_bet * list[i].count_bet%></td>
                <% if (list[i].is_win == true && list[i].integral_indiana_result_status == 1 || list[i].is_win == true && list[i].integral_indiana_result_status == 2) { %>
                <td class="identity_relation">是</td>
                <% } else if (list[i].integral_indiana_result_status == null) { %>
                <td class="identity_relation">否</td>
                <% } %>

                <% if (list[i].is_win == false && list[i].integral_indiana_result_status == 1) { %>
                <td>未中奖</td>
                <% }else if (list[i].is_win == true && list[i].integral_indiana_result_status == 1){ %>
                <td>未领取</td>
                <% } else if (list[i].is_win == true && list[i].integral_indiana_result_status == 2) { %>
                <td>已领取</td>
                <% } %>
                <% if (list[i].is_win == true && list[i].integral_indiana_result_status == 1) { %>
                <td><a href="#">去领取</a></td>
                <% }else{ %>
                <td></td>
                <% } %>
            </tr>
            <% } %>
        </tbody>
    </table>
</script>