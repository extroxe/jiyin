/**
 * Created by sailwish001 on 2016/11/18.
 */
app.directive('autoFocus', function(){
    return function(scope, element){
        element[0].focus();
    };
});
app.controller('integraCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', function ($scope, _jiyin, dataToURL, $stateParams, $state) {
    $scope.integraList = {};
    $scope.inputPage = 1;
    $scope.keyword = '';
    $scope.status = [];
    $scope.state = '0';
    $scope.is_agents = ['全部', '是', '否'];
    $scope.is_agent = '全部';
    $scope.select_all = false;
    $scope.select_one = false;
    $scope.checkedArray = [];
    $scope.pageSize = {};
    $scope.pageSize.page = '10';
    $scope.pageSizes = ['10','50','100','200'];
    /*
     搜索
     */
    $scope.search = function () {
        _jiyin.dataPost('admin/order_admin/paginate/'+$scope.inputPage+'/' + $scope.pageSize.page, dataToURL({
            is_point: 1,
            keyword: $scope.keyword,
            start_create_time: $scope.register_start_time,
            end_create_time:$scope.register_end_time,
            order_status:$scope.state,
            is_agent : $scope.is_agent_id
        }))
            .then(function(result){
                if(result.success){
                    $scope.integraList = result.data;
                    //展开收起中间变量
                    $scope.tempReportList = [];
                    if($scope.integraList){
                        for (var i = 0; i < $scope.integraList.length; i++) {
                            if ($scope.integraList[i].sub_order.length > 2) {
                                $scope.tempReportList[i] = $scope.integraList[i].sub_order.slice(2, $scope.integraList[i].sub_order.length);
                                $scope.integraList[i].sub_order = $scope.integraList[i].sub_order.slice(0, 2)
                            } else {
                                $scope.tempReportList[i] = [];
                            }
                        }
                    }
                    $scope.totalPage = result.total_page;
                    var checkedNum = 0;
                    angular.forEach($scope.orderList,function (value,index) {
                        if($scope.checkedArray.indexOf(value.id) != -1){
                            value.checked = true;
                            checkedNum++;
                            return;
                        }
                        value.checked = false;
                    });

                    $scope.select_all = checkedNum === $scope.orderList.length;
                }else{
                    $scope.select_all = false;
                    $scope.integraList = [];
                    _jiyin.msg('e', '未查询到相关订单！');
                }
            });
    };
    //选中所有
    $scope.select_all = false;
    $scope.selectAll = function () {
        $scope.select_all = !$scope.select_all;
        if ($scope.select_all) {
            angular.forEach($scope.orderList, function (value, index) {
                if( $scope.checkedArray.indexOf(value.id) < 0){
                    $scope.checkedArray.push(value.id);
                }
                value.checked = true;
            });
        } else { // 清空全选
            for(var i = 0; i<$scope.orderList.length; i++){
                if( $scope.checkedArray.indexOf($scope.orderList[i].id) >= 0){
                    $scope.checkedArray.splice($scope.checkedArray.indexOf($scope.orderList[i].id), 1);
                    i--;
                }
            }
            angular.forEach($scope.orderList, function(value,index) {
                value.checked = false;
            });
        }
        console.log($scope.select_all);
        console.log($scope.checkedArray);
    };
    //选中单个
    $scope.selectOne = function (id) {
        angular.forEach($scope.integraList, function(item) {
            var localIndex = $scope.checkedArray.indexOf(id);
            // 选中
            if (localIndex === -1 && item.checked && id == item.id) {
                $scope.checkedArray.push(item.id);
            } else if (localIndex !== -1 && !item.checked && id == item.id) { // 取消选中
                $scope.checkedArray.splice(localIndex, 1);
            }
        });
        $scope.select_all = $scope.integraList.length === $scope.checkedArray.length;
        console.log($scope.checkedArray);
        console.log($scope.select_all);
    };
    // //选中所有
    // $scope.selectAll = function () {
    //     if ($scope.select_all) {
    //         $scope.checkedArray = [];
    //         angular.forEach($scope.integraList, function(value,index) {
    //             value.checked = true;
    //             $scope.checkedArray.push(value.id);
    //         });
    //     } else { // 清空全选
    //         $scope.checkedArray = [];
    //         angular.forEach($scope.integraList, function(value,index) {
    //             value.checked = false;
    //         });
    //     }
    //     console.log($scope.select_all);
    // };
    // //选中单个
    // $scope.selectOne = function () {
    //     angular.forEach($scope.integraList, function(item) {
    //         var localIndex = $scope.checkedArray.indexOf(item.id);
    //         // 选中
    //         if (localIndex === -1 && item.checked) {
    //             $scope.checkedArray.push(item.id);
    //         } else if (localIndex !== -1 && !item.checked) { // 取消选中
    //             $scope.checkedArray.splice(localIndex, 1);
    //         }
    //     });
    //     $scope.select_all = $scope.integraList.length === $scope.checkedArray.length;
    //     console.log($scope.select_all);
    // };

    $scope.enterEvent = function(e) {
        var keycode = window.event?e.keyCode:e.which;
        if(keycode==13){
            $scope.search();
        }
    };

    //查看订单详情

    $scope.getOrderDetailModal = function (data) {
        $scope.notPaidValue = 0;
        $scope.paidValue = 0;
        $scope.deliveredValue = 0;
        $scope.sentbackValue = 0;
        $scope.assayingValue = 0;
        // $('div.col-xs-3').find('.node').removeClass('active');
        $('#orderDetail').modal('show');
        $scope.orderDetail = angular.copy(data);
        if(data.status_id == '10'){
            $('div.not_paid').find('.node').addClass('active').siblings('div').find('.node').removeClass('active');
            $('div.not_paid').siblings('div').find('.node').removeClass('active');
            $scope.notPaidValue = 50;
        }else if(data.status_id == '20'){
            $('div.paid').find('.node').addClass('active').siblings('div').find('.node').removeClass('active').prevAll('.node').addClass('active');
            $('div.paid').siblings('div').find('.node').removeClass('active');
            $('div.paid').prevAll('div').find('.node').addClass('active');
            $scope.notPaidValue = 100;
            $scope.paidValue = 50;
        }else if(data.status_id == '30'){
            $('div.delivered').find('.node').addClass('active').siblings('div').find('.node').removeClass('active').prevAll('.node').addClass('active');
            $('div.delivered').siblings('div').find('.node').removeClass('active');
            $('div.delivered').prevAll('div').find('.node').addClass('active');
            $scope.notPaidValue = 100;
            $scope.paidValue = 100;
            $scope.deliveredValue = 50;
        }else if(data.status_id == '40'){
            $('div.sentback').find('.node').addClass('active').siblings('div').find('.node').removeClass('active').prevAll('.node').addClass('active');
            $('div.sentback').siblings('div').find('.node').removeClass('active');
            $('div.sentback').prevAll('div').find('.node').addClass('active');
            $scope.notPaidValue = 100;
            $scope.paidValue = 100;
            $scope.deliveredValue = 100;
            $scope.sentbackValue = 50;
        }else if(data.status_id == '50'){
            $('div.assaying').find('.node').eq(0).addClass('active').siblings('div').find('.node').removeClass('active').prevAll('.node').addClass('active');
            $('div.assaying').siblings('div').find('.node').removeClass('active');
            $('div.assaying').prevAll('div').find('.node').addClass('active');
            $scope.notPaidValue = 100;
            $scope.paidValue = 100;
            $scope.deliveredValue = 100;
            $scope.sentbackValue = 100;
            $scope.assayingValue = 50;
        }else{
            $('div.order-progress').find('.node').addClass('active');
            $scope.notPaidValue = 100;
            $scope.paidValue = 100;
            $scope.deliveredValue = 100;
            $scope.sentbackValue = 100;
            $scope.assayingValue = 100;
        }
    };
    $scope.modifyAmountModal = function () {
        $('#modifyAmount').modal('show');
    };
    $scope.cancelOrderModal = function (id) {
        $scope.cancelOrderDetail.id = id;
        $('#cancelOrder').modal('show');
    };
    $scope.cancel = function (id) {
        $(id).modal('hide');
    };

    //打印
    $scope.Jprintf = function () {
        $('#closeOrderDetail').hide();
        $('#printBtn').hide();
        $('#order_footer').hide();
        $("#orderDetail").jqprint({
            importCSS: true
        });
        setTimeout(function () {
            $('#closeOrderDetail').show();
            $('#printBtn').show();
            $('#order_footer').show();
        }, 1000);
    };

    //取消订单
    $scope.cancelOrderDetail = {};
    $scope.cancelOrder = function () {
        $scope.cancelOrderDetail.status_id = '100';
        if(!$scope.cancelOrderDetail.id){
            _jiyin.msg('e', '请选择要取消的订单');
            return;
        }
        if(!$scope.cancelOrderDetail.reason){
            _jiyin.msg('e', '请填写取消订单原因');
            return;
        }

        _jiyin.dataPost('admin/order_admin/update', dataToURL($scope.cancelOrderDetail))
            .then(function (result) {
                if(result.success){
                    $('#cancelOrder').modal('hide');
                    _jiyin.msg('s', result.msg);
                }else{
                    _jiyin.msg('e', result.msg);
                }
            })
    };

    _jiyin.dataGet('admin/order_admin/get_all_order_status')
        .then(function (result) {
            if(result.success){
                $scope.status = result.data;
            }
        });
    $scope.stateIsAgent = function (eq) {
        angular.forEach($scope.is_agents, function (data, index) {
            if(eq == index){
                $scope.is_agent = data;
                $scope.is_agent_id = eq;
            }
        })
    };

    $scope.download = function () {
        if($scope.checkedArray.length <= 0){
            _jiyin.msg('e','请选择需要导出订单');
            return;
        }
        var params = "?";
        if($scope.checkedArray.length != 0){
            params += "&order_id=" + $scope.checkedArray.join("_");
        }
        params += "&is_online=1";
        window.open(SITE_URL + 'admin/order_admin/download_order' + params);
    };
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/order_admin/paginate/'+$scope.inputPage+'/10', dataToURL({
            is_point: 1,
            keyword: $scope.keyword,
            start_create_time: $scope.register_start_time,
            end_create_time:$scope.register_end_time,
            order_status:$scope.state,
            is_agent : $scope.is_agent_id
        }))
            .then(function(result){
                $scope.integraList = result.data;

                //展开收起中间变量
                $scope.tempReportList = [];
                if($scope.integraList){
                    for (var i = 0; i < $scope.integraList.length; i++) {
                        if ($scope.integraList[i].sub_order.length > 2) {
                            $scope.tempReportList[i] = $scope.integraList[i].sub_order.slice(2, $scope.integraList[i].sub_order.length);
                            $scope.integraList[i].sub_order = $scope.integraList[i].sub_order.slice(0, 2)
                        } else {
                            $scope.tempReportList[i] = [];
                        }
                    }
                }
                $scope.totalPage = result.total_page;
                var checkedNum = 0;
                angular.forEach($scope.orderList,function (value,index) {
                    if($scope.checkedArray.indexOf(value.id) != -1){
                        value.checked = true;
                        checkedNum++;
                        return;
                    }
                    value.checked = false;
                });

                $scope.select_all = checkedNum === $scope.orderList.length;
            });
    };
    $scope.getData();

    //展开更多
    $scope.push_report = function (index) {
        for (var i = 0; i < $scope.tempReportList[index].length; i++) {
            $scope.orderList[index].sub_order.push($scope.tempReportList[index][i]);
        }
    };

    //收起更多
    $scope.shift_report = function (index) {
        for (var i = 0; i < $scope.tempReportList[index].length; i++) {
            $scope.orderList[index].sub_order = $scope.orderList[index].sub_order.slice(0, 2)
        }
    };

    // 选择订单
    $scope.searchOrderByStatus = function(orderStatusId){
        $scope.state = orderStatusId;
        $scope.search();
    };
    //订单状态选择
    $(document).on('click','#operation li',function () {
        $(this).addClass('active').siblings().removeClass('active');
    });
    /**
     * 编辑
     */
    $scope.editList = function (data) {
        $scope.title = '编辑数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-orderList.html',
            tempCtrl : 'modalOrderCtrl',
            ok : $scope.edit,
            size : 'lg',
            params : {
                title: $scope.title,
                infoList: data,
                ael: 'edit',
                isPoint: true
            }
        });
    };
    $scope.edit = function (list) {
        _jiyin.dataPost('admin/order_admin/update', dataToURL(list))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s',result.msg);
                    $scope.getData();
                }else{
                    _jiyin.msg('e',result.msg);
                }
            })
    };
    $scope.lookSub = function (data) {
        $state.go('app.subOrderList', {id: data.id, type: 'int'});
    };
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
    // $scope.cancel = function () {
    //     $("#list").modal('hide');
    // };
    /**
     * 删除信息
     * @param data
     */
    $scope.deleteData = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.data('',dataToURL({ id: data.id}))
                .then(function(result){
                    _jiyin.msg('s', '删除成功');
                    $scope.getData();
                });
        }
    };
    /**
     * 下一页
     */
    $scope.nextPage = function(){
        if($scope.inputPage < $scope.totalPage){
            $scope.inputPage++;
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
            $scope.inputPage--;
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