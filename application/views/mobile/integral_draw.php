<div ng-controller="integralDrawCtrl">
    <div class="home-page" ng-init="user_id = '<?php echo isset($user_id) && !empty($user_id) ? $user_id : 0; ?>'">
        <header>
            <div class="titlebar">
                <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
                <h1 class="text-center" style="padding-right: 32px"><?php echo $title; ?></h1>
            </div>
        </header>

        <div class="slider-container" id="carousel1" style="height: 188px;">
            <div class="slider-wrapper" style="position:relative;">
                <?php if (isset($banner) && !empty($banner)):?>
                <?php foreach ($banner as $item):?>
                <div class="slider-slide" style="width: 414px;">
                    <a href="<?php echo $item['url'];?>"><img class="slide-banner" src="<?php echo site_url($item['path']); ?>"/></a>
                </div>
                <?php endforeach;?>
                <?php endif;?>
            </div>
            <div class="slider-pagination"></div>
        </div>
        <span class="my-integral" style="margin-top: 20px">我的积分：{{ user_info.current_point }}</span>
        <img ng-src="{{ SITE_URL + 'source/mobile/img/integral35.png' }}" id="shan-img" style="display:none;" />
        <img ng-src="{{ SITE_URL + 'source/mobile/img/2.png' }}" id="sorry-img" style="display:none;" />
<!--        <img class="img-path" ng-repeat="commodity_img in commodity_img_path" ng-src="{{ SITE_URL + commodity_img }}" id="{{$index}}" style="display:none;" />-->
        <div class="banner">
            <div class="turnplate" style="background-image:url(<?php echo site_url('source/mobile/img/turnplate-bg.png'); ?>);background-size:100% 100%;">
                <canvas class="item" id="wheelcanvas" width="422px" height="422px"></canvas>
                <img class="pointer" ng-src="{{ SITE_URL + 'source/mobile/img/turnplate-pointer.png' }}" ng-click="start_sweepstakes()"/>
            </div>
            <span class="my-award" ng-click="openPage('#my-awards')">我的奖品</span>
        </div>
        <div class="group awards-list" >
            <div id="oDiv">
                <ul id="oUl">
                    <li ng-repeat="scroll_prize in scroll_prizes">恭喜{{ scroll_prize.phone }}获得{{ scroll_prize.prize_name | interception}}</li>
                </ul>
            </div>
        </div>
        <a class="activity-rules-btn" href="#rules">活动规则<i class="icon icon-arrowdown"></i></a>
        <p id="rules" ng-bind-html="sweepstakes_rules | to_trusted"></p>
        <img class="back-top" ng-click="back_top()" ng-src="{{ SITE_URL + 'source/mobile/img/top.png'}}">
    </div>

    <section id="my-awards" data-animation="slideRight" class="page" style="z-index:100;background-color:#f4f4f4;">
        <header>
            <div class="titlebar">
                <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
                <h1 class="text-center" style="padding-right: 32px">我的奖品</h1>
            </div>
        </header>
        <article>
            <div>
                <ul class="grid" data-col="2" data-rowspace="8">
                    <li ng-repeat="prize in my_prizes">
                        <div>
                            <div class="group my-awards-lists" >
                                <img ng-if="prize.commodity_id != null" ng-src="{{ SITE_URL + prize.commodity_path}}">
                                <img ng-if="prize.commodity_id == null" ng-src="{{ SITE_URL + 'source/mobile/img/indiana_icon.png' }}">
                                <div class="prize-info">
                                    <p ng-if="prize.commodity_id != null" class="name">{{ prize.commodity_name }}</p>
                                    <p ng-if="prize.commodity_id == null" class="name">{{ prize.point }} 积分</p>
                                    <span class="time">{{ prize.create_time}}</span>
                                    <a class="button accept-prize" ng-click="accept_prize(prize.sweepstakes_commodity_id, prize.result_id)">去领奖</a>
                                    <!--                            <a ng-if="prize.status == 1" class="button accept-prize"">已领取</a>-->
                                </div>
                                <div class="mask-accept" ng-if="prize.status == 1">
                                    <p>已领取</p>
                                </div>
                                <div style="clear: both"></div>
                            </div>
                        </div>
                    </li>
                </ul>

            </div>
        </article>
    </section>
</div>