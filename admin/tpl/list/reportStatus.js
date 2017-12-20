/**
 * Created by sailwish001 on 2016/11/18.
 */
app.directive('autoFocus', function () {
    return function (scope, element) {
        element[0].focus();
    };
});
app.controller('reportStatusCtrl', ['$rootScope', '$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', '$filter', function ($rootScope, $scope, _jiyin, dataToURL, $stateParams, $state, $filter) {
    $scope.is_admin = $rootScope.is_admin;
    $scope.orderList = {};
    $scope.inputPage = 1;
    $scope.pageSize = {};
    $scope.pageSize.page = '10';
    $scope.keyword = '';

    $scope.list = {};
    $scope.open = false;
    $scope.check = false;
    $scope.state = 0;
    $scope.register_start_time = '';
    $scope.register_end_time = '';
    $scope.checkList = new Array();
    $scope.is_agent = '全部';
    $scope.totalPage = 1;
    $scope.status = [];
    $scope.checkNum = 0;
    $scope.allCheckNum = 10;
    $scope.select_all = false;
    $scope.select_one = false;
    $scope.checkedArray = [];
    $scope.back = function () {
        window.history.go(-1);
    };

    //选中所有
    // $scope.select_all = false;
    $scope.selectAll = function (data) {
        // $scope.select_all = !$scope.select_all;
        // data.checked = !data.checked;
        if (data.checked) {
            angular.forEach(data, function (value, index) {
                if ($scope.checkedArray.indexOf(value.id) < 0) {
                    $scope.checkedArray.push(value.id);
                }
                value.checked = true;
            });
        } else { // 清空全选
            for (var i = 0; i < data.length; i++) {
                if ($scope.checkedArray.indexOf(data[i].id) >= 0) {
                    $scope.checkedArray.splice($scope.checkedArray.indexOf(data[i].id), 1);
                    i--;
                }
            }
            angular.forEach(data, function (value, index) {
                value.checked = false;
            });
        }

        console.log($scope.checkedArray);
    };
    //选中单个
    $scope.selectSingle = function (sub_order, order) {
        angular.forEach(order, function (item) {
            var localIndex = $scope.checkedArray.indexOf(item.id);
            // 选中
            if (localIndex === -1 && item.checked && sub_order.id == item.id) {

                $scope.checkedArray.push(item.id);
                order.count++;
            } else if (localIndex !== -1 && !item.checked && sub_order.id == item.id) { // 取消选中
                $scope.checkedArray.splice(localIndex, 1);
                order.count--
            }
        });
        // console.log(order);
        order.checked = order.length === order.count;
        console.log($scope.checkedArray);
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
        _jiyin.dataPost('admin/report_admin/get_report_list_by_status/' + $scope.inputPage + '/' + $scope.pageSize.page, dataToURL({
            order_status: $scope.state
        })).then(function (result) {
            if (result.success) {
                $scope.orderList = result.data;
                if ($scope.orderList) {
                    for (var i = 0; i < $scope.orderList.length; i++) {

                    }
                }
                $scope.allCheckNum = $scope.orderList.length;
                angular.forEach($scope.orderList, function (data, index) {
                    if(data.sub_order){
                        var checkedNum = 0;
                        angular.forEach(data.sub_order, function (item, eq) {
                            if ($scope.checkedArray.indexOf(item.id) != -1) {
                                item.checked = true;
                                checkedNum++;
                                return;
                            }
                            item.checked = false;
                        });
                        if(checkedNum == data.sub_order.length){
                            data.sub_order.checked = true;
                        }
                        data.sub_order.count = 0;
                    }
                });

                // angular.forEach($scope.orderList, function(data, index){
                //     if(data.sub_order){
                //         angular.forEach(data.sub_order, function (item, eq) {
                //            item.checked = false;
                //         });
                //         data.sub_order.count = 0;
                //     }
                // });
                // console.log($scope.orderList);
                // $scope.select_all = checkedNum === $scope.orderList.length;
            } else {
                $scope.orderList = [];
                // $scope.select_all = false;
                _jiyin.msg('e', result.msg);
            }
            $scope.totalPage = result.total_page;
            $scope.total_count = result.total_count;
        });
    };
    $scope.getData();

    //回车监听搜索和搜索
    $("#search").keydown(function (e) {
        if (e.keyCode == 13) {
            $scope.getData();
        }
    });
    $scope.search = function () {
        $scope.getData();
    }

    /**
     * 获取报告状态
     */
    $scope.getStatus = function () {
        _jiyin.dataGet('admin/report_admin/get_all_report_status/').then(function (result) {
            if (result) {
                $scope.status = result;
            }
        });
    };
    $scope.getStatus();

    //展开更多
    // $scope.push_report = function (index) {
    //     for (var i = 0; i < $scope.tempReportList[index].length; i++) {
    //         $scope.orderList[index].sub_order.push($scope.tempReportList[index][i]);
    //     }
    // };

    //收起更多
    // $scope.shift_report = function (index) {
    //     for (var i = 0; i < $scope.tempReportList[index].length; i++) {
    //         $scope.orderList[index].sub_order = $scope.orderList[index].sub_order.slice(0, 2)
    //     }
    // };

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