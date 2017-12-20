/**
 * Created by sailwish001 on 2017/4/27.
 */

app.directive('autoFocus', function () {
    return function (scope, element) {
        element[0].focus();
    };
});

app.controller('reportListCtrl1', ['$scope', '_jiyin', 'dataToURL', '$state', '$stateParams', '$rootScope', function ($scope, _jiyin, dataToURL, $state, $stateParams, $rootScope) {
    $scope.is_admin = $rootScope.is_admin;
    $scope.inputPage = 1;
    $scope.pageSize = 10;

    $scope.open = false;
    $scope.check = false;
    //搜索变量
    $scope.keywords = '';
    $scope.start_time = '';
    $scope.end_time = '';
    $scope.agent_id = '';
    $scope.role_id = '';

    //监听
    $scope.$watch('currentUser', function (nv) {
        if (nv) {
            $scope.role_id = $rootScope.currentUser.role_id;
        }
        if ($rootScope.currentUser.role_id == 20) {
            _jiyin.dataPost('admin/user_admin/get_agents')
                .then(function (result) {
                    if (result.success == true) {
                        $scope.agents = result.data;
                    } else {
                        _jiyin.msg('e', result.msg);
                    }
                });
        }
    })

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

    $scope.deleteBatch = function () {
        $scope.$broadcast('deleteReportFlag', {
            open: true
        });
        $("#deleteReportModal").modal('show');
    };
    $scope.confirmReport = function () {
        $("#deleteReportModal").modal('hide');
    };

    //获取数据
    $scope.reportList = [];
    $scope.getData = function () {
        _jiyin.dataPost('admin/report_admin/get_report_by_suborder/' + $scope.inputPage + '/' + $scope.pageSize, dataToURL({
            keywords: $scope.keywords,
            start_time: $scope.start_time,
            end_time: $scope.end_time,
            agent_id: $scope.agent_id
        })).then(function (response) {
            if (response.success) {
                angular.forEach(response.data, function (data) {
                    if (data.birth != null && data.birth != '') {
                        data.birth = data.birth.substr(0, 10);
                    }
                });
                $scope.reportList = response.data;

                //展开收起中间变量
                $scope.tempReportList = [];
                for (var i = 0; i < $scope.reportList.length; i++) {
                    if ($scope.reportList[i].report_list.length > 2) {
                        $scope.tempReportList[i] = $scope.reportList[i].report_list.slice(2, $scope.reportList[i].report_list.length);
                        $scope.reportList[i].report_list = $scope.reportList[i].report_list.slice(0, 2)
                    } else {
                        $scope.tempReportList[i] = [];
                    }
                }
            } else {
                _jiyin.msg('e', response.msg);
            }
            $scope.totalPage = response.total_page;
            $scope.totalNum = response.total_num;
        });
    };
    $scope.getData();

    //搜索
    $scope.search = function () {
        $scope.inputPage = 1;
        _jiyin.dataPost('admin/report_admin/get_report_by_suborder/' + $scope.inputPage + '/' + $scope.pageSize, dataToURL({
            keywords: $scope.keywords,
            start_time: $scope.start_time,
            end_time: $scope.end_time,
            agent_id: $scope.agent_id
        }))
            .then(function (response) {
                if (response.success) {
                    angular.forEach(response.data, function (data) {
                        if (data.birth != null && data.birth != '') {
                            data.birth = data.birth.substr(0, 10);
                        }
                    });
                    $scope.reportList = response.data;

                    //展开收起中间变量
                    $scope.tempReportList = [];
                    for (var i = 0; i < $scope.reportList.length; i++) {
                        if ($scope.reportList[i].report_list.length > 2) {
                            $scope.tempReportList[i] = $scope.reportList[i].report_list.slice(2, $scope.reportList[i].report_list.length);
                            $scope.reportList[i].report_list = $scope.reportList[i].report_list.slice(0, 2)
                        } else {
                            $scope.tempReportList[i] = [];
                        }
                    }
                } else {
                    $scope.reportList = [];
                    _jiyin.msg('e', response.msg);
                }
                $scope.totalPage = response.total_page;
                $scope.totalNum = response.total_num;
            });
    };

    //回车响应搜索
    $("#search").keydown(function (e) {
        if (e.keyCode == 13) {
            $scope.search();
        }
    });

    //删除报告
    $scope.delete = function (data) {
        var info = '';
        if (data.report_status == 1) {
            info = '此报告已录入检测人信息，确定要删除吗?';
        } else {
            info = '确定要删除吗?';
        }

        if (confirm(info)) {
            _jiyin.dataPost('admin/report_admin/delete', dataToURL({id: data.id}))
                .then(function (result) {
                    if (result.success == true) {
                        _jiyin.msg('s', '删除成功');
                        $scope.getData();
                    } else {
                        _jiyin.msg('e', '删除失败');
                    }
                });
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

    //展开更多
    $scope.push_report = function (index) {
        for (var i = 0; i < $scope.tempReportList[index].length; i++) {
            $scope.reportList[index].report_list.push($scope.tempReportList[index][i]);
        }
    };

    //收起更多
    $scope.shift_report = function (index) {
        for (var i = 0; i < $scope.tempReportList[index].length; i++) {
            $scope.reportList[index].report_list = $scope.reportList[index].report_list.slice(0, 2)
        }
    }

    //下载报告
    $scope.download = function (data) {
        window.open(SITE_URL + data.report_attachment);
    };

    $scope.$on('attachment_id', function (event, attachment_id) {
        $scope.reportData.attachment_id = attachment_id;
    });

    //编辑报告
    $scope.edit = function (order, report) {
        //编辑模态框子订单信息
        $scope.order = order;
        //编辑模态框报告信息
        $scope.reportData = report;
        $scope.province = report.province;
        $scope.is_edit = true;
        $scope.open = true;
        var province = $('#province');

        province.val($scope.reportData.province_code);
        $scope.searchNextLevel(province[0], $scope.reportData.city_code, $scope.reportData.district_code);
        $('#province').val(report.province_code);
        $("#reportModal").modal('show');
        $scope.$broadcast('open', {
            open: $scope.open
        });
    };

    //添加报告
    $scope.add = function () {
        $scope.reportData = {};
        $scope.is_edit = false;
        $scope.open = true;

        $("#reportModal").modal('show');
        setTimeout(function () {
            $('#focus_number').focus();
        }, 500);

        $("#focus_number")[0].focus();
        $scope.$broadcast('open', {
            open: $scope.open
        });
    };

    //导入模板
    $scope.download_for_template = function (order) {
        var url = SITE_URL + 'admin/report_admin/download_report_template?order_id=' + order.order_id + '&order_commodity_id=' + order.order_commodity_id;
        if (order.terminal_type == 3) {
            url = url + '&is_online=2';
        } else {
            url = url + '&is_online=1';
        }
        window.open(url);
    };

    //获取模板
    $scope.getCommos = function () {
        _jiyin.dataPost('admin/detection_template_admin/get_detection_template')
            .then(function (result) {
                if (result.success) {
                    $scope.commoLists = result.data;
                } else {
                    $scope.commoLists = [];
                }
            })
    };
    // $scope.getCommos();

    //获取选择的模板最大项目数
    $scope.max_project = 0;
    $scope.$watch('reportData.template_id', function (nv) {
        if (nv) {
            angular.forEach($scope.commoLists, function (data, index) {
                if (nv == data.id) {
                    $scope.max_project = data.projects.length;
                }
            })
        }
    });

    $('#focus_number').bind('keypress', function (event) {
        if (event.keyCode == "13") {
            document.getElementById('project_num').focus();
        }
    });
    $('#project_num').bind('keypress', function (event) {
        if (event.keyCode == "13") {
            $scope.ok();
        }
    });

    //输入检测项目数时，判断输入是否大于最大项目数
    $scope.is_more = false;
    $scope.checkProjectNum = function () {
        if ($scope.reportData.project > $scope.max_project) {
            $scope.is_more = true;
        } else {
            $scope.is_more = false;
        }
    };

    //是否选择订单
    $scope.has_order_id = false;
    $scope.$watch('order.order_id', function (nv) {
        if (nv && nv != '') {
            $scope.has_order_id = true;
        } else {
            $scope.has_order_id = false;
        }
    });

    //模态框确定按钮，添加或编辑报告
    $scope.ok = function () {
        var url;

        $scope.reportData.birth = $('.user-birthday').val();
        if ($scope.is_edit == false) {
            url = 'admin/report_admin/add';
        } else {
            url = 'admin/report_admin/update'
        }
        _jiyin.dataPost(url, dataToURL($scope.reportData))
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
        $scope.reportData.province = obj[obj.options.selectedIndex].text;
        $scope.reportData.province_code = obj[obj.options.selectedIndex].value;
    }, false);

    $('#city')[0].addEventListener('change', function () {
        var obj = this;
        $scope.searchNextLevel(obj);
        $scope.reportData.city = obj[obj.options.selectedIndex].text;
        $scope.reportData.city_code = obj[obj.options.selectedIndex].value;
    }, false);

    $('#district')[0].addEventListener('change', function () {
        var obj = this;
        $scope.searchNextLevel(obj);
        $scope.reportData.district = obj[obj.options.selectedIndex].text;
        $scope.reportData.district_code = obj[obj.options.selectedIndex].value;
    }, false);


    /**
     * 下一页
     */
    $scope.nextPage = function () {
        if ($scope.inputPage < $scope.totalPage) {
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
        } else {
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
        } else {
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
                $scope.inputPage = page;
                $scope.getData();
            }
        }, 500)
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


app.controller('deleteReportCtrl', ['$scope', 'FileUploader', '_jiyin', 'dataToURL', function ($scope, FileUploader, _jiyin, dataToURL) {
    var uploader = $scope.uploader = new FileUploader({
        url: SITE_URL + 'admin/report_admin/batch_delete_report'
    });
    $scope.$on('deleteReportFlag', function (event, args) {
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
            $("#deleteReportModal").modal('hide');
            if (response.data != null && response.data.length > 0) {
                $scope.$emit('reportData', response.data);
                $("#deleteReportModal").modal('show');
            }
        } else {
            _jiyin.msg('e', response.msg);
        }
    };
}]);