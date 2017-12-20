/**
 * Created by Boss Peng on 2016/11/22.
 */
'use strict';

app.controller('modalRecomCtrl', ['$scope', '$modalInstance', '_jiyin', 'params', 'FileUploader', 'dataToURL',
    function ($scope, $modalInstance, _jiyin, params, FileUploader, dataToURL) {
        $scope.infoList = params.infoList;
        $scope.title = params.title;
        $scope.ael = params.ael;
        $scope.is_point = params.isPoint;
        if ($scope.ael == 'edit') {
            if ($scope.infoList.commodity_center_name != null) {
                var specification_name = $scope.infoList.commodity_center_name;
            } else {
                var specification_name = $scope.infoList.commodity_specification_name;
            }
            $scope.infoList.commodity_name = $scope.infoList.commodity_name + ' ' + specification_name + ' ' + $scope.infoList.package_type_name;
        }

        if($scope.ael == 'add'){
            if($scope.is_point == false){
                $scope.infoList.type_id = '1';
            }else{
                $scope.infoList.type_id = '2';
            }
        }
        /**
         * 获取推荐类型
         */
        $scope.getType = function () {
            _jiyin.dataPost('admin/system_code_admin/get_by_type/recommend_commodity_type')
                .then(function (result) {
                    $scope.typeList = result;
                })
        };
        $scope.getType();

        /**
         * 获取商品
         */
        $scope.getCommo = function () {
            if($scope.is_point === true){
                _jiyin.dataPost('admin/commodity_admin/get_all_commodity_by_is_point', dataToURL({
                    is_point: 1
                }))
                    .then(function (result) {
                        $scope.commoList = result.data;
                    })
            }else{
                _jiyin.dataPost('admin/commodity_admin/get_all_commodity_by_is_point', dataToURL({
                    is_point: 0
                }))
                    .then(function (result) {
                        $scope.commoList = result.data;
                    })
            }

        };

        $scope.getCommo();
        //添加商品
        $scope.selectCommodity = function () {
            _jiyin.modal({
                tempUrl : '/source/admin/tpl/modal/modal-agentAddCommodity.html',
                tempCtrl : 'agentAddCommodityCtrl',
                ok : $scope.add,
                size : 'lg',
                params : {
                    title: $scope.title,
                    infoList: $scope.discount,
                    roleList: $scope.roleList,
                    is_point: $scope.is_point,
                    ael: 'add',
                    select: 's'
                }
            });
        };

        $scope.add = function (data) {
            if (data[0].commodity_center_name != null) {
                var specification_name = data[0].commodity_center_name;
            } else {
                var specification_name = data[0].commodity_specification_name;
            }

            $scope.infoList.price = data[0].price;
            $scope.infoList.commodity_name = data[0].commodity_name + ' ' + specification_name + ' ' + data[0].package_type_name;
            $scope.infoList.commodity_id = data[0].commodity_id;
            $scope.infoList.commodity_specification_id = data[0].id;
        };

        /**
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
        $scope.ok = function () {
            if(!$scope.infoList.commodity_id){
                _jiyin.msg('e','商品名称不能为空');
                return ;
            }
            if(!$scope.infoList.type_id){
                _jiyin.msg('e','商品类型不能为空');
                return ;
            }
            if(!$scope.infoList.start_time){
                _jiyin.msg('e','生效起始时间不能为空');
                return ;
            }
            if(!$scope.infoList.end_time){
                _jiyin.msg('e','生效结束时间不能为空');
                return ;
            }
            $modalInstance.close($scope.infoList);

        }
    }]);
