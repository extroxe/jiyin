<div class="container">
    <div class="tab-pane active" id="home">
        <table class="table table-hover" style="margin-top: 30px;" id="report_list"></table>
    </div>
    <div class="row-fluid">
        <div class="span6" style="text-align: center">
            <a href="http://a.com/"><img style="width: 251px" src="http://a.com/source/img/u2.png"></a>
            <span style="font-size: 25px; border-left: 1px solid #999; padding-left: 15px; margin-left: 10px">录 入 检 测 人 信 息</span>
        </div>

    </div>
    <div class="tab-pane" id="receiving_info" style="min-height: 420px">
        <div class="form-horizontal" method="post">
            <div class="control-group">
                <label class="control-label head"><span>*请填写您真实有效的信息，若信息有误，将影响检测结果！</span></label>
            </div>
            <div class="control-group" style="border-top: none">
                <label class="control-label" for="order-number">请输入报告编号<span class="redstar">*</span>:</label>
                <div class="controls">
                    <input type="text" id="order_number" placeholder="报告编号">
                    <button class="btn btn-link btn-2" id="search_btn" style="padding-right: 14px">查 询</button>
                </div>
            </div>
        </div>
        <form class="form-horizontal" id="report-list" style="display: none;">
            <div class="control-group">
                <label class="control-label" for="name">姓名<span class="redstar">*</span>:</label>
                <div class="controls">
                    <input type="text" id="name" name="name" placeholder="请填写您的真实姓名">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="phone">手机号<span class="redstar">*</span>:</label>
                <div class="controls">
                    <input type="number" id="phone" name="phone" placeholder="请输入手机号">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="identity_card">身份证号码<span class="redstar">*</span>:</label>
                <div class="controls">
                    <input type="text" id="identity_card" name="identity_card" placeholder="请填写18位身份证号码">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="gender">性别<span class="redstar">*</span>:</label>
                <div class="controls">
                    <input type="radio" name="gender" value="1">男
                    <input type="radio" name="gender" style="margin-left:5px;" value="0">女
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="birth">出生日期<span class="redstar">*</span>:</label>
                <input type="text" name="birth" style="margin-left: 20px; color:#999; font-size: 13px" placeholder="无需填写，根据身份证号自动匹配" id="birth">
            </div>
<!--            <div class="control-group">-->
<!--                <label class="control-label" for="smoking">是否吸烟<span class="redstar">*</span>:</label>-->
<!--                <div class="controls">-->
<!--                    <input type="radio" name="smoking" value="1">是-->
<!--                    <input type="radio" style="margin-left:5px;" name="smoking" value="0">否-->
<!--                </div>-->
<!--            </div>-->

            <div class="control-group">
                <label class="control-label" for="height">身高（cm）<span class="redstar">*</span>:</label>
                <div class="controls">
                    <input type="number" id="height" name="height" placeholder="请输入被检测人身高">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="weight">体重（kg）<span class="redstar">*</span>:</label>
                <div class="controls">
                    <input type="number" id="weight" name="weight" placeholder="请输入被检测人体重">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">所在地区<span style="color: red; margin-left: 5px">*</span></label>
                <select id="province" name="province" style="margin-left: 20px" class="border_radius"
                        onchange="searchNextLevel(this)">
                    <option>--省--</option>
                </select>
                <select id="city" name="city" class="border_radius" onchange="searchNextLevel(this)">
                    <option>--市--</option>
                </select>
                <select id="district" name="district" class="border_radius" onchange="searchNextLevel(this)">
                    <option>--区--</option>
                </select>
                <span id="district_error" class="help-inline error"></span>
            </div>
            <div class="control-group">
                <label class="control-label" for="identify-card">详细地址<span class="redstar">*</span>:</label>
                <div class="controls">
                    <input type="text" id="address" name="address" placeholder="请输入详细地址">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="identify-card">个人病史:</label>
                <div class="controls">
                    <textarea id="personal_history" name="personal_history" placeholder="选填，请输入个人病史"></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="identify-card">家族病史:</label>
                <div class="controls">
                    <textarea id="family_history" name="family_history" placeholder="选填，请输入家族病史"></textarea>
                </div>
            </div>
            <!--<div class="control-group">
                <label class="control-label" for="identify-card">血缘关系<span class="redstar">*</span>:</label>
                <div class="controls">
                    <select id="relationship" name="relationship" class="input-text" style="width: 220px">
                        <option value="0">请选择真实关系</option>
                        <option value="130">本人</option>
                        <option value="140">爱人</option>
                        <option value="150">小孩</option>
                        <option value="10">父亲</option>
                        <option value="20">母亲</option>
                        <option value="30">哥哥</option>
                        <option value="40">弟弟</option>
                        <option value="50">姐姐</option>
                        <option value="60">妹妹</option>
                        <option value="70">爷爷</option>
                        <option value="80">奶奶</option>
                        <option value="90">舅舅</option>
                        <option value="100">叔叔</option>
                        <option value="110">阿姨</option>
                        <option value="120">姑姑</option>
                        <option value="160">其他</option>
                    </select>
                </div>
            </div>-->
            <div class="control-group project-select">
                <label class="control-label" for="identify-card">项目选择
                    <span class="redstar">*</span>:
                </label>
                <div class="controls project-lists" style="max-width: 78%;"></div>
            </div>
            <div class="control-group" style="padding-left: 180px">
                <div class="controls">
                    <input type="submit" id="submit-report-info" style="padding-right: 14px" class="btn btn-link btn-2"
                           value="提交">
                </div>
            </div>
        </form>
    </div>
</div>


<script id="project_tpl" type="text/html">
    <% for(var i = 0; i < list.length; i++) {%>
    <div style="overflow:hidden;">
        <% if(i == 0) {%>
        <span class="project_num_title" class="project_num_title" style="display: block; font-size: 15px; margin: 0px 0 10px">请选择<font class="project_num" class="project_num"><%:=list[i][0].project_num%></font>个项目（<%:=list[i][0].template_name%>套餐）</span>
        <% }else {%>
        <span class="project_num_title" class="project_num_title" style="display: block; font-size: 15px; margin: 10px 0">请选择<font class="project_num" class="project_num"><%:=list[i][0].project_num%></font>个项目（<%:=list[i][0].template_name%>套餐）</span>
        <% } %>
       <span class="parent-span" style="margin-left: 0">
             <% for(var j = 0; j < list[i].length; j++) {%>
                <span style="margin: 0 10px 0 0;display: inline-block;">
                    <input id="<%:=list[i][j].name%>" data-project-id = '<%:=list[i][j].id%>' data-template-id = "<%:=list[i][j].template_id%>" data-project-num = "<%:=list[i][j].project_num%>" class="project-name" type="checkbox" style="margin: 0"> <label style="display: inline-block" for="<%:=list[i][j].name%>"><%:=list[i][j].name%></label>
                </span>
            <% } %>
        </span>
    </div>
    <% } %>
</script>




