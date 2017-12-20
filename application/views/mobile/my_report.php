<header>
    <div class="titlebar my-report">
        <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
        <h1 class="text-center"><?php echo $title; ?></h1>
    </div>
</header>
<article ng-controller="myReportCtrl" style="margin-bottom: 25px;">

    <div class="item" ng-repeat="report in reports" ng-if="report.report_status == '1'">
        <div class="item-title">订单编号：{{ report.order_number }}</div>
        <hr>
        <div class="item-content">
            <div>
                <p class="item-name">检测人：{{ report.name }}</p>
                <p>联系方式：<span>{{ report.phone }}</span></p>
                <p ng-if="report.attachment_id == null" style="color: #333;font-size: 13px;">检测报告正在制作中</p>
                <p ng-if="report.attachment_id != null"  ng-click="watch_pdf(report)">查看电子报告<i class="icon size20 icon-arrowright"></i></p>
            </div>
            <div>
                <img ng-src="{{ SITE_URL + 'source/mobile/img/code.png' }}">
                <p>{{ report.number }}</p>
            </div>
        </div>

    </div>
    <div ng-if="reports.length == 0" style="padding: 77px;text-align: center;">
        未查到相关报告！！
    </div>
    <div id='container'></div>
    <div class="group add-report">
        <div style="line-height: 50px;padding: 4px 12px 8px 12px;">
            <a class="radius4 button block margin8" href="<?php echo site_url('weixin/user/add_report');?>">
                <label style="color: #f9f9f9">添加新报告信息</label>
            </a>
        </div>
    </div>
</article>
