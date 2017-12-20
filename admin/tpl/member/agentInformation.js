/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('agentCtrl', ['$scope', '_jiyin', 'dataToURL', '$filter', function ($scope, _jiyin, dataToURL, $filter) {
    $scope.memberList = {};
    $scope.inputPage = 1;
    $scope.keyword = '';
    $scope.start_time = '';
    $scope.end_time = '';
    $scope.role_id = '';
    $scope.time = {};
    $scope.register_start_time = '';
    $scope.register_end_time = '';

    $scope.upload = function () {
        $("#upload").modal('show');
    };
    var start_time = '';
    var end_time = '';
    $scope.ok = function () {
        $("#upload").modal('hide');
    };
    $scope.$on('memberList', function (event, memberList) {
        $scope.memberList = memberList;
    });
    $scope.$on('totalPage', function (event, totalPage) {
        $scope.totalPage = totalPage;
    });

    /**
     * 同步代理商
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
        _jiyin.dataPost('admin/erp_admin/synchronize_agent_from_erp/', dataToURL({
            start_time: start_time,
            end_time: end_time
        }))
            .then(function (result) {
                if (result['success']) {
                    _jiyin.msg('s', result['msg']);
                } else {
                    _jiyin.msg('e', result['msg']);
                }

            });
    };

    /*
     搜索
     */
    $scope.search = function () {
        $scope.inputPage = 1;
        $scope.getData();
    };

    $("#search").keydown(function (e) {
        if (e.keyCode == 13) {
            $scope.getData();
        }
    });

    /**
     * 获取数据
     */
    $scope.getData = function () {
        _jiyin.dataPost('admin/user_admin/get_agents_page', dataToURL({
            page: $scope.inputPage,
            keyword: $scope.keyword,
            start_time: $scope.register_start_time,
            end_time: $scope.register_end_time
        })).then(function (result) {
            if (result.success) {
                $scope.memberList = result.data;
            } else {
                _jiyin.msg('e', result.msg);
                $scope.memberList = [];
            }
            $scope.totalPage = result.total_page;
            $scope.role_id = result.role_id;
        });
    };
    $scope.getData();

    /**
     * 增加
     */
    $scope.addList = function () {
        $scope.title = '增加数据';
        _jiyin.modal({
            tempUrl: '/source/admin/tpl/modal/modal-agentInfo.html',
            tempCtrl: 'modalAgentCtrl',
            ok: $scope.add,
            size: 'lg',
            params: {
                title: $scope.title,
                infoList: {},
                ael: 'add'
            }
        });
    };
    $scope.add = function (infoList) {
        _jiyin.dataPost('admin/user_admin/add_info', dataToURL(infoList))
            .then(function (result) {
                if (result.success == true) {
                    _jiyin.msg('s', result.msg);
                    $scope.getData();
                } else {
                    _jiyin.msg('e', result.error);
                }
            })
    };

    /**
     * 编辑
     */
    $scope.editList = function (data) {
        $scope.title = '编辑数据';
        _jiyin.modal({
            tempUrl: '/source/admin/tpl/modal/modal-agentInfo.html',
            tempCtrl: 'modalAgentCtrl',
            ok: $scope.edit,
            size: 'lg',
            params: {
                title: $scope.title,
                infoList: data,
                ael: 'edit'
            }
        });
    };
    $scope.edit = function (infoList) {
        _jiyin.dataPost('admin/user_admin/update_agent_info', dataToURL(infoList))
            .then(function (result) {
                if (result.success == true) {
                    _jiyin.msg('s', result.msg);
                    $scope.getData();
                } else {
                    _jiyin.msg('e', result.error);
                }
            })
    };

    /**
     * 修改代理商密码
     * @param data
     */
    $scope.changPasswdList = function (data) {
        $scope.title = '修改密码';
        _jiyin.modal({
            tempUrl: '/source/admin/tpl/modal/modal-agentChangePasswd.html',
            tempCtrl: 'modalAgentChangePasswdCtrl',
            ok: $scope.changePasswd,
            size: 'lg',
            params: {
                title: $scope.title,
                infoList: data,
                ael: 'edit'
            }
        });
    };
    $scope.changePasswd = function (infoList) {
        console.log(infoList);
        _jiyin.dataPost('admin/user_admin/update_agent_passwd', dataToURL(infoList))
            .then(function (result) {
                if (result.success == true) {
                    _jiyin.msg('s', result.msg);
                } else {
                    _jiyin.msg('e', result.error);
                }
                infoList.newPasswd = '';
                infoList.rePasswd = '';
            })
    };

    /**
     * 删除信息
     * @param data
     */
    $scope.deleteData = function (data) {
        if (confirm('确认删除这条数据吗?')) {
            _jiyin.dataPost('admin/user_admin/soft_delete', dataToURL({id: data.id}))
                .then(function (result) {
                    if (result.success == true) {
                        _jiyin.msg('s', '删除成功');
                        $scope.getData();
                    } else {
                        _jiyin.msg('e', result.msg);
                    }
                });
        }
    };

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

app.controller('memberFileUploadCtrl', ['$scope', 'FileUploader', '_jiyin', 'dataToURL', function ($scope, FileUploader, _jiyin, dataToURL) {
    var uploader = $scope.uploader = new FileUploader({
        url: SITE_URL + 'admin/user_admin/batch_up_user_data'
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
            _jiyin.msg('s', response.msg);
            if (response.error) {
                var error = '';
                angular.forEach(response.error, function (data, index) {
                    error = error + data + ',';
                });
                alert(error);
            }

            _jiyin.dataPost('admin/user_admin/get_page_info/', dataToURL({
                page: 1
            })).then(function (result) {
                $scope.$emit('memberList', result.data);
                $scope.$emit('totalPage', result.total_page);
            });
        } else {
            _jiyin.msg('e', response.msg);
        }
        // if(response.success == true && !response.error){
        //     _jiyin.msg('s', response.msg);
        //     _jiyin.dataPost('admin/user_admin/get_page_info/', dataToURL({
        //         page: 1
        //     })).then(function(result){
        //         $scope.$emit('memberList', result.data);
        //         $scope.$emit('totalPage', result.total_page);
        //     });
        // }else if(response.success == true && response.error){
        //     angular.forEach(response.error, function (data, index) {
        //         _jiyin.msg('e', data);
        //     });
        // }
    };
    $scope.$on('clearQueue', function () {
        uploader.clearQueue();
    });
}]);