<div id="receiving_info"  class="model_box">
    <div class="control-group" style=" border: none">
        <h5>收货信息</h5>
    </div>
    <div class="control-group">
        <span id="add_or_update_address_title" data-id="0">新增</span>收货地址
    </div>
    <div class="control-group">
        <label class="prev_label">所在地区<span style="color: red; margin-left: 5px">*</span></label>
        <select id="province" class="border_radius" onchange="searchNextLevel(this)">
            <option>--省--</option>
        </select>
        <select id="city" class="border_radius" onchange="searchNextLevel(this)">
            <option>--市--</option>
        </select>
        <select id="district" class="border_radius" onchange="searchNextLevel(this)">
            <option>--区--</option>
        </select>
        <span id="district_error" class="help-inline error"></span>
    </div>
    <div class="control-group">
        <label class="prev_label">详细地址<span style="color: red; margin-left: 5px">*</span></label>
        <textarea class="border_radius" id="address" placeholder="建议如实填写详细收货地址"></textarea>
        <span id="address_error" class="help-inline error"></span>
    </div>
    <div class="control-group">
        <label class="prev_label" style="float: left">收货人姓名<span style="color: red; margin-left: 5px">*</span></label>
        <input class="border_radius" id="name" type="text" placeholder="长度不超过25个字">
        <span id="name_error" class="help-inline error"></span>
    </div>
    <div class="control-group">
        <label class="prev_label">手机号码<span style="color: red; margin-left: 5px">*</span></label>
        <input class="border_radius" id="phone" type="tel" >
        <span id="phone_error" class="help-inline error"></span>
    </div>
    <div class="control-group">
        <input id="default_addr" type="checkbox" style="margin: 0">
        <label style="display: inline-block; margin-left: 5px; color: #117d94;" for="default_addr">设置为默认收货地址</label>
    </div>
    <div class="control-group">
        <button type="button" class="btn btn-2 btn-link pull-right" style="padding-right:10px" id="save_address">保存地址</button>
    </div>

    <div class="control-group">
        <label>已保存的<span id="address_count">0</span>条地址</label>
        <table class="table table-hover">
            <thead style="border: 1px solid #eee;background-color: #f2f7ff;">
            <tr>
                <th>收货人</th>
                <th>所在城市</th>
                <th>详细地址</th>
                <th>电话/手机</th>
                <th>操作</th>
                <th style="min-width: 50px;"></th>
            </tr>
            </thead>
            <tbody id="address_list_container">

            </tbody>
        </table>
    </div>

    <script id="address_item_tpl" type="text/html">
        <% for (var i = 0; i < list.length; i++) { %>
        <tr>
            <td class="name" data-name="<%:=list[i].name%>"><%:=list[i].name%></td>
            <% if (list[i].district == null ) {
                list[i].district = "";
                list[i].district_code = "";
            }%>
            <td class="city" data-province="<%:=list[i].province%>" data-province-code="<%:=list[i].province_code%>" data-city="<%:=list[i].city%>" data-city-code="<%:=list[i].city_code%>" data-district="<%:=list[i].district%>" data-district-code="<%:=list[i].district_code%>"><%:=list[i].province%> <%:=list[i].city%> <%:=list[i].district%></td>
            <td class="address" data-address="<%:=list[i].address%>"><%:=list[i].address%></td>
            <td class="phone" data-phone="<%:=list[i].phone%>"><%:=list[i].phone%></td>
            <td>
                <span class="fontstyle edit_address" data-id="<%:=list[i].id%>">修改</span><span> | </span>
                <span class="fontstyle delete_address" data-id="<%:=list[i].id%>">删除</span>
            </td>
            <td class="default" data-default="<%:=list[i].default%>">
                <% if (list[i].default == "1") {%>
                <span class="label label-info">默认地址</span>
                <%}%>
            </td>
        </tr>
        <% } %>
    </script>
</div>