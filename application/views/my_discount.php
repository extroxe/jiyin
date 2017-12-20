<div id="my_discount" class="model_box">
    <div class="control-group">
        <h5>我的优惠券</h5>
    </div>
    <div class="user-discount">
        <a href="#my_discount_modal" data-toggle="modal">我的优惠券</a>
    </div>
    <div class="discount-wrapper">
        <ul id="discount">
        </ul>
    </div>
</div>
<div class="modal fade" style="display: none;" id="my_discount_modal" tabindex="-1" role="dialog" aria-labelledby="myDiscountModal" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">我的优惠券</h4>
            </div>
            <div class="modal-body">
                <div class="model_box">
                    <table class="table table-hover">
                        <thead style="border: 1px solid #eee;background-color: #f2f7ff;">
                        <tr>
                            <th>名称</th>
                            <th>描述</th>
                            <th>兑换时间</th>
                            <th>到期时间</th>
                            <th>状态</th>
                        </tr>
                        </thead>
                        <tbody id="user_discount">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <a type="button" class="btn confirm-discount" data-dismiss="modal">确定</a>
            </div>
        </div>
    </div>
</div>
<script id="discount_tpl" type="text/html">
    <% for(var i = 0; i < list.length; i++) { %>
    <li class="item <%:=(list[i].user_discount_coupon_id === null) ? '' : 'received'%>">
        <div class="item-wrapper">
            <div class="price">
                ¥<span><%:=list[i].privilege%></span>
            </div>
            <div class="desc">
                <span>满<%:=list[i].condition%>使用</span>
                <span>优惠券</span>
            </div>
            <div class="status">
                <% if (list[i].user_discount_coupon_id === null) { %>
                <a href="#" id="receive_discount" data-discount-id="<%:=list[i].id%>">点击领取</a>
                <% }else{ %>
                <a href="#">已领取</a>
                <% } %>
            </div>
        </div>
        <div class="useful-life">使用时间：<%:=list[i].start_time%> 至 <%:=list[i].end_time%></div>
    </li>
    <% } %>
</script>
<script id="user_discount_tpl" type="text/html">
    <% for (var i = 0; i < list.length; i++) { %>
    <tr>
        <td><%:=list[i].name%></td>
        <td>满<%:=list[i].condition%>减去<%:=list[i].privilege%>优惠券</td>
        <td><%:=list[i].create_time%></td>
        <td><%:=list[i].end_time%></td>
        <% if (list[i].status_id == 1) {%>
        <td><a href="<%:=SITE_URL%>">去使用</a></td>
        <% } else { %>
        <td><a href="javascript:void(0)">已使用</a></td>
        <% } %>
    </tr>
    <% } %>
</script>