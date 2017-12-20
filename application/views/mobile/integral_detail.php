<header>
    <div class="titlebar">
        <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
        <h1 class="text-center"><?php echo $title; ?></h1>
    </div>
</header>
<article style="padding-top: 0;" ng-controller="integralDetailCtrl">
    <div class="integral-bg">
        <span>可用积分</span>
        <p>{{ user_info.current_point }}<span>点</span></p>
    </div>
    <div class="item-block" style="margin-top: 0;">
<!--        <div ng-click="goIntegralHome()">-->
        <div ng-click="goIntegralExchange()">
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/mall.png' }}" alt="">
            <p>积分商城</p>
            <p>积分消费请点我</p>
        </div>
        <div ng-click="goIntegralHome()">
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/money-o.png' }}" alt="">
            <p>获得积分</p>
            <p>积分获取请点我</p>
        </div>
    </div>
</article>