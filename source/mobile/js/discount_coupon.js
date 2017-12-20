angular.module('app')
    .controller('discountCouponCtrl', ['$scope', 'ajax', function($scope, ajax){
        $scope.flag = 'receive';
        $scope.select_nav = function(target, $event){
            $scope.flag = target;
            $($event.target).addClass('active').siblings('li').removeClass('active');
        };
        //获取用户的优惠券
        $scope.discount_coupons = [];
        //获取可领取的优惠券
        $scope.pulished_discount_coupons = [];

        //获取可领取的优惠券
        $scope.get_pulished_discount_coupon = function () {
            ajax.req('POST', 'user/get_pulished_discount_coupon')
                .then(function (data) {
                    if (data.success) {
                        $scope.pulished_discount_coupons = data.data;
                    }
                });
        };
        //获取用户的优惠券
        $scope.get_discount_coupon_by_user_id = function () {
            ajax.req('POST', 'user/get_discount_coupon_by_user_id')
                .then(function (data) {
                    if (data.success) {
                        $scope.discount_coupons = data.data;
                    }
                });
        };
        //获取已过期的优惠劵
        $scope.get_expired_coupon = function () {
            ajax.req('POST', 'user/get_discount_coupon_by_user_id')
                .then(function (data) {
                    if (data.success) {
                        $scope.expired_coupons = data.data;
                    }
                });
        };
        //领取优惠券
        $scope.receiveDiscountCoupon = function (discount_coupon_id) {
            ajax.req('POST', 'user/add_discount_coupon_to_user', {id : discount_coupon_id})
                .then(function (data) {
                    var msg;
                    if (data.success){
                        msg = data.msg;
                        $scope.get_discount_coupon_by_user_id();
                        $scope.get_pulished_discount_coupon();
                    }else{
                        msg = data.msg;
                    }
                    var popToast;
                    if (!popToast || popToast&&!popToast.toastBox){
                        popToast = new Toast(msg, {
                            "onHid" : function (e) {
                                e.destroy();
                            }
                        });
                    }
                    popToast.show();
                })
        };
        $scope.goUse = function () {
            window.location.href = SITE_URL + 'weixin/index';
        };

        $scope.get_discount_coupon_by_user_id();
        $scope.get_pulished_discount_coupon();
        $scope.get_expired_coupon();
    }]);