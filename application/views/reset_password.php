<div id="page-content" class="home-page">
    <div class="container">
        <div class="row-fluid">
            <div class="top">
                <img  src="<?php echo site_url('source/img/psd.png');?>">
                <p>找回密码</p>
            </div>
        </div>
        <div class="row-fluid">
            <div id="crumbs">
                <div style="padding: 20px 0 0 50px;">
                    <ul>
                        <li><a href="#">1、确认账号</a></li>
                        <li><a href="#">2、重置密码</a></li>
                    </ul>
                    <div class="fixed"></div>
                </div>
            </div>
            <form id="register_form" class="form-horizontal">
                <div class="control-group">
                    <label class="control-label" for="username">新密码：</label>
                    <div class="controls">
                        <input type="text" name="username" id="username" placeholder="请输入新密码">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="verification_code">确认密码：</label>
                    <div class="controls">
                        <input type="text" name="verification_code" id="verification_code" placeholder="请确认新密码">
                    </div>
                </div>
                <div class="next control-group">
                    <a id="next" type="button" class="btn btn-2 btn-link">确定</a>
                </div>
            </form>
        </div>
    </div>
</div>