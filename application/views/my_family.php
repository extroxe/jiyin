
<div id="my_family" class="model_box">
    <div class="control-group">
        <h5>我的家族</h5>
    </div>
    <div class="control-group">
        <h5 id="add_edit">新增亲属信息</h5>
    </div>
        <div class="control-group">
            <label class="prev_label" style="float: left;line-height: 28px" for="member_name">姓名：</label>
            <input class="input_text "  id="member_name" type="text" placeholder="姓名" value="">
        </div>

        <div class="control-group">
            <label class="prev_label">性别：</label>
            <input type="radio" style="margin: 0 5px" name="member_sex" value="1" checked>男
            <input type="radio" style="margin: 0 5px" name="member_sex" value="0">女
        </div>
        <div class="control-group">
            <label class="prev_label" style="float: left;line-height: 28px">出生年月：</label>
            <input type="text" placeholder="请选择日期" id="J-xl">
        </div>
        <div class="control-group">
            <label class="prev_label" style="float: left;line-height: 28px" for="member_tel">联系电话：</label>
            <input class="input_text" id="member_tel" type="text" placeholder="联系电话" value="">
        </div>
        <div class="control-group">
            <label class="prev_label" style="float: left;line-height: 28px" for="member_id">亲属身份证号：</label>
            <input class="input_text" id="member_id" type="text" placeholder="亲属身份证号" value="">
        </div>
        <div class="control-group">
            <label class="prev_label" for="member_id">用药史及疗效：</label>
            <textarea id="medication_history" placeholder="请填写用药史及疗效"></textarea>
        </div>
        <div class="control-group">
            <label class="prev_label" for="name">亲属病史：</label>
            <ul class="treatment">
                <li>
                    <input type="checkbox" id="treat" value="10" data-treat-id="10">
                    <label for="treat">手术</label>
                </li>
                <li>
                    <input type="checkbox" id="radiotherapy" value="20" data-treat-id="20">
                    <label for="radiotherapy">放疗</label>
                </li>
                <li>
                    <input type="checkbox" id="chemotherapy" value="30" data-treat-id="30">
                    <label for="chemotherapy">化疗</label>
                </li>
                <li>
                    <input type="checkbox" id="targeted_therapies" value="40" data-treat-id="40">
                    <label for="targeted_therapies">靶向药物治疗</label>
                </li>
            </ul>
        </div>
        <div class="control-group">
            <label class="prev_label" for="member_relation">关系：</label>
            <select id="member_relation">
                <option value="0">--请选择--</option>
                <option value="10">父亲</option>
                <option value="20">母亲</option>
            </select>
        </div>
        <div class="control-group">
            <label class="prev_label" for="health_status">健康状态：</label>
            <select id="health_status">
                <option value="0">--请选择--</option>
                <option value="10">健康</option>
                <option value="20">亚健康</option>
                <option value="30">疾病</option>
            </select>
        </div>
        <div class="control-group">
            <div class="operation_btn">
                <a class="btn btn-2 btn-link button_img" style="padding-right: 12px" data-member-id="" id="save_family_menber">新增信息</a>
                <a class="btn btn-2 btn-link button_img" style="padding-right: 12px" data-member-id="" id="update_family_menber">更新信息</a>
                <a class="btn btn-link cancel-update">取消</a>
            </div>
        </div>
        <div class="control-group">
            <label>已保存的<span id="family_member_count" style="color: #117d94; font-size: 18px"> 0 </span>条亲属信息</label>
            <table class="table table-hover">
                <thead style="border: 1px solid #eee;background-color: #f2f7ff;">
                <tr>
                    <th>姓名</th>
                    <th>性别</th>
                    <th>电话/手机</th>
                    <th>身份证号</th>
                    <th>关系</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="family_list_container">

                </tbody>
            </table>
        </div>
        <script id="family_item_tpl" type="text/html">
            <% for (var i = 0; i < list.length; i++) { %>
            <tr>
                <td class="name" data-name="<%:=list[i].name%>"><%:=list[i].name%></td>
                <% if (list[i].district == null ) {
                list[i].district = "";
                list[i].district_code = "";
                }%>
                <td class="sex"><%:=list[i].gender%></td>
                <td class="phone"><%:=list[i].phone%></td>
                <td class="identity_card"><%:=list[i].identity_card%></td>
                <td class="identity_relation"><%:=list[i].relation%></td>
                <td>
                    <span class="fontstyle" id="edit_family_menber" data-id="<%:=list[i].id%>">修改</span><span> | </span>
                    <span class="fontstyle" id="delt_family_menber" data-id="<%:=list[i].id%>">删除</span>
                </td>
            </tr>
            <% } %>
        </script>
</div>



<!-- Avatar Modal -->


