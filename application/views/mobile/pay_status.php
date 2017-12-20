<header>
    <div class="titlebar">
        <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
        <h1 class="text-center"><?php echo $title; ?></h1>
    </div>
</header>
<article ng-controller="orderCtrl" ng-init="order.order_id = '<?php echo isset($order_id) && intval($order_id) > 0 ? $order_id : ''; ?>'">
    <div class="status-icon" ng-if="order_info.status_id === '20'">
        <div class="icon-container">
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/hook.png' }}" alt="">
        </div>
        <p ng-if="order_info.sub_orders[0].is_point === '0'">支付成功</p>
        <p ng-if="order_info.sub_orders[0].is_point === '1'">兑换成功</p>
    </div>
    <div class="status-icon" ng-if="order_info.status_id !== '20'">
        <div class="icon-container">
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/hook2.png' }}" alt="">
        </div>
        <p>支付状态确认中...</p>
        <p>请稍后查看订单状态</p>
    </div>
    <div class="pay-price" ng-if="order_info.sub_orders[0].is_point === '0'">
        <p>
            <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/rmb.png' }}" alt="">
            <span>{{order_info.total_price}}</span>
        </p>
        <p ng-if="order_info.points && agent_id == 0">订单完成后您将获得<span>{{order_info.points}}</span> 积分</p>
    </div>
    <div class="pay-price" ng-if="order_info.sub_orders[0].is_point === '1'">
        <p>
            <span>{{order_info.total_price}}积分</span>
        </p>
        <p ng-if="order_info.points">订单完成后您将获得<span>{{order_info.points}}</span> 积分</p>
    </div>
    <div class="item">
        <div>
            <span>收货人</span>
            <span>{{order_info.address.name}}</span>
        </div>
        <div>
            <span>联系方式</span>
            <span>{{order_info.address.phone}}</span>
        </div>
        <div>
            <span>订单编号</span>
            <span>{{order_info.number}}</span>
        </div>
        <div class="item-flex">
            <span>收货地址</span>
            <span>{{order_info.address.province}} {{order_info.address.city}} {{order_info.address.district}} {{order_info.address.address}}</span>
        </div>
    </div>
    <div class="item-footer">
        <button class="button lrpadding8 radius4"><a href="javascript:void(0)" ng-click="go_home()">返回商城</a></button>
        <button class="button lrpadding8 radius4"><a href="javascript:void(0)" ng-click="go_order_detail(order_info.id)">订单详情</a></button>
    </div>
</article>