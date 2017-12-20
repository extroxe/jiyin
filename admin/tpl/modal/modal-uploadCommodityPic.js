/**
 * Created by sailwish001 on 2016/12/6.
 */
'use strict';

app.controller('uploadCommodityPicCtrl', ['$scope', '$modalInstance', '_jiyin', 'params', 'FileUploader', 'dataToURL',
    function ($scope, $modalInstance, _jiyin, params, FileUploader, dataToURL) {
        $scope.infoList = [];
        $scope.gkpath = '';
        $scope.title = params.title;
        $scope.infoList.id = params.id;
        $scope.ael = params.ael;
        $scope.gkpath = angular.copy(params.path);
        $scope.attachment_id = '';
        $scope.path = '';
        /**
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
        $scope.$on('attachment_id', function(event, attachment_id) {
                $scope.infoList.attachment_id = attachment_id;
                console.log($scope.infoList);
            });
        $scope.ok = function () {
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
        $scope.$emit('path', response.path);
    };
    $scope.$on('clearQueue', function() {
        uploader.clearQueue();
    });
}]);