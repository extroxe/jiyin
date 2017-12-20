/**
 * Created by sailwish001 on 2016/12/2.
 */
app.controller('subOrderCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', function ($scope, _jiyin, dataToURL, $stateParams, $state) {
    $scope.subOrderList = {};
    /**
     * 物流信息
     */
    $scope.exinfo = function (data) {
        _jiyin.dataGet('admin/order_admin/show_express_info_by_order_id/'+data.id+'')
            .then(function (result) {
                if(result.success == true){
                    $("#list").modal('show');
                    $scope.exinfo = result.data.Traces;
                }else{
                    _jiyin.msg('e',result.msg);
                }
            });
    };
    $scope.cancel = function () {
        $("#list").modal('hide');
    };
    /**
     * 获取数据
     */
    if($stateParams.type == 'ord'){
        $scope.flag = true;
    }else if($stateParams.type == 'int'){
        $scope.flag = false;
    }
    $scope.getData = function(){
        _jiyin.dataPost('admin/order_admin/show_sub_order', dataToURL({
            order_id: $stateParams.id
        }))
            .then(function(result){
                $scope.subOrderList = result.data;
            });
    };
    $scope.getData();
    /**
     * 编辑
     */
    $scope.editList = function (data) {
        $scope.title = '编辑数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-subOrderList.html',
            tempCtrl : 'modalSubOrderCtrl',
            ok : $scope.edit,
            size : 'lg',
            params : {
                title: $scope.title,
                infoList: data,
                ael: 'edit'
            }
        });
    };
    $scope.edit = function (list) {
        list.order_id = $stateParams.id;
        _jiyin.dataPost('admin/order_admin/update_sub_order', dataToURL(list))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s',result.msg);
                    $scope.getData();
                }else{
                    _jiyin.msg('e',result.msg);
                }
            })
    };

    $scope.report = function (data) {
        $state.go('app.report', {id: data.id, order_id: $stateParams.id});
    };
}]);