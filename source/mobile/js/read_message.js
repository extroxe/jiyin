angular.module('app')
    .controller('readMessageCtrl', ['$scope', 'ajax', function ($scope, ajax) {
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

        $scope.$watch('message_id', function(nv, ov){
            if (nv){
                $scope.message_id = nv;
                ajax.req('POST', 'user/get_message_by_id', {message_id: $scope.message_id})
                .then(function (response) {
                    if (response.success){
                        $scope.message = response.data;
                        if ($scope.message.status_id == 0){
                            $scope.readMsg();
                        }
                    }else{
                        $scope.show_popToast(response.msg);
                    }
                });
            }
        });

        //修改信息状态，改为已读
        $scope.readMsg = function(){
            ajax.req('POST', 'user/read_message', {message_id: $scope.message_id})
                .then(function (response) {
                    if (response.success){
                        $scope.message.status_id = 1;
                    }else{
                        $scope.show_popToast(response.msg);
                    }
                });
        }
    }]);
