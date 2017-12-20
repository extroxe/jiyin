<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <title>代理商主页</title>
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
    <style>
        body{
            background-color: #f3f4f9;
        }
        .titlebar-button{
            color: #fff;
        }
        .titlebar{
            background-color: #2a9e92;
        }
        .titlebar h1{
            text-align: center;
            color: #fff;
            padding-right: 32px;
        }
        .hinter-info{
            padding: 100px 50px;
            text-align: center;
        }
        .hinter-info span{
            font-size: 60px;
            color: #D9534F;
            margin-left: -30px;
        }
        .hinter-info p{
            margin-top: 80px;
            font-size: 18px;
            line-height: 31px;
        }
        .hinter-info p a{
            font-size: 18px;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<header>
    <div class="titlebar">
        <a id="go_back" class="titlebar-button" href="javascript:back()" style="visibility: hidden;"><i class="icon icon-arrowleft"></i></a>
        <h1>提示</h1>
    </div>
    <div class="hinter-info">
        <p>
            <img src="<?php echo site_url('source/mobile/img/icon/warning.png');?>" style="width: 120px;">
        </p>
        <p>请通过<strong>“太平乐享健康”</strong>微信健康商城的产品介绍页面进行分享！</p>
    </div>
</header>


<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="<?=site_url('source/assets/angular/angular.min.js')?>"></script>
<script src="<?=site_url('source/admin/vendor/angular/angular-file-upload/angular-file-upload.min.js')?>"></script>
<script src="<?=site_url('source/admin/vendor/jquery/md5/spark-md5.js')?>"></script>
<script src="<?=site_url('source/mobile/js/app.js')?>"></script>
<script src="<?=site_url('source/mobile/js/dropload.min.js')?>"></script>
<script src="<?=site_url('source/assets/seedsui/scripts/lib/seedsui/seedsui.min.js')?>"></script>
</body>
</html>