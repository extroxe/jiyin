/**
 * Created by sailwish009 on 2017/2/21.
 */
angular.module('app')
    .controller('publishEvaluation',['$scope', 'ajax', function ($scope, ajax) {

        //发表回复及评论
        /*$scope.publish_replay = function () {
            ajax.req('POST', 'post/add_comment',
                {
                    post_id:16
                    content:$scope.content,
                    root_comment_id:95
                    comment_id:95
                    to_user_id:4
                })
                .then(function (response) {
                    var popToast;
                    if(response.success) {
                        $scope.recommend_bar = response.data;
                        console.log(response.data);
                    }else {
                        if(!popToast || popToast&&!popToast.toastBox){
                            popToast=new Toast(response.msg,{
                                "onHid":function(e){
                                    e.destroy();
                                }
                            });
                        }
                        popToast.show();
                    }
                });
        };*/
        //定义exmobi返回
        $scope.back = function () {
            history.go(-1);
        };
    }]);
