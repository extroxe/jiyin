<header>
    <div class="titlebar">
        <a class="titlebar-button" ng-click="back()"><i class="icon size14 icon-arrowleft back_btn"></i></a>
        <h1 class="text-center"><?php echo $title; ?></h1>
    </div>
</header>
<article ng-controller="userInfoCtrl">
    <div style="height: 60px;margin-bottom: 20px;">
        <span style="margin-top: 18px;">头像</span>
        <input type="file" name="file" nv-file-select="" uploader="uploader">
        <span class="float-right" style="position: absolute;right: 15px;z-index: 1;">
            <img ng-if=" user_info.avatar_path == null" ng-src="{{ SITE_URL + 'source/img/default-user.png' }}" alt="" class="avatar-img">
            <img ng-if=" user_info.avatar_path != null" ng-src="{{ SITE_URL + user_info.avatar_path }}" alt="" class="avatar-img">
            <a href=""><i class="icon size14 icon-arrowright"></i></a>
        </span>
    </div>
    <div ng-click="show_mid_frame('#edit_nickname')">
        <span>昵称</span>
        <span class="float-right">{{ user_info.nickname }}</span>
    </div>
    <hr>
    <div ng-click="show_mid_frame('#select_gender')">
        <span>性别</span>
        <span ng-if=" user_info.gender === '1'" class="float-right">男 <a href=""><i class="icon size14 icon-arrowright"></i></a></span>
        <span ng-if=" user_info.gender === '0'" class="float-right">女 <a href=""><i class="icon size14 icon-arrowright"></i></a></span>
        <span ng-if=" user_info.gender === null" class="float-right">保密 <a href=""><i class="icon size14 icon-arrowright"></i></a></span>
    </div>
    <hr>
    <div>
        <span>联系电话</span>
        <span class="float-right" ng-if="user_info.phone">{{ user_info.phone }}</span>
        <span class="float-right" ng-if="!user_info.phone" style="color: #999">未绑定手机号</span>
    </div>
    <hr>
    <div class="inputbox">
        <label class="inputbox-left" style="line-height: 21px;">出生年月日</label>
        <div class="inputbox-right inputbox">
            <input type="text" class="input-text text-right SID-Date" placeholder="请选择日期" readonly="readonly" value="{{ user_info.birthday }}" style="padding: 0;color: #666666;margin-right: -10px;"/>
        </div>
        <i class="icon size14 icon-arrowright"></i>
    </div>
    <section id="select_gender" data-animation="zoom" class="page">
        <div>
            <input type="radio" class="input-radio"  value="1" name="sex" ng-model="gender"/>
            <span>男</span>
            <input type="radio" class="input-radio"  value="0" name="sex" ng-model="gender"/>
            <span>女</span>
            <span class="button" ng-click="saveGender()">保存</span>
            <span ng-click="back()" class="cancel-btn">取消</span>
        </div>
    </section>
    <section id="edit_nickname" data-animation="zoom" class="page">
        <div style="width: 240px;;">
            <input type="text" class="input-text" placeholder="请输入新的昵称" ng-model="nickname" style="width: 145px;;"/>
            <span class="button"ng-click="saveNickname()" style="margin-left: 45px;">保存</span>
            <span ng-click="back()" class="cancel-btn">取消</span>
        </div>
    </section>
</article>