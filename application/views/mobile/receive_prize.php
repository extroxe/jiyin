<div ng-controller="receiptPrizeCtrl" ng-init="commodity_id = '<?php echo isset($commodity_id) ? $commodity_id : 0; ?>';id = '<?php echo isset($id) ? $id : 0; ?>';is_indiana = '<?php echo isset($is_indiana) && $is_indiana ? 1 : 0; ?>'">
    <header style="z-index: -1;">
        <div class="titlebar">
            <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
            <h1 class="text-center" style="margin-right: 40px;"><?php echo $title; ?></h1>
        </div>
    </header>
    <article style="padding-top: 0;margin-top: 44px;">
        <div class="content-title">
            选择地址
        </div>
        <hr>
        <div class="address-info" style="margin-top: 30px;">
            <p class="address-user-info">
                <span>收货人:</span>
                <span>{{default_address.name}}</span>
                <span>{{default_address.phone}}</span>
            </p>
            <hr style="margin: 0 15px;">
            <p class="address-place">
                <span><img ng-src="{{ SITE_URL + 'source/mobile/img/icon/address.png' }}" alt=""></span>
                <span>{{default_address.province}}{{default_address.city}}{{default_address.district}}{{default_address.address}}</span>
                <span><i class="icon size18 icon-arrowright"></i></span>
            </p>
        </div>
        <a class="page-link-address" href="#page_address" data-toggle="page"></a>
        <div class="card">
            <div class="inputbox" style="display: flex;">
                <img ng-if="commodity.path != null" ng-src="{{ SITE_URL + commodity.path }}" alt="">
                <img ng-if="commodity.path == null" ng-src="{{ SITE_URL + 'source/mobile/img/photo.jpg' }}" alt="">
                <div class="info-box">
                    <div class="info-title" ng-bind="commodity.name"></div>
                    <div class="info-price" ng-if="commodity.is_point == 0">
                        <span>¥ </span>
                        <span ng-bind="commodity.price"></span>
                    </div>
                </div>
                <div class="item-num">
                    <span>X</span>
                    <span style="font-size: 20px;" >1</span>
                </div>
            </div>
            <hr style="margin: 0 15px;">
            <div class="inputbox">
                <span>配送方式：</span>
                <span class="item-span-right">顺丰快递</span>
            </div>
            <hr style="margin: 0 15px;">
            <div class="inputbox">
                <span>优惠方式：</span>
                <span class="item-span-right">奖品领取</span>
            </div>
        </div>
    </article>
    <div class="confirm-order-footer">
        <div class="confirm-btn" ng-click="confirm_order()">提交订单</div>
    </div>
    <section id="page_address" data-animation="slideRight" class="page" style="background-color:#F9F9F9;">
        <header>
            <div class="titlebar">
                <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft"></i></a>
                <h1 class="text-center">收货地址</h1>
                <a href="{{ SITE_URL + 'weixin/user/receipt_address' }}" style="color:#fff;">编辑</a>
            </div>
        </header>
        <article>
            <div class="address-item" ng-repeat="address_info in address_infos">
                <div class="item">
                    <div class="item-title">
                        <span>{{address_info.name}}</span>
                        <span>{{address_info.phone}}</span>
                    </div>
                    <div class="item-info">
                        <span>{{address_info.province}}{{address_info.city}}{{address_info.district}}{{address_info.address}}</span>
                    </div>
                </div>
                <hr>
                <div class="item">
                    <div class="item-operation">
                        <span ng-click="select_address(address_info)">
                            <input type="radio" class="input-radio" ng-if="address_info.default === '1'" ng-checked="true" name="address">
                            <input type="radio" class="input-radio" ng-if="address_info.default === '0'" ng-checked="false" name="address">
                        </span>
                        <span style="{{ address_info.default == 1 ? 'color: #117D94;' : '' }}">选择地址</span>
                        <span><a href="{{ SITE_URL + 'weixin/user/receipt_address' }}"></a></span>
                    </div>
                </div>
            </div>
        </article>
    </section>
</div>