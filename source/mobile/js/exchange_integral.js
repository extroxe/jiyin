angular.module('app')
    .controller('exchangeIntegralCtrl', ['$scope', 'ajax', function($scope, ajax){
        //初始化轮播图
        var s1 = new Slider("#carousel1",{
            "pagination": ".slider-pagination",
            "loop": true,
            "autoplay": 5000
        });

        var img_height = parseFloat(screen.width)*0.38;
        $('.slider-container, .slider-container div, .slider-container img').css('height', img_height+'px');
        
        $scope.user_id = [];
        $scope.user_info = [];
        $scope.$watch('user.user_id', function (nv) {
           if (nv){
               $scope.user_id = nv;
               ajax.req('POST', 'user/get_personal_info', {id : nv})
                   .then(function (data) {
                       if (data.success){
                           $scope.user_info = data.data;
                       }
                   })
           }
        });
        $scope.exchange_commoditys = [];
        ajax.req('POST', 'commodity/get_hot_exchange_commodity')
            .then(function (data) {
                if (data.success){
                    $scope.exchange_commoditys = data.data;
                }
            });

        //积分商品首页
        $scope.toIndex = function (url) {
            window.location.href = SITE_URL + 'weixin/integral/' + url;
        }

        //商品详情页
        $scope.commodityDetail = function (commodity) {
            window.location.href = SITE_URL + 'weixin/integral/commodity_detail/' + commodity.commodity_id + '/' + commodity.commodity_specification_id;
        }
    }]);
