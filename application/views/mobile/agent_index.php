 <!doctype html>
<html>
<head lang="zh-cn">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <title>赛安基因城</title>
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
            background-color: #f3f4f9;
        }
        header{
            background-color: <?php echo isset($color) && !empty($color) ? $color : '#2a9e92';?>;
        }
        .slider-container{
            /*position: absolute;*/
            top: 56px;
        }
        .slide-banner{
            height: 212px;
        }
        .slider-pagination{
            text-align: center;
            display: block;
        }
        form.inputbox{
            margin-right: 68px;
            margin-left: 20px;
            border-radius: 6px;
        }
        .head-shopcart{
            position: absolute;
            right: 26px;
            top: 13px;
            font-size: 30px;
            color: #fff;
        }
        .group{
            margin-top: 57px;
            background-color: #f3f4f9;
            /*height: 500px;*/
        }
        .group:before, .group:after{
            height: 0px;
        }
        .commodity-list{
            background-color: #fff;
            padding: 0 0 5px;
            text-align: left;
        }
        .grid li{
            padding: 0px;
            position: relative;
        }
        .grid li:nth-child(odd){
            padding-right: 4px;
        }
        .grid li:nth-child(even){
            padding-left: 4px;
        }
        .grid li .commodity-list > label{
            display: block;
            margin-top: 8px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            box-sizing: border-box;
        /* font-size: 12px; */
            min-height: 16px;
            color: #9e9e9e;
        }

        .commodity-list p{
            font-size: 14px;
            text-align: left;
            max-height: 35px;
            margin: 10px 3px 0;
            word-wrap: break-word;
            text-overflow: ellipsis;
            color: #333;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp:1;
            overflow: hidden;
            height: 19px;
            line-height: 19px;
        }
        .commodity-list p.specification{
            font-size: 12px;
            color: #999;
            margin: 3px 3px 10px;
            display: inherit;
        }
        .commodity-list .color-1{
            width: 100%;
        }
        .commodity-list img{
            /*height: 250px;*/
            width: 100%;
        }
        .commodity-list span.price{
            text-align: left;
            margin-left: 4px;
            color: #D9534F;
        }
        .commodity-shopcart{
            position: absolute;
            right: 20px;
            font-size: 27px;
            color: #333;
            margin-top: -3px;
        }
        .dropload-down, .dropload-up{
            text-align: center;
            font-size: 12px;
            color: #999;
        }
        .loading{
            width: 17px;
            height: 17px;
        }
        .loading:before, .loading:after{
            width: 0px;
            height: 0px;
        }
        .tip.shopping-cart {
            position: absolute;
            top: 13px;
            right: 14px;
            height: 14px;
            width: auto;
            line-height: inherit;
            padding: 0 4px;
            background-color: #FF5400;
        }
        .no-result{
            padding: 50px 20px;
            text-align: center;
            font-size: 20px;
            color: #2A9E92;
        }
        .no-result img{
            width: 41px;
            display: block;
            margin: 0px auto 15px;
        }
        .ng-cloak{
            display: none;
        }
    </style>

</head>
<body ng-app="app">
    <div ng-controller="agentHomeCtrl">
        <header>
            <form class="inputbox margin8 radius20 overflow-hidden" data-input="clear">
                <i class="color-placeholder icon icon-search" ng-click="search()"></i>
                <input type="search" placeholder="搜索框" class="search input-text" ng-change="search()" ng-model="key_words">
                <i class="color-placeholder icon icon-clear-fill hide size20"></i>
            </form>
                <i class="color-placeholder icon head-shopcart icon-shopcart" ng-click="goShoppingCart()"></i>
                <span class="tip shopping-cart" ng-if="cart_num > 0" ng-bind="cart_num"></span>
        </header>
        <!--    banner-->
        <div class="slider-container" id="carousel1">
            <div class="slider-wrapper">
                <?php if (!empty($banner)): ?>
                    <?php foreach ($banner as $row) : ?>
                        <div class="slider-slide">
                            <a href="<?php echo $row['url']; ?>">
                                <img class="slide-banner" src="<?php echo site_url($row['path']); ?>"/>
<!--                                <img class="slide-banner" src="--><?php //echo site_url('source/mobile/img/1.jpg'); ?><!--"/>-->
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="slider-pagination"></div>
        </div>
        <div class="content">
            <div class="group" ng-cloak>
                <ul class="grid" data-col="2" data-rowspace="8">
                    <li ng-repeat="commodity in commodities">
                        <div class="commodity-list">
                            <a class="color-1" href="{{ SITE_URL + 'weixin/index/commodity_detail/' + commodity.id + '/' + commodity.commodity_specification_id}}">
                                <img ng-if="commodity.path != null" ng-cloak ng-src="{{SITE_URL + commodity.path}}"/>
                                <img ng-if="commodity.path == null" ng-cloak ng-src="{{SITE_URL + 'source/img/default_commodity_pic.jpg'}}" />
                            </a>
                           <p ng-cloak class="ng-cloak">{{commodity.packagetype_name}}</p>
                           <!-- <p ng-cloak class="ng-cloak">{{commodity.commodity_name}}</p> -->
                           <!-- <p ng-cloak class="ng-cloak specification">{{commodity.name}}</p> -->
