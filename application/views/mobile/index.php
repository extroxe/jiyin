<div class="titlebar top_title" >
    <h1><?php echo $title; ?></h1>
</div>
<div id="page-content" class="home-page" ng-controller="shoppingMallCtrl">
    <div class="slider-container" id="carousel1">
        <div class="slider-wrapper">
            <?php if (!empty($banner)): ?>
                <?php foreach ($banner as $row) : ?>
                    <div class="slider-slide">
                        <a href="<?php echo $row['url']; ?>"><img class="slide-banner" src="<?php echo site_url($row['path']); ?>"/></a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="slider-pagination"></div>
    </div>
    <ul class="tabbar tabbar-line animated">
        <li class="tab active">
            <label class="tab-label">全部</label>
        </li>
        <li class="tab" ng-repeat="parent_category in parent_categorys" ng-if="$index <= 3">
            <a href="{{ SITE_URL + 'weixin/index/category/' + parent_category.id }}" style="color: #333333"><label class="tab-label">{{parent_category.name}}</label></a>
        </li>
        <li id="icon_down" class="tab" style="color: #fff">
            <i class="size16 icon icon-arrowdown" style="color: #000"></i>
        </li>
    </ul>
    <details>
        <summary class="underline" style="display: none;">
            <span></span>
        </summary>
        <ul class="timeline leftline">
            <div class="table-box" >
                <div >
                    <table align="center" style="border: none">
                        <tbody>
                        <tr>
                            <td ng-repeat="parent_category in parent_categorys">
                                <a href="{{ SITE_URL + 'weixin/index/category/' + parent_category.id}}" style="color: #333333">{{parent_category.name}}</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </ul>
    </details>
    <div class="commodity_lists" style="margin-bottom: 60px; padding-bottom: 60px">
        <ul class="grid" data-col="2" data-rowspace="8">
            <li ng-repeat="recommend in recommends">
                <div class="lists_box">
                    <a href="{{ SITE_URL + 'weixin/index/commodity_detail/' + recommend.commodity_id + '/' + recommend.commodity_specification_id }}">
                        <div style="height: 199px">
                            <img ng-if="recommend.path !== null" ng-src="{{ SITE_URL + recommend.path }}">
                            <img ng-if="recommend.path === null" ng-src="{{ SITE_URL + 'source/mobile/img/photo.jpg' }}">
                        </div>
                    </a>
                    <p><a href="{{ SITE_URL + 'weixin/index/commodity_detail/' + recommend.commodity_id + '/' + recommend.commodity_specification_id }}">{{recommend.commodity_specification_name}}</a></p>
                    <span class="price">
                        ¥&nbsp;<font style="font-size: 18px;">{{recommend.price}}</font>
                        <font ng-if="recommend.market_price != null" style="text-decoration: line-through; color: #999; font-size: 14px;">¥&nbsp;{{recommend.market_price}}</font>
                    </span>
                    <span class="sales_volume">{{recommend.sales_volume}}件已售</span>
                </div>
            </li>
        </ul>
    </div>
</div>