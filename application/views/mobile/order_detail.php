<article ng-controller="orderDetailCtrl" ng-init="order_id = '<?php echo isset($order_id) && intval($order_id) ? $order_id : ''; ?>'">
    <div class="titlebar">
        <a class="titlebar-button" ng-click="back()"><i style="color: #eee" class="icon icon-arrowleft back_btn"></i></a>
        <h1 class="order_detail"><?php echo $title; ?></h1>
    </div>
    <div class="group">
        <div class="order_title">
            <label class="order_num">订单编号：</label>
            <span>{{order.number}}</span>
            <span class="done">{{order.order_status_name}}</span>
        </div>
    </div>
    <div class="group receiving">
        <div class="receiving_info">
            <div class="user_info">
                <label  style="color: #777;">收货人：</label>
                <span style="font-size: 14px">{{order.address.name}}</span>
                <span class="tel">{{order.address.phone}}</span>
            </div>
            <div class="user_addr">
                <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/address.png' }}">
                <span class="addr_detail">{{order.address.province}} {{order.address.city}} {{order.address.district}} {{order.address.address}}</span>
            </div>
        </div>
    </div>
    <div class="group card">
        <div class="inputbox" ng-repeat="item in order.sub_orders">
            <img ng-if="item.thumbnail_path !== null" ng-click="commodity_detail(item.commodity_id, item.commodity_specification_id)"  ng-src="{{ SITE_URL + item.thumbnail_path }}">
            <img ng-if="item.thumbnail_path == null" ng-click="commodity_detail(item.commodity_id, item.commodity_specification_id)" ng-src="{{ SITE_URL + 'source/mobile/img/photo.jpg' }}">
            <div class="info-box">
                <div class="info-title" ng-click="commodity_detail(item.commodity_id, item.commodity_specification_id)">
                    {{item.commodity_name}}
                </div>
                <div class="info-price" ng-if="item.is_point == 0">
                    <span>¥ </span>
                    <span>{{item.price}}</span>
                </div>
                <div class="info-price" ng-if="item.is_point == 1">
                    <span>{{item.price}}</span>
                    <span>积分</span>
                </div>
            </div>
            <div class="item-num">
                <span>X</span>
                <span ng-if="item.is_point == 0" style="font-size: 24px;">{{item.amount}}</span>
                <span ng-if="item.is_point == 1" style="font-size: 24px;">1</span>
            </div>
            <button class="button apply-refund" ng-click="applyRefund(order.id, item, $event)">申请退款</button>
        </div>
    </div>
    
    <div class="group info_box">
        <div class="inputbox span_right underline">
            <label class="inputbox-left">支付方式</label>
            <span>{{order.payment_type_name}}</span>
        </div>
        <div class="inputbox span_right underline">
            <label class="inputbox-left">留言</label>
            <span>{{order.message}}</span>
        </div>
        <div class="underline commodity_info">
            <ul>
                <li><label>商品数量</label><label>x{{commodity_total_amount}}</label></li>
                <li ng-if="order.sub_orders[0].is_point == 0"><label>商品总额</label><label>¥{{order.total_price}}</label></li>
                <li ng-if="order.sub_orders[0].is_point == 1"><label>积分总额</label><label>{{order.total_price}}</label></li>
                <li ng-if="order.sub_orders[0].is_point == 0"><label>已优惠</label><label>¥{{order.total_price - order.payment_amount}}</label></li>
            </ul>
        </div>
        <div class="total_time underline">
            <ul>
                <li ng-if="order.sub_orders[0].is_point == 0"><label class="total_head">合计：</label><span class="total_price">¥{{order.payment_amount}}</span></li>
                <li ng-if="order.sub_orders[0].is_point == 1"><label class="total_head">合计：</label><span class="total_price">{{order.payment_amount}} 积分</span></li>
                <li><label>下单时间：</label><span style="color: #888">{{order.create_time}}</span></li>
            </ul>
        </div>
    </div>
    <div class="group button_footer">
        <div>
            <input ng-if="order.status_id == 60 || order.status_id == 80 || order.status_id == 90" type="button" class="review button lrpadding8" ng-click="evaluate_order()" value="评价晒单"/>
            <input ng-if="order.status_id == 10 && order.sub_orders[0].is_point == 0" type="button" class="buy_again button lrpadding8"  ng-click="cancel_order(order.id, order.status_id)" value="取消订单"/>
            <input ng-if="order.status_id != 10 && order.sub_orders[0].is_point == 0" type="button" class="buy_again button lrpadding8"  ng-click="add_shopping_cart(order.sub_orders)" value="再次购买"/>
            <input ng-if="order.status_id != 10 && order.sub_orders[0].is_point == 1" type="button" class="buy_again button lrpadding8"  ng-click="exchange_again(order.sub_orders[0].commodity_id)" value="再次兑换"/>
            <input ng-if="order.status_id == 10" type="button" class="buy_again button lrpadding8" style="color: #d9534f; border-color: #d9534f" ng-click="pay()" value="立即支付"/>
        </div>
    </div>

    <section id="page_2" data-animation="slideRight" class="page" style="background: rgba(111,111,111,.6);">
        <article style="background: #fff;">
            <div class="commodity-detail">
                <div class="img">
                    <img ng-if="refundCommodity.commodityPath" ng-src="{{SITE_URL + refundCommodity.commodityPath}}" alt="">
                    <img ng-if="!refundCommodity.commodityPath" ng-src="{{SITE_URL + 'source/mobile/img/photo.jpg'}}" alt="">
                </div>
                <div class="info">
                    <span>{{refundCommodity.price}}</span>
                    <span style="width: 90%;">{{refundCommodity.commodityName}}</span>
                </div>
                <span class="close" ng-click="closePage()">X</span>
            </div>
            <div class="group">
                <div class="inputbox underline">
                    <label class="inputbox-left" style="padding: 10px 0 10px 12px;">下单价格</label>
                    <div class="inputbox-right inputbox">
                        <input type="text" class="input-text" class="job" readonly ng-model="refundCommodity.price" style="padding:15px 12px;color: #777;"/>

                    </div>
                </div>
                <div class="inputbox underline">
                    <label class="inputbox-left" style="padding: 10px 0 10px 12px;">可退款数量</label>
                    <div class="inputbox-right inputbox">
                        <input type="text" class="input-text" class="company" readonly ng-model="refundCommodity.refundAvailable" style="padding:15px 12px;color: #777;"/>
                    </div>
                </div>
                <div class="inputbox underline">
                    <label class="inputbox-left" style="padding: 10px 0 10px 12px;">退款数量</label>
                    <div class="inputbox-right inputbox">
                        <input type="text" class="input-text" class="school" placeholder="退款数量" ng-model="refundInfo.amount" style="padding:15px 12px;"/>
                    </div>
                </div>
                <div class="inputbox underline">
                    <label class="inputbox-left" style="padding: 10px 0 10px 12px;">退款理由</label>
                    <div class="inputbox-right inputbox">
                        <input type="text" class="input-text text-center" ng-model="refundInfo.reason" placeholder="退款理由" readonly="readonly" id="ID-Sp"/>
                    </div>
                </div>
            </div>
            </div>
        </article>
        <button class="confirm" ng-click="confirm()">确定</button>
    </section>
</article>

