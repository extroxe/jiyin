/**
 * Created by sailwish001 on 2016/12/12.
 */
'use strict';

app.controller('modalArticleCtrl', ['$scope', '$modalInstance', '_jiyin', 'params', 'FileUploader', 'dataToURL',
    function ($scope, $modalInstance, _jiyin, params, FileUploader, dataToURL) {
        $scope.infoList = params.infoList;
        $scope.title = params.title;
        $scope.ael = params.ael;
        $scope.items = ['fontname', 'fontsize', 'forecolor', 'bold', 'underline', 'emoticons', 'link'];
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
        /**
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
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
            $modalInstance.close($scope.infoList);
        };
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