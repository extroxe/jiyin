<div id="page-content" class="home-page">
    <div class="container">
        <div class="row-fluid">
            <div class="top">
                <img  src="<?php echo site_url('source/img/psd.png');?>">
                <p>找回密码</p>
            </div>
        </div>
        <div class="row-fluid" id="find_password">
        </div>
    </div>
</div>
<script type="text/html" id="find_password_tpl">
    <div id="crumbs">
        <div style="padding: 20px 0 0 50px;">
            <ul>
                <li><a href="javascript:void(0)" style="color: #fff;">找回密码</a></li>
            </ul>
            <div class="fixed"></div>
        </div>
    </div>
    <form id="register_form" class="form-horizontal">
        <div class="control-group">
            <label class="control-label" for="phone">请输入需要找回的账号：</label>
            <div class="controls">
                <% if(phone_num != ''){ %>
                <input type="text" name="username" id="phone" value="<%:=phone_num%>">
                <% }else{ %>
                <input type="text" name="username" id="phone" placeholder="手机号码">
                <% } %>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="new_password">新密码：</label>
            <div class="controls">
                <input type="password" name="new_password" id="new_password" placeholder="请输入新密码">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="confirm_password">确认密码：</label>
            <div class="controls">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="请确认新密码">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="verification_code">手机验证码：</label>
            <div class="controls">
                <input type="text" name="verification_code" id="verification_code" placeholder="请输入手机验证码">
                <button id="get_verification_code" type="button" class="btn btn-2 btn-link">获取验证码</button>
            </div>
        </div>
        <div class="next control-group">
            <a id="submit" type="button" href="javascript:void(0)" class="btn btn-2 btn-link">提交</a>
        </div>
    </form>
</script>