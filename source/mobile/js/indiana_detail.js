angular.module('app')
    .controller('commodityDetailCtrl', ['$scope', 'ajax', function($scope, ajax){
        //初始化轮播图
        var s1 = new Slider("#carousel1",{
            "pagination":".slider-pagination",
            "loop":true,
            "autoplay": 5000
        });

        //popToast弹窗函数
        var popToast;
        $scope.pop = function (msg) {
            if(!popToast || popToast && !popToast.toastBox){
                popToast = new Toast(msg,{
                    "onHid": function(e){
                        e.destroy();
                    }
                });
            }
            popToast.show();
        };

        var img_height = parseFloat(screen.width)*0.8;
        $('.slider-container, .slider-container div, .slider-container img').css('height', img_height+'px');

        $scope.favorite_flag = false;

        //获取商品详情
        $scope.commodity = [];
        $scope.$watch('commodity.specification_id', function(nv, ov){
            if (nv){
                $scope.commodity.id = nv;
                ajax.req('POST', 'index/get_commodity_by_id', {specification_id : nv})
                    .then(function(response){
                        if (response.success) {
                            $scope.commodity = response.data;
                        } else {
                            $scope.commodity = [];
                            $scope.pop(response.msg);
                        }
                    });

                ajax.req('POST', 'favorite/check_favorite_by_commodity_id', {commodity_id : nv})
                    .then(function(response){
                        if (response.success){
                            $scope.favorite_flag = true;
                        }else{
                            $scope.favorite_flag = false;
                        }
                    });
            }
        });

        //收藏
        $scope.favorite = function(commodity, $event){
            if ($scope.favorite_flag){
                ajax.req('POST', 'favorite/delete_by_commodity_id',
                    {
                        commodity_id : commodity.id,
                        commodity_specification_id: commodity.commodity_specification_id
                    }).then(function(response){
                            if(response.success){
                                $($event.target).removeClass('icon-heart-fill').addClass('icon-heart');
                                $scope.favorite_flag = !$scope.favorite_flag;
                                $scope.pop("取消收藏成功");
                            }else {
                                $scope.pop(response.msg);
                            }
                        });
            } else {
                ajax.req('POST', 'favorite/add',
                    {
                        commodity_id: commodity.id,
                        commodity_specification_id: commodity.commodity_specification_id
                    }).then(function(response){
                        if(response.success){
                            $($event.target).removeClass('icon-heart').addClass('icon-heart-fill');
                            $scope.favorite_flag = !$scope.favorite_flag;
                            $scope.pop("收藏成功");
                        }else {
                            $scope.pop(response.msg);
                            if (response.timeout) {
                                setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                            }
                        }
                    });
            }
        };

        //去夺宝
        $scope.integral_indiana = function(){
            window.location.href = SITE_URL + 'weixin/integral/indiana';
        };

        $scope.goShoppingCart = function () {
            ajax.req('GET', 'user/get_personal_info')
                .then(function(response){
                    if (response.success){
                        window.location.href = SITE_URL + 'weixin/index/shopping_cart';
                    }else{
                        $scope.pop(response.msg);
                        if (response.timeout) {
                            setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                        }
                    }
                });
        };
        //查看商品评价
        $scope.commodity_evaluation = function () {
            window.location.href = SITE_URL + 'weixin/user/evaluation_list/'+ $scope.commodity.id;
        };

        //定义exmobi返回
        function back(){
            history.go(-1);
        }
    }]);
