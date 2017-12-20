<!DOCTYPE html>
<html lang="en" ng-app="login">
<head>
    <meta charset="utf-8" />
    <title>上海赛安生物基因商城后台管理</title>
    <meta name="description" content="app, web app, responsive, responsive layout, admin, admin panel, admin dashboard, flat, flat ui, ui kit, AngularJS, ui route, charts, widgets, components" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/css/bootstrap.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/css/animate.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/css/font-awesome.min.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/css/simple-line-icons.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/css/font.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/css/app.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/vendor/angular/toaster.css" type="text/css" />
</head>
<body ng-controller="LoginCtrl">
<toaster-container toaster-options="{'time-out': 2000, 'close-button':true, 'animation-class': 'toast-top-right'}"></toaster-container>
<div class="container w-xxl w-auto-xs" style="position: relative; top:200px">
    <a href class="navbar-brand block m-t">基因商城</a>
    <div class="m-b-lg">
        <div class="wrapper text-center">
            <strong>登录</strong>
        </div>
        <div class="list-group list-group-sm">
            <div class="list-group-item">
                <input placeholder="用户名" id="userAccount" class="form-control no-border" ng-init="focusMe()" ng-keydown="keydown($event)" ng-model="user.account" required>
            </div>
            <div class="list-group-item">
                <input type="password" id="userPassword" placeholder="密码" class="form-control no-border" ng-keydown="keydown($event)" ng-model="user.password" required>
            </div>
        </div>
        <button type="button" class="btn btn-lg btn-primary btn-block" ng-click="login()">登录</button>
        <div class="line line-dashed"></div>
    </div>
</div>

<script>
    var SITE_URL = "<?php echo site_url();?>";
</script>
<!-- jQuery -->
<script src="<?php echo base_url();?>source/admin/vendor/jquery/jquery.min.js"></script>

<!-- Angular -->
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular.js"></script>

<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-animate/angular-animate.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-cookies/angular-cookies.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-resource/angular-resource.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-sanitize/angular-sanitize.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-touch/angular-touch.js"></script>
<!-- Vendor -->
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-ui-router/angular-ui-router.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/angular/ngstorage/ngStorage.js"></script>

<!-- bootstrap -->
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-bootstrap/ui-bootstrap-tpls.js"></script>

<!-- lazyload -->
<script src="<?php echo base_url();?>source/admin/vendor/angular/oclazyload/ocLazyLoad.js"></script>
<!-- translate -->
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-translate/angular-translate.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-translate/loader-static-files.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-translate/storage-cookie.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-translate/storage-local.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/angular/toaster.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-file-upload/angular-file-upload.min.js"></script>
<!-- App -->
<script src="<?php echo base_url();?>source/admin/js/app.js"></script>
<script src="<?php echo base_url();?>source/admin/js/config.js"></script>
<script src="<?php echo base_url();?>source/admin/js/config.lazyload.js"></script>
<script src="<?php echo base_url();?>source/admin/js/config.router.js"></script>
<script src="<?php echo base_url();?>source/admin/js/main.js"></script>
<script src="<?php echo base_url();?>source/admin/js/services/ui-load.js"></script>
<script src="<?php echo base_url();?>source/admin/js/service/sw.service.js"></script>
<script src="<?php echo base_url();?>source/admin/js/filters/fromNow.js"></script>
<script src="<?php echo base_url();?>source/admin/js/directives/setnganimate.js"></script>
<script src="<?php echo base_url();?>source/admin/js/directives/ui-butterbar.js"></script>
<script src="<?php echo base_url();?>source/admin/js/directives/ui-focus.js"></script>
<script src="<?php echo base_url();?>source/admin/js/directives/ui-fullscreen.js"></script>
<script src="<?php echo base_url();?>source/admin/js/directives/ui-jq.js"></script>
<script src="<?php echo base_url();?>source/admin/js/directives/ui-module.js"></script>
<script src="<?php echo base_url();?>source/admin/js/directives/ui-nav.js"></script>
<script src="<?php echo base_url();?>source/admin/js/directives/ui-scroll.js"></script>
<script src="<?php echo base_url();?>source/admin/js/directives/ui-shift.js"></script>
<script src="<?php echo base_url();?>source/admin/js/directives/ui-toggleclass.js"></script>
<script src="<?php echo base_url();?>source/admin/js/directives/ui-validate.js"></script>
<script src="<?php echo base_url();?>source/admin/js/controllers/bootstrap.js"></script>
<script src="<?php echo base_url();?>source/admin/js/controllers/angular-table.min.js"></script>
<script src="<?php echo base_url();?>source/admin/js/login.js"></script>
<!-- Lazy loading -->
</body>
</html>