/**
 * Created by sailwish009 on 2016/12/29.
 */
angular.module('app')
    .controller('signIn', ['$scope', 'ajax', function ($scope, ajax) {
        $scope.userInfo = [];
        var prompt = new Prompt();
        $scope.submitForm = function (userInfo) {
            if (userInfo.username.length < 3){
                prompt.setText('用户名不少于3个字符');
                prompt.show();
                return;
            }
            ajax.req('POST', 'index/do_login',{
                username:userInfo.username,
                password:userInfo.password
            })
                .then(function (data) {
                    if(data.success){
                        window.location.href = SITE_URL + 'weixin/user/center';
                    }else{
                        prompt.setText(data.msg);
                        prompt.show();
                    }
                })
        };

        $scope.back = function () {
            history.go(-1);
        }
    }]);