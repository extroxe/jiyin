/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('articleCtrl', ['$scope', '_jiyin', 'dataToURL', function ($scope, _jiyin, dataToURL) {

    $scope.articleList = {};
    $scope.inputPage = 1;
    $scope.infoList = {};

    $scope.show=function(){
        $scope.title = '增加';
        $scope.add = true;
        $scope.infoList = {};
        $("#article").modal('show');
    };
    $scope.edit = function (data) {
        $scope.title = '编辑';
        $scope.infoList = data;
        $scope.add = false;
        $("#article").modal('show');
    };

    /**
     * 获取商品状态
     */
    $scope.getStatus = function () {
        _jiyin.dataPost('admin/system_code_admin/get_by_type/article_status')
            .then(function (result) {
                $scope.statusList = result;
            })
    };
    $scope.getStatus();

    $scope.$on('attachment_id', function(event, attachment_id) {
        $scope.infoList.thumbnail_id = attachment_id;
    });

    $scope.ok = function () {
        if(!$scope.infoList.title){
            _jiyin.msg('e','文章标题不能为空');
            return ;
        }
        if(!$scope.infoList.abstract){
            _jiyin.msg('e','文章摘要不能为空');
            return ;
        }
        if(!$scope.infoList.content){
            _jiyin.msg('e','文章内容不能为空');
            return ;
        }
        if(!$scope.infoList.status_id){
            _jiyin.msg('e','状态不能为空');
            return ;
        }
        if(!$scope.infoList.thumbnail_id){
            _jiyin.msg('e','还没有上传图片');
            return ;
        }

        if($scope.add == true){
            _jiyin.dataPost('admin/article_admin/add',dataToURL($scope.infoList))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s','添加成功');
                        $scope.getData();
                        $("#article").modal('hide');
                    }else{
                        _jiyin.msg('e',result.error);
                    }
                });
        }else{
            _jiyin.dataPost('admin/article_admin/update',dataToURL($scope.infoList))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s','修改成功');
                        $scope.getData();
                        $("#article").modal('hide');
                    }else{
                        _jiyin.msg('e',result.error);
                    }
                });
        }

    };
    $scope.cancel = function () {
        $("#article").modal('hide');
    };
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataGet('admin/article_admin/paginate/'+$scope.inputPage+'/10')
            .then(function(result){
                if(result.total_page === false){
                    $scope.totalPage = 1;
                }else{
                    $scope.totalPage = result.total_page;
                }
                $scope.articleList = result.data;
            });
    };
    $scope.getData();
    /**
     * 增加
     */
    /*$scope.addList = function () {
        $scope.title = '增加数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-article.html',
            tempCtrl : 'modalArticleCtrl',
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
        _jiyin.dataPost('admin/article_admin/add',dataToURL(infoList))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s','添加成功');
                    $scope.getData();
                }else{
                    _jiyin.msg('e','添加失败');
                }
            });
    };
    /!**
     * 编辑
     *!/
    $scope.editList = function (data) {
        $scope.title = '编辑数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-article.html',
            tempCtrl : 'modalArticleCtrl',
            ok : $scope.edit,
            size : 'lg',
            params : {
                title: $scope.title,
                infoList: data,
                ael: 'edit'
            }
        });
    };
    $scope.edit = function (infoList) {
        _jiyin.dataPost('admin/article_admin/update',dataToURL(infoList))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s','修改成功');
                    $scope.getData();
                }else{
                    _jiyin.msg('e','修改失败');
                }
            });
    };*/
    /**
     * 删除信息
     * @param data
     */
    $scope.deleteData = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.dataPost('admin/article_admin/delete',dataToURL({ id: data.id}))
                .then(function(result){
                    if(result.success == true){
                        _jiyin.msg('s', result.msg);
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

app.controller('FileUploadCtrl', ['$scope', 'FileUploader', '_jiyin', 'dataToURL', function($scope, FileUploader, _jiyin, dataToURL) {
    var uploader = $scope.uploader = new FileUploader({
        url: SITE_URL + 'attachment/up_attachment'
    });
    // FILTERS
    uploader.filters.push({
        name: 'customFilter',
        fn: function(item /*{File|FileLikeObject}*/ , options) {
            return this.queue.length < 2;
        }
    });
    $scope.upload = function(item){
        _jiyin.fileMd5(item._file).then(function (result) {
            _jiyin.dataPost('attachment/check_md5', dataToURL({md5_code: result.md5Code}))
                .then(function (result) {
                    if(result.exist == true){
                        $scope.$emit('attachment_id', result.attachment_id);
                        item.file.size = item._file.size;
                        item.progress = 100;
                        item.isSuccess = true;
                        item.isUploaded = true;
                        item.uploader.progress += 100/uploader.queue.length;
                    }else{
                        item.upload();
                    }
                });
        });
    };
    $scope.uploadAll = function () {
        angular.forEach(uploader.queue, function (data, index) {
            _jiyin.fileMd5(data._file).then(function (result) {
                _jiyin.dataPost('attachment/check_md5', dataToURL({md5_code: result.md5Code}))
                    .then(function (result) {
                        if(result.exist == true){
                            $scope.$emit('attachment_id', result.attachment_id);
                            data.file.size = data._file.size;
                            data.progress = 100;
                            data.isSuccess = true;
                            data.isUploaded = true;
                            uploader.progress += 100/uploader.queue.length;
                        }else{
                            data.upload();
                        }
                    });
            });
        });
    };
    uploader.onSuccessItem = function(fileItem, response, status, headers) {
        $scope.$emit('attachment_id', response.attachment_id);
    };
    $scope.$on('clearQueue', function() {
        uploader.clearQueue();
    });
}]);