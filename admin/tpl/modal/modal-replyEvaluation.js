'use strict';

app.controller("modalReplyContentCtrl", ["$scope", "$modalInstance", "_jiyin", "params",
    function ($scope, $modalInstance, _jiyin, params) {
        $scope.infoList = {};
        $scope.infoList.id = params.id;
        $scope.title = params.title;
        $scope.ael = params.ael;
        $scope.cancel = function () {
            $modalInstance.dismiss("cancel")
        };
        $scope.ok = function () {
            if(!$scope.infoList.replyContent){
                _jiyin.msg('e','评论不能为空');
                return ;
            }
            $modalInstance.close($scope.infoList);
        };
    }]);