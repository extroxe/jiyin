/**
 * Created by sailwish009 on 2017/2/15.
 */
angular.module('app')
    .controller('postBar',['$scope', 'ajax', function ($scope, ajax) {
        


        /**
         * 导航
         */

        [].slice.call(document.querySelectorAll('.tabbar')).forEach(function(tabbar){
            tabbar.onclick=function(e){
                [].slice.call(tabbar.querySelectorAll('.tab')).forEach(function(tab) {
                    tab.classList.remove("active");
                });
                e.target.classList.add("active");
            }
        });
        //定义exmobi返回
        $scope.back = function () {
            history.go(-1);
        };
    }]);