<div ng-controller="exchangeIntegralCtrl">
    <header>
        <div class="titlebar">
            <h1 class="text-center"><?php echo $title; ?></h1>
        </div>
    </header>
    <article ng-init="user.user_id = '<?php echo isset($user_id) && intval($user_id) ? $user_id : ''; ?>'">
        <div class="slider-container" id="carousel1">
            <div class="slider-wrapper">
                <?php if (!empty($banner)): ?>
                <?php foreach ($banner as $row) : ?>
                <div class="slider-slide">
                    <a href="<?php echo $row['url'];?>"><img class="slide-banner" src="<?php echo site_url($row['path']); ?>"/></a>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="slider-pagination"></div>
        </div>
        <div class="item-info">
            <span>我的积分：<span class="price-color">{{user_info.current_point}}</span></span>
            <span class="float-right"><a href="{{ SITE_URL + 'weixin/integral' }}" style="color: #666666;">获取积分 <i class="icon size20 icon-arrowright" style="padding-bottom: 3px;"></i></a></span>
        </div>
        <div class="group activity">
            <ul class="grid" data-col="2" data-rowspace="8">
                <li ng-click="toIndex('indiana')">
                    <a class="size60 bg-1">
                        <img ng-src="<?php echo site_url('source/mobile/img/u1010.png'); ?>">
                    </a>
                    <label>积分夺宝</label>
                </li>
                <li ng-click="toIndex('draw')">
                    <a class="size60 bg-2">
                        <img ng-src="<?php echo site_url('source/mobile/img/u1012.png'); ?>">
                    </a>
                    <label>积分抽奖</label>
                </li>
            </ul>
        </div>
        <div class="item-title sliver underline">
            <span class="peg"></span>
            <p class="sliver-title">今日必兑</p>
        </div>

        <div class="group" style="margin: 1px 0 60px 0;">
            <!--    热门推荐-->
            <div class="discount">
                <div class="group hot" style="margin-top: 0; margin-bottom: 0;">
                    <ul class="grid" data-col="2" data-rowspace="8">
                        <li ng-repeat="exchange_commodity in exchange_commoditys">
                            <div class="product_desc">
                                <div>
                                    <a href="{{ SITE_URL + 'weixin/integral/commodity_detail/' + exchange_commodity.commodity_id + '/' + exchange_commodity.commodity_specification_id }}">
                                        <img ng-if="exchange_commodity.path === null" ng-src="{{ SITE_URL + 'source/mobile/img/photo.jpg' }}">
                                        <img ng-if="exchange_commodity.path !== null" ng-src="{{ SITE_URL + exchange_commodity.path }}">
                                    </a>
                                </div>
                                <p ng-click="commodityDetail(exchange_commodity)">{{exchange_commodity.commodity_name}}</p>
                                <span class="integral_change">兑换：<span class="price" >{{exchange_commodity.price | trans_int }} 积分</span></span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </article>
