/**
 * Created by sailwish001 on 2016/11/23.
 */
app.controller('modalDisCtrl', ['$scope', '$modalInstance', '_jiyin', 'params',
    function ($scope, $modalInstance, _jiyin, params) {
        $scope.infoList = params.infoList;
        $scope.title = params.title;
        $scope.ael = params.ael;
        if ($scope.ael == 'edit') {
            if ($scope.infoList.commodity_center_name != null) {
                var name = $scope.infoList.commodity_center_name;
            } else {
                var name = $scope.infoList.commodity_specification_name;
            }
            $scope.infoList.commodity_name = $scope.infoList.commodity_name + ' ' + name + ' ' + $scope.infoList.package_type_name;
        }
        
        /**
         * 获取商品
         */
        $scope.getCommo = function () {
            _jiyin.dataPost('admin/commodity_admin/get_all_commodity_by_is_point')
                .then(function (result) {
                    $scope.commoList = result.data;
                })
        };
        $scope.getCommo();

        //添加代理商商品
        $scope.add_commodity = function () {
            _jiyin.modal({
                tempUrl : '/source/admin/tpl/modal/modal-agentAddCommodity.html',
                tempCtrl : 'agentAddCommodityCtrl',
                ok : $scope.add,
                size : 'lg',
                params : {
                    title: $scope.title,
                    infoList: $scope.discount,
                    roleList: $scope.roleList,
                    ael: 'add',
                    select: 's'
                }
            });
        };
        $scope.commodity_ids = [];
        $scope.commodity_list = [];
        $scope.selecteCommodities = [];
        $scope.selecteCommodityList = [];
        $scope.deleteList = [];
        $scope.ids = [];

        $scope.add = function (list) {
            if (list[0].commodity_center_id != null) {
                $scope.infoList.commodity_name = list[0].commodity_name + ' ' + list[0].commodity_center_name + ' ' + list[0].package_type_name;
            } else {
                $scope.infoList.commodity_name = list[0].commodity_name + ' ' + list[0].commodity_specification_name + ' ' + list[0].package_type_name;
            }

            $scope.infoList.commodity_specification_id = list[0].id;
            $scope.infoList.commodity_id = list[0].commodity_id;
            $scope.infoList.selling_price = list[0].price;
            $scope.infoList.market_price = list[0].market_price;
           // if($scope.selecteCommodityList.length != 0){
           //     angular.forEach($scope.selecteCommodityList, function (data, index) {
           //         if(JSON.stringify($scope.commodity_list).indexOf(data.id) != -1){
           //             angular.forEach($scope.commodity_list, function (listData, eq) {
           //                 if(listData.id == data.id){
           //                     $scope.commodity_list.splice(eq, 1);
           //                     $scope.commodity_ids.splice(data.id, 1);
           //                 }
           //             })
           //         }
           //      });
           // }
           //  angular.forEach($scope.commodity_list, function (data,index) {
           //      $scope.selecteCommodityList.push(angular.copy(data));
           //  });

            // $scope.idStr = $scope.commodity_ids.join(',');
        };

        /**
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
        $scope.ok = function () {
            if(!$scope.infoList.commodity_id && $scope.ael == 'add'){
                _jiyin.msg('e','商品名称不能为空');
                return ;
            }
            if(!$scope.infoList.commodity_specification_id && $scope.ael == 'add'){
                _jiyin.msg('e','商品规格不能为空');
                return ;
            }
            if(!$scope.infoList.price){
                _jiyin.msg('e','折扣价格不能为空');
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