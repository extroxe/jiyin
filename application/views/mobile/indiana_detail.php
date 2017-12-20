<div ng-controller="commodityDetailCtrl">
    <header>
        <div class="titlebar">
            <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn" style="margin-top: 0px;"></i></a>
            <h1 style="padding-right: 25px" class="text-center"><?php echo $title; ?></h1>
        </div>
    </header>
    <article ng-init="commodity.specification_id = '<?php echo isset($specification_id) && intval($specification_id) > 0 ? $specification_id : ''; ?>'">
        <div class="slider-container" id="carousel1">
            <div class="slider-wrapper">
                <?php if (!empty($thumbnails)) : ?>
                    <?php foreach ($thumbnails as $row) : ?>
                        <div class="slider-slide">
                            <img class="slide-banner" src="<?php echo site_url($row['pic_path']) ?>"/>
                        </div>
                    <?php endforeach; ?>
                <?php elseif (empty($thumbnails)) : ?>
                    <div class="slider-slide">
                        <img class="slide-banner" src="<?php echo site_url('source/mobile/img/photo.jpg'); ?>"/>
                    </div>
                <?php endif; ?>
            </div>
            <div class="slider-pagination"></div>
        </div>
        <div class="item-info">
            <div style="width: 80%;display: inline-block;">
                <p class="title-commodity">{{ commodity.commodity_name }}</p>
                <div class="intro-commodity" ng-bind-html="commodity.introduce | to_trusted"></div>
            </div>
            <div style="width: 18%;display: inline-block;text-align: center;">
                <i class="icon size32 icon-heart favorite" ng-click="favorite(commodity, $event)" ng-if="!favorite_flag"></i>
                <i class="icon size32 icon-heart-fill favorite" ng-click="favorite(commodity, $event)" ng-if="favorite_flag"></i>
            </div>
            <span class="price-span">{{commodity.price | trans_int}}<span class="inline-span"> 积分</span></span>
            <span class="salves-item">销量：<span class="inline-span">{{ commodity.sales_volume }}笔</span></span>
        </div>
        <hr>
        <ul class="tabbar tabbar-line animated">
            <li class="tab active">
                <label class="tab-label">商品详情</label>
            </li>
            <li class="tab" ng-click="commodity_evaluation()">
                <label class="tab-label">商品评价</label>
            </li>
        </ul>
        <div class="detail" ng-bind-html="commodity.detail | to_trusted"></div>
    </article>
    <div class="commodity-detail-footer" ng-if="commodity.is_point == 0">
        <div class="add-cart" ng-click="integral_indiana()">
            <label>立即夺宝</label>
        </div>
    </div>
</div>