/**
 * Created by sailwish001 on 2016/11/18.
 */
app.directive('autoFocus', function () {
    return function (scope, element) {
        element[0].focus();
    };
});
app.controller('offLineOrderCtrl', ['$rootScope', '$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', '$filter', function ($rootScope, $scope, _jiyin, dataToURL, $stateParams, $state, $filter) {
    $scope.is_admin = $rootScope.is_admin;
    $scope.orderList = {};
    $scope.inputPage = 1;
    $scope.pageSize = 10;
    $scope.keyword = '';
    $scope.role_id = 30;
    $scope.off_line = 1;

    $scope.list = {};
    $scope.open = false;
    $scope.check = false;
    $scope.state = 0;
    $scope.register_start_time = '';
    $scope.register_end_time = '';
    $scope.checkList = new Array();
    // $scope.time = {};
    $scope.is_agents = ['全部', '是', '否'];
    $scope.is_agent_id = 0;
    $scope.is_agent = '全部';
    $scope.totalPage = 1;
    $scope.status = [];
    $scope.checkNum = 0;
    $scope.allCheckNum = 10;
    $scope.select_all = false;
    $scope.select_one = false;
    $scope.checkedArray = [];
    $scope.compress = {};
    $scope.back = function () {
        window.history.go(-1);
    };

    var start_time = '';
    var end_time = '';
    /**
     * 同步订单
     */
    $scope.synchronize = function () {
        start_time = $scope.register_start_time;
        end_time = $scope.register_end_time;
        if (start_time == '' || end_time == '') {
            _jiyin.msg('e', '请输入开始时间和结束时间');
            return;
        }
        if (start_time > end_time) {
            _jiyin.msg('e', '开始时间不能大于结束时间');
            return;
        }
        $scope.inputPage = 1;
        _jiyin.dataPost('admin/erp_admin/synchronize_order_from_erp/', dataToURL({
            start_time: start_time,
            end_time: end_time
        })).then(function (result) {
            if (result['success']) {
                $scope.getData();
                _jiyin.msg('s', result['msg']);
            } else {
                _jiyin.msg('e', result['msg']);
            }

        });
    };

    /**
     * 同步作废订单
     */
    $scope.synchronize_cancel = function () {
        start_time = $scope.register_start_time;
        end_time = $scope.register_end_time;
        if (start_time == '' || end_time == '') {
            _jiyin.msg('e', '请输入开始时间和结束时间');
            return;
        }
        if (start_time > end_time) {
            _jiyin.msg('e', '开始时间不能大于结束时间');
            return;
        }
        $scope.inputPage = 1;
        _jiyin.dataPost('admin/erp_admin/synchronize_invalid_order_from_erp/', dataToURL({
            start_time: start_time,
            end_time: end_time
        })).then(function (result) {
            if (result['success']) {
                $scope.getData();
                _jiyin.msg('s', result['msg']);
            } else {
                _jiyin.msg('e', result['msg']);
            }

        });
    };

    /**
     * 同步退货订单
     */
    $scope.synchronize_sales_return = function () {
        start_time = $scope.register_start_time;
        end_time = $scope.register_end_time;
        if (start_time == '' || end_time == '') {
            _jiyin.msg('e', '请输入开始时间和结束时间');
            return;
        }
        if (start_time > end_time) {
            _jiyin.msg('e', '开始时间不能大于结束时间');
            return;
        }
        $scope.inputPage = 1;
        _jiyin.dataPost('admin/erp_admin/synchronize_sales_return_from_erp/', dataToURL({
            start_time: start_time,
            end_time: end_time
        })).then(function (result) {
            if (result['success']) {
                $scope.getData();
                _jiyin.msg('s', result['msg']);
            } else {
                _jiyin.msg('e', result['msg']);
            }

        });
    };

    //选中所有
    $scope.select_all = false;
    $scope.selectAll = function () {
        $scope.select_all = !$scope.select_all;
        if ($scope.select_all) {
            angular.forEach($scope.orderList, function (value, index) {
                if ($scope.checkedArray.indexOf(value.id) < 0) {
                    $scope.checkedArray.push(value.id);
                }
                value.checked = true;
            });
        } else { // 清空全选
            for (var i = 0; i < $scope.orderList.length; i++) {
                if ($scope.checkedArray.indexOf($scope.orderList[i].id) >= 0) {
                    $scope.checkedArray.splice($scope.checkedArray.indexOf($scope.orderList[i].id), 1);
                    i--;
                }
            }
            angular.forEach($scope.orderList, function (value, index) {
                value.checked = false;
            });
        }
    };
    //选中单个
    $scope.selectOne = function (id) {
        angular.forEach($scope.orderList, function (item) {
            var localIndex = $scope.checkedArray.indexOf(id);
            // 选中
            if (localIndex === -1 && item.checked && id == item.id) {
                $scope.checkedArray.push(item.id);
            } else if (localIndex !== -1 && !item.checked && id == item.id) { // 取消选中
                $scope.checkedArray.splice(localIndex, 1);
            }
        });
        $scope.select_all = $scope.orderList.length === $scope.checkedArray.length;
    };

    $scope.stateIsAgent = function (eq) {
        angular.forEach($scope.is_agents, function (data, index) {
            if (eq == index) {
                $scope.is_agent = data;
                $scope.is_agent_id = eq;
            }
        })
    };

    $scope.listcheck = function () {
        $scope.checkNum = 0;
        angular.forEach($scope.orderList, function (value, index) {
            if (value.checked) {
                $scope.checkNum++;
            }
        });
    };

    /**
     * 获取数据
     */
    $scope.getData = function () {
        _jiyin.dataPost('admin/order_admin/paginate/' + $scope.inputPage + '/' + $scope.pageSize, dataToURL({
            is_point: 0,
            keyword: $scope.keyword,
            start_create_time: $scope.register_start_time,
            end_create_time: $scope.register_end_time,
            order_status: $scope.state,
            is_agent: $scope.is_agent_id,
            off_line: $scope.off_line
        })).then(function (result) {
            if (result.success) {
                $scope.orderList = result.data;
                $scope.role_id = result.role_id;
                //展开收起中间变量
                $scope.tempReportList = [];
                if ($scope.orderList) {
                    for (var i = 0; i < $scope.orderList.length; i++) {
                        if ($scope.orderList[i].sub_order.length > 2) {
                            $scope.tempReportList[i] = $scope.orderList[i].sub_order.slice(2, $scope.orderList[i].sub_order.length);
                            $scope.orderList[i].sub_order = $scope.orderList[i].sub_order.slice(0, 2)
                        } else {
                            $scope.tempReportList[i] = [];
                        }
                    }
                }
                $scope.allCheckNum = $scope.orderList.length;
                var checkedNum = 0;
                angular.forEach($scope.orderList, function (value, index) {
                    if ($scope.checkedArray.indexOf(value.id) != -1) {
                        value.checked = true;
                        checkedNum++;
                        return;
                    }
                    value.checked = false;
                });

                $scope.select_all = checkedNum === $scope.orderList.length;
            } else {
                $scope.orderList = [];
                $scope.select_all = false;
                _jiyin.msg('e', result.msg);
            }
            $scope.totalPage = result.total_page;
            $scope.totalCount = result.total_count;
        });
    };
    $scope.getData();

    $("#search").keydown(function (e) {
        if (e.keyCode == 13) {
            $scope.getData();
        }
    });

    $scope.search = function () {
        $scope.getData();
    }

    // 选择订单
    $scope.searchOrderByStatus = function (orderStatusId) {
        $scope.state = orderStatusId;
        $scope.getData();
    }

    _jiyin.dataGet('admin/order_admin/get_all_order_status')
        .then(function (result) {
            if (result.success) {
                $scope.status = result.data;
            }
        });

    /**
     * 获取所有快递公司
     */
    _jiyin.dataGet('admin/express_admin/get_all_express_company')
        .then(function (result) {
            if (result.success) {
                $scope.compress = result.data;
            }
        });

    $scope.download = function () {
        if ($scope.checkedArray.length <= 0) {
            _jiyin.msg('e', '请选择需要导出订单');
            return;
        }
        var params = "?";
        if ($scope.checkedArray.length != 0) {
            params += "&order_id=" + $scope.checkedArray.join("_");
        }
        params += "&is_online=2";
        window.open(SITE_URL + 'admin/order_admin/download_order' + params);
    };

    /**
     * 物流信息
     */
    $scope.exinfo = function (data) {
        _jiyin.dataGet('admin/order_admin/show_express_info_by_order_id/' + data.id + '')
            .then(function (result) {
                if (result.success == true) {
                    $("#list").modal('show');
                    $scope.exinfo = result.data.Traces;
                } else {
                    _jiyin.msg('e', result.msg);
                }
            });
    };
    $scope.cancel = function (id) {
        $(id).modal('hide');
    };

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

    /**
     * 编辑
     */
    $scope.editList = function (data) {
        $scope.title = '编辑数据';
        _jiyin.modal({
            tempUrl: '/source/admin/tpl/modal/modal-orderList.html',
            tempCtrl: 'modalOrderCtrl',
            ok: $scope.edit,
            size: 'lg',
            params: {
                title: $scope.title,
                infoList: data,
                ael: 'edit',
                isPoint: false
            }
        });
    };
    $scope.edit = function (list) {
        _jiyin.dataPost('admin/order_admin/update', dataToURL(list))
            .then(function (result) {
                if (result.success == true) {
                    _jiyin.msg('s', result.msg);
                    $scope.getData();
                } else {
                    _jiyin.msg('e', result.msg);
                }
            })
    };
    $scope.lookSub = function (data) {
        $state.go('app.subOrderList', {id: data.id, type: 'ord'});
    };

    //订单状态选择
    $(document).on('click', '#operation li', function () {
        $(this).addClass('active').siblings().removeClass('active');
    });

    //查看订单详情

    $scope.getOrderDetailModal = function (data) {
        $scope.order_status_id = data.status_id;
        $scope.notPaidValue = 0;
        $scope.paidValue = 0;
        $scope.deliveredValue = 0;
        $scope.sentbackValue = 0;
        $scope.assayingValue = 0;
        // $('div.col-xs-3').find('.node').removeClass('active');
        $('#orderDetail').modal('show');
        $('#closeOrderDetail').show();
        $('#printBtn').show();
        $('#order_footer').show();
        $scope.orderDetail = angular.copy(data);
        setTimeout(function () {
            $scope.$apply(function () {
                if (data.status_id == '10' || data.status_id == '20') {
                    $('div.not_paid').find('.node').addClass('active').siblings('div').find('.node').removeClass('active');
                    $('div.not_paid').siblings('div').find('.node').removeClass('active');
                    $scope.notPaidValue = 50;
                } else if (data.status_id == '30') {
                    $('div.delivered').find('.node').addClass('active').siblings('div').find('.node').removeClass('active').prevAll('.node').addClass('active');
                    $('div.delivered').siblings('div').find('.node').removeClass('active');
                    $('div.delivered').prevAll('div').find('.node').addClass('active');
                    $scope.notPaidValue = 100;
                    $scope.paidValue = 100;
                    $scope.deliveredValue = 50;
                } else if (data.status_id == '40') {
                    $('div.sentback').find('.node').addClass('active').siblings('div').find('.node').removeClass('active').prevAll('.node').addClass('active');
                    $('div.sentback').siblings('div').find('.node').removeClass('active');
                    $('div.sentback').prevAll('div').find('.node').addClass('active');
                    $scope.notPaidValue = 100;
                    $scope.paidValue = 100;
                    $scope.deliveredValue = 100;
                    $scope.sentbackValue = 50;
                } else if (data.status_id == '50') {
                    $('div.assaying').find('.node').eq(0).addClass('active').siblings('div').find('.node').removeClass('active').prevAll('.node').addClass('active');
                    $('div.assaying').siblings('div').find('.node').removeClass('active');
                    $('div.assaying').prevAll('div').find('.node').addClass('active');
                    $scope.notPaidValue = 100;
                    $scope.paidValue = 100;
                    $scope.deliveredValue = 100;
                    $scope.sentbackValue = 100;
                    $scope.assayingValue = 50;
                } else {
                    $('div.order-progress').find('.node').addClass('active');
                    $scope.notPaidValue = 100;
                    $scope.paidValue = 100;
                    $scope.deliveredValue = 100;
                    $scope.sentbackValue = 100;
                    $scope.assayingValue = 100;
                }
            })
        }, 300)
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

    $scope.modifyAmountModal = function () {
        $('#modifyAmount').modal('show');
    };

    $scope.delivery = function () {
        $('#deliverOrder').modal('show');
    };
    /**
     * 下一页
     */
    $scope.nextPage = function () {
        if ($scope.inputPage < $scope.totalPage) {
            $scope.check = false;
            $scope.inputPage++;
            $scope.getData();
        } else {
            _jiyin.msg('e', '当前是最后一页');
        }
    };
    /**
     * 上一页
     */
    $scope.previousPage = function () {
        if ($scope.inputPage > 1) {
            $scope.check = false;
            $scope.inputPage--;
            $scope.getData();
        } else {
            _jiyin.msg('e', '当前是第一页');
        }
    };
    /**
     * 第一页
     */
    $scope.firstPage = function () {
        if ($scope.totalPage == 1) {
            _jiyin.msg('e', '当前是第一页');
        }
        else {
            $scope.check = false;
            $scope.inputPage = 1;
            $scope.getData();
        }
    };
    /**
     * 最后一页
     */
    $scope.lastPage = function () {
        if ($scope.totalPage == 1) {
            _jiyin.msg('e', '当前是最后一页');
        }
        else {
            $scope.check = false;
            $scope.inputPage = $scope.totalPage;
            $scope.getData();
        }
    };
    $scope.selectPage = function (page) {
        if ($scope.totalPage == 1) {
            _jiyin.msg('e', '当前是最后一页');
        }
        else {
            $scope.check = false;
            $scope.inputPage = page;
            $scope.getData();
        }
    }
}]);