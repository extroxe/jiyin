/**
 * Created by sailwish001 on 2016/12/6.
 */
'use strict';

app.controller('modalSubOrderCtrl', ['$scope', '$modalInstance', '_jiyin', 'params', 'FileUploader', 'dataToURL',
    function ($scope, $modalInstance, _jiyin, params, FileUploader, dataToURL) {
        $scope.infoList = params.infoList;
        $scope.title = params.title;
        $scope.ael = params.ael;
        /**
         * 获取快递公司
         */
        $scope.getExpress = function () {
            _jiyin.dataPost('admin/express_admin/get_all_express_company')
                .then(function (result) {
                    $scope.expressList = result.data;
                })
        };
        $scope.getExpress();
        /**
         * 获取商品
         */
        $scope.getCommo = function () {
            _jiyin.dataPost('admin/commodity_admin/get_all')
                .then(function (result) {
                    $scope.commoList = result.data;
                })
        };
        //$scope.getCommo();
        $scope.$on('attachment_id', function(event, attachment_id) {
            $scope.infoList.report_id = attachment_id;
        });
        /**
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
        $scope.ok = function () {
            if(!$scope.infoList.total_price){
                _jiyin.msg('e','订单总价不能为空');
                return ;
            }
            if(!$scope.infoList.points){
                _jiyin.msg('e','订单获取积分不能为空');
                return ;
            }
            /*if(!$scope.infoList.name){
                _jiyin.msg('e','被检测人姓名不能为空');
                return ;
            }
            if(!$scope.infoList.gender){
                _jiyin.msg('e','被检测人性别不能为空');
                return ;
            }
            if(!$scope.infoList.age){
                _jiyin.msg('e','被检测人年龄不能为空');
                return ;
            }
            if(!$scope.infoList.identify_card){
                _jiyin.msg('e','被检测人身份证号不能为空');
                return ;
            }*/
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