<!--                            <span class="price" style="font-size: 14px;background-color:#F6BF00;color: #FFF;padding: 1px 4px;border-radius: 3px" ng-if="userType">尊享价</span>-->
                            <span class="price" ng-cloak>¥&nbsp;<font style="font-size: 18px;" ng-bind="commodity.agent_price"></font></span>
                            <span class="price" style="font-size: 12px;background-color:#F6BF00;color: #FFF;padding: 1px 4px;border-radius: 3px" ng-if="userType">尊享价</span>

                            <i class="color-placeholder commodity-shopcart icon icon-shopcart" ng-click="add_cart(commodity)"></i>
                        </div>
                    </li>
                </ul>
                <div class="no-result" ng-if="!data_flag" ><img ng-src="{{SITE_URL + 'source/mobile/img/warning.png'}}">未找到相关商品</div>
            </div>
        </div>
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
            .controller('agentHomeCtrl', ['$scope', '$http', 'ajax', function ($scope, $http, ajax) {
                $scope.SITE_URL = SITE_URL;
                $scope.commodities = [];
                $scope.page = 1;
                $scope.page_size = 10;
                $scope.category_id = "<?php echo $category_id;?>";

                //提示框
                $scope.hinter = function (text) {
                    var popToast;
                    if(!popToast || popToast&&!popToast.toastBox){
                        popToast=new Toast(text,{
                            "onHid":function(e){
                                e.destroy();
                            }
                        });
                    }
                    popToast.show();
                };
                /*获取用户类型*/
                $scope.getUserType = function () {
                    ajax.req('GET', 'user/get_personal_info')
                        .then(function(response){
                            if(response.success){
                                $scope.userType = response.data.uid;
                            }
                        });
                };
                $scope.getUserType();
                //加载页面数据

                //获取购物车数量
                $scope.cart_num = 0;
                ajax.req('GET', 'shopping_cart/amount')
                    .then(function(response){
                        if (!response.success && response.timeout) {
                            // 购物车为空或未登录
                        }else if (response){
                            $scope.cart_num = response;
                        }
                    });


                $scope.load_data = function (me) {
                    ajax.req('POST', 'weixin/agent/get_commodity_list_by_agent_id',{
                        page: $scope.page,
                        page_size: $scope.page_size,
                        category_id:$scope.category_id
                    })
                    .then(function(response){
                        if (response.success){
                            angular.forEach(response.data, function (data, index) {
                                $scope.commodities.push(data);
                            });
                            if(me){
                                setTimeout(function(){
                                    // 每次数据加载完，必须重置
                                    me.resetload();
                                },1000);
                            }
                            setTimeout(function () {
                                var img_height = $('.color-1')[0].clientWidth;
                                $('.color-1 img, .color-1').css('height', img_height + 'px');
                            },100);

                        }else {
//                            $scope.hinter(response.msg);
                            if(me){
                                // 锁定
                                me.lock();
                                // 无数据
                                me.noData();
                                // 即使加载出错，也得重置
                                me.resetload();
                            }
                        }
                    });
                    $scope.page++;
                };

                $scope.load_data();
                //下拉刷新加载商品列表
                $('.content').dropload({
                    scrollArea : window,
                    loadUpFn: function (me) {
                        $scope.load_data(me);
                    },
                    loadDownFn : function(me){
                        $scope.load_data(me);
                    }
                });

                //搜索商品
                $scope.search_result = $scope.commodities;
                $scope.data_flag = true;
                $scope.search = function () {
                    if($scope.key_words == ''){
                        $scope.commodities = $scope.search_result;
                    }else{
                        $scope.commodities = [];
                        angular.forEach($scope.search_result, function (data, index) {
                            if(data.packagetype_name.indexOf($scope.key_words) >= 0){
                                $('.content').addClass('content');
                                $scope.data_flag = true;
                                $scope.commodities.push(data);

                            }else{
                                $scope.data_flag = false;
                                $('.dropload-down').remove();
                            }
                        });
                        if($scope.data_flag){
                            $('.dropload-down').remove();
                        }
                    }

                    var img_height = $('.color-1')[0].clientWidth;
                    $('.color-1 img, .color-1').css('height', img_height + 'px');
                };

                //加入购物车
                $scope.add_cart = function(commodity){
                    ajax.req('POST', 'shopping_cart/add', {
                        commodity_id: commodity.id,
                        specification_id: commodity.commodity_specification_id,
                        amount: 1
                    }).then(function(response){
                        if (response.success){
                            $scope.hinter(response.msg);
                            $scope.cart_num = parseInt($scope.cart_num) + 1;
                        }else {
                            $scope.hinter(response.msg);
                            if (response.timeout) {
                                setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                            }
                        }
                    });
                };

                $scope.$watch('cart_num', function(nv, ov){
                    if (nv > 99){
                        $('.tip.shopping-cart').text('99+');
                    }
                });

                //查看我的购物车
                $scope.goShoppingCart = function () {
                    ajax.req('GET', 'user/get_personal_info')
                        .then(function(response){
                            if (response.success){
                                window.location.href = SITE_URL + 'weixin/index/shopping_cart';
                            }else{
                                $scope.show_popToast(response.msg);
                                if (response.timeout) {
                                    setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                                }
                            }
                        });
                };

                window.onload=function(){
                    // 初始化轮播图
                    var s1=new Slider("#carousel1",{
                        "pagination":".slider-pagination",
                        "autoplay":3000,
                        "loop":true
                    });
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


