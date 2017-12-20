/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('integralCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', function ($scope, _jiyin, dataToURL, $stateParams, $state) {
    $scope.integralList = {};
    $scope.list = {};
    $scope.inputPage = 1;
    $scope.isPoint = true;
    $scope.picList = [];
    $scope.keyword = '';
    $scope.urlPC = '';
    $scope.urlWC = '';
    $scope.open = false;

    /**
     * 进入商品规格页面
     */
    $scope.specification = function (data) {
        $state.go('app.specification', {commodity_id: data.id, type: 'int'});
    };

    /**
     * 查看评价页面
     * @param data  积分商品信息
     */
    $scope.lookEva = function (data) {
        $state.go('app.evaluate', {commodity_id: data.id,type: 'int'});
    };

    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/commodity_admin/paginate/' + $scope.inputPage + '/10', dataToURL({
            keyword: $scope.keyword,
            is_point: 1,
            agent_id:'admin'
        })).then(function(result){
            if (result.success) {
                $scope.integralList = result.data;
                $scope.totalPage = result.total_page;
            } else {
                $scope.inputPage = 1;
                $scope.totalPage = 1;
                _jiyin.msg('e', result.msg);
            }
        });
    };
    $scope.getData();

    $("#search").keydown(function (e) {
        if(e.keyCode==13) {
            $scope.getData();
        }
    });

    $scope.show = function(){
        $scope.title = '增加';
        $scope.ael = 'add';
        $scope.list = {};
        $scope.url = false;
        $scope.getThumbnail();
        $scope.getType();
        $scope.getCate();
        $scope.getLevel();
        $scope.open = true;
        $scope.$broadcast('open', {
            open: $scope.open
        });
        $("#add").modal('show');
    };
    $scope.edit = function(data){
        $scope.title = '编辑';
        $scope.ael = 'edit';
        $scope.url = true;
        $scope.urlPC = SITE_URL + '/commodity/index/' + data.id;
        $scope.urlWC = SITE_URL + '/weixin/index/commodity_detail/' + data.id;
        $scope.list = data;
        $scope.getThumbnail();
        $scope.getType();
        $scope.getCate();
        $scope.getLevel();
        $scope.open = true;
        $scope.$broadcast('open', {
            open: $scope.open
        });
        $("#add").modal('show');
    };

    /**
     * 获取缩略图
     */
    $scope.getThumbnail = function(){
        if($scope.list.id){
            _jiyin.dataPost('admin/commodity_admin/show_thumbnail', dataToURL({commodity_id: $scope.list.id}))
                .then(function (result) {
                    $scope.picList = result.data;
                });
        }
    };

    /**
     * 获取商品类型
     */
    $scope.getType = function () {
        $scope.typeList = [];
        _jiyin.dataPost('admin/system_code_admin/get_by_type/commodity_type')
            .then(function (result) {
                if(result){
                    angular.forEach(result, function (data) {
                        if(data.value != '1'){
                            $scope.typeList.push(data);
                        }
                    });

                }
            });
    };

    /**
     * 获取等级
     */
    $scope.getLevel = function () {
        _jiyin.dataGet('admin/level_admin/get_level')
            .then(function(result){
                if (result.success) {
                    $scope.levelList = result.data;
                } else {
                    $scope.levelList = [];
                    _jiyin.msg('e', result.msg);
                }
            });
    };

    /**
     * 获取商品分类
     */
    $scope.getCate = function () {
        _jiyin.dataPost('admin/category_admin/get_categories')
            .then(function (result) {
                if (result.success) {
                    $scope.cateList = result.data;
                } else {
                    $scope.cateList = [];
                    _jiyin.msg('e', result.msg);
                }
            });
    };

    //上传图片
    $scope.$on('attachment_ids', function(event, attachment_ids) {
        if (!$scope.list.attachment_ids){
            $scope.list.attachment_ids = [];
        }
        $scope.list.attachment_ids.push(attachment_ids);
    });
    $scope.$on('path', function(event, path) {
        if ($scope.picList == null){
            $scope.picList = [];
        }
        var thumb = [];
        thumb['path'] = path;
        $scope.picList.push(thumb);
    });

    /**
     * 删除图片
     */
    $scope.removePic = function (id, index) {
        if(confirm('确定删除该图片吗?')){
            if(!id){
                $scope.picList.splice(index, 1);
            }else{
                _jiyin.dataPost('admin/commodity_admin/delete_thumbnail',dataToURL({id: id}))
                    .then(function (result) {
                        $scope.picList.splice(index, 1);
                        $scope.getThumbnail();
                    });
            }
        }
    };

    $scope.ok = function () {
        if(!$scope.list.name){
            _jiyin.msg('e','商品名称不能为空');
            return ;
        }
        if(!$scope.list.number){
            _jiyin.msg('e','商品编号不能为空');
            return ;
        }
        if(!$scope.list.category_id){
            _jiyin.msg('e','商品分类不能为空');
            return ;
        }
        if(!$scope.list.type_id){
            _jiyin.msg('e','商品类型不能为空');
            return ;
        }
        if(!$scope.list.detail){
            _jiyin.msg('e','商品详情不能为空');
            return ;
        }
        if($scope.ael == 'add'){
            $scope.list.is_point = 1;
            _jiyin.dataPost('admin/commodity_admin/add',dataToURL($scope.list))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s','添加成功');
                        $scope.getData();
                        $("#add").modal('hide');
                    }else{
                        _jiyin.msg('e', result.msg);
                    }
                });
        }else{
            _jiyin.dataPost('admin/commodity_admin/update',dataToURL($scope.list))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s','修改成功');
                        $scope.getData();
                        $("#add").modal('hide');
                    }else{
                        _jiyin.msg('e', result.msg);
                    }
                });
        }
    };

    /**
     * 删除信息
     * @param data
     */
    $scope.deleteData = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.dataPost('admin/commodity_admin/delete',dataToURL({ id: data.id}))
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

app.controller('intFileUploadCtrl', ['$scope', 'FileUploader', '_jiyin', 'dataToURL', function($scope, FileUploader, _jiyin, dataToURL) {
    $scope.attachment_ids = [];
    var uploader = $scope.uploader = new FileUploader({
        url: SITE_URL + 'attachment/up_attachment',
        removeAfterUpload: true,
        queueLimit: 5
    });
    $scope.$on('open', function (event, args) {
        if(args.open == true){
            uploader.clearQueue();
        }
    });
    // FILTERS
    uploader.filters.push({
        name: 'customFilter',
        fn: function(item /*{File|FileLikeObject}*/ , options) {
            return this.queue.length < 6;
        }
    });

    $scope.upload = function(item){
        _jiyin.fileMd5(item._file).then(function (result) {
            _jiyin.dataPost('attachment/check_md5', dataToURL({md5_code: result.md5Code}))
                .then(function (result) {
                    if(result.exist == true){
                        $scope.attachment_ids = [];
                        $scope.attachment_ids.push(result.attachment_id);
                        $scope.$emit('attachment_ids', $scope.attachment_ids);
                        $scope.$emit('path', result.path);
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
                            $scope.attachment_ids = [];
                            $scope.attachment_ids.push(result.attachment_id);
                            $scope.$emit('attachment_ids', $scope.attachment_ids);
                            $scope.$emit('path', result.path);
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
        $scope.attachment_ids = [];
        $scope.attachment_ids.push(response.attachment_id);
        $scope.$emit('attachment_ids', $scope.attachment_ids);
        $scope.$emit('path', response.url);
    };
    $scope.$on('clearQueue', function() {
        uploader.clearQueue();
    });
}]);