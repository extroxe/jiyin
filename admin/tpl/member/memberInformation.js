/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('memberCtrl', ['$scope', '_jiyin', 'dataToURL', function ($scope, _jiyin, dataToURL) {
    $scope.memberList = {};
    $scope.inputPage = 1;
    $scope.keyword = '';
    $scope.role_id = '';

    $scope.upload=function(){
        $("#upload").modal('show');
    };

    $scope.ok = function () {
        $("#upload").modal('hide');
    };
    $scope.$on('memberList', function(event, memberList) {
        $scope.memberList = memberList;
    });
    $scope.$on('totalPage', function(event, totalPage) {
        $scope.totalPage = totalPage;
    });

    /*
    搜索
     */
    $scope.search = function () {
        $scope.inputPage = 1;
        _jiyin.dataPost('admin/user_admin/get_page_info/', dataToURL({
            page: $scope.inputPage,
            keyword: $scope.keyword
        }))
            .then(function(result){
                $scope.memberList = result.data;
                $scope.totalPage = result.total_page;
                $scope.role_id = result.role_id;
            });
    };

    //回车查询
    $('#search').on('keyup', function (event) {
        var e = window.event || event;
        if (e.keyCode == 13) {
            $scope.search();
        }
    });
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/user_admin/get_page_info/', dataToURL({
            page: $scope.inputPage,
            keyword: $scope.keyword
        }))
            .then(function(result){
                $scope.memberList = result.data;
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
            tempUrl : '/source/admin/tpl/modal/modal-userInfo.html',
            tempCtrl : 'modalUserCtrl',
            ok : $scope.add,
            size : 'lg',
            params : {
                title: $scope.title,
                infoList: {},
                ael: 'add'
            }
        });
    };
    $scope.add = function (infoList) {
        _jiyin.dataPost('admin/user_admin/add_info', dataToURL(infoList))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s',result.msg);
                    $scope.getData();
                }else{
                    _jiyin.msg('e',result.error);
                }
            })
    };
    /**
     * 编辑
     */
    $scope.editList = function (data) {
        $scope.title = '编辑数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-userInfo.html',
            tempCtrl : 'modalUserCtrl',
            ok : $scope.edit,
            size : 'lg',
            params : {
                title: $scope.title,
                infoList: data,
                role_id: $scope.role_id,
                ael: 'edit'
            }
        });
    };
    $scope.edit = function (infoList) {
        _jiyin.dataPost('admin/user_admin/update_info', dataToURL(infoList))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s',result.msg);
                    $scope.getData();
                }else{
                    _jiyin.msg('e',result.error);
                }
            })
    };
    /**
     * 删除信息
     * @param data
     */
    $scope.deleteData = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.dataPost('admin/user_admin/soft_delete',dataToURL({ id: data.id}))
                .then(function(result){
                    if(result.success == true){
                        _jiyin.msg('s', '删除成功');
                        $scope.getData();
                    }else {
                        _jiyin.msg('e', result.msg);
                    }
                });
        }
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

app.controller('memberFileUploadCtrl', ['$scope', 'FileUploader', '_jiyin', 'dataToURL', function($scope, FileUploader, _jiyin, dataToURL) {
    var uploader = $scope.uploader = new FileUploader({
        url: SITE_URL + 'admin/user_admin/batch_up_user_data'
    });
    // FILTERS
    uploader.filters.push({
        name: 'customFilter',
        fn: function(item /*{File|FileLikeObject}*/ , options) {
            return this.queue.length < 2;
        }
    });

    $scope.upload = function(item){
        if(uploader.queue.length > 1){
            _jiyin.msg('e', '只能上传一个文件');
            return;
        }
        item.upload();
    };
    $scope.uploadAll = function () {
        if(uploader.queue.length > 1){
            _jiyin.msg('e', '只能上传一个文件');
            return;
        }
        angular.forEach(uploader.queue, function (data, index) {
            data.upload();
        });
    };
    uploader.onSuccessItem = function(fileItem, response, status, headers) {
        if(response.success == true){
            _jiyin.msg('s', response.msg);
            if(response.error){
                var error = '';
                angular.forEach(response.error, function (data, index) {
                    error = error + data + ',';
                });
                alert(error);
            }

            _jiyin.dataPost('admin/user_admin/get_page_info/', dataToURL({
                page: 1
            })).then(function(result){
                $scope.$emit('memberList', result.data);
                $scope.$emit('totalPage', result.total_page);
            });
        }else{
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
    $scope.$on('clearQueue', function() {
        uploader.clearQueue();
    });
}]);