<header>
    <div class="titlebar search-report <?php echo $is_agent == 1 ? 'another_titlebar' : '';?>">
        <a class="titlebar-button" ng-click="back()">
            <i class="icon size16 icon-arrowleft"></i>
        </a>
        <h1 class="text-center"><?php echo $title; ?></h1>
    </div>
</header>
<article ng-controller="searchReportCtrl">
    <div class="group" style=" padding: 0 8px;">
        <img style="width: 100%" src="<?php echo site_url('source/mobile/img/search_report_banner.png');?>">
    </div>
    <div class="group index-info">
        <div class="inputbox underline" ng-click="fill_report_info()">
            <label class="inputbox-left" style="padding: 10px 0 10px 10px; width: 75px">报告编号：</label>
            <div class="inputbox-right inputbox">
                <input type="text" id="phone" class="input-text" ng-model="number"  placeholder="报告编号" style="padding:15px 12px;"/>
            </div>
        </div>
        <div class="inputbox underline">
            <label class="inputbox-left" style="padding: 10px 0 10px 10px; width: 75px">验证码：</label>
            <div class="inputbox-right inputbox">
                <input id="verification_code" type="text" class="input-text"  ng-model="verification_code" placeholder="验证码" style="padding:15px 12px;"/>
                <canvas id="canvas" width="120" height="40"></canvas>
                <img style="width: 20px;vertical-align: super;margin-right: 12px;" id="changeImg" src="<?php echo site_url('source/mobile/img/icon/refresh.png');?>">
            </div>
        </div>
    </div>
    <div class="group footer bottom-btn">
        <div style="line-height: 50px;padding: 4px 12px 8px 12px; text-align: center;">
            <a class="radius4 button block submit-btn margin8 <?php echo $is_agent == 1 ? 'another_button' : '';?>" ng-click="search_report_info('#search_result')">
                <label>查询</label>
            </a>
        </div>
    </div>

    <section id="search_result" data-animation="slideRight" class="page" style="background-color:#F9F9F9;position: fixed;">
        <header>
            <div class="titlebar my-report <?php echo $is_agent == 1 ? 'another_titlebar' : '';?>">
                <a class="titlebar-button" ng-click="backPage('#search_result', '.search-report')"><i class="icon size16 icon-arrowleft back_btn"></i></a>
                <h1 class="text-center">查询结果</h1>
            </div>
        </header>
        <article style="padding-bottom: 0">
            <div class="item" >
                <div class="item-title">
                    <?php echo $is_agent == 1 ? '<img ng-src="{{ SITE_URL + \'source/mobile/img/code-10.png\' }}">' : '<img ng-src="{{ SITE_URL + \'source/mobile/img/code-1.png\' }}">';?>
                    <p>{{ report_info.number }}</p>
                </div>
                <hr>
                <div class="item-content">
                    <div>
                        <p class="item-name">检测人：<span>{{ report_info.name ? report_info.name : '**' }}</span></p>
                        <p class="item-name">更新时间：<span>{{ report_info.update_time }}</span></p>
                        <p class="item-name">套餐名称：<span>{{ report_info.template_name ? report_info.template_name : '线下商品' }}</span></p>
                        <p class="item-name">检测机构：<span>上海赛安基因科技有限公司</span></p>
                    </div>
                </div>
                <p ng-if="report_info.path == null" class="pdf-btn " style="background: #ccc">待出报告</p>
                <p ng-if="report_info.path != null" class="pdf-btn" style="background: <?php echo $is_agent == 1 ? '#44b9fc' : '#117d94';?>"  ng-click="watch_pdf(report_info)">查看报告</p>
                <!--                <a ng-if="report_info.path != null" class="pdf-btn" style="background: --><?php //echo $is_agent == 1 ? '#44b9fc' : '#117d94';?><!--"  href="{{SITE_URL + report_info.path}}">查看报告</a>-->

            </div>
        </article>
    </section>
    <section id="watch_pdf" data-animation="slideRight" class="page" style="background-color:#F9F9F9;position: fixed;">
        <header>
            <div class="titlebar my-report <?php echo $is_agent == 1 ? 'another_titlebar' : '';?>">
                <a class="titlebar-button" ng-click="backPdfList()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
                <h1 class="text-center">查看报告</h1>
            </div>
        </header>
        <article style="background: #f5f5f5">
            <div id="myPDF">
            </div>
        </article>
    </section>
</article>