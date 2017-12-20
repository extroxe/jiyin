<div id="account_contact"  class="model_box">
    <div class="control-group" style=" border: none">
        <h5>账户关联</h5>
    </div>
<!--    --><?php //dd($user);?>
    <div class="control-group">
        <div style="float: left">
            <img style="width: 20px" src="https://gold-cdn.xitu.io/images/register-login/wechat.svg">
            <label class="prev_label" for="wechat">微信</label>
        </div>

        <?php if(isset($user['openid']) && $user['openid'] != ''):?>
            <span style="float: left"><img style="width: 30px; height: 30px" onerror="javascript:this.src='<?php echo site_url('source/img/default-user.png');?>';" src="<?php echo ($user['weixin_avator']);?>" alt=""></span>
            <input style="margin-left: 10px" class="input_text bind_content" id="wechat" type="text" readonly value="<?php echo $user['weixin_nickname'];?>">
            <div id="cancel_weixin_bind" style="display: inline-block;position: absolute;right: 0; color: #007fff;">
                <span>解除绑定</span>
            </div>
        <?php else:?>
            <input class="input_text bind_content" id="wechat" type="text" readonly placeholder="未绑定">
            <div style="display: inline-block;position: absolute;width:75px;right: 0;">
                <img src="https://gold-cdn.xitu.io/images/register-login/binding-icon.svg">
                <span id="bind_wechat" class="edit-point">绑定</span>
            </div>
        <?php endif;?>
    </div>

<!--    <div class="control-group">-->
<!--        <div style="float: left">-->
<!--            <img src="https://gold-cdn.xitu.io/images/register-login/mail-icon.svg" style="width: 20px;">-->
<!--            <label class="prev_label" for="confirm_new_psd">用户邮箱</label>-->
<!--        </div>-->
<!--        <input class="input_text bind_content" id="email" type="text" placeholder="未绑定"  value="--><?php //echo $user['email']; ?><!--" style="width:200px;">-->
<!--        <span class="pro-msg" id="bind_msg" style="display: none;">已发送验证信息到邮箱，请及时处理！</span>-->
<!--        <div class="bind _edit" style="display: inline-block;">-->
<!--            <img src="https://gold-cdn.xitu.io/images/register-login/binding-icon.svg">-->
<!--            <span id="bind_email" style="color: #007fff;">绑定</span>-->
<!--        </div>-->
<!--        <div class="save_cancel cancel_bind" style="display: inline-block">-->
<!--            <span class="edit-point unbind" id="cancel_bind_email">解除绑定</span>-->
<!--        </div>-->
<!--    </div>-->
    <div class="control-group">
        <div style="float: left">
            <img style="width: 20px" src="https://gold-cdn.xitu.io/images/register-login/phone.svg">
            <label class="prev_label" for="new_psd">手机号码</label>
        </div>
        <input class="input_text bind_content" id="tel" type="text" value="<?php echo $user['phone']; ?>" style="width:200px;" <?php echo isset($user['phone']) && strlen($user['phone']) > 0 ? 'disabled="disabled"' : 'placeholder="未绑定"'; ?>>
        <input class="input_text" id="verification_code" type="text" placeholder="请输入验证码" style="width:200px;display: none;border: 1px solid #cccccc;">
        <div class="bind _edit" id="bind_phone" style="display: inline-block;">
            <img src="https://gold-cdn.xitu.io/images/register-login/binding-icon.svg">
            <span class="edit-point bind_iphone">绑定</span>
        </div>
        <div class="save_cancel" style="display: none;width:200px;">
            <span class="bind-btn" style="color: #007fff; display: none" id="send_code">发送验证码</span>
            <span class="bind-btn" style="color: #007fff; display: none" id="ensure">确定</span>
            <span class="bind-btn" style="color: #007fff; display: none;" id="ensure_cancel_bind">确定</span>
            <span class="cancel" style="display: none">取消</span>
        </div>
        <div class="cancel_bind" style="display: inline-block;">
            <span class="unbind" style="color: #007fff;" id="cancel_bind_phone">解除绑定</span>
        </div>
    </div>
</div>