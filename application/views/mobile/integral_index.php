<article ng-controller="integralCtrl">
<div class="titlebar top_title" >
    <h1><?php echo $title; ?></h1>
</div>
<div class="group">
    <div class="slider-container" id="carousel1" style="margin-top: -8px; background-color: #f9f9f9">
        <div class="slider-wrapper">
            <?php if (!empty($banner)): ?>
                <?php foreach ($banner as $row) : ?>
                    <div class="slider-slide">
                        <a href="<?php echo $row['url']; ?>"><img style="height: 100%" class="slide-banner" src="<?php echo site_url($row['path']); ?>"/></a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="slider-pagination"></div>
    </div>
</div>
<div class="group">
    <!--    热门推荐-->
    <div class="discount">
        <div class="sliver underline">
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/hot2.png' }}">
            <p style="margin-left: 15px; color: #888" class="sliver-title">本周热换</p>
        </div>
        <div class="group hot" style="margin-top: 0">
            <ul class="grid" data-col="2" data-rowspace="8">
                <li ng-repeat="exchange_commodity in exchange_commoditys">
                    <div class="product_desc">
                        <a href="{{ SITE_URL + 'weixin/integral/commodity_detail/' + exchange_commodity.commodity_id + '/' + exchange_commodity.commodity_specification_id}}">
                            <div>
                                <img ng-if=" exchange_commodity.path !== null" ng-src="{{ SITE_URL + exchange_commodity.path }}">
                                <img ng-if=" exchange_commodity.path === null" ng-src="{{ SITE_URL + 'source/mobile/img/photo.jpg' }}">
                            </div>
                        </a>
                        <p ng-click="commodityDetail(exchange_commodity)">{{ exchange_commodity.commodity_name }}</p>
                        <span class="integral_change">兑换：<span class="price" >{{ exchange_commodity.price | trans_int }} 积分</span></span>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
</article>