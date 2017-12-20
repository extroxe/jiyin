<!DOCTYPE html>
<html lang="en" data-ng-app="app">
<head>
    <meta charset="utf-8" />
    <title>上海赛安生物基因商城后台管理</title>
    <meta name="description" content="app, web app, responsive, responsive layout, admin, admin panel, admin dashboard, flat, flat ui, ui kit, AngularJS, ui route, charts, widgets, components" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/css/bootstrap.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/css/animate.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/css/font-awesome.min.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/css/simple-line-icons.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/css/font.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/css/app.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/css/ordinaryList.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/css/farbtastic.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/vendor/angular/toaster.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/vendor/jquery/footable/footable.core.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/vendor/jquery/kindeditor/themes/default/default.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/css/bootstrap-datetimepicker.min.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/assets/sweetalert/sweetalert.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>source/admin/vendor/angular/angular-bootstrap-colorpicker/css/colorpicker.min.css" type="text/css" />

</head>
<body ng-controller="AppCtrl">
<div ng-include="'/source/admin/tpl/loading.html'" ng-if="isLoading"></div>
<toaster-container toaster-options="{'time-out': 3000, 'close-button':true, 'animation-class': 'toast-top-right'}"></toaster-container>
<div class="app" id="app" ng-class="{'app-header-fixed':app.settings.headerFixed, 'app-aside-fixed':app.settings.asideFixed, 'app-aside-folded':app.settings.asideFolded, 'app-aside-dock':app.settings.asideDock, 'container':app.settings.container}" ui-view></div>

<script>
    var SITE_URL = "<?php echo site_url();?>";
</script>
<!-- jQuery -->

<script src="<?php echo base_url();?>source/admin/vendor/jquery/jquery.min.js"></script>
<!--<script src="--><?php //echo base_url();?><!--source/admin/vendor/jquery/jquery-1.4.4.min.js"></script>-->
<script src="<?php echo base_url();?>source/admin/vendor/jquery/bootstrap.js"></script>
<script src="http://www.jq22.com/jquery/jquery-migrate-1.2.1.min.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/jquery/jquery.jqprint-0.3.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/jquery/jQuery.print.js"></script>

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
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-bootstrap-colorpicker/js/bootstrap-colorpicker-module.min.js"></script>


<!-- lazyload -->
<script src="<?php echo base_url();?>source/admin/vendor/angular/oclazyload/ocLazyLoad.js"></script>
<!-- translate -->
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-translate/angular-translate.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-translate/loader-static-files.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-translate/storage-cookie.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-translate/storage-local.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/angular/toaster.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/angular/angular-file-upload/angular-file-upload.min.js"></script>

<script src="<?php echo base_url();?>source/admin/vendor/jquery/footable/footable.all.min.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/jquery/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/jquery/kindeditor/kindeditor-all.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/jquery/md5/spark-md5.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/template.js"></script>
<script src="<?php echo base_url();?>source/admin/vendor/farbtastic.js"></script>
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
<script src="<?php echo base_url();?>source/admin/js/directives/ui-editor.js"></script>
<script src="<?php echo base_url();?>source/admin/js/directives/ui-validate.js"></script>
<script src="<?php echo base_url();?>source/admin/js/controllers/bootstrap.js"></script>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=f751de256e5a028b11c8b2cc8b4d4ad4&plugin=AMap.DistrictSearch"></script>
<!-- Lazy loading -->
</body>
</html>