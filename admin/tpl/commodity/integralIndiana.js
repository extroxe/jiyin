/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('indianaCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', function ($scope, _jiyin, dataToURL, $stateParams, $state) {
    $scope.indianaList = {};
    $scope.inputPage = 1;

    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataGet('admin/integral_indiana_admin/get_all_indiana_info/' + $scope.inputPage + '/10')
            .then(function(result){
                if (result.success) {
                    for (var i = 0; i < result.data.length; i++) {
                        if (result.data[i].commodity_center_name != null) {
                            result.data[i].commodity_name = result.data[i].commodity_name + ' ' + result.data[i].commodity_center_name;
                        } else {
                            result.data[i].commodity_name = result.data[i].commodity_name + ' ' + result.data[i].commodity_specification_name;
                        }
                    }
                    $scope.indianaList = result.data;
                } else {
                    $scope.indianaList = [];
                    _jiyin.msg('e', result.msg);
                }
                $scope.totalPage = result.total_page;
            });
    };
    $scope.getData();

    //选择商品控件
    $scope.selectCommodity = function () {
        $scope.title = '添加积分夺宝商品';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-agentAddCommodity.html',
            tempCtrl : 'agentAddCommodityCtrl',
            ok : $scope.selected,
            size : 'lg',
            params : {
                title: $scope.title,
                ael: 'add',
                select: 's'
            }
        });
    }

    //选择商品控件添加回调函数
    $scope.selected = function (data) {
        if (data[0].commodity_center_name == null) {
            var specification_center_name = '';
        } else {
            var specification_center_name = data[0].commodity_center_name;
        }
        $scope.list.price = data[0].price;
        $scope.list.commodity_name = data[0].commodity_name + ' ' + specification_center_name + ' ' + data[0].package_type_name;
        $scope.list.commodity_id = data[0].commodity_id;
        $scope.list.commodity_specification_id = data[0].id;
    }

    /**
     * 增加
     */
    $scope.addInfo = function () {
        $scope.title = '添加积分夺宝商品';
        $scope.add = true;
        $scope.list = {};
        $('#partyModal').modal('show');
    };

    /**
     * 编辑
     */
    $scope.editList = function (data) {
        $scope.title = '编辑积分夺宝商品';
        $scope.add = false;
        $scope.list = data;
        $('#partyModal').modal('show');
    };

    $scope.ok = function () {
        if(!$scope.list.commodity_id || !$scope.list.total_points || !$scope.list.amount_bet){
            _jiyin.msg('e','带*号为必填，请先填写必填项');
            return;
        }
        if($scope.add == true){
            _jiyin.dataPost('admin/integral_indiana_admin/add',dataToURL($scope.list))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s','添加成功');
                        $scope.getData();
                        $('#partyModal').modal('hide');
                    }else{
                        _jiyin.msg('e', result.msg);
                    }
                });
        }else if($scope.add == false){
            _jiyin.dataPost('admin/integral_indiana_admin/update',dataToURL($scope.list))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s','修改成功');
                        $scope.getData();
                        $('#partyModal').modal('hide');
                    }else{
                        _jiyin.msg('e', result.msg);
                    }
                });
        }
    };

    /**
     * 删除信息
     * @param data
     */
    $scope.deleteData = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.dataPost('admin/integral_indiana_admin/delete',dataToURL({ id: data.id}))
                .then(function(result){
                    if(result.success == true){
                        _jiyin.msg('s', '删除成功');
                        $scope.getData();
                    }else{
                        _jiyin.msg('e', result.msg);
                    }
                });
        }
    };

    /**
     * 查看用户
     */
    $scope.look = function (data) {
        $state.go('app.indianaUser', {id: data.id});
    };

    /**
     * 下一页
     */
    $scope.nextPage = function(){
        if($scope.inputPage < $scope.totalPage){
            $scope.inputPage ++;
            $scope.getData();
        }else{
            _jiyin.msg('e', '当前是最后一页');
        }
    };

    /**
     * 上一页
     */
    $scope.previousPage = function(){
        if($scope.inputPage > 1){
            $scope.inputPage --;
            $scope.getData();
        }else{
            _jiyin.msg('e', '当前是第一页');
        }
    };

    /**
     * 第一页
     */
    $scope.firstPage = function () {
        $scope.inputPage = 1;
        $scope.getData();
    };

    /**
     * 最后一页
     */
    $scope.lastPage = function () {
        $scope.inputPage = $scope.totalPage;
        $scope.getData();
    };
    $scope.selectPage = function (page) {
        $scope.inputPage = page;
        $scope.getData();
    }
}]);