angular.module('app')
    .controller('userCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        $scope.user_info = [];
        ajax.req('POST', 'user/get_personal_info')
            .then(function (data) {
                if (data.success){
                    $scope.user_info = data.data;
                    $scope.is_sign = true;
                }else {
                    $scope.is_sign = false;
                }
            });

        $scope.goToUrl = function (url) {
            if ($scope.is_sign){
                window.location.href = SITE_URL + 'weixin/user/'+ url;
            }else{
                var popToast;
                popToast = new Toast("您还未登录，请先登录", {
                    "OnHid" : function (e) {
                        e.destroy;
                    }
                });
                popToast.show();
                setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
            }
        };  

        //登出
        $scope.sigh_out = function () {
            window.location.href = SITE_URL + 'weixin/index/weixin_sign_out';
        };

    }]);
