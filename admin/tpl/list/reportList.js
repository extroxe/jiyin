/**
 * Created by sailwish001 on 2017/4/27.
 */

app.directive('autoFocus', function () {
    return function (scope, element) {
        element[0].focus();
    };
});

app.controller('reportListCtrl', ['$scope', '_jiyin', 'dataToURL', '$state', '$stateParams', '$rootScope', function ($scope, _jiyin, dataToURL, $state, $stateParams, $rootScope) {
    $scope.is_admin = $rootScope.is_admin;
    $scope.reportList = {};
    $scope.inputPage = 1;
    $scope.keyword = '';
    $scope.province = '';
    $scope.list = {};
    $scope.list.project_num = 1;

    $scope.open = false;
    $scope.check = false;
    $scope.state = 2;
    $scope.register_start_time = '';
    $scope.register_end_time = '';
    $scope.checkList = new Array();
    $scope.checkState = ["全部", "已提交", "未提交", "全部"];
    $scope.totalPage = 1;
    $scope.checkNum = 0;
    $scope.allCheckNum = 10;

    $scope.back = function () {
        window.history.go(-1);
    };
    $scope.upload = function (item) {
        $("#upload").modal('show');
    };

    $scope.ok = function () {
        $("#upload").modal('hide');
    };

    $scope.$on("reportList", function (event, reportList) {
        $scope.reportList = reportList;
    });
    $scope.$on("totalPage", function (event, totalPage) {
        $scope.totalPage = totalPage;
    });

    $scope.openImport = function (item) {
        $scope.$broadcast('openImport', {
            open: true
        });
        $("#importModal").modal('show');
    };

    //监听
    $scope.$watch('currentUser', function (nv) {
        if (nv) {
            $scope.role_id = '';
            console.log($rootScope.currentUser.role_id);
            $scope.role_id = $rootScope.currentUser.role_id;

            if ($scope.role_id == 20 && $scope.isOnline) {
                $scope.getOnlineOrderNumber();
            } else if ($scope.role_id == 20) {
                $scope.getOfflineOrderNumber();
            }
        }
    })

    $scope.ImportReport = function () {
        $scope.$broadcast('importReportFlag', {
            open: true
        });
        $("#importReportModal").modal('show');
    };
    $scope.confirmImport = function () {
        $scope.getData();
        $("#importReportModal").modal('hide');
    };
    $scope.closeImport = function () {
        $("#importModal").modal('hide');
    };
    $scope.checked = function () {
        $scope.check = !$scope.check;
        if ($scope.check) {
            angular.forEach($scope.reportList, function (value, index) {
                value.checked = true;
            })
        }
        else {
            angular.forEach($scope.reportList, function (value, index) {
                value.checked = false;
            })
        }
    };
    if ($stateParams.reportIsOnline == 2) {
        $scope.isOnline = false;
    } else {
        $scope.isOnline = true;
    }
    $scope.listcheck = function () {
        $scope.checkNum = 0;
        angular.forEach($scope.reportList, function (value, index) {
            if (value.checked) {
                $scope.checkNum++;
            }
        });
    };
    $scope.$watch('checkNum', function (newNum) {
        if (newNum < $scope.allCheckNum) {
            $scope.check = false;
        }
        else {
            $scope.check = true;
        }
    });
    $scope.stateFlagSub = function () {
        $scope.state = 1;
        $scope.checkState[0] = $scope.checkState[1];
    };
    $scope.stateFlagNsub = function () {
        $scope.state = 0;
        $scope.checkState[0] = $scope.checkState[2];
    };
    $scope.stateFlagReset = function () {
        $scope.state = 2;
        $scope.checkState[0] = $scope.checkState[3];
    };
    /**
     * 获取数据
     */
    $scope.getData = function () {
        _jiyin.dataPost('admin/report_admin/get_report_by_page', dataToURL({
            keyword: $scope.keyword,
            attachment: $scope.state,
            has_written: 2,
            start_create_time: $scope.register_start_time,
            end_create_time: $scope.register_end_time,
            page: $scope.inputPage,
            page_size: 10,
            is_online: $stateParams.reportIsOnline
        })).then(function (result) {
            if (result.success) {
                angular.forEach(result.data, function (data, index) {
                    if (data.birth != null && data.birth != '') {
                        data.birth = data.birth.substr(0, 10);
                    }
                });
                $scope.reportList = result.data;
                $scope.allCheckNum = $scope.reportList.length;
                angular.forEach($scope.reportList, function (value, index) {
                    value.checked = false;
                });
            } else {

            }
            $scope.totalPage = result.total_page;
            $scope.totalNum = result.total;
        });
    };
    $scope.getData();
    /*
     搜索
     */
    $scope.search = function () {
        $scope.inputPage = 1;
        _jiyin.dataPost('admin/report_admin/get_report_by_page', dataToURL({
            keyword: $scope.keyword,
            attachment: $scope.state,
            has_written: 2,
            start_create_time: $scope.register_start_time,
            end_create_time: $scope.register_end_time,
            page: $scope.inputPage,
            page_size: 10,
            is_online: $stateParams.reportIsOnline
            // is_online: $stateParams.reportIsOnline
        }))
            .then(function (result) {
                if (result.success) {
                    $scope.check = false;
                    $scope.reportList = result.data;
                    $scope.allCheckNum = $scope.reportList.length;
                    angular.forEach($scope.reportList, function (value, index) {
                        value.checked = false;
                    });
                } else {
                    $scope.reportList = [];
                    _jiyin.msg('e', '未查询到相关列表');
                }
                $scope.totalPage = result.total_page;
                $scope.totalNum = result.total;
            });
    };

    /*
     同步收样信息
     */
    $scope.update_report_status = function () {
        _jiyin.dataPost('admin/Erp_admin/search_report_from_erp', dataToURL({
            start_time: $scope.register_start_time,
            end_time: $scope.register_end_time
        }))
            .then(function (result) {
                if (result.success) {
                    _jiyin.msg('s', '同步收样信息成功');
                    // $scope.getData();
                } else {
                    _jiyin.msg('e', result.msg);
                }

            });
    };

    $("#search").keydown(function (e) {
        if (e.keyCode == 13) {
            $scope.search();
        }
    });

    $scope.download = function (data) {
        /*$scope.checkList = [];
         angular.forEach($scope.reportList,function (value,index) {
         if (value.checked){
         $scope.checkList.push(value.number);
         }
         });
         if ($scope.checkList.length>0) {*/
        window.open(SITE_URL + data.path);
        /*}
         else {
         _jiyin.msg('e', '请选择要下载的报告');
         }*/
    };

    $scope.$on('attachment_id', function (event, attachment_id) {
        $scope.list.attachment_id = attachment_id;
    });

    $scope.add = function () {
        $scope.list = {};
        $scope.list.template_id = 23;
        $scope.flag = true;
        $scope.open = true;

        $scope.list.project_num = 1

        $("#reportModal").modal('show');
        setTimeout(function () {
            $('#focus_number').focus();
        }, 500);
        if ($stateParams.reportIsOnline == 2) {
            $scope.isOnline = false;
        } else {
            $scope.isOnline = true;
        }
        /*document.getElementById("focus_number").focus();*/
        $("#focus_number")[0].focus();
        $scope.$broadcast('open', {
            open: $scope.open
        });
    };

    $scope.downloadTemplate = function () {
        $scope.list = {};
        $scope.list.template_id = 23;
        $scope.flag = true;
        $scope.open = true;

        $scope.list.project_num = 1

        $("#reportTemplateModal").modal('show');
        setTimeout(function () {
            $('#focus_number').focus();
        }, 500);
        if ($stateParams.reportIsOnline == 2) {
            $scope.isOnline = false;
        } else {
            $scope.isOnline = true;
        }
        /*document.getElementById("focus_number").focus();*/
        $("#focus_number")[0].focus();
        $scope.$broadcast('open', {
            open: $scope.open
        });
    };
    /*获取模板*/
    $scope.getCommos = function () {
        _jiyin.dataPost('admin/detection_template_admin/get_detection_template')
            .then(function (result) {
                $scope.commoLists = result.data;
            })
    };
    // $scope.getCommos();
    /*获取线上订单编号*/
    $scope.getOnlineOrderNumber = function () {
        _jiyin.dataPost('admin/order_admin/get_paid_order')
            .then(function (result) {
                $scope.OrderNumberList = result.data;
            })
    };
    /*获取线下订单编号*/
    $scope.getOfflineOrderNumber = function () {
        _jiyin.dataPost('admin/order_admin/get_off_line_order')
            .then(function (result) {
                $scope.OffOrderNumberList = result.data;
            })
    };

    /*根据获取订单编号获取子订单商品*/
    $scope.getOrderCommodity = function () {
        if ($scope.isOnline) {
            _jiyin.dataPost('admin/order_admin/show_sub_order_commodity', dataToURL({order_id: $scope.list.order_id}))
                .then(function (result) {
                    $scope.OrderCommodityList = result.data;
                })
        } else {
            _jiyin.dataPost('admin/order_admin/show_sub_commodity_name', dataToURL({order_id: $scope.list.order_id}))
                .then(function (result) {
                    $scope.OrderCommodityList = result.data;
                })
        }
    };
    $scope.project_nums = 0;
    $scope.$watch('list.template_id', function (nv) {
        if (nv) {
            angular.forEach($scope.commoLists, function (data, index) {
                if (nv == data.id) {
                    $scope.project_nums = data.projects.length;
                }
            })
        }
    });
    $('#focus_number').bind('keypress', function (event) {
        if (event.keyCode == "13" && $stateParams.reportIsOnline == 2) {
            document.getElementById('project_num').focus();
        }
    });
    /* $('#templates').bind('keypress',function(e){
     if(e.keyCode == "13" && $stateParams.reportIsOnline == 2) {
     e.keyCode = '9';
     $(this).blur();
     document.getElementById('project_num').focus();
     // $('#project_num').focus();

     }
     });*/
    $('#project_num').bind('keypress', function (event) {
        if (event.keyCode == "13" && $stateParams.reportIsOnline == 2) {
            $scope.ok();
        }
    });
    $scope.is_more = false;
    $scope.checkProjectNum = function () {
        if ($scope.list.project_num > $scope.project_nums) {
            // _jiyin.msg('e', '项目数超出最大数量');
            $scope.is_more = true;
        } else {
            $scope.is_more = false;
        }
    };


    $scope.edit = function (data) {
        $scope.list = data;
        $scope.province = data.province;
        $scope.flag = false;
        $scope.open = true;
        var province = $('#province');

        province.val($scope.list.province_code);
        $scope.searchNextLevel(province[0], $scope.list.city_code, $scope.list.district_code);
        $('#province').val(data.province_code);
        $("#reportModal").modal('show');
        $scope.$broadcast('open', {
            open: $scope.open
        });
    };

    $scope.delete = function (data) {
        if (data.report_status == 1) {
            if (confirm('此报告已录入检测人信息，确定要删除吗?')) {
                _jiyin.dataPost('admin/report_admin/delete', dataToURL({
                    id: data.id,
                    number: data.number,
                    is_online: $stateParams.reportIsOnline
                }))
                    .then(function (result) {
                        if (result.success == true) {
                            _jiyin.msg('s', '删除成功');
                            $scope.getData();
                        } else {
                            _jiyin.msg('e', '删除失败');
                        }
                    });
            }
        } else {
            if (confirm('确定要删除吗?')) {
                _jiyin.dataPost('admin/report_admin/delete', dataToURL({
                    id: data.id,
                    number: data.number,
                    is_online: $stateParams.reportIsOnline
                }))
                    .then(function (result) {
                        if (result.success == true) {
                            _jiyin.msg('s', '删除成功');
                            $scope.getData();
                        } else {
                            _jiyin.msg('e', '删除失败');
                        }
                    });
            }
        }

    };

    //删除报告文件
    $scope.deleteReport = function (id) {
        _jiyin.dataPost('admin/report_admin/delete_report_attachment', dataToURL({report_id: id}))
            .then(function (result) {
                if (result.success == true) {
                    _jiyin.msg('s', '删除成功');
                    $scope.getData();
                } else {
                    _jiyin.msg('e', '删除失败');
                }
            });
    };

    //是否选择订单
    $scope.has_order_id = false;
    $scope.$watch('list.order_id', function (nv) {
        if (nv && nv != '') {
            $scope.has_order_id = true;
        } else {
            $scope.has_order_id = false;
        }
    });

    $scope.ok = function () {
        var url;
        $scope.list.birth = $('.user-birthday').val();
        if (!$scope.list.number) {
            _jiyin.msg('e', '报告编号不能为空');
            return;
        }
        if (!$scope.list.order_id || $scope.list.order_id == '') {
            _jiyin.msg('e', '订单编号不能为空');
            return;
        }
        // if (!$scope.list.order_commodity_id || $scope.list.order_commodity_id == '') {
        //     _jiyin.msg('e', '商品名称不能为空');
        //     return;
        // }
        if ($scope.has_order_id) {
            if (!$scope.list.order_commodity_id) {
                _jiyin.msg('e', '请选择订单商品');
                return;
            }
        }

        if ($scope.flag == true) {
            url = 'admin/report_admin/add';
        } else {
            url = 'admin/report_admin/update'
        }
        _jiyin.dataPost(url, dataToURL($scope.list))
            .then(function (result) {
                if (result.success == true) {
                    _jiyin.msg('s', result.msg);
                    $scope.getData();
                    $("#reportModal").modal('hide');
                    $scope.open = false;
                } else {
                    _jiyin.msg('e', result.msg);
                }
            });
    };

    $scope.download_for_template = function (is_online) {
        var url;
        if (!$scope.list.order_id) {
            _jiyin.msg('e', '请选择线下订单');
            return;
        }
        if (!$scope.list.order_commodity_id) {
            _jiyin.msg('e', '请选择线下子订单');
            return;
        }
        var params = "";
        if ($scope.list.order_id) {
            params += "?order_id=" + $scope.list.order_id;
        }
        if ($scope.list.order_commodity_id) {
            params += "&order_commodity_id=" + $scope.list.order_commodity_id;
        }
        params += "&is_online=" + is_online;
        window.open(SITE_URL + 'admin/report_admin/download_report_template' + params);
    };

    //选择地址信息
    var district = new AMap.DistrictSearch({
        level: 'country',
        showbiz: false,
        subdistrict: 1
    });
    /**
     * 初始化省市区选择控件
     */
    $scope.initAddress = function () {
        district.search('中国', function (status, result) {
            if (status == 'complete') {
                if (result.districtList.length > 0) {
                    $scope.getAdministrativeRegion(result.districtList[0]);
                } else {
                    console.log('获取省级行政区失败');
                }
            }
        });
    };
    /**
     * 解析省市区信息
     * @param data
     */
    $scope.getAdministrativeRegion = function (data, city_code, district_code) {
        var subList = data.districtList;
        var level = data.level;
        //清空下一级别的下拉列表
        if (level === 'province') {
            nextLevel = 'city';
            $("#city").innerHTML = '';
            $('#district').innerHTML = '';
            $("#city").empty();
            $("#city").val("");
            $('#district').empty();
            $('#district').val("");
        } else if (level === 'city') {
            nextLevel = 'district';
            $('#district').innerHTML = '';
            $('#district').empty();
            $('#district').val("");
        }
        if (subList) {
            if (subList.length > 0) {
                $('#' + subList[0].level).empty();
            }

            var contentSub;

            if (level == 'province') {
                contentSub = new Option('--省--');
            } else if (level == 'city') {
                contentSub = new Option('-- 区 --');
            } else {
                contentSub = new Option('--省--');
            }

            contentSub.setAttribute("value", "");
            for (var i = 0, l = subList.length; i < l; i++) {
                var name = subList[i].name;
                var value = subList[i].adcode;
                var levelSub = subList[i].level;
                var cityCode = subList[i].citycode;

                if (i == 0) {
                    document.querySelector('#' + levelSub).add(contentSub);
                    document.querySelector('#' + levelSub).removeAttribute('disabled');
                }
                contentSub = new Option(name);
                contentSub.setAttribute("value", value);
                contentSub.center = subList[i].center;
                contentSub.adcode = subList[i].adcode;

                document.querySelector('#' + levelSub).add(contentSub);
            }
            if (typeof(city_code) != 'undefined' && city_code != "" && levelSub == "city") {
                $('#' + levelSub).val(city_code);
                $scope.searchNextLevel($('#' + levelSub)[0], city_code, district_code);
            } else if (typeof(district_code) != 'undefined' && district_code != "" && levelSub == "district") {
                $('#' + levelSub).val(district_code);
            }
        } else {
            if (level == "province") {
                // 将市级、县级下拉列表置为不可用
                $("#city").attr('disabled', 'disabled');
                $("#district").attr('disabled', 'disabled');
            } else if (level == "city") {
                // 将县级下拉列表置为不可用
                $("#district").attr('disabled', 'disabled');
            }
        }

    };
    /**
     * 根据当前所选省市搜索下级行政区域列表
     * @param obj
     * @param city_code 城市代码，编辑地址时初始化控件使用
     * @param district_code 区县代码，编辑地址时初始化控件使用
     */
    $scope.searchNextLevel = function (obj, city_code, district_code) {
        var option = obj[obj.options.selectedIndex];
        var keyword = option.text; //关键字
        var adcode = option.adcode;
        city_code = city_code || '';
        district_code = district_code || '';
        district.setLevel(option.value); //行政区级别
        //行政区查询
        //按照adcode进行查询可以保证数据返回的唯一性
        district.search(adcode, function (status, result) {
            if (status === 'complete') {
                $scope.getAdministrativeRegion(result.districtList[0], city_code, district_code);
            }
        });
    };

    $scope.initAddress();

    //监听地址选择事件
    $('#province')[0].addEventListener('change', function () {
        var obj = this;
        $scope.searchNextLevel(obj);
        $scope.list.province = obj[obj.options.selectedIndex].text;
        $scope.list.province_code = obj[obj.options.selectedIndex].value;
    }, false);

    $('#city')[0].addEventListener('change', function () {
        var obj = this;
        $scope.searchNextLevel(obj);
        $scope.list.city = obj[obj.options.selectedIndex].text;
        $scope.list.city_code = obj[obj.options.selectedIndex].value;
    }, false);

    $('#district')[0].addEventListener('change', function () {
        var obj = this;
        $scope.searchNextLevel(obj);
        $scope.list.district = obj[obj.options.selectedIndex].text;
        $scope.list.district_code = obj[obj.options.selectedIndex].value;
    }, false);


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
    var timer;
    $scope.selectPage = function (page) {
        clearTimeout(timer);
        timer = setTimeout(function () {
            if ($scope.totalPage == 1) {
                _jiyin.msg('e', '当前是最后一页');
            }
            else {
                $scope.check = false;
                $scope.inputPage = page;
                $scope.getData();
            }
        }, 500);

    };
    //接收下级控制器发射的error信息（报告导入产生的错误信息）
    $scope.$on('error', function (event, args) {
        $scope.error = args;
    });
    //接收下级控制器发射的importInfoMsg信息（报告导入产生的提示信息）
    $scope.$on('importInfoMsg', function (event, args) {
        $scope.importInfoMsg = args;
    });
    //关闭失败信息模态框并显示提示信息
    $scope.closeError = function () {
        $("#reportErrorModal").modal('hide');
        _jiyin.msg('w', $scope.importInfoMsg);
    };
    $scope.closeData = function () {
        $("#reportResultModal").modal('hide');
        $scope.getData();
    };

    $scope.$on('reportData', function (data, egv) {
        angular.forEach(egv, function (item, index) {
            item.selectSingle = false;
        });
        $scope.resultData = egv;
    });

//    覆盖全选
    $scope.reportsIds = [];
    $scope.reportData = [];
    $scope.selectAll = function (e) {
        if ($(e.target).prop('checked')) {
            $scope.reportsIds = [];
            angular.forEach($scope.resultData, function (data, index) {
                $scope.reportsIds.push(data.report_id);
            });
            $scope.reportData = $scope.resultData;
            $('.select-single').prop('checked', true);
        } else {
            $scope.reportsIds = [];
            $('.select-single').prop('checked', false);
            $scope.reportData = [];
        }
        console.log($scope.reportsIds);
        console.log($scope.reportData);
    };

//    覆盖单选
    $scope.selectSingle = function (data, e) {
        if ($(e.target).prop('checked') && $.inArray(data.report_id, $scope.reportsIds) == -1) {
            $scope.reportsIds.push(data.report_id);
            $scope.reportData.push(data);
        } else if (!$(e.target).prop('checked') && $.inArray(data.report_id, $scope.reportsIds) != -1) {
            for (var i = 0; i < $scope.reportsIds.length; i++) {
                var eq = $.inArray(data.report_id, $scope.reportsIds);
                if (eq != -1) {
                    $scope.reportsIds.splice(eq, 1);
                    $scope.reportData.splice(eq, 1);
                    i--;
                }
            }
        }
        if ($('.select-single:checked').length == $scope.resultData.length) {
            $('.select-all').prop('checked', true);
        } else {
            $('.select-all').prop('checked', false);
        }

        console.log($scope.reportsIds);
        console.log($scope.reportData);
    };

//    覆盖报告
    $scope.coverSingle = function (report) {
        $scope.coverReport(new Array(report));
    };
    $scope.coverData = function () {
        $scope.coverReport($scope.reportData);
    };


    $scope.coverReport = function (data) {
        $scope.submitData = angular.copy(data);
        angular.forEach($scope.submitData, function (data, index) {
            $.each(data, function (key, val) {
                if (key != 'report_id' && key != 'now_attachment_id') {
                    delete data[key];
                }
            });
        });
        if (confirm('确认覆盖已存在的报告信息吗')) {
            _jiyin.dataPost('admin/report_admin/batch_update_report', dataToURL({val: JSON.stringify($scope.submitData)}))
                .then(function (result) {
                    if (result.success) {
                        _jiyin.msg('s', '操作成功');

                    } else {
                        _jiyin.msg('e', result.msg);
                    }
                    $scope.getData();
                })
        }
    }
}]);

