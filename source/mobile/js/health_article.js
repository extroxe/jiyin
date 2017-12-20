/**
 * Created by sailwish009 on 2017/1/20.
 */
angular.module('app')
    .controller('healthArticleCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        $scope.back = function () {
            history.go(-1);
        };

    }])