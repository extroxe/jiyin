<div id="page-content" class="home-page" ng-controller="signUp">
    <div class="titlebar" style="background-color: #117d94; padding-left: 10px">
        <i class="icon icon-arrowleft back_btn" ng-click="back()" style="margin-top: 8px;"></i>
        <h1 style="    color: #eee;
    padding-right: 40px;
    text-align: center;"><?php echo $title; ?></h1>
    </div>
    <article>

        <form>
            <div class="group "  id="logo" style="">
                <img style="width: 250px" ng-src="{{SITE_URL + 'source/mobile/img/u2.png'}}">
            </div>
            <div class="group" >
                <div class="inputbox underline">
                    <img ng-src="{{SITE_URL + 'source/mobile/img/icon/sign_in.png'}}">
                    <input class="input-text" ng-model="userInfo.username" type="text" placeholder="请输入账号">
                </div>

                <div class="inputbox underline">
                    <img ng-src="{{SITE_URL + 'source/mobile/img/icon/password.png'}}">

                    <input type="{{type ? 'text' : 'password'}}" ng-model="userInfo.password" class="input-text" placeholder="密码" name="password"/>
                    <label id="eye_kan" for="check_password" class="color-placeholder icon icon-eye"></label>
                    <input type="checkbox" ng-model="type" id="check_password" style="display: none">
                </div>
                <div class="inputbox underline">
                    <img ng-src="{{SITE_URL + 'source/mobile/img/icon/password.png'}}">
                    <input type="{{type1 ? 'text' : 'password'}}" ng-model="userInfo.password_confirm" class="input-text" placeholder="确认密码" name="reconfirm"/>
                    <label id="eye_kan" for="check_confirm_password" class="color-placeholder icon icon-eye"></label>
                    <input type="checkbox" ng-model="type1" id="check_confirm_password" style="display: none">
                </div>
                <div class="inputbox underline">
                    <img ng-src="{{SITE_URL + 'source/mobile/img/icon/telephone.png'}}">
                    <input type="text" ng-model="userInfo.phone" class="input-text" placeholder="手机号码" name="reconfirm"/>
                </div>
                <div class="inputbox underline">
                    <img ng-src="{{SITE_URL + 'source/mobile/img/icon/checkcode.png'}}">
                    <input type="text" ng-model="userInfo.verification_code" class="input-text" placeholder="请输入验证码" name="reconfirm"/>
                    <button type="button" class="button radius4 checkcode" ng-disabled="flag == true" ng-click="send_checkcode(userInfo)" >{{send_code_ope}}</button>
                </div>


                <div class="sendcode">
                    <a class="button block radius4" ng-click="submitForm(userInfo)">注册</a>
                </div>
            </div>
        </form>

    </article>
</div>