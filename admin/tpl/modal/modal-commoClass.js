/**
 * Created by sailwish001 on 2016/11/18.
 */
'use strict';

app.controller('modalCommoClassCtrl', ['$scope', '$modalInstance', '_jiyin', 'params', 'FileUploader', 'dataToURL',
    function ($scope, $modalInstance, _jiyin, params, FileUploader, dataToURL) {
        $scope.infoList = params.commonclassList;
        $scope.title = params.title;
        $scope.ael = params.ael;
        $scope.isPoint = params.isPoint;
        if($scope.infoList){
            //$scope.infoList.price = parseInt($scope.infoList.price);
            $scope.infoList.points = parseInt($scope.infoList.points);
            $scope.infoList.sales_volume = parseInt($scope.infoList.sales_volume);
        }
        $scope.items = ['fontname', 'fontsize', 'forecolor', 'bold', 'underline', 'emoticons', 'link'];
        /**
         * 获取缩略图
         */
        $scope.getThumbnail = function(){
            if($scope.infoList.id){
                _jiyin.dataPost('admin/commodity_admin/show_thumbnail', dataToURL({commodity_id: $scope.infoList.id}))
                    .then(function (result) {
                        $scope.picList = result.data;
                    });
            }
        };
        $scope.getThumbnail();
        /**
         * 获取商品状态
         */
        $scope.getStatus = function () {
            _jiyin.dataPost('admin/system_code_admin/get_by_type/commodity_status')
                .then(function (result) {
                    $scope.statusList = result;
                })
        };
        $scope.getStatus();
        /**
         * 获取商品类型
         */
        $scope.getType = function () {
            _jiyin.dataPost('admin/system_code_admin/get_by_type/commodity_type')
                .then(function (result) {
                    $scope.typeList = result;
                });
        };
        $scope.getType();
        /**
         * 获取商品分类
         */
        $scope.getCate = function () {
            _jiyin.dataPost('admin/category_admin/get_categories')
                .then(function (result) {
                    $scope.cateList = result.data;
                });
        };
        $scope.getCate();

        $scope.$on('attachment_ids', function(event, attachment_ids) {
            $scope.infoList.attachment_ids = attachment_ids;
        });
        /**
         * 删除图片
         */
        $scope.removePic = function (id) {
          if(comfirm('确定删除该图片吗?')){
              _jiyin.dataPost('admin/commodity_admin/delete_thumbnail',dataToURL({id: id}))
                  .then(function (result) {
                      if (result.success) {
                          $scope.getThumbnail();
                      }
                  });
          }
        };
        $scope.removeEditor = function () {
            var introduce = $('#introduce');
            var detail = $('#detail');
            KindEditor.remove(introduce);
            KindEditor.remove(detail);
        };
        /**
         * 取消关闭
         */
        $scope.cancel = function() {
            $scope.removeEditor();
            $modalInstance.dismiss('cancel');
        };
        $scope.ok = function () {
            if(!$scope.infoList.name){
                _jiyin.msg('e','商品名称不能为空');
                return ;
            }
            if(!$scope.infoList.number){
                _jiyin.msg('e','商品编号不能为空');
                return ;
            }
            if(!$scope.infoList.price){
                _jiyin.msg('e','商品价格不能为空');
                return ;
            }
            if($scope.isPoint == false){
                if(!$scope.infoList.points){
                    _jiyin.msg('e','商品购买获得积分不能为空');
                    return ;
                }
            }
            if(!$scope.infoList.category_id){
                _jiyin.msg('e','商品分类不能为空');
                return ;
            }
            if(!$scope.infoList.sales_volume){
                _jiyin.msg('e','商品销量不能为空');
                return ;
            }
            if(!$scope.infoList.status_id){
                _jiyin.msg('e','商品状态不能为空');
                return ;
            }
            if(!$scope.infoList.type_id){
                _jiyin.msg('e','商品类型不能为空');
                return ;
            }
            if(!$scope.infoList.detail){
                _jiyin.msg('e','商品详情不能为空');
                return ;
            }
            if(!$scope.infoList.attachment_ids && $scope.ael == 'add'){
                _jiyin.msg('e','还没有上传图片');
                return ;
            }
            $modalInstance.close($scope.infoList);
        };
    }]);
app.controller('FileUploadCtrl', ['$scope', 'FileUploader', '_jiyin', 'dataToURL', function($scope, FileUploader, _jiyin, dataToURL) {
    $scope.attachment_ids = [];
    var uploader = $scope.uploader = new FileUploader({
        url: SITE_URL + 'attachment/up_attachment'
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
                        $scope.attachment_ids.push(result.attachment_id);
                        $scope.$emit('attachment_ids', $scope.attachment_ids);
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
                            $scope.attachment_ids.push(result.attachment_id);
                            $scope.$emit('attachment_ids', $scope.attachment_ids);
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
        $scope.attachment_ids.push(response.attachment_id);
        $scope.$emit('attachment_ids', $scope.attachment_ids);
    };
    $scope.$on('clearQueue', function() {
        uploader.clearQueue();
    });
}]);