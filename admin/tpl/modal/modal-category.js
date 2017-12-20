/**
 * Created by sailwish001 on 2016/11/23.
 */
'use strict';

app.controller('modalCategoryCtrl', ['$scope', '$modalInstance', '_jiyin', 'params', 'FileUploader', 'dataToURL',
    function ($scope, $modalInstance, _jiyin, params, FileUploader, dataToURL) {
        $scope.infoList = params.infoList;
        $scope.title = params.title;
        $scope.ael = params.ael;
        $scope.checkFlag = false;
        if($scope.ael == 'edit' && $scope.infoList.parent_id != null){
            $scope.checkFlag = true;
        }
        /**
         * 获取父类
         */
        $scope.getCate = function () {
            _jiyin.dataPost('admin/category_admin/get_father_category')
                .then(function (result) {
                    $scope.cateList = result.data;
                })
        };
        $scope.getCate();
        /**
         * 获取商品类型
         */
        $scope.getType = function () {
            _jiyin.dataPost('admin/system_code_admin/get_by_type/commodity_type')
                .then(function (result) {
                    $scope.typeList = result;
                })
        };
        $scope.getType();
        /**
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
        $scope.ok = function () {
            if(!$scope.infoList.name){
                _jiyin.msg('e','分类名称不能为空');
                return ;
            }
            if($scope.checkFlag === true){
                if(!$scope.infoList.parent_id){
                    _jiyin.msg('e','父类名称不能为空');
                    return ;
                }
            }
            if($scope.checkFlag != true){
                $scope.infoList.parent_id = 0;
            }
            $modalInstance.close($scope.infoList);

        }
    }]);