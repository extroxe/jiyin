
<div id="personal_info" class="model_box">
    <div class="control-group">
        <h5>编辑个人信息</h5>
    </div>
    <div class="control-group">
        <label style="display: inline-block;width: 100px;">头像</label>
        <img style="width: 50px;height:50px;" id="avatar_img" src="<?php echo site_url(isset($user['avatar_path']) && !is_null($user['avatar_path']) ? $user['avatar_path'] : 'source/img/default-user.png'); ?>">
        <a class="btn btn-2 btn-link button_img" id="show_upload_avatar">上传头像</a>
    </div>
    <div class="control-group">
        <label class="prev_label" style="float: left;line-height: 28px" for="username">用户名</label>
        <input class="input_text" id="username" type="text" placeholder="用户名" value="<?php echo $user['username']; ?>" disabled="disabled">
    </div>
    <div class="control-group">
        <label class="prev_label" style="float: left;line-height: 28px" for="nickname">昵称</label>
        <input class="input_text" id="nickname" type="text" placeholder="昵称" data-old="<?php echo $user['nickname']; ?>" value="<?php echo $user['nickname']; ?>">
        <div class="_edit" style="display: inline-block;">
            <img src="https://gold-cdn.xitu.io/images/register-login/edit-icon.svg">
            <span class="edit-point">编辑</span>
        </div>
        <div class="save_cancel" style="display: none">
            <span class="save" style="color: #007fff">保存</span>
            <span class="cancel">取消</span>
        </div>
        <div id="data_content"></div>
    </div>
    <div class="control-group">
        <label class="prev_label" style="float: left;line-height: 28px" for="name">真实姓名</label>
        <input class="input_text" id="name" type="text" placeholder="请填写您的姓名" data-old="<?php echo $user['name']; ?>" value="<?php echo $user['name']; ?>">
        <div class="_edit" style="display: inline-block;">
            <img src="https://gold-cdn.xitu.io/images/register-login/edit-icon.svg">
            <span class="edit-point">编辑</span>
        </div>
        <div class="save_cancel" style="display: none">
            <span class="save" style="color: #007fff">保存</span>
            <span class="cancel">取消</span>
        </div>
    </div>
    <div class="control-group">
        <label style="display: inline-block; width: 100px;">性别</label>
        <?php if ($user['gender'] == 1) : ?>
        <input type="radio" style="margin: 0 5px" name="sex" value="1" checked>男
        <input type="radio" style="margin: 0 5px" name="sex" value="0">女
        <?php elseif ($user['gender'] == 0) : ?>
        <input type="radio" style="margin: 0 5px" name="sex" value="1">男
        <input type="radio" style="margin: 0 5px" name="sex" value="0" checked>女
        <?php endif; ?>
        <div class="edit" style="display: inline-block;">
            <span class="fa fa-check" style="color: #007fff; margin-right: 5px"></span><span id="gender-save" class="edit-point">保存</span>
        </div>
    </div>
    <div class="control-group" >
        <label style="display: inline-block; width: 100px;" id="birthday" data-date="<?php echo $user['birthday']; ?>">生日</label>
        <select name="year"></select>
        <select name="month"></select>
        <select name="day"></select>
        <div class="edit" style="display: inline-block;">
            <span class="fa fa-check" style="color: #007fff; margin-right: 5px"></span><span id="birthday-save" class="edit-point">保存</span>
        </div>
    </div>
</div>

<!-- Avatar Modal -->
<div class="modal fade bs-example-modal-lg" id="avatar_modal" tabindex="-1" role="dialog" aria-labelledby="avatarModal" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalLabel">上传头像</h4>
            </div>
            <div class="modal-body">
                <div id="clipArea"></div>
                <div id="view"></div>
                <input type="file" id="file" name="file">
                <a class="btn btn-2" id="clipBtn" style="padding-right: 12px;">截取</a>
            </div>
            <div class="modal-footer">
                <a type="button" class="btn" data-dismiss="modal">取消</a>
                <a type="button" id="upload_avatar" class="btn btn-2" style="padding-right: 12px;">确定</a>
            </div>
        </div>
    </div>
</div>
