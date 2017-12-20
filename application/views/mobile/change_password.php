.<section id="page_modify"  style="background-color:#F9F9F9;" ng-controller="changePassword">
    <header>
        <div class="titlebar">
            <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
            <h1 class="text-center" style="padding-right: 20px"><?php echo $title; ?></h1>
        </div>
    </header>
    <article>
        <div class="inputbox underline">
            <label class="inputbox-left">已绑定手机</label>
            <div class="inputbox-right inputbox">
                <input type="text" class="input-text" ng-model="bindPhone" ng-disabled="true" name="phone"/>
            </div>
        </div>
        <div class="inputbox underline">
            <label class="inputbox-left">手机号码</label>
            <div class="inputbox-right inputbox">
                <input type="text" class="input-text" ng-model="userInfo.phone" placeholder="请输入您的手机号码" name="phone"/>
            </div>
        </div>
        <div class="inputbox">
            <label class="inputbox-left">验证码</label>
            <div class="inputbox-right inputbox">
                <input type="text" class="input-text" placeholder="请输入验证码" ng-model="userInfo.verification_code" name="username"/>
                <button type="button" class="button lrpadding8 send_passcode" ng-disabled="flag == true" ng-click="send_checkcode(userInfo)" >{{send_code_ope}}</button>
            </div>
        </div>
        <div class="inputbox underline" style="margin-top: 12px;">
            <label class="inputbox-left">新密码</label>
            <div class="inputbox-right inputbox" id="ID-Area">
                <input type="password" class="input-text" ng-model="new_password" placeholder="请输入新密码" style="padding-right: 12px;"/>
            </div>
        </div>
        <div class="inputbox">
            <label class="inputbox-left">确认密码</label>
            <div class="inputbox-right inputbox">
                <input type="password" class="input-text" ng-model="confirm_password" placeholder="请再次输入密码" style="padding-right: 12px;" />
            </div>
        </div>
        <div class="sub-btn">
            <button class="button" ng-click="save(userInfo)">确&nbsp;&nbsp;&nbsp;&nbsp;认</button>
        </div>
    </article>
</section>