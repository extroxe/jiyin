<div id="page-content" class="home-page">
    <div class="container-fluid" style="background: url('<?=site_url('source/img/sign_in_bg.jpg'); ?>')">
        <div class="row-fluid">
            <div class="span12"></div>
        </div>
        <div class="row-fluid" style="height: 527px;">
            <div class="span4 offset8">
                <div id="login_box">
                    <div class="tabbable">
                        <ul class="nav nav-tabs" id="selected">
                            <li class="active"><a href="#tab1" data-toggle="tab">账户登录</a></li>
                            <li><a href="#tab2" data-toggle="tab">验证码登录</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab1">
                                <form class="form-horizontal">
                                    <div class="control-group">
                                        <div class="row-fluid">
                                            <div class="span12" text-align="center">
                                                <div class="input-prepend">
                                                    <span class="add-on fa fa-user"></span>
                                                    <input  class="span12 info_input" id="username" type="text" placeholder="请输入用户名">
                                                    <label class="user_hinter " style="margin-left: 10px">输入用户名不存在</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="row-fluid">
                                            <div class="span12" text-align="center">
                                                <div class="input-prepend">
                                                    <span class="add-on fa fa-key"></span>
                                                    <input class="span12 info_input" id="password" type="password" placeholder="请输入密码">
                                                    <label class="user_hinter" style="margin-left: 10px">输入密码有误</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="control-group" style="margin-bottom: 15px">
                                        <div class="row-fluid">
                                            <div class="span6" style="padding-left: 42px;">
                                                <input type="checkbox" id="remenberUser" style="margin-top: 0" >
                                                <label for="remenber" style="display: inline-block">记住密码</label>
                                            </div>
                                            <div class="span6">
                                                <a style="font-size: 14px;" href="<?php echo site_url('index/find_password'); ?>">忘记密码?</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="row-fluid">
                                            <div class="span12">
                                                <a class="btn btn-2 btn-link" id="login" href="javascript:void(0)">登 录</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="tab2">
                                <form class="form-horizontal">
                                   <div class="phone-info">
                                       <span class="phone">手机号：<input  class="span12 info_input" id="phone" type="text" placeholder="请输入手机号"></span>
                                       <span class="code">
                                           验证码：
                                           <input  class="span12 info_input" id="code" type="text" placeholder="请输入验证码">
                                           <button id="get_verification_code">获取验证码</button>
                                       </span>
                                   </div>
                                    <div class="control-group">
                                        <div class="row-fluid">
                                            <div class="span12">
                                                <a class="btn btn-2 btn-link" id="login_by_code" href="javascript:void(0)">登 录</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="control-group">
                                <div class="row-fluid">
                                    <div class="span12" style="position: relative;">
                                        <a id="weixin_login" href="javascript:void(0)">
                                            <span><img src="<?php echo site_url('source/img/QQ-weixin.png');?>" alt=""></span>微信
                                        </a>
                                        <a class="sign_up_directly" href="<?php echo site_url('index/sign_up'); ?>">立即注册</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>