app.controller('reportFileUploadCtrl', ['$scope', 'FileUploader', '$modal', '_jiyin', 'dataToURL', function ($scope, FileUploader, $modal, _jiyin, dataToURL) {
    var uploader = $scope.uploader = new FileUploader({
        url: SITE_URL + 'attachment/upload_report'
    });
    $scope.$on('open', function (event, args) {
        if (args.open == true) {
            uploader.clearQueue();
        }
    });
    // FILTERS
    uploader.filters.push({
        name: 'customFilter',
        fn: function (item /*{File|FileLikeObject}*/, options) {
            return this.queue.length < 2;
        }
    });
    $scope.upload = function (item) {
        if (uploader.queue.length > 1) {
            _jiyin.msg('e', '只能上传一个文件');
            return;
        }
        _jiyin.fileMd5(item._file).then(function (result) {
            _jiyin.dataPost('attachment/check_md5', dataToURL({md5_code: result.md5Code}))
                .then(function (result) {
                    if (result.exist == true) {
                        $scope.$emit('attachment_id', result.attachment_id);
                        item.file.size = item._file.size;
                        item.progress = 100;
                        item.isSuccess = true;
                        item.isUploaded = true;
                        item.uploader.progress += 100 / uploader.queue.length;
                    } else {
                        item.upload();
                    }
                });
        });
    };
    $scope.uploadAll = function () {
        if (uploader.queue.length > 1) {
            _jiyin.msg('e', '只能上传一个文件');
            return;
        }
        angular.forEach(uploader.queue, function (data, index) {
            _jiyin.fileMd5(data._file).then(function (result) {
                _jiyin.dataPost('attachment/check_md5', dataToURL({md5_code: result.md5Code}))
                    .then(function (result) {
                        if (result.exist == true) {
                            $scope.$emit('attachment_id', result.attachment_id);
                            data.file.size = data._file.size;
                            data.progress = 100;
                            data.isSuccess = true;
                            data.isUploaded = true;
                            uploader.progress += 100 / uploader.queue.length;
                        } else {
                            data.upload();
                        }
                    });
            });
        });
    };
    uploader.onSuccessItem = function (fileItem, response, status, headers) {
        $scope.$emit('attachment_id', response.attachment_id);
    };
    $scope.$on('clearQueue', function () {
        uploader.clearQueue();
    });

}]);

