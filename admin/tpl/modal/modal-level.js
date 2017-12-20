/**
 * Created by sailwish001 on 2016/12/5.
 */
'use strict';

app.controller('modalLevelCtrl', ['$scope', '$modalInstance', '_jiyin', 'params', 'FileUploader', 'dataToURL',
    function ($scope, $modalInstance, _jiyin, params, FileUploader, dataToURL) {
        $scope.infoList = params.infoList;
        $scope.title = params.title;
        $scope.ael = params.ael;

        $scope.$on('attachment_id', function(event, attachment_id) {
            $scope.infoList.attachment_id = attachment_id;
        });
        if($scope.ael == 'edit'){
            $scope.infoList.attachment_id = $scope.infoList.icon_id;
        }
        /**
         * 获取等级信息
         */
        $scope.getLevel = function () {
          _jiyin.dataPost('admin/level_admin/get_info')
              .then(function (result) {
                    $scope.levelInfo = result.data;
              });
        };
        $scope.getLevel();
        /*
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
        $scope.ok = function () {
            if(!$scope.infoList.name){
                _jiyin.msg('e','等级名称不能为空');
                return ;
            }
            if(!$scope.infoList.rank){
                _jiyin.msg('e','等级排序不能为空');
                return ;
            }
            if(!$scope.infoList.price_discount){
                _jiyin.msg('e','折扣不能为空');
                return ;
            }
            if(!$scope.infoList.points_coefficient){
                _jiyin.msg('e','等级积分系数不能为空');
                return ;
            }
            /*if(!$scope.infoList.price){
                _jiyin.msg('e','到当前等级所需价格不能为空');
                return ;
            }*/
            if($scope.ael == 'add'){
                var names = [];
                var ranks = [];
                angular.forEach($scope.levelInfo, function (data, index) {
                    names[names.length] = data.name;
                    ranks[ranks.length] = data.rank;
                });
                if(!$.inArray($scope.infoList.name, names)){
                    _jiyin.msg('e','等级名称不能重复');
                    return ;
                }
                if(!$.inArray($scope.infoList.rank, ranks)){
                    _jiyin.msg('e','等级排序不能重复');
                    return ;
                }
            }
            if(!$scope.infoList.attachment_id){
                _jiyin.msg('e','还没有上传图片');
                return ;
            }
            $modalInstance.close($scope.infoList);

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