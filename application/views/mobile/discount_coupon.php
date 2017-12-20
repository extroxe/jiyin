<div ng-controller="discountCouponCtrl">
    <header>
        <div class="titlebar">
            <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
            <h1 class="text-center"><?php echo $title;?></h1>
        </div>
    </header>
    <article>
        <ul class="tabbar tabbar-line animate underline" data-type="slide">
            <li class="tab active" ng-click="select_nav('receive', $event)">
                <label class="tab-label">可领取</label>
            </li>
            <li class="tab" ng-click="select_nav('use', $event)">
                <label class="tab-label">可使用</label>
            </li>
            <li class="tab" ng-click="select_nav('expired', $event)">
                <label class="tab-label">已过期</label>
            </li>
        </ul>
        <div class="item-content" ng-if="flag == 'receive'">
            <div ng-repeat="pulished_discount_coupon in pulished_discount_coupons" ng-class="pulished_discount_coupon.user_discount_coupon_id == null ? 'item receive' : 'item received'">
                <div class="discount-privilege">¥ <span>{{pulished_discount_coupon.privilege}}</span></div>
                <div class="discount-condition">
                    <p>满{{pulished_discount_coupon.condition}}使用</p>
                    <p>优惠券</p>
                </div>
                <div class="discount-btn">
                    <button class="button" ng-if="pulished_discount_coupon.user_discount_coupon_id == null" ng-click="receiveDiscountCoupon(pulished_discount_coupon.id)">点击领取</button>
                    <button class="button" ng-if="pulished_discount_coupon.user_discount_coupon_id != null" disabled>已领取</button>
                </div>
                <div class="discount-bottom">使用时间 : {{pulished_discount_coupon.start_time | substring : 0 : 10}} 至 {{pulished_discount_coupon.end_time | substring : 0 : 10}}</div>
            </div>
        </div>
        <div class="item-content" ng-if="flag == 'use'">
            <div class="item" ng-repeat="discount_coupon in discount_coupons" ng-if="discount_coupon.status_id==1" ng-class="discount_coupon.status_id == 1 ? 'item receive' : 'item received'">
                <div class="discount-privilege">¥ <span>{{discount_coupon.privilege}}</span></div>
                <div class="discount-condition">
                    <p>满{{discount_coupon.condition}}使用</p>
                    <p>优惠券</p>
                </div>
                <div class="discount-btn" ng-if="discount_coupon.status_id == 1"><button class="button" ng-click="goUse()">去使用</button></div>
                <div class="discount-btn" ng-if="discount_coupon.status_id == 2"><button class="button">已使用</button></div>
                <div class="discount-bottom">使用时间 : {{discount_coupon.start_time | substring : 0 : 10}} 至 {{discount_coupon.end_time | substring : 0 : 10}}</div>
            </div>
        </div>
        <div class="item-content" ng-if="flag == 'expired'">
            <div class="item" ng-repeat="expired_coupon in expired_coupons" ng-if="expired_coupon.status_id==3" ng-class="expired_coupon.status_id == 1 ? 'item receive' : 'item received'">
                <div class="discount-privilege">¥ <span>{{expired_coupon.privilege}}</span></div>
                <div class="discount-condition">
                    <p>满{{expired_coupon.condition}}使用</p>
                    <p>优惠券</p>
                </div>
                <div class="discount-btn" ng-if="expired_coupon.status_id == 3"><button class="button">已过期</button></div>
                <div class="discount-bottom">使用时间 : {{expired_coupon.start_time | substring : 0 : 10}} 至 {{expired_coupon.end_time | substring : 0 : 10}}</div>
            </div>
        </div>
    </article>
</div>