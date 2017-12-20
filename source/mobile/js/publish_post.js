/**
 * Created by sailwish009 on 2017/2/21.
 */
angular.module('app')
    .controller('post',['$scope','$timeout', 'ajax', function ($scope, $timeout, ajax) {
        $scope.url = window.location.href;
        $scope.post_bar_id = $scope.url.substring($scope.url.lastIndexOf('/') + 1);

        //发帖
        $scope.publish_post = function() {
            ajax.req('POST', 'post/add',
                {
                    post_bar_id: $scope.post_bar_id,
                    title: $scope.title,
                    content: $scope.content,
                    status_id: 2
                })
                .then(function (response) {
                    var popToast;
                    if (response.success) {
                        popToast = new Toast("发表成功");
                        popToast.show();
                        setTimeout(function(){
                            $scope.back();
                        },1000);
                        /*$timeout(function () {
                            window.location.href = SITE_URL + 'weixin/user/post/' + $scope.post_bar_id;
                        },2000);*/
                    } else {
                        if (!popToast || popToast && !popToast.toastBox) {
                            popToast = new Toast(response.msg, {
                                "onHid": function (e) {
                                    e.destroy();
                                }
                            });
                        }
                        popToast.show();
                    }
                });
        };
    }]);
