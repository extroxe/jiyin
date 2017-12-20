angular.module('app')
    .controller('integralDetailCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        $scope.user_info = [];
        ajax.req('POST', 'user/get_personal_info')
            .then(function (data) {
                if (data.success){
                    $scope.user_info = data.data;
                }
            });
        $scope.goIntegralExchange = function () {
            window.location.href = SITE_URL + 'weixin/integral/exchange';
        };
        $scope.goIntegralHome = function () {
            window.location.href = SITE_URL + 'weixin/integral';
        }
    }]);