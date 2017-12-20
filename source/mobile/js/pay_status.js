angular.module('app')
    .controller('orderCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        //h获取用户id对应的订单信息
        $scope.order_info = [];
        $scope.order_info.points = 0;
        $scope.agent_id = 0;
        $scope.$watch('order.order_id', function (nv) {
            if (nv) {
                ajax.req('POST', 'order/get_order_by_id', {id: nv}, true)
                    .then(function (data) {
                        var all_points = 0;
                        if (data.success) {
                            $scope.order_info = data.data;
                            for (var i = 0; i < data.data.sub_orders.length; i++) {
                                all_points += parseFloat(data.data.sub_orders[i].points);
                            }
                            if (data.agent_id != null && parseInt(data.agent_id) > 0) {
                                $scope.agent_id = parseInt(data.agent_id);
                            }
                        }
                        $scope.order_info.points = all_points;
                    });
            }
        });
        $scope.go_home = function () {
            if ($scope.agent_id > 0) {
                window.location.href = 'http://wxtest.life.cntaiping.com/taiping-lxjk/oauth/oauth_login_HealthStore.do';
            }else {
                window.location.href = SITE_URL + 'weixin';
            }
        };

        $scope.go_order_detail = function (order_id){
            window.location.href = SITE_URL + 'weixin/index/order_detail/' + order_id;
        };
        $scope.back = function () {
            history.go(-1);
        }
    }]);
