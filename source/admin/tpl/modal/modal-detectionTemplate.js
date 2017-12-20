/**
 * Created by sailwish001 on 2017/5/19.
 */

app.controller('modalDeplateCtrl', ['$scope', '$modalInstance', '_jiyin', 'params', 'FileUploader', 'dataToURL',
    function ($scope, $modalInstance, _jiyin, params, FileUploader, dataToURL) {
        $scope.infoList = params.infoList;
        $scope.title = params.title;
        $scope.ael = params.ael;
        /**
         * 获取模板
         */
        /*$scope.getCommo = function () {
            _jiyin.dataPost('admin/detection_template_admin/get_detection_template')
                .then(function (result) {
                    $scope.commoList = result.data;
                })
        };
        $scope.getCommo();*/
        /**
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
        $scope.ok = function () {
            if(!$scope.infoList.name){
                _jiyin.msg('e','模板名称不能为空');
                return ;
            }
            $modalInstance.close($scope.infoList);
        }
    }]);