angular.module('app')
    .controller('sendMessageCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        //popToast弹窗函数
        var popToast;
        $scope.show_popToast = function (msg) {
            if(!popToast || popToast&&!popToast.toastBox){
                popToast=new Toast(msg,{
                    "onHid":function(e){
                        e.destroy();
                    }
                });
            }
            popToast.show();
        };

        $scope.$watch('user_id', function(nv, ov){
            if (nv){
                $scope.user_id = nv;
                ajax.req('POST', 'user/get_personal_info', {id: $scope.user_id})
                .then(function (response) {
                    if (response.success){
                        $scope.user = response.data;
                    }else{
                        $scope.show_popToast(response.msg);
                    }
                });
            }
        });


        //发送信息
        $scope.sendMsg = function(){
            if ($scope.content){
                ajax.req('POST', 'user/add_message', {
                    content: $scope.content,
                    receive_user_id: $scope.user.id
                })
                    .then(function (response) {
                        if (response.success){
                            $scope.content = null;
                            $scope.show_popToast('发送成功!');
                        }else{
                            $scope.show_popToast('发送失败...');
                        }
                    });
            }
        }
    }]);