app.controller('reportTemplateFileUploadCtrl', ['$scope', 'FileUploader', '_jiyin', 'dataToURL', function ($scope, FileUploader, _jiyin, dataToURL) {
    var uploader = $scope.uploader = new FileUploader({
        url: SITE_URL + 'admin/report_admin/batch_up_report_info'
    });
    $scope.$on('openImport', function (event, args) {
        if (args.open == true) {
            uploader.clearQueue();
        }
    });
    // FILTERS
    uploader.filters.push({
        name: 'customFilter',
        fn: function (item /*{File|FileLikeObject}*/, options) {
            return this.queue.length < 2;
        }
    });

    $scope.upload = function (item) {
        if (uploader.queue.length > 1) {
            _jiyin.msg('e', '只能上传一个文件');
            return;
        }
        item.upload();
    };
    $scope.uploadAll = function () {
        if (uploader.queue.length > 1) {
            _jiyin.msg('e', '只能上传一个文件');
            return;
        }
        angular.forEach(uploader.queue, function (data, index) {
            data.upload();
        });
    };
    uploader.onSuccessItem = function (fileItem, response, status, headers) {
        if (response.success == true) {
            $("#importModal").modal('hide');
            if (response.all == true) {
                _jiyin.msg('s', response.msg);
            } else {
                $scope.$emit('error', response.error);
                $scope.$emit('importInfoMsg', response.msg);
                $("#reportErrorModal").modal('show');
            }


            _jiyin.dataPost('admin/report_admin/get_report_by_page/', dataToURL({
                page: 1,
                keyword: '',
                attachment: 2,
                has_written: 2,
                start_create_time: '',
                end_create_time: '',
                page_size: 10,
                is_online: 2
            })).then(function (result) {
                if (result.success) {
                    $scope.$emit('reportList', result.data);
                    $scope.$emit('totalPage', result.total_page);
                } else {
                    _jiyin.msg('s', result.msg);
                }
            });
        } else {
            _jiyin.msg('e', response.msg);
        }
    };
    $scope.$on('openImport', function () {
        uploader.clearQueue();
    });
}]);

app.controller('importReportCtrl', ['$scope', 'FileUploader', '_jiyin', 'dataToURL', function ($scope, FileUploader, _jiyin, dataToURL) {
    var uploader = $scope.uploader = new FileUploader({
        url: SITE_URL + 'admin/report_admin/batch_upload_report_info'
    });
    $scope.$on('importReportFlag', function (event, args) {
        if (args.open == true) {
            uploader.clearQueue();
        }
    });
    // FILTERS
    uploader.filters.push({
        name: 'customFilter',
        fn: function (item /*{File|FileLikeObject}*/, options) {
            return this.queue.length < 2;
        }
    });
    $scope.upload = function (item) {
        if (uploader.queue.length > 1) {
            _jiyin.msg('e', '只能上传一个文件');
            return;
        }
        item.upload();
    };
    uploader.onSuccessItem = function (fileItem, response, status, headers) {
        if (response.success == true) {
            _jiyin.msg('s', response.msg);
            $("#importReportModal").modal('hide');
            if (response.data != null && response.data.length > 0) {
                $scope.$emit('reportData', response.data);
                $("#reportResultModal").modal('show');
            }
        } else {
            _jiyin.msg('e', response.msg);
        }
    };
}]);