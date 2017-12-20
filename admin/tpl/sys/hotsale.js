/**
 * Created by sailwish001 on 2017/3/22.
 */
app.controller('hotsaleCtrl', ['$scope', '_jiyin', 'FileUploader', 'dataToURL', function ($scope, _jiyin, FileUploader, dataToURL) {
    var uploader = $scope.uploader = new FileUploader({
        url: SITE_URL + 'attachment/up_attachment'
    });
    uploader.onAfterAddingFile = function(item){
        _jiyin.fileMd5(item._file).then(function (result) {
            _jiyin.dataPost('attachment/check_md5', dataToURL({md5_code: result.md5Code}))
                .then(function (result) {
                    if(result.exist == true){
                        $scope.path = result.path;
                        uploader.clearQueue();
                        $scope.save($scope.path);
                    }else{
                        item.upload();
                    }
                });
        });
    };
    uploader.onSuccessItem = function(fileItem, response, status, headers) {
        $scope.path = response.url;
        uploader.clearQueue();
        $scope.save($scope.path);
    };
    $scope.save = function (path) {
        _jiyin.dataPost('admin/system_setting_admin/hot_sale_cover', dataToURL({path: path}))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s','设置成功');
                    $scope.get();
                }
            });
    };
    $scope.get = function () {
        _jiyin.dataPost('admin/system_setting_admin/get_hot_sale_cover')
            .then(function (result) {
                if(result.success == true){
                    $scope.path = result.data.value;
                }else{
                    $scope.path  = '';
                }
            });
    };
    $scope.get();
}]);