<header>
    <div class="titlebar">
        <a class="titlebar-button" ng-click="back()">
            <i style="color: #eee" class="icon icon-arrowleft back_btn"></i>
        </a>
        <h1 class="text-center" style="margin-right: 48px"><?php echo $title; ?></h1>
    </div>
</header>
<article ng-controller="logisticsCtrl" ng-init="order.order_id = '<?php echo isset($order_id) && intval($order_id) ? $order_id : ''; ?>'">
    <div>
        <span style="margin-right: 10px;">物流状态</span>
        <span class="color-font">{{express_infos.StateName}}</span>
    </div>
    <div class="item-info">
        <img ng-if="details.sub_orders[0].thumbnail_path === null" ng-src="{{ SITE_URL + 'source/mobile/img/photo.jpg' }}" alt="">
        <img ng-if="details.sub_orders[0].thumbnail_path !== null" ng-src="{{ SITE_URL + details.sub_orders[0].thumbnail_path }}" alt="">
        <div>
            <span>订单编号：{{details.number}}</span>
            <span>下单时间：{{details.create_time}}</span>
            <span>承运公司：{{details.express_company_name}}</span>
        </div>
    </div>
    <div class="detail-title">
        <span style="margin-right: 10px;">运单编号</span>
        <span>{{details.express_number}}</span>
    </div>
    <div class="detail-content">
        <details open>
            <summary></summary>
            <ul class="timeline" style="padding-right: 0;" ng-if="traces.length <= 0">
                <li>
                    <div class="timeline-badge">
                        <span class="active-point"><i></i></span>
                    </div>
                    <div class="card box-flex-1 padding8 color-font" style="border: none;">
                        <p>暂无物流信息</p>
                    </div>
                </li>
            </ul>
            <ul class="timeline leftline" style="padding-right: 0;"  ng-if="traces.length > 0">
                <li ng-repeat="trace in traces" class="{{ ($index + 1) == traces.length ? 'endline' : '' }}">
                    <div class="timeline-badge">
                        <span class="{{ $index == 0 ? 'active-point' : 'point' }}"><i></i></span>
                    </div>
                    <div class="card box-flex-1 padding8 {{ $index == 0 ? 'color-font' : '' }}">
                        <p>{{trace.AcceptStation}}</p>
                        <p>{{trace.AcceptTime}}</p>
                    </div>
                </li>
            </ul>
        </details>
    </div>
</article>