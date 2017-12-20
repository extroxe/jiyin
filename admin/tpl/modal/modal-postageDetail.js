/**
 * Created by sailwish001 on 2016/12/6.
 */
'use strict';

app.controller('modalPostageDetailCtrl', ['$scope', '$modalInstance', '_jiyin', 'params', 'FileUploader', 'dataToURL',
    function ($scope, $modalInstance, _jiyin, params, FileUploader, dataToURL) {
        $scope.roleList = [];
        $scope.levelList = [];
        $scope.infoList = params.infoList;
        $scope.title = params.title;
        $scope.ael = params.ael;

        /**
         * 获取角色
         */
        $scope.getRole = function () {
            _jiyin.dataPost('admin/system_code_admin/get_by_type/role')
                .then(function (result) {
                    $scope.roleList = result;
                })
        };
        $scope.getRole();

        /**
         * 获取等级
         */
        $scope.getLevel = function () {
            _jiyin.dataPost('admin/level_admin/get_level')
                .then(function (result) {
                    $scope.levelList = result.data;
                })
        };
        $scope.getLevel();

        /**
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
        $scope.ok = function () {
            $scope.infoList.is_show = 1;
            if(!$scope.infoList.name){
                _jiyin.msg('e','名称不能为空');
                return ;
            }
            if(!$scope.infoList.role_id){
                _jiyin.msg('e', '角色不能为空');
                return;
            }
            if(!$scope.infoList.total_cost){
                _jiyin.msg('e', '订单金额不能为空');
                return;
            }
            if(!$scope.infoList.commodities_count){
                _jiyin.msg('e', '订单数量不能为空');
                return;
            }
            if(!$scope.infoList.member_level_id){
                _jiyin.msg('e', '会员等级不能为空');
                return;
            }
            if(!$scope.infoList.client_id){
                _jiyin.msg('e', '下单终端不能为空');
                return;
            }
            if(!$scope.infoList.single_postage){
                _jiyin.msg('e', '单件商品邮费不能为空');
                return;
            }

            $modalInstance.close($scope.infoList);
        }
    }]);