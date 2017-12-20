/**
 * Created by sailwish001 on 2017/4/27.
 */
app.directive('autoFocus', function () {
    return function (scope, element) {
        element[0].focus();
    };
});


app.controller('sampleMagnet2Ctrl', ['$rootScope', '$scope', '_jiyin', 'dataToURL', function ($rootScope, $scope, _jiyin, dataToURL) {
    $scope.is_admin = $rootScope.is_admin;
    $scope.inputPage = 1;
    $scope.pageSize = 10;
    $scope.keyword = '';

    $scope.check = false;
    $scope.totalPage = 1;
    $scope.checkedArray = [];
    $scope.inputNumber = '';
    $scope.back = function () {
        window.history.go(-1);
    };


    $scope.info = {};
    $scope.submit = [];

    $scope.downloadSamples = function (is_excel) {
        $scope.submit = [];
        angular.forEach($scope.dataList, function (data) {
            $scope.checkedArray.push(data.number);
            $scope.info.number = data.number;
            $scope.submit.push(angular.copy($scope.info));
        });

        if (!$scope.submit || $scope.submit.length < 1) {
            _jiyin.msg('e', "请输入要导出的报告编号");
            return;
        }

        $('.remark_input').each(function (index, ele) {
            angular.forEach($scope.submit, function (data, eq) {
                if (data.number == $(ele).data('number') && $(ele).val() != '') {
                    data.remark = $(ele).val();
                } else if (data.number == $(ele).data('number') && $(ele).val() == '') {
                    data.remark = '';
                }
            })
        });

        $("#real_number").val(JSON.stringify($scope.submit));
        $("#is_excel").val(is_excel);
        document.getElementById("reportInputForm").submit();
    };

    //导出成CSV格式
    $scope.downloadSamplesForCSV = function () {
        $scope.submit = [];
        angular.forEach($scope.dataList, function (data) {
            $scope.checkedArray.push(data.number);
            $scope.info.number = data.number;
            $scope.submit.push(angular.copy($scope.info));
        });

        $('.remark_input').each(function (index, ele) {
            angular.forEach($scope.submit, function (data, eq) {
                if (data.number == $(ele).data('number') && $(ele).val() != '') {
                    data.remark = $(ele).val();
                } else if (data.number == $(ele).data('number') && $(ele).val() == '') {
                    data.remark = '';
                }
            })
        });
        $("#real_number").val(JSON.stringify($scope.submit));
        $("#is_excel").val(0);
        window.open(SITE_URL + 'admin/report_admin/download_report_for_csv' + params);

    };

    //录码导出模态框显示
    $scope.inputToExport = function (item) {
        $scope.inputNumber = null;

        $("#reportExportModal").modal('show');
    };

    //根据输入的检测码导出样本CSV或Excel文件
    $scope.downloadSamplesFromInput = function (is_excel) {
        var temp_number = $scope.inputNumber.replace(/\t/g, "");
        temp_number = $.trim(temp_number);
        $("#real_number").val(temp_number.replace(/\n/g, "_"));
        $("#is_excel").val(is_excel);
        document.getElementById("reportInputForm").submit();
    };

    $scope.dataList = [];
    //监听输入框回车事件
    $(document).on('keyup', '.sample-number', function (e) {
        $scope.hasReport = false;
        var this_input = $(this);
        var number = $(this).val();
        angular.forEach($scope.dataList, function (data, index) {
            if (data.number == number && e.keyCode == 13) {
                $scope.$apply(function () {
                    _jiyin.msg('e', '报告信息已存在！');
                });
                this_input.val('');
                $scope.hasReport = true;
            }
        });

        if (e.keyCode == 13 && !$scope.hasReport) {
            get_info_by_number(number, this_input)
        }
    });

    //根据编码获取信息
    $scope.not_fill_count = 0;
    $scope.fill_count = 0;
    function get_info_by_number(number, this_input) {

        _jiyin.dataPost('admin/report_admin/get_report_by_number', dataToURL({number: number}))
            .then(function (result) {
                if (result.success) {
                    $scope.dataList.push(result.data);
                    if (result.data.report_status == 0) {
                        $scope.not_fill_count++;
                    } else {
                        $scope.fill_count++;
                    }
                } else {
                    _jiyin.msg('e', result.msg);
                }
                this_input.val('');
                $('.not-fill').text($scope.not_fill_count);
                $('.fill').text($scope.fill_count);
                var h = $(document).height() - $(window).height();
                $(document).scrollTop(h);
            })
    }

    //删除报告信息
    $scope.delete = function (data) {
        for (var i = 0; i < $scope.dataList.length; i++) {
            if ($scope.dataList[i].id == data.id) {
                if ($scope.dataList[i].report_status == 0) {
                    $scope.not_fill_count--;
                } else {
                    $scope.fill_count--;
                }
                $scope.dataList.splice(i, 1);
                i--;
                _jiyin.msg('s', '删除成功！');
            }
        }
        $('.not-fill').text($scope.not_fill_count);
        $('.fill').text($scope.fill_count);
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