<header>
    <div class="titlebar">
        <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
        <h1 class="text-center"><?php echo $title; ?></h1>
    </div>
</header>
<article ng-controller="memberCtrl">
    <div style="display: flex;margin-bottom: 20px;">
        <img ng-if="user_info.avatar_path === null" ng-src="{{ SITE_URL + 'source/mobile/img/Hamster.jpg' }}" alt="" class="avatar-img">
        <img ng-if="user_info.avatar_path !== null" ng-src="{{ SITE_URL + user_info.avatar_path }}" alt="" class="avatar-img">
        <img ng-src="{{ SITE_URL + user_info.level_icon_path }}" alt="" class="level-icon" ng-if="user_info.level_icon_path">
        <div class="item-info">
            <p>
                <span>{{user_info.nickname}}</span>
                <span ng-if="user_info.level_name" class="level-flag">{{user_info.level_name}}</span>
            </p>
            <p>
                <span>账号：{{user_info.username}}</span>
            </p>
        </div>
    </div>
    <div>
        <div class="table-box" ng-if="level_info != null && level_info.length > 0">
            <div>
                <table align="center" style="border:none;">
                    <thead>
                        <tr>
                            <th>名称</th>
                            <th>图标</th>
                            <th>折扣率</th>
                            <th>积分率</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="level in level_info">
                            <th>{{level.name}}</th>
                            <th><img  ng-src="{{SITE_URL + level.path}}" style="max-width:30px;max-height: 30px"></th>
                            <th>{{level.price_discount}}</th>
                            <th>{{level.points_coefficient}}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div ng-if="level_info == null || level_info.length < 1" style="text-align: center;">
            <i class="icon size32 icon-rdowarn"></i>
            <label>没有会员等级信息</label>
        </div>
    </div>
</article>