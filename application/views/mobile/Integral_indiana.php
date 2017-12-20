<div ng-controller="IntegralIndianaCtrl">
    <div class="home-page">
        <header>
            <div class="titlebar">
                <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
                <h1 class="text-center" style="padding-right: 32px"><?php echo $title; ?></h1>
            </div>
        </header>
        <div class="group nav-list">
            <ul class="grid" data-col="3" data-rowspace="8">
                <li ng-click="openPage('#my_awards')">
                    <a class="size60 bg-1">
                        <img ng-src="{{ SITE_URL + 'source/mobile/img/u1564.png' }}">
                    </a>
                    <label>我的夺宝</label>
                </li>
                <li ng-click="openPage('#new-awards')">
                    <a class="size60 bg-2">
                        <img ng-src="{{ SITE_URL + 'source/mobile/img/u1568.png' }}">
                    </a>
                    <label>最新揭晓</label>
                </li>
                <li ng-click="openPage('#help-center')">
                    <a class="size60 bg-3">
                        <img ng-src="{{ SITE_URL + 'source/mobile/img/u1566.png' }}">
                    </a>
                    <label>帮助中心</label>
                </li>
            </ul>
        </div>
        <div class="group awards-list" ng-if="awards.length > 0">
            <img width="14" height="14" ng-src="{{ SITE_URL + 'source/mobile/img/u1578.png' }}">
            <div id="oDiv">
                <ul id="oUl">
                    <li style="font-size: 14px;" ng-repeat="award in awards">恭喜{{award.nickname || award.username}}获得{{award.commodity_name}}</li>
                </ul>
            </div>
        </div>

        <div class="group awards" style="padding: 10px 5px; background-color: #eee">
            <ul class="grid" data-col="2" data-rowspace="8">
                <li ng-if="activity_info.integral_indiana_status != 3" ng-repeat="activity_info in activity_infos">
                    <div>
                        <a class="bg-1" ng-click="indiana_detail(activity_info.commodity_specification_id)">
                            <img ng-if="activity_info.path != null" ng-src="{{ SITE_URL + activity_info.path }}">
                            <img ng-if="activity_info.path == null" ng-src="{{ SITE_URL + 'source/mobile/img/photo.jpg' }}">
                        </a>
                        <p class="publish-icon" ng-if="activity_info.integral_indiana_status == '2'">已结束</p>
                        <label>{{ activity_info.commodity_name }}</label>
                        <span ng-if="activity_info.amount_bet * activity_info.current_bet <= activity_info.total_points">{{ activity_info.amount_bet * activity_info.current_bet }}积分</span>
                        <span ng-if="activity_info.amount_bet * activity_info.current_bet > activity_info.total_points">{{ activity_info.total_points }}积分</span>
                        <div class="progressbox-content">
                            <div class="progress radius40">
                                <span class="progress-bar theme-1 radius40" style="max-width:100%;width: {{(activity_info.amount_bet * activity_info.current_bet) * 100 / activity_info.total_points}}%"></span>
                            </div>
                        </div>
                        <span>总需 {{ activity_info.total_points }} 积分</span>
                        <a href="javascript:void(0)" ng-if="activity_info.integral_indiana_status == '2'" class="publish-awards">已结束</a>
                        <a href="javascript:void(0)" ng-if="activity_info.integral_indiana_status == '1'" class="publish-awards" ng-click="indiana(activity_info.id, activity_info.amount_bet)">立即夺宝</a>
                    </div>
                </li>
            </ul>
        </div>
        <a class="activity-rules-btn" href="#rules">活动规则<i class="icon icon-arrowdown"></i></a>
        <p id="rules" ng-bind-html="indiana_rules | to_trusted"></p>
        <img class="back-top" ng-click="back_top()" ng-src="{{ SITE_URL + 'source/mobile/img/top.png'}}">
    </div>
    <section id="my_awards" data-animation="slideRight" class="page" style="z-index:100;background-color:#f4f4f4;">
        <header>
            <div class="titlebar">
                <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
                <h1 class="text-center" style="padding-right: 32px">我的夺宝</h1>
            </div>
        </header>
        <article>
            <div class="box">
                <ul class="grid" data-col="2" data-rowspace="8">
                    <li class="group my-awards-lists" ng-repeat="my_prize in my_prizes">
                        <div>
                            <img ng-src="{{ SITE_URL + my_prize.path}}">
                            <div class="prize-info">
                                <p class="name">{{ my_prize.commodity_name }}</p>
                                <span class="time">{{ my_prize.create_time }}</span>
                                <span ng-if="my_prize.integral_indiana_result_status == null" class="button accept-prize">未中奖</span>
                                <span ng-if="my_prize.integral_indiana_status == 1" class="button accept-prize">进行中</span>
                                <span ng-if="my_prize.integral_indiana_status == 2 && my_prize.integral_indiana_result_status == '0'" class="button accept-prize unknow-result">未揭晓</span>
                                <a ng-if="my_prize.is_win == true && my_prize.integral_indiana_result_status == '1'" class="button accept-prize go-accept" ng-click="accept_prize(my_prize.integral_indiana_id, my_prize.integral_indiana_bet_id)">去领奖</a>
                                <a ng-if="my_prize.is_win == true && my_prize.integral_indiana_result_status == '2'" class="button accept-prize go-accept" >已领取</a>
                            </div>
                            <div class="mask-accept" ng-if="my_prize.is_win == true && my_prize.integral_indiana_result_status == '2'">
                                <p>已领取</p>
                            </div>
                            <div style="clear: both"></div>
                        </div>
                    </li>
                    <li></li>
                </ul>
            </div>
        </article>
        <article ng-if="my_prizes == null" style="background-color: #f9f9f9; padding-bottom: 0" >
            <img style="width: 100%; height: 100%" ng-src="<?php echo site_url('source/mobile/img/no_prizes.png'); ?>">
            <img class="indiana" ng-click="back()" ng-src="<?php echo site_url('source/mobile/img/liji.png'); ?>">
        </article>
    </section>

    <section id="help-center" data-animation="slideRight" class="page" style="z-index: 100;background-color:#f4f4f4;">
        <header>
            <div class="titlebar">
                <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
                <h1 class="text-center" style="padding-right: 32px">帮助中心</h1>
            </div>
        </header>
        <article>
            帮助中心：
            一、玩法说明

            1、用户可根据自己的喜好商品选择要参与的人次并支付一定的礼券参与夺宝。

            5、大陆地区免奖品发货运费，如因用户问题产生额外费用的由用户自行承担；若快递无法配送至收货地址的，由用户自行联系快递进行自提；
            13、该抽奖活动与苹果公司无关。
        </article>
    </section>

    <section id="new-awards" data-animation="slideRight" class="page" style="z-index:100;background-color:#f4f4f4;">
        <header>
            <div class="titlebar">
                <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
                <h1 class="text-center" style="padding-right: 32px">最新揭晓</h1>
            </div>
        </header>
        <article>
            <div class="group" ng-repeat="award in awards">
                <p class="new-awards">
                    <span class="user-name">{{award.nickname || award.username}}</span>获得{{award.commodity_name}}
                </p>
            </div>
        </article>
    </section>
</div>


