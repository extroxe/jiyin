/**
 * Created by sailwish001 on 2017/1/4.
 */
'use strict';

app.controller('modalRefundCtrl', ['$scope', '$modalInstance', '_jiyin', 'params',
    function ($scope, $modalInstance, _jiyin, params) {
        $scope.infoList = params.infoList;
        $scope.title = params.title;
        $scope.ael = params.ael;

        $scope.infoList.address = $scope.infoList.address.province + $scope.infoList.address.city + $scope.infoList.address.district + $scope.infoList.address.address;
        /*
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
        $scope.ok = function () {
            $modalInstance.dismiss('cancel');
        }
    }]);