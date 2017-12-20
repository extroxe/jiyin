/**
 * Created by sailwish001 on 2016/12/2.
 */
app.controller('specificationCtrl', ['$scope', '$state', '_jiyin', 'dataToURL', '$stateParams', function ($scope, $state, _jiyin, dataToURL, $stateParams) {
    $scope.commodity_id = $stateParams.commodity_id;
    $scope.specificationList = {};
    $scope.inputPage = 1;
    $scope.picList = [];
    $scope.keyword = '';
    $scope.list = {};
    $scope.commodity_type = $stateParams.commodity_type;
   
    $scope.dataShow = true;
    if($stateParams.type == 'com'){
        $scope.flag = true;
        $scope.isPoint = false;
    }else if($stateParams.type == 'int'){
        $scope.flag = false;
        $scope.isPoint = true;
    }

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

    /*
     搜索、获取规格数据
     */
    $scope.getData = function () {
        _jiyin.dataPost('admin/commodity_admin/paginate_for_specification/' + $scope.inputPage + '/10' + '/' + $scope.commodity_id, dataToURL({
            keyword: $scope.keyword
        }))
            .then(function(result){
                if(result.success){
                    $scope.dataShow = true;
                    $scope.totalPage = result.total_page;
                    $scope.specificationList = result.data;
                }else{
                    $scope.totalPage = 1;
                    $scope.dataShow = false;
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
    
    //删除规格
    $scope.delete = function (specification_id) {
        _jiyin.dataPost('admin/commodity_admin/delete_commodity_specification', dataToURL({
            specification_id : specification_id
        })).then(function(result){
                if(result.success){
                    _jiyin.msg('s', result.msg);
                    $scope.getData();
                }else{
                    _jiyin.msg('e', result.msg);
                }
            });
    }

    $scope.show = function(){
        $scope.title = '增加';
        $scope.ael = 'add';
        $scope.list = {};
        $scope.url = false;
        $scope.list.commodity_id = $stateParams.commodity_id;
        $scope.open = true;
        $scope.getStatus();
        $scope.$broadcast('open', {
            open: $scope.open
        });
        $("#add").modal('show');
    };

    $scope.edit = function(data){
        $scope.title = '编辑';
        $scope.picList = [];
        $scope.ael = 'edit';
        $scope.list = angular.copy(data);
        $scope.url = true;
        $scope.urlPC = SITE_URL+'commodity/index/' + data.commodity_id + '/' + data.id;
        $scope.urlWC = SITE_URL+'weixin/index/commodity_detail/' + data.commodity_id + '/' + data.id;
        $scope.getThumbnail();
        $scope.getStatus();
        $scope.open = true;
        $scope.$broadcast('open', {
            open: $scope.open
        });
        $("#add").modal('show');
    };

    //检测模板管理模态框
    $scope.detectionManage = function (data) {
        $('#detection').modal('show');
        $scope.getTemplate();
        $scope.getSpecificationTemplate(data.id);
        //当前规格ID
        $scope.currentId = data.id;
        $scope.add = [];
    }
    // 获取包装类型
    $scope,getPackType = function(){
        _jiyin.dataGet('admin/system_code_admin/get_by_type/packagetype')
            .then(function (response) {
                if(response){
                    $scope.packList = response;
                }else{
                    $scope.packList = [];
                }
            })
    }

    $scope,getPackType();

    //获取所有检测模板
    $scope.getTemplate = function () {
        _jiyin.dataPost('admin/detection_template_admin/get_template_by_page', dataToURL({page: 1, page_size: 999}))
            .then(function (response) {
                if (response.success) {
                    $scope.templates = response.data;
                } else {
                    $scope.templates = [];
                    _jiyin.msg('e', response.msg);
                }
            })
    }

    //获取已设置的检测模板
    $scope.getSpecificationTemplate = function (id) {
        _jiyin.dataPost('admin/commodity_admin/paginate_commodity_specification_template/1/999', dataToURL({specification_id: id}))
            .then(function (response) {
                if (response.success) {
                    $scope.specificationTemplates = response.data;
                } else {
                    $scope.specificationTemplates = [];
                    _jiyin.msg('e', response.msg);
                }
            })
    }

    //添加检测模板
    $scope.addTemplate = function () {
        if (!$scope.add.template_id) {
            _jiyin.msg('e', '请选择检测模板');
            return;
        }
        if (!$scope.add.number) {
            _jiyin.msg('e', '请输入检测项目数量');
            return;
        }
        //判断检测项目数量
        if ($scope.add.number && $scope.add.template_id) {
            for (var i = 0; i < $scope.templates.length; i++) {
                if($scope.templates[i].id == $scope.add.template_id && parseInt($scope.add.number) > parseInt($scope.templates[i].project_count)) {
                    _jiyin.msg('e', "检测项目数量最多" + $scope.templates[i].project_count + "项，请重新输入");
                    return;
                }
            }
        }

        _jiyin.dataPost('admin/commodity_admin/add_commodity_specification_template', dataToURL({
            specification_id: $scope.currentId,
            template_id: $scope.add.template_id,
            project_num: $scope.add.number
        })).then(function (response) {
            if (response.success) {
                _jiyin.msg('s', response.msg);
                $scope.getSpecificationTemplate($scope.currentId);
                $scope.add = [];
            } else {
                _jiyin.msg('e', response.msg);
            }
        })
    }

    //删除检测模板
    $scope.deleteTemplate = function (id) {
        _jiyin.dataGet('admin/commodity_admin/delete_commodity_specification_template/' + id)
            .then(function (response) {
                if (response.success) {
                    _jiyin.msg('s', response.msg);
                    $scope.getSpecificationTemplate($scope.currentId);
                } else {
                    _jiyin.msg('e', response.msg);
                }
            })
    };

    //添加商品组件模态框
    $scope.add_commodity = function () {
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-agentAddCommodity.html',
            tempCtrl : 'agentAddCommodityCtrl',
            ok : $scope.add,
            size : 'lg',
            params : {
                infoList: $scope.discount,
                roleList: $scope.roleList,
                is_point: $scope.isPoint,
                ael: 'add',
                select: 's'
            }
        });
    };

    $scope.add = function (list) {
        var specification_name = list[0].commodity_center_id ? list[0].specification_center_name : list[0].specification_name;
        $scope.infoList.commodity_name = list[0].commodity_name + ' ' + specification_name + ' ' + list[0].package_type_name;
        $scope.infoList.recommend_specification_id = list[0].id;
        $scope.infoList.recommend_commodity_id = list[0].commodity_id;
    };

    //推荐商品模态框
    $scope.recommend = function (data) {
        $scope.specification_id = data.id;

        //获取该规格商品的推荐商品列表
        $scope.recommendList = [];
        $scope.getRecommendList();

        //添加推荐商品信息
        $scope.infoList = [];
        $scope.infoList.commodity_id = data.commodity_id;
        $scope.infoList.specification_id = data.id;
        $("#recommend_modal").modal('show');
    };

    //添加推荐商品
    $scope.addRecommend = function () {
        _jiyin.dataPost('admin/commodity_admin/add_commodity_recommend_commodity', dataToURL({
            commodity_id: $scope.infoList.commodity_id,
            specification_id: $scope.infoList.specification_id,
            recommend_commodity_id: $scope.infoList.recommend_commodity_id,
            recommend_specification_id: $scope.infoList.recommend_specification_id
        })).then(function (result){
                if(result.success){
                    $scope.infoList = [];
                    _jiyin.msg('s', result.msg);
                    $scope.getRecommendList();
                } else {
                    _jiyin.msg('e', result.msg);
                }
            });
    };

    //获取推荐商品列表
    $scope.getRecommendList = function () {
        _jiyin.dataPost('admin/commodity_admin/get_commodity_recommend_commodity', dataToURL({
            specification_id: $scope.specification_id
        })).then(function (result){
            if(result.success){
                $scope.recommendList = result.data;
            }else {
                _jiyin.msg('e', result.msg);
                $scope.recommendList = [];
            }
        });
    }

    //删除商品推荐
    $scope.deleteRecommend = function (id) {
        if(confirm('确认删除此相关推荐吗?')){
            _jiyin.dataPost('admin/commodity_admin/delete_commodity_recommend_commodity', dataToURL({id: id}))
                .then(function(result){
                    if(result.success = true){
                        _jiyin.msg('s', '删除成功');
                        $scope.getRecommendList();
                    }else{
                        _jiyin.msg('e', result.msg);
                    }
                });
        }
    };

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
                        if (result.success) {
                            $scope.picList.splice(index, 1);
                            $scope.getThumbnail();
                        }
                    });
            }
        }
    };

    /**
     * 获取缩略图
     */
    $scope.getThumbnail = function(){
        if($scope.list.id){
            _jiyin.dataPost('admin/commodity_admin/get_specification_thumbnail', dataToURL({commodity_id: $scope.list.commodity_id, commodity_specification_id:$scope.list.id}))
                .then(function (result) {
                    if (result.success) {
                        $scope.picList = result.data;
                    }
                });
        }
    };
    /**
     * 获取商品状态
     */
    $scope.getStatus = function () {
        _jiyin.dataPost('admin/system_code_admin/get_by_type/commodity_specification_status')
            .then(function (result) {
                if($scope.ael == 'add'){
                    $scope.statusList = result.slice(0,2);
                }else{
                    $scope.statusList = result;
                }
            })
    };

    //查看评价
    $scope.getEvaluateBySpecification = function (data) {
        $state.go('app.evaluate',{'commodity_id':data.commodity_id, 'type':$stateParams.type, 'specification_id' : data.id});
    };

    $scope.ok = function () {
        var url = ''
        if($scope.ael == 'add'){
            url = 'admin/commodity_admin/add_commodity_specification'
        }else{
            url = 'admin/commodity_admin/update_commodity_specification'
        }
        //规格不是从erp传入时，commodity_specification_name为null，这时，才检查list.name
        if($scope.list.commodity_specification_name == null && !$scope.list.name){
            _jiyin.msg('e','商品规格名称不能为空');
            return ;
        }
        if(!$scope.list.market_price){
            _jiyin.msg('e','市场价格不能为空');
            return ;
        }
        if(!$scope.list.selling_price){
            _jiyin.msg('e','销售价格不能为空');
            return ;
        }
        if(!$scope.list.status_id){
            _jiyin.msg('e','商品状态不能为空');
            return ;
        }

        if ($scope.list.attachment_ids){
            $scope.list.attachment_ids = $scope.list.attachment_ids.toString();
        }

        //重新组装添加/更新数据，防止不更新的数据传入后台
        var updateData = {
            id: $scope.list.id,
            name: $scope.list.name,
            market_price: $scope.list.market_price,
            selling_price: $scope.list.selling_price,
            status_id: $scope.list.status_id,
            packagetype: $scope.list.packagetype,
            points: $scope.list.points,
            goodsunit: $scope.list.goodsunit,
            commodity_id: $scope.list.commodity_id,
            attachment_ids:  $scope.list.attachment_ids,
        };
        var addData = {
             id: $scope.list.commodity_id,
             name: $scope.list.commodity_specification_name != null ? $scope.list.commodity_specification_name : $scope.list.name,
             market_price: $scope.list.market_price,
             selling_price: $scope.list.selling_price,
             status_id: $scope.list.status_id,
             packagetype: $scope.list.packagetype,
             points: $scope.list.points,
             goodsunit: $scope.list.goodsunit,
             attachment_ids:  $scope.list.attachment_ids,
        };
        if($scope.ael == 'add'){
           var submitData = addData;  
        }else{
            var submitData = updateData;
        }
        _jiyin.dataPost(url, dataToURL(submitData))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s', result.msg);
                    $scope.getData();
                    $("#add").modal('hide');
                }else{
                    _jiyin.msg('e', result.msg);
                }
            });
    };

    /**
    * 上传规格图片
    */
    $scope.upload = function (data) {
        $scope.title = '上传规格图片';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-uploadCommodityPic.html',
            tempCtrl : 'uploadCommodityPicCtrl',
            ok : $scope.uploadPic,
            size : 'lg',
            params : {
                title: $scope.title,
                id: data.id,
                path: data.path,
                ael: 'edit'
            }
        });
    };
    $scope.uploadPic = function (list) {
        console.log(list);
        _jiyin.dataPost('admin/commodity_admin/upload_commodity_specification_attachment', dataToURL({id: list.id, attachment_id: list.attachment_id}))
            .then(function (result) {
                if(result.success == true) {
                    _jiyin.msg('s',result.msg);
                    $scope.getData();
                }else{
                    _jiyin.msg('e',result.error);
                }
            });
    };

    /**
     * 下一页
     */
    $scope.nextPage = function(){
        if($scope.inputPage < $scope.totalPage){
            $scope.inputPage++;
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
            $scope.inputPage--;
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


app.controller('speFileUploadCtrl', ['$scope', 'FileUploader', '_jiyin', 'dataToURL', function($scope, FileUploader, _jiyin, dataToURL) {
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