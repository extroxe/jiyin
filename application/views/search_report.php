<div class="condition-wrapper">
    <div class="condition">
<!--        <div class="item identity-card">-->
<!--            <label for="identity-card">身份证号：</label>-->
<!--            <input type="text" placeholder="身份证号后四位(选填)" id="identity_card">-->
<!--        </div>-->
        <div class="item phone">
            <label for="phone">手机号：</label>
            <input type="text" placeholder="手机号" id="phone">
            <button class="btn" id="send_code_btn">发送验证码</button>
        </div>
        <div class="item verification-code">
            <label for="verification-code">验证码：</label>
            <input type="text" placeholder="验证码" id="verification_code">
        </div>
        <div class="item">
            <button class="btn" id="search_btn">查 询</button>
        </div>
    </div>
</div>
<div class="list-wrapper">
    <div id="report_list">
        <div class="img-wrapper">
            <img src="<?php echo site_url('source/img/book-icon.png') ?>" alt="">
        </div>
    </div>
</div>

<script id="report_tpl" type="text/html">
    <h3 style="text-align: center; margin-bottom: 20px;">报告列表</h3>
    <table class="table table-hover">
        <thead style="border: 1px solid #eee;background-color: #f2f7ff;">
        <tr>
            <th>商品名称</th>
            <th>姓名</th>
            <th>报告编号</th>
            <th>更新时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <% for (var i = 0; i < list.length; i++) { %>
        <tr>
            <td class="commodity_name">
                <% if (list[i].commodity_specification_name != null) { %>
                    <%:= list[i].commodity_specification_name %>
                <% } else if (list[i].erp_commodity_specification_name != null) { %>
                    <%:= list[i].erp_commodity_specification_name %>
                <% } %>
            </td>
            <td class="name"><%:=list[i].name%></td>
            <td class="number"><%:=list[i].number%></td>
            <td class="update_time"><%:=list[i].update_time%></td>
            <td><a href="<%=SITE_URL + list[i].path%>" download="report"><i class="glyphicon icon-download-alt"></i>下载</a></td>
        </tr>
        <% } %>
        </tbody>
    </table>
</script>