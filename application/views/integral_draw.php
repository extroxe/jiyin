<div id="page-content" class="home-page">
    <div class="container" style="background-color: #754c9a">
        <div class="top">
            <img style="margin-top: -1px; width: 100%" src="<?php echo site_url('source/img/prizes-banner.png');?>">
        </div>
        <div style="margin-top: 80px;">
            <div style="float: left;    margin-left: 10%;">
                <img src="<?php echo site_url('source/img/1.png'); ?>" id="shan-img" style="display:none;" />
                <img src="<?php echo site_url('source/img/2.png'); ?>" id="sorry-img" style="display:none;" />
                <div class="banner">
                    <div class="turnplate" style="background-image:url(<?php echo site_url('source/img/rotate.png'); ?>);background-repeat:no-repeat;background-size: 300px 300px;">
                        <canvas class="item" id="wheelcanvas" width="422px" height="422px"></canvas>
                     </div>

                </div>
                <img class="pointer" src="<?php echo site_url('source/img/turnplate-pointer.png'); ?>"/>
            </div>
            <div class="winning-record" style="float: left">
                <p class="time">活动时间：
                    <span class="on-activity">2017年12月12日 - 2018年12月12日</span>
                    <span class="no-activity">暂无抽奖活动</span>
                </p>
                <div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>中奖者</th>
                            <th>奖品</th>
                            <th>联系方式</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><div></div></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
        <div class="my-prizes-btn">
            <a class="btn btn-warning my-activity-record" href="<?php echo site_url('user/user_center/my_sweepstake'); ?>">查看我的活动纪录</a>
        </div>
        <div class="rules">
            <h4>抽奖规则</h4>
            <p> 1、用户可根据自己的喜好商品选择要参与的人次并支付一定的礼券参与夺宝。 <br>
                2、大陆地区免奖品发货运费，如因用户问题产生额外费用的由用户自行承担.<br>
                3、该抽奖活动与苹果公司无关。<br>
            </p>
        </div>
    </div>
</div>

<script id="get_scroll_prizes_tpl" type="text/html">

    <% for (var i = 0; i < data.length; i++) { %>
    <tr>
        <td><%:=data[i].nickname%></td>
        <td><%:=data[i].prize_name%></td>
        <td><%:=data[i].phone%></td>
    </tr>
    <% } %>
</script>