<header>
    <div class="titlebar">
        <h1 class="text-center"><?php echo $title; ?></h1>
    </div>
</header>
<article ng-controller="userCtrl" style="padding-bottom: 120px;">
    <div style="display: flex;margin-bottom: 20px;" ng-click="goToUrl('user_info')">
        <img ng-if="user_info.avatar_path == null" ng-src="{{ SITE_URL + 'source/img/default-user.png' }}" alt="" class="avatar-img">
        <img ng-if="user_info.avatar_path != null" ng-src="{{ SITE_URL + user_info.avatar_path }}" alt="" class="avatar-img">
        <img ng-if="is_sign == true && user_info.level_icon_path" ng-src="{{ SITE_URL + user_info.level_icon_path }}" alt="" class="level-icon">
        <div ng-if="is_sign == true" class="item-info">
            <p>
                <span>{{user_info.nickname}}</span>
                <span ng-if="user_info.level_name != null" class="level-flag">{{user_info.level_name}}</span>
            </p>
            <p>
                <span>账号：{{user_info.username}}</span>
            </p>
        </div>
        <div ng-if="is_sign == false" class="item-info">
            <p>
                <span><a href="{{ SITE_URL + 'weixin/index/sign_in' }}" style="color: #666666;">未登录，去登录</a></span>
            </p>
            <p>
                <span><a href="{{ SITE_URL + 'weixin/index/sign_up' }}" style="color: #117D94;">去注册</a></span>
            </p>
        </div>
        <a href="" class="right-arraow"><i class="icon size16 icon-arrowright"></i></a>
    </div>
    <div ng-click="goToUrl('integral_detail')">
        <span><img ng-src="{{ SITE_URL + 'source/mobile/img/icon/badge.png' }}" alt="" class="item-icon"></span>
        <span>积分详情</span>
        <span class="float-right"><i class="icon size16 icon-arrowright"></i></span>
    </div>
    <hr>
    <div ng-click="goToUrl('receipt_address')">
        <span><img ng-src="{{ SITE_URL + 'source/mobile/img/icon/position.png' }}" alt="" class="item-icon"></span>
        <span>收货地址</span>
        <span class="float-right"><i class="icon size16 icon-arrowright"></i></span>
    </div>
    <hr>
    <div ng-click="goToUrl('collection_list')">
        <span><img ng-src="{{ SITE_URL + 'source/mobile/img/icon/collection.png' }}" alt="" class="item-icon"></span>
        <span>我的收藏</span>
        <span class="float-right"><i class="icon size16 icon-arrowright"></i></span>
    </div>
    <hr>
    <div ng-click="goToUrl('order_list')">
        <span><img ng-src="{{ SITE_URL + 'source/mobile/img/icon/file.png' }}" alt="" class="item-icon"></span>
        <span>订单查看</span>
        <span class="float-right"><i class="icon size16 icon-arrowright"></i></span>
    </div>
    <hr>
    <div ng-click="goToUrl('member_center')">
        <span><img ng-src="{{ SITE_URL + 'source/mobile/img/icon/crown.png' }}" alt="" class="item-icon"></span>
        <span>会员中心</span>
        <span class="float-right"><i class="icon size16 icon-arrowright"></i></span>
    </div>
    <hr>
    <div ng-click="goToUrl('change_password')">
        <span><img ng-src="{{ SITE_URL + 'source/mobile/img/icon/clock.png' }}" alt="" class="item-icon"></span>
        <span>密码修改</span>
        <span class="float-right"><i class="icon size16 icon-arrowright"></i></span>
    </div>
    <hr>
    <div ng-click="goToUrl('discount_coupon')">
        <span><img ng-src="{{ SITE_URL + 'source/mobile/img/icon/label.png' }}" alt="" class="item-icon"></span>
        <span>我的优惠券</span>
        <span class="float-right"><i class="icon size16 icon-arrowright"></i></span>
    </div>
    <hr>
    <div ng-click="goToUrl('my_report')">
        <span><img ng-src="{{ SITE_URL + 'source/mobile/img/icon/file.png' }}" alt="" class="item-icon"></span>
        <span>我的报告</span>
        <span class="float-right"><i class="icon size16 icon-arrowright"></i></span>
    </div>
    <hr>
    <div ng-click="goToUrl('my_family')">
        <span><img ng-src="{{ SITE_URL + 'source/mobile/img/icon/my_family.png' }}" alt="" class="item-icon"></span>
        <span>我的家族</span>
        <span class="float-right"><i class="icon size16 icon-arrowright"></i></span>
    </div>
    <hr>
    <div ng-click="goToUrl('my_city')">
        <span><img ng-src="{{ SITE_URL + 'source/mobile/img/icon/home.png' }}" alt="" class="item-icon"></span>
        <span>我的城</span>
        <span class="float-right"><i class="icon size16 icon-arrowright"></i></span>
    </div>
    <hr>
    <div ng-if="is_sign != false">
        <button id="confirm_submit" class="radius4 button block submit-btn margin8" ng-click="sigh_out()" style="background-color: #d43f3a; width: 95%;">
            退出登录
        </button>
    </div>
</article>