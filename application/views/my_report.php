<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#home"  data-toggle="tab">
            <div id="my_report" class="model_box">
                <div class="control-group my-report" style=" border: none;">
                    <h5>我的报告列表</h5>
                </div>
            </div>
        </a>
    </li>
    <li><a href="<?php echo site_url('index/add_report'); ?>">
            <div id="add_report" class="model_box add-report-info">
                <div class="control-group my-report" style=" border: none;">
                    <h5>添加报告信息</h5>
                </div>
            </div>
        </a>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane active" id="home">
        <table class="table table-hover" style="margin-top: 30px;" id="report_list"></table>
    </div>
    <div class="tab-pane" id="receiving_info">
        <form class="form-horizontal">
            <div class="control-group" >
                <label class="control-label head"><span>*以下填写均为真实信息，若有虚假，概不负责</span></label>
            </div>
            <div class="control-group" style="border-top: none">
                <label class="control-label" for="order-number">请输入报告编号<span class="redstar">*</span>:</label>
                <div class="controls">
                    <input type="text" id="order_number" placeholder="报告编号">
<!--                    <button id="add-report-info" class="btn btn-link btn-2" style="padding-right: 14px">查询</button>-->
                </div>
            </div>
        </form>
        <form class="form-horizontal" id="report-list">
            <div class="control-group">
                <label class="control-label" for="name">姓名<span class="redstar">*</span>:</label>
                <div class="controls">
                    <input type="text" id="name" placeholder="姓名">
                </div>
                <label class="control-label" for="gender">性别<span class="redstar">*</span>:</label>
                <div class="controls">
                    <input type="radio" style="margin: 0 5px" name="sex" value="1">男
                    <input type="radio" style="margin: 0 5px" name="sex" value="0">女
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="birth">出生日期<span class="redstar">*</span>:</label>
                <input type="text" style="margin-left: 20px" placeholder="请选择日期" id="J-xl">
                <label class="control-label" for="smoking">是否吸烟<span class="redstar">*</span>:</label>
                <div class="controls">
                    <input type="radio" style="margin: 0 5px" name="smoking" value="1">是
                    <input type="radio" style="margin: 0 5px" name="smoking" value="0">否
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="phone">联系电话<span class="redstar">*</span>:</label>
                <div class="controls">
                    <input type="number" id="phone" placeholder="请输入联系方式">
                </div>
               <!-- <label class="control-label" for="identity_card">身份证号码<span class="redstar">*</span>:</label>
                <div class="controls" style="width: 200px">
                    <input type="number" style="width: 196px;" id="identity_card" placeholder="请输入真实信息">
                </div>-->
            </div>
            <div class="control-group">
                <label class="control-label" for="identity_card">身份证号码<span class="redstar">*</span>:</label>
                <div class="controls">
                    <input type="number" id="identity_card" placeholder="请输入真实信息">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="identify-card">身高/体重<span class="redstar">*</span>:</label>
                <div class="controls">
                    <input type="number" id="height" style="width: 116px" placeholder="请输入身高信息"> cm
                    <input type="number" id="weight" style="width: 116px" placeholder="请输入体重信息"> kg
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">所在地区<span style="color: red; margin-left: 5px">*</span></label>
                <select id="province" style="margin-left: 20px" class="border_radius" onchange="searchNextLevel(this)">
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
                <label class="control-label" for="identify-card">详细地址<span class="redstar">*</span>:</label>
                <div class="controls">
                    <input type="text" id="address" placeholder="请输入详细地址">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="identify-card">个人病史<span style="visibility: hidden" class="redstar">*</span>:</label>
                <div class="controls">
                    <textarea id="personal_history" placeholder="选填，请输入个人病史"></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="identify-card">家族病史<span style="visibility: hidden" class="redstar">*</span>:</label>
                <div class="controls">
                    <textarea id="family_history" placeholder="选填，请输入家族病史"></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="identify-card">血缘关系<span class="redstar">*</span>:</label>
                <div class="controls">
                    <select id="relationship" class="input-text" style="width: 220px">
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
            </div>
            <div class="control-group">
                <div class="controls">
                    <button id="submit-report-info" style="padding-right: 14px" class="btn btn-link btn-2">提交</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script id="report_tpl" type="text/html">
    <thead style="border: 1px solid #eee;background-color: #f2f7ff;">
    <tr>
        <th>商品名称</th>
        <th>姓名</th>
        <th>手机号</th>
        <th>身份证号</th>
        <th>报告编号</th>
        <th>更新时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
        <% for (var i = 0; i < list.length; i++) { %>
        <tr>
            <td class="commodity-name"><%:=list[i].commodity_name%></td>
            <td class="name"><%:=list[i].name%></td>
            <td class="phone"><%:=list[i].phone%></td>
            <td class="identity-card"><%:=list[i].identity_card%></td>
            <td class="number"><%:=list[i].number%></td>
            <td class="update_time"><%:=list[i].update_time%></td>
            <td><a href="<%=SITE_URL+list[i].path%>" download="report"><i class="glyphicon icon-download-alt"></i>下载</a></td>
        </tr>
        <% } %>
    </tbody>
</script>


