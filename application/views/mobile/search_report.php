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
<!--        <div class="inputbox underline">-->
<!--            <label class="inputbox-left" style="padding: 10px 0 10px 10px; width: 75px">身份证号：</label>-->
<!--            <div class="inputbox-right inputbox">-->
<!--                <input type="text" class="input-text code-result" ng-model="userInfo.card" placeholder="身份证号后四位(选填)" style="padding:15px 12px;"/>-->
<!--            </div>-->
<!--        </div>-->
        <div class="inputbox underline" ng-click="fill_report_info()">
            <label class="inputbox-left" style="padding: 10px 0 10px 10px; width: 75px">姓名：</label>
            <div class="inputbox-right inputbox">
                <input type="text" id="phone" class="input-text" ng-model="userInfo.name"  placeholder="姓名" style="padding:15px 12px;"/>
            </div>
        </div>
        <div class="inputbox underline" ng-click="fill_report_info()">
            <label class="inputbox-left" style="padding: 10px 0 10px 10px; width: 75px">手机号：</label>
            <div class="inputbox-right inputbox">
                <input type="text" id="phone" class="input-text"  placeholder="手机号" ng-model="userInfo.phone" style="padding:15px 12px;"/>
                </div>
        </div>
        <div class="inputbox underline">
            <label class="inputbox-left" style="padding: 10px 0 10px 10px; width: 75px">验证码：</label>
            <div class="inputbox-right inputbox">
                <input id="verification_code" type="text" class="input-text"  ng-model="userInfo.verification_code" placeholder="验证码" style="padding:15px 12px;"/>
                <button type="button" class="button lrpadding8 send_passcode <?php echo $is_agent == 1 ? 'another_send_passcode' : '';?>" ng-disabled="flag == true" ng-click="send_checkcode(userInfo)" >{{send_code_ope}}</button>
            </div>
        </div>
        <div class="inputbox underline">
            <label class="inputbox-left" style="padding: 10px 0 10px 10px; width: 75px">&nbsp;</label>
            <div class="inputbox-right inputbox" style="padding-right: 24px; color: #117d94; display: block; text-align: right">
                <a class="input-text"  ng-click="search_report_by_number()" style=" display:inline;padding-right: 0;" >使用样本编号查询</a>
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
                <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
                <h1 class="text-center">查询结果</h1>
            </div>
        </header>
        <article style="background: #eee">
            <div class="item" ng-repeat="report in reports" >
                <div class="item-title">
                    <?php echo $is_agent == 1 ? '<img ng-src="{{ SITE_URL + \'source/mobile/img/code-10.png\' }}">' : '<img ng-src="{{ SITE_URL + \'source/mobile/img/code-1.png\' }}">';?>
                    <p>{{ report.number }}</p>
                </div>
                <hr>
                <div class="item-content">
                    <div>
                        <p class="item-name">检测人：<span>{{ report.name ? report.name : '**' }}</span></p>
                        <p class="item-name">更新时间：<span>{{ report.update_time }}</span></p>
                        <p class="item-name">套餐名称：<span>{{ report.template_name ? report.template_name : '线下商品' }}</span></p>
                        <p class="item-name">检测机构：<span>上海赛安基因科技有限公司</span></p>
                    </div>
                </div>
                <p ng-if="report.attachment_id == null" class="pdf-btn " style="background: #ccc">待出报告</p>
                <p ng-if="report.attachment_id != null" class="pdf-btn" style="background: <?php echo $is_agent == 1 ? '#44b9fc' : '#117d94';?>"  ng-click="watch_pdf(report)">查看报告</p>

            </div>
        </article>

    </section>
</article>