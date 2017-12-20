<div id="member_center"  class="model_box">
    <div class="control-group" style=" border: none">
        <h5>会员中心</h5>
    </div>
    <div class="control-group">
        <label style="display: inline-block;font-size: 15px;">会员等级：<?php echo $user['level_name'] ? $user['level_name'] : '无等级'; ?></label>
        <?php if ($user['level_icon_path']): ?>
        <img src="<?php echo site_url($user['level_icon_path']); ?>" alt="">
        <?php endif; ?>
    </div>
    <div id="level_list_container"></div>

    <script id="level_tpl" type="text/html">
        <% if (list.length > 0) { %>
        <div class="table-box">
            <div>
                <table align="center" style="border:none;width:100%;">
                    <thead>
                    <tr>
                        <th>名称</th>
                        <th>图标</th>
                        <th>折扣率</th>
                        <th>积分率</th>
                    </tr>
                    </thead>
                    <tbody>
                    <% for (var i = 0; i < list.length; i++) { %>
                    <tr>
                        <th><%:=list[i].name%></th>
                        <th><img  src="<%:=SITE_URL+list[i].path%>" style="max-width:30px;max-height: 30px"></th>
                        <th><%:=list[i].price_discount%></th>
                        <th><%:=list[i].points_coefficient%></th>
                    </tr>
                    <% } %>
                    </tbody>
                </table>
            </div>
        </div>
        <% } %>
    </script>
</div>