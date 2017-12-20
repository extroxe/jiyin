'use strict';

app.controller("modalAgentChangePasswdCtrl", ["$scope", "$modalInstance", "_jiyin", "params",
    function ($scope, $modalInstance, _jiyin, params) {
        $scope.infoList = params.infoList;
        $scope.title = params.title;
        $scope.ael = params.ael;

        $scope.cancel = function () {
            $modalInstance.dismiss("cancel")
        };
        $scope.ok = function () {
            if(!$scope.infoList.newPasswd){
                _jiyin.msg('e','密码不能为空');
                return ;
            }
            if($scope.infoList.newPasswd != $scope.infoList.rePasswd){
                _jiyin.msg('e', '密码不一致');
                return;
            }
            $modalInstance.close($scope.infoList);
        };
    }]);