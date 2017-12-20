angular.module('app')
    .controller('integralCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        var s1 = new Slider("#carousel1",{
            "pagination":".slider-pagination",
            "autoplay":"3000",
            "loop":true
        });

        var img_height = parseFloat(screen.width)*0.38;
        $('.slider-container, .slider-container div, .slider-container img').css('height', img_height+'px');

        $scope.exchange_commoditys = [];
        ajax.req('POST', 'commodity/get_hot_exchange_commodity')
            .then(function (data) {
                if (data.success){
                    $scope.exchange_commoditys = data.data;
                }
            });

        //商品详情页
        $scope.commodityDetail = function (commodity) {
            window.location.href = SITE_URL + 'weixin/integral/commodity_detail/' + commodity.commodity_id + '/' + commodity.commodity_specification_id;
        }
    }]);
