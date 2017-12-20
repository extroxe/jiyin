<div id="page-content" class="home-page" ng-controller="signIn">
    <div class="titlebar" style="background-color: #117d94">
        <a class="titlebar-button" ng-click="back()"><i class="icon icon-arrowleft back_btn"></i></a>
        <h1 style="color: #eee;
    padding-right: 40px;
    text-align: center;"><?php echo $title; ?></h1>
    </div>
    <article class="sign_in">

        <form>
            <div class="group" style="margin: 60px 0 40px;">
                <img style="width: 250px" ng-src="{{SITE_URL + 'source/mobile/img/u2.png'}}">
            </div>
            <div class="group" >
                <div class="inputbox underline" style="    ">
                    <img ng-src="{{SITE_URL + 'source/mobile/img/icon/sign_in.png'}}">
                    <input class="input-text" ng-model="userInfo.username" type="text" placeholder="请输入账号">
                </div>

                <div class="inputbox underline" style="     margin-top: 30px">
                    <img ng-src="{{SITE_URL + 'source/mobile/img/icon/password.png'}}">
                    <input class="input-text" ng-model="userInfo.password" type="{{type ? 'text' : 'password'}}" placeholder="请输入密码">
                    <label id="eye_kan" for="check_confirm_password" class="color-placeholder icon icon-eye"></label>
                    <input type="checkbox" ng-model="type" id="check_confirm_password" style="display: none">
                </div>

                <div class="sendcode" style="margin-top: 50px">
                    <a class="button block radius4" style="
    background: rgba(238, 59, 59, 0.7);" ng-click="submitForm(userInfo)">登录</a>
                </div>
                <div class="sendcode" style="margin-top: 0">
                    <a class="button block radius4" style="
    background: rgba(34, 172, 236, 0.7);" ng-href="<?php echo site_url('weixin/index/sign_up'); ?>">立即注册</a>
                </div>
            </div>
        </form>
    </article>
</div>