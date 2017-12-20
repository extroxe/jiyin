angular.module('app')
    .controller('memberCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        $scope.user_info = [];
        $scope.level_info = [];

        ajax.req('POST', 'user/get_personal_info')
            .then(function (data) {
                if (data.success){
                    $scope.user_info = data.data;
                }else {
                    window.location.href = SITE_URL + '/weixin';
                }
            });

        ajax.req('POST', 'user/get_all_level')
            .then(function (data) {
                if (data.success) {
                    $scope.level_info = data.data;
                }
            })
    }]);