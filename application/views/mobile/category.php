<div class="titlebar top_title" >
    <h1><?php echo $title; ?></h1>
</div>

<div id="page-content" class="home-page" ng-controller="categoryCtrl" style="padding-top: 35px" ng-init="parent.id = '<?php echo isset($parent_id) && intval($parent_id) > 0 ? $parent_id : ''; ?>'">
    <div class="group">
        <div class="tab_nav grid" data-col="2" >
            <ul class="slidebar nav_lists">
                <li ng-click="get_child(key, $event)" class="tab {{ $index == 0 ? 'active' : ''}}" ng-repeat="(key, category) in all_categorys" >
                    <label class="tab-label">{{category.type_name}}</label>
                </li>
            </ul>
            
            <div id="category_container">
                <ul>
                    <li class="second-category" ng-click="gotoCommodityList(child_category.id)" ng-repeat="child_category in child_categorys.category">
                        <details>
                            <hr>
                            <summary class="underline">
                                {{child_category.name}}
                                <i class="size16 icon icon-arrowright" ng-click="openChildCategory($event)"  style="color: #000"></i>
                            </summary>
                            <ul class="third-category">
                                <li ng-repeat="third_category in child_category.children_category">
                                    <a href="{{ SITE_URL + 'weixin/index/search_result?category=' + child_category.id }}">
                                        {{third_category.name}}
                                    </a>
                                </li>
                                <li class="no-more-commodity" ng-if="child_category.children_category.length == 0">
                                    无更多商品
                                </li>
                            </ul>
                        </details>
                    </li>
                </ul>
                <div ng-if="child_categorys == null" id="developing">
                    建设中尽情期待
                </div>
            </div>
        </div>
    </div>
    <!--    热门推荐-->
    <div class="discount">
        <div class="sliver underline">
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/hot2.png' }}">
            <p style="margin-left: 15px;color: #666;" class="sliver-title">热门推荐</p>
        </div>
        <div class="group hot" style="margin-top: 0; padding-bottom: 60px;">
            <ul class="grid" data-col="2" data-rowspace="8">
                <li ng-repeat="recommend in recommends">
                    <div class="product_desc" style="position: relative; background-color: #f9f9f9; padding-bottom: 8px">
                        <a href="{{ SITE_URL + 'weixin/index/commodity_detail/' + recommend.commodity_id + '/' + recommend.commodity_specification_id }}">
                            <div style="height: 199px">
                                <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/hot.png' }}">
                                <img ng-if="recommend.path !== null" ng-src="{{ SITE_URL + recommend.path }}">
                                <img ng-if="recommend.path === null" ng-src="{{ SITE_URL + 'source/mobile/img/photo.jpg' }}">
                            </div>
                        </a>
                        <p><a href="{{ SITE_URL + 'weixin/index/commodity_detail/' + recommend.commodity_id + '/' + recommend.commodity_specification_id }}">{{recommend.commodity_specification_name}}</a></p>
                        <span class="price">
                            <font style="font-size: 18px;">¥&nbsp;{{recommend.flash_sale_price != null ? recommend.flash_sale_price : recommend.price}}</font>
                            <font ng-if="recommend.market_price != null" style="font-size: 14px; color: #999; text-decoration: line-through">¥&nbsp;{{recommend.market_price}}</font>
                        </span>
                        <span class="sale" >{{recommend.sales_volume}}件已售</span>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>