angular.module('app')
    .controller('logisticsCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        //初始化
        $scope.order_id = [];
        $scope.details = [];
        $scope.express_infos = [];
        $scope.traces = [];
        $scope.$watch('order.order_id', function (nv) {
            if (nv){
                $scope.order_id = nv;
                ajax.req('POST', 'order/get_order_by_id', {id : nv}, true)
                    .then(function (data) {
                        if (data.success){
                            $scope.details = data.data;
                        }
                    });
                ajax.req('GET', 'order/show_express_info_by_order_id/' + nv)
                    .then(function (data) {
                        if (data.success){
                            $scope.express_infos = data.data;
                            var j = 0;
                            for (var i = $scope.express_infos.Traces.length - 1; i >= 0; i--){
                                $scope.traces[j] =  data.data.Traces[i];
                                j++;
                            }
                        }
                    })
            }
        });
    }]);
