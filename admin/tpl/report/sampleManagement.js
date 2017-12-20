/**
 * Created by sailwish001 on 2017/4/27.
 */
app.directive('autoFocus', function(){
    return function(scope, element){
        element[0].focus();
    };
});


app.controller('sampleManagementCtrl', ['$scope', '_jiyin', 'dataToURL', '$state', '$stateParams', function ($scope, _jiyin, dataToURL, $state, $stateParams) {
    $scope.reportList = {};
    $scope.inputPage = 1;
    $scope.pageSize = 10;
    $scope.keyword = '';
    $scope.province = '';

    $scope.list = {};
    $scope.open = false;
    $scope.check = false;
    $scope.state = 2;
    $scope.register_start_time = '';
    $scope.register_end_time = '';
    $scope.checkList = new Array();
    $scope.checkState = ["全部","已填写","未填写","全部"];
    $scope.totalPage = 1;
    $scope.checkNum = 0;
    $scope.allCheckNum = 10;
    $scope.select_all = false;
    $scope.checkedArray = [];
    $scope.inputNumber = '';
    $scope.back = function () {
        window.history.go(-1);
    };

    //选中所有
    $scope.selectAll = function () {
        if ($scope.select_all) {
            $scope.checkedArray = [];
            angular.forEach($scope.reportList, function(value,index) {
                value.checked = true;
                $scope.checkedArray.push(value.id);
            });
        } else { // 清空全选
            $scope.checkedArray = [];
            angular.forEach($scope.reportList, function(value,index) {
                value.checked = false;
            });
        }
    };
    //选中单个
    $scope.selectOne = function () {
        angular.forEach($scope.reportList, function(item) {
            var localIndex = $scope.checkedArray.indexOf(item.id);
            // 选中
            if (localIndex === -1 && item.checked) {
                $scope.checkedArray.push(item.id);
            } else if (localIndex !== -1 && !item.checked) { // 取消选中
                $scope.checkedArray.splice(localIndex, 1);
            }
        });
        $scope.select_all = $scope.reportList.length === $scope.checkedArray.length;
        console.log($scope.select_all);
    };

    $scope.checked = function () {
        $scope.check  = ! $scope.check;
        if($scope.check){
            angular.forEach($scope.reportList,function (value,index) {
                value.checked = true;
            })
        }
        else {
            angular.forEach($scope.reportList,function (value,index) {
                value.checked = false;
            })
        }
    };
    $scope.listcheck = function () {
        $scope.checkNum = 0;
        angular.forEach($scope.reportList,function (value,index) {
            if (value.checked) {
                $scope.checkNum ++;
            }
        });
    };
    $scope.$watch('checkNum',function (newNum) {
        if (newNum < $scope.allCheckNum){
            $scope.check = false;
        }
        else {
            $scope.check = true;
        }
    });
    $scope.stateFlagSub = function () {
        $scope.state = 1;
        $scope.checkState[0]=$scope.checkState[1];
    };
    $scope.stateFlagNsub = function () {
        $scope.state = 0;
        $scope.checkState[0]=$scope.checkState[2];
    };
    $scope.stateFlagReset = function () {
        $scope.state = 2;
        $scope.checkState[0]=$scope.checkState[3];
    };
    /**
     * 获取数据
     */
    $scope.getData = function () {
        _jiyin.dataPost('admin/report_admin/get_report_by_page_for_sample', dataToURL({
            keyword: $scope.keyword,
            attachment:2,
            has_written : $scope.state,
            start_create_time:$scope.register_start_time,
            end_create_time:$scope.register_end_time,
            page: $scope.inputPage,
            page_size:$scope.pageSize
        })).then(function (result) {
            if(result.success){
                $scope.reportList = result.data;
                $scope.totalPage = result.total_page;
                $scope.totalNum = result.total;
                $scope.allCheckNum = $scope.reportList.length;
                angular.forEach($scope.reportList,function (value,index) {
                    value.checked = false;
                });
            }else{

            }
        });
    };
    $scope.getData();
    /*
     搜索
     */
    $scope.search = function () {
        $scope.inputPage = 1;
        _jiyin.dataPost('admin/report_admin/get_report_by_page_for_sample', dataToURL({
            keyword : $scope.keyword,
            attachment:2,
            has_written : $scope.state,
            start_create_time :$scope.register_start_time,
            end_create_time : $scope.register_end_time,
            page : $scope.inputPage,
            page_size : $scope.pageSize
        }))
            .then(function (result) {
                $scope.select_all = false;
                $scope.check = false;
                $scope.reportList = result.data;
                $scope.allCheckNum = $scope.reportList.length;
                result.total_page ? $scope.totalPage = result.total_page : $scope.totalPage = 1;
                $scope.totalNum = result.total;
                angular.forEach($scope.reportList,function (value,index) {
                    value.checked = false;
                });
            });
    };

    $("#filter").keydown(function (e) {
        if (e.keyCode == 13) {
            $scope.search();
        }
    });
    //导出成表格形式
    $scope.downloadSamples = function () {
        var params= "?";
        if ($scope.register_start_time != undefined && $scope.register_start_time != "") {
            params += "start_create_time=" + $scope.register_start_time + '&';
        }
        if ($scope.register_end_time != undefined && $scope.register_end_time != "") {
            params += "end_create_time=" + $scope.register_end_time + '&';
        }
        if ($scope.state != undefined && $scope.state >= 0) {
            params += "has_written=" + $scope.state + '&';
        }
        if ($scope.keyword != undefined && $scope.keyword != "") {
            params += "keyword=" + $scope.keyword + '&';
        }
        if($scope.checkedArray.length != 0){
            params += "report_id=" + $scope.checkedArray.join("_");
        }
        // if(params.length >2){
        //     params = params.substr(0, params.length-1);
        // }
        window.open(SITE_URL + 'admin/report_admin/download_report' + params);
    };

    //导出成CSV格式
    $scope.downloadSamplesForCSV = function () {
        var params= "?";
        if ($scope.register_start_time != undefined && $scope.register_start_time != "") {
            params += "start_create_time=" + $scope.register_start_time + '&';
        }
        if ($scope.register_end_time != undefined && $scope.register_end_time != "") {
            params += "end_create_time=" + $scope.register_end_time + '&';
        }
        if ($scope.state != undefined && $scope.state >= 0) {
            params += "has_written=" + $scope.state + '&';
        }
        if ($scope.keyword != undefined && $scope.keyword != "") {
            params += "keyword=" + $scope.keyword + '&';
        }
        if($scope.checkedArray.length != 0){
            params += "report_id=" + $scope.checkedArray.join("_");
        }
        window.open(SITE_URL + 'admin/report_admin/download_report_for_csv' + params);
    };

    //录码导出模态框显示
    $scope.inputToExport = function(item){
        $scope.inputNumber = null;

        $("#reportExportModal").modal('show');
    };

    //根据输入的检测码导出样本CSV或Excel文件
    $scope.downloadSamplesFromInput = function (is_excel) {
        var temp_number = $scope.inputNumber.replace(/\t/g,"");
        temp_number = $.trim(temp_number);
        $("#real_number").val(temp_number.replace(/\n/g,"_"));
        $("#is_excel").val(is_excel);
        document.getElementById("reportInputForm").submit();
    };

    $scope.$on('attachment_id', function(event, attachment_id) {
        $scope.list.attachment_id = attachment_id;
    });


    $scope.add = function () {
        $scope.list = {};
        $scope.flag = true;
        $scope.open = true;
        $("#reportModal").modal('show');
        /*document.getElementById("focus_number").focus();*/
        $("#focus_number")[0].focus();
        $scope.$broadcast('open', {
            open: $scope.open
        });
    };

    $scope.edit = function (data) {
        $scope.list = data;
        $scope.province = data.province;
        $scope.flag = false;
        $scope.open = true;

        $("#reportModal").modal('show');
        $scope.$broadcast('open', {
            open: $scope.open
        });
    };

    $scope.delete = function (data) {
        if(confirm('确定删除此报告吗?')){
            _jiyin.dataPost('admin/report_admin/delete', dataToURL({id: data.id}))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s', '删除成功');
                        $scope.getData();
                    }else {
                        _jiyin.msg('e', '删除失败');
                    }
                });
        }
    };

    $scope.ok = function () {
        var url;
        if(!$scope.list.number){
            _jiyin.msg('e','报告编号不能为空');
            return ;
        }
        if(!$scope.list.name){
            _jiyin.msg('e','名字不能为空');
            return ;
        }
        if(!$scope.list.gender){
            _jiyin.msg('e','性别不能为空');
            return ;
        }
        if(!$scope.list.age){
            _jiyin.msg('e','年龄不能为空');
            return ;
        }
        if(!$scope.list.phone){
            _jiyin.msg('e','电话不能为空');
            return ;
        }
        if(!$scope.list.identity_card){
            _jiyin.msg('e','身份证号不能为空');
            return ;
        }
        if(!$scope.list.attachment_id){
            _jiyin.msg('e','还没有上传报告');
            return ;
        }
        var regPhone = /^1(3|4|5|7|8)\d{9}$/;
        var regIdentity = /^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X|x)$/;
        if($scope.list.phone && regPhone.test($scope.list.phone) == false){
            _jiyin.msg('e','手机号不符合规则');
            return ;
        }
        if($scope.list.identity_card && regIdentity.test($scope.list.identity_card) == false){
            _jiyin.msg('e','身份证号不符合规则');
            return ;
        }
        if($scope.flag == true){
            url = 'admin/report_admin/add';
        }else{
            url = 'admin/report_admin/update'
        }
        $scope.list.order_commodity_id = $stateParams.id;
        _jiyin.dataPost(url, dataToURL($scope.list))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s',result.msg);
                    $scope.getData();
                    $("#reportModal").modal('hide');
                    $scope.open = false;
                }else {
                    _jiyin.msg('e', result.msg);
                }
            });
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
        }, 500)
    };
}]);

app.controller('reportFileUploadCtrl', ['$scope', 'FileUploader', '_jiyin', 'dataToURL', function ($scope, FileUploader, _jiyin, dataToURL) {
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