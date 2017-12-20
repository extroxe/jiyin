<div ng-controller="commodityDetailCtrl">
    <header>
        <div class="titlebar">
            <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn" style="margin-top: 0px;"></i></a>
            <h1 class="text-center"><?php echo $title; ?></h1>
            <a class="titlebar-button" href="javascript:void(0)" ng-click="goShoppingCart()">
                <img class="titlebar-shopping-cart" ng-src="{{ SITE_URL + 'source/mobile/img/icon/shopping-cart-o.png' }}" alt="">
                <span class="tip shopping-cart" ng-if="cart_num > 0">{{ cart_num }}</span>
            </a>
        </div>
    </header>
    <article class="parent-detail" ng-init="commodity.id = '<?php echo isset($commodity_id) && intval($commodity_id) > 0 ? $commodity_id : ''; ?>'; commodity.pack_id = '<?php echo isset($commodity_specification_id) && intval($commodity_specification_id) > 0 ? $commodity_specification_id : ''; ?>'">
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
                <p class="title-commodity">{{ commodity.commodity_specification_name }}</p>
                <div class="intro-commodity" ng-bind-html="commodity.introduce | to_trusted"></div>
            </div>
            <div style="width: 18%;display: inline-block;text-align: center; position: relative">
                <i class="icon size32 icon-heart favorite" ng-click="favorite()" ng-if="!favorite_flag"></i>
                <i class="icon size32 icon-heart-fill favorite" ng-click="favorite()" ng-if="favorite_flag"></i>
            </div>
            <span class="price-span" style="font-size: 14px;background-color:#F6BF00;color: #FFF;padding: 1px 4px;border-radius: 3px" ng-if="userType">{{agent_name}}尊享价</span>
            <span ng-if="specification_pack_id == '' && commodity_pack_id == '' && commodity.is_point == '0'">
                <span class="price-span" ng-if="commodity.is_point === '0' && userType == null"><font style="font-size: 18px; margin-right: -3px;">¥</font> {{ commodity.flash_sale_price ? commodity.flash_sale_price : commodity.price }}</span>
                <span class="price-span" style=" color: #F6BF00" ng-if="commodity.is_point === '0' && userType"><font style="font-size: 18px; color: #F6BF00; margin-right: -3px;">¥</font> {{ commodity.agent_price }}</span>
                <span class="price-span original_price" ng-if="commodity.is_point === '0' && commodity.market_price "><font style="font-size: 12px;">¥</font> {{ commodity.market_price }}</span>
            </span>
            <span class="price-span" ng-if="(specification_pack_id != '' || commodity_pack_id != '') && commodity.is_point == '0'">
                <font ng-if="userType == null" style="font-size: 18px;">¥</font>{{userType?'':specification_pack_price}}
                <span ng-if="commodity.market_price != null && userType == null" style="font-weight: normal; color: #999; text-decoration: line-through; font-size: 16px;">
                    <font style="font-size: 13px;">¥</font>{{commodity.market_price}}
                </span>
                <span ng-if="userType" >
                    <font style="font-size: 13px;">¥</font>{{commodity.agent_price}}
                </span>
            </span>
            <span class="price-span" ng-if="commodity.is_point === '1'">
                <span ng-if="specification_pack_id == '' && commodity_pack_id == ''"> {{commodity.price | trans_int}}<span class="inline-span"> 积分</span></span>
                <span class="price-span" ng-if="specification_pack_id != '' || commodity_pack_id != ''">{{specification_pack_price}} <font style="font-size: 15px">积分</font>
                <span ng-if="commodity.market_price != null && commodity.market_price != '0.00'" style="font-weight: normal; color: #999; text-decoration: line-through; font-size: 16px;">{{commodity.market_price}} 积分</span>
            </span>
            </span>
            <span class="salves-item">销量：<span class="inline-span">{{ commodity.sales_volume }}笔</span></span>
        </div>
        <hr>
        <div class="item-info item-num" style="position:relative;">
            <span>已选规格：</span>
            <span>{{specification_pack_name}}</span>
            <span style="font-size: 23px;position: absolute;right: 20px;top: 21px;" ng-click="openPage()" >···</span>
        </div>
        <hr>
        <div class="item-info item-num">
            <span>购买数量：</span>
            <div class="numbox bordered margin8">
                <input type="button" class="button grayscale outline" value="-" ng-click="sub_num()" />
                <input type="number" class="input-text" ng-model="num"/>
                <input type="button" class="button grayscale outline" value="+" ng-click="add_num()" />
            </div>
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
    <div class="commodity-detail-footer" >
        <div class="add-cart" ng-click="add_cart()">
            <label>加入购物车</label>
        </div>
        <div class="buy-directly" ng-click="buy_direct()">
            <label>立即购买</label>
        </div>
    </div>
    <section  id="page_2" data-animation="slideRight" class="page" style="background: rgba(111,111,111,.6);">
        <article>
            <div class="commodity-detail">
                <div class="img">
                    <img ng-if="commodity_path" ng-src="{{SITE_URL + commodity_path}}" alt="" style="width: 100%">
                    <img ng-if="!commodity_path" ng-src="{{SITE_URL + 'source/mobile/img/photo.jpg'}}" alt="" style="width: 100%">
                </div>
                <div class="info">
                    <span ng-if="commodity.is_point == 0 && userType == null">￥{{commodity_price}}</span>
                    <span ng-if="commodity.is_point == 0 && userType">￥{{agent_price}}</span>
                    <span ng-if="commodity.is_point == 1">{{commodity_price}} 积分</span>
                    <span>{{commodity.commodity_specification_name}}</span>
                </div>
                <span class="close" ng-click="closePage()">X</span>
            </div>
            <div class="specification-pack">
                <div class="specification">
                    <p>规格</p>
                    <div>
                        <button class="specification-btn select-specification" data-id="{{data.commodity_center_id}}" ng-class="{'active': data.hasActive,'disabled': data.hasnopack}" ng-disabled="data.hasnopack" ng-click="select_specification($event, data, '')" ng-repeat="data in specification">{{data[0].name || data[0].commodity_specification_name}}</button>
                    </div>
                </div>
                <div class="pack">
                    <p>包装</p>
                    <div>
                        <button class="se-btn jinz select-pack" ng-class="{'disabled': hasnojinz}" ng-disabled="hasnojinz" ng-click="select_pack($event, '')">精装</button>
                        <button class="se-btn jianz select-pack"  ng-class="{'disabled': hasnojianz}" ng-disabled="hasnojianz" ng-click="select_pack($event, '')">简装</button>
                    </div>

                </div>
            </div>
        </article>
        <button class="confirm" ng-if="selectSpecification" ng-click="confirm_pack()">加入购物车</button>
        <button class="confirm" ng-if="!selectSpecification" ng-click="confirm_pack()">确定</button>
    </section>

    <div class="commodity-detail-footer" ng-if="commodity.is_point == 1">
        <div class="exchange-directly" ng-click="exchange()">
            <label>立即兑换</label>
        </div>
    </div>
    <div class="commodity-detail-footer" ng-if="commodity.is_point == 1">
        <div class="exchange-directly" style="background-color: #b9b9b9;">
            <label>当前积分不够</label>
        </div>
    </div>
</div>