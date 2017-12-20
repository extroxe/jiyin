<!doctype html>
<html>
<head lang="zh-cn">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <title>基因检测</title>
    <!-- 禁止屏幕缩放 -->
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="renderer" content="webkit">
    <!-- No Baidu Siteapp-->
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta name="viewport" content="width=device-width,minimum-scale=1,maximum-scale=1,initial-scale=1,user-scalable=no" />
    <link rel="alternate icon" type="image/png" href="<?=site_url('source/img/favicon.png')?>">
    <link rel="apple-touch-icon-precomposed" href="<?=site_url('source/img/favicon.png')?>">
    <meta name="apple-mobile-web-app-title" content="AMUI React">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="stylesheet" href="<?=site_url('source/assets/seedsui/scripts/lib/seedsui/seedsui.min.css')?>" media="screen">

    <!--    页面样式-->
    <style>
        body{
            background-color: #fff;
        }
        .group{
            margin-top: 0;
            background-color: #fff;
            text-align: center;
            /*height: 500px;*/
        }
        .group:before, .group:after{
            height: 0px;
        }

        .img-group{
            padding: 20px;
        }
        .img-group:first-child{
            padding: 20px 120px;
        }
        .img-group img{
            width: 90%;
        }
        .butt button{
            margin: 0 8px;
        }
    </style>

</head>
<body ng-app="app">
<div ng-controller="agentSearchCtrl">
    <div class="group">
        <div class="img-group">
            <img src="<?php echo site_url('source/mobile/img/taiping_logo.png');?>">
        </div>
        <div class="img-group">
            <img src="<?php echo site_url('source/mobile/img/taiping_people.png');?>">
        </div>
    </div>
    <div class="group butt" style="width: 100%;bottom: 0; margin: 0; margin-top: 20px">
        <div style="line-height: 50px;padding: 4px 12px 8px 12px; text-align: center;">
            <button class="radius4 button block submit-btn margin8 " style="width: 95%" ng-click="click('add_report/1/<?php echo time();?>', true)">
                <label>检测人信息登记</label>
            </button>
        </div>
    </div>
    <div class="group butt" style="width: 100%;bottom: 0; margin: 0;">
        <div style="line-height: 50px;padding: 4px 12px 8px 12px; text-align: center;">
            <button class="radius4 button block submit-btn margin8 " style="width: 95%" ng-click="click('search_report/1/<?php echo time();?>', true)">
                <label>检测报告查询</label>
            </button>
        </div>
    </div>
<!--    <div class="group butt" style="width: 100%;bottom: 0; margin: 0;">-->
<!--        <div style="line-height: 50px;padding: 4px 12px 8px 12px; text-align: center;">-->
<!--            <button class="radius4 button block submit-btn margin8 " style="width: 95%" ng-click="click('http://jycheck.sailwish.com', false)">-->
<!--                <label>太平乐享基因检测活动</label>-->
<!--            </button>-->
<!--        </div>-->
<!--    </div>-->
</div>

<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="<?=site_url('source/assets/angular/angular.min.js')?>"></script>
<script src="<?=site_url('source/admin/vendor/angular/angular-file-upload/angular-file-upload.min.js')?>"></script>
<script src="<?=site_url('source/admin/vendor/jquery/md5/spark-md5.js')?>"></script>
<script src="<?=site_url('source/mobile/js/app.js')?>"></script>
<script src="<?=site_url('source/mobile/js/dropload.min.js')?>"></script>
<script src="<?=site_url('source/assets/seedsui/scripts/lib/seedsui/seedsui.min.js')?>"></script>

<script type="text/javascript">
    var SITE_URL = "<?php echo site_url();?>";
    angular.module('app')
        .controller('agentSearchCtrl', ['$scope', '$http', 'ajax', function ($scope, $http, ajax) {
            $scope.click = function (operate, is_local) {
                if (is_local) {
                    window.location.href = SITE_URL + 'weixin/user/' + operate + '/' + new Date().getTime();
                }else {
                    window.location.href = operate;
                }
            }
        }])
</script>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?c181a068a633bab12df08a455a79aeec";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
</body>
</html>


