'use strict';

app.controller('modalAgentCommodityCtrl', ['$scope', '$modalInstance', '_jiyin', 'params', 'FileUploader', 'dataToURL',
    function ($scope, $modalInstance, _jiyin, params, FileUploader, dataToURL) {
        $scope.infoList = params.infoList;
        $scope.title = params.title;
        // $scope.operate = angular.copy($stateParams.operate);
        $scope.ael = params.ael;

        //获取全部代理商

        $scope.getAgent = function () {
            $scope.title = '添加代理商';
            _jiyin.modal({
                tempUrl : '/source/admin/tpl/modal/modal-agentAddCommodity.html',
                tempCtrl : 'agentAddCommodityCtrl',
                ok : $scope.addAgent,
                size : 'lg',
                params : {
                    title: $scope.title,
                    ael: 'add'
                }
            });
        };
        $scope.addAgent = function (data) {
            $scope.agentName = data.name;
            $scope.infoList.agent_id = data.id;
        };

        //添加代理商商品
        $scope.add_commodity = function () {
            if(!$scope.agentName){
                _jiyin.msg('e', '请选择代理商');
                return;
            }
            $scope.title = '添加代理商商品';
            _jiyin.modal({
                tempUrl : '/source/admin/tpl/modal/modal-agentAddCommodity.html',
                tempCtrl : 'agentAddCommodityCtrl',
                ok : $scope.add,
                size : 'lg',
                params : {
                    title: $scope.title,
                    infoList: $scope.discount,
                    roleList: $scope.roleList,
                    ael: 'add'
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
            angular.forEach(list, function(data, index){
                if($scope.commodity_ids.length == 0 || $scope.commodity_ids.indexOf(data.id) == -1){
                    data.number = 1;
                    $scope.commodity_ids.push(data.id);
                    $scope.commodity_list.push(data);
                }
            });
           if($scope.selecteCommodityList.length != 0){
               angular.forEach($scope.selecteCommodityList, function (data, index) {
                   if(JSON.stringify($scope.commodity_list).indexOf(data.id) != -1){
                       angular.forEach($scope.commodity_list, function (listData, eq) {
                           if(listData.id == data.id){
                               $scope.commodity_list.splice(eq, 1);
                               $scope.commodity_ids.splice(data.id, 1);
                           }
                       })
                   }
                });
           }
            angular.forEach($scope.commodity_list, function (data,index) {
                $scope.selecteCommodityList.push(angular.copy(data));
            });

            $scope.idStr = $scope.commodity_ids.join(',');
        };

         //删除代理商商品
        $scope.delete_commodity = function(id){
            //删除添加的商品
            if(confirm('确定删除该商品吗？')){
                for(var i = 0; i<$scope.selecteCommodityList.length; i++){
                    if($scope.selecteCommodityList[i].id == id){
                        $scope.selecteCommodityList.splice(i, 1);
                        $scope.commodity_ids.splice(id, 1);
                        i--;
                    }
                }
                 $scope.commodity_list = angular.copy($scope.selecteCommodityList);
                $scope.idStr = $scope.commodity_ids.join(',');
            }
        };

        /**
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
        $scope.confirm = function () {
            if(!$scope.infoList.agent_id){
                _jiyin.msg('e','代理商名称为空');
                return ;
            }
            if(!$scope.selecteCommodityList || $scope.selecteCommodityList == [] || $scope.selecteCommodityList.length == 0){
                _jiyin.msg('e', '请添加商品');
                return;
            }
            $scope.submitData = [];
            $scope.submitSub = {};
            //提取需要的商品信息
            angular.forEach($scope.selecteCommodityList, function(data,index){
                $scope.submitSub.commodity_id = data.commodity_id;
                $scope.submitSub.commodity_specification_id = data.id;
                $scope.submitSub.price = data.price;
                $scope.submitSub.agent_id = $scope.infoList.agent_id;
                $scope.submitData.push(angular.copy($scope.submitSub));
            });
            // _jiyin.dataPost('admin/agent_admin/add_agent_commodity', dataToURL({commodity_category:JSON.stringify($scope.submitData)}))
            //     .then(function (result) {
            //         if(result.success == true){
            //             _jiyin.msg('s',result.msg);
            //         }else{
            //             _jiyin.msg('e',result.msg);
            //         }
            //     })
            $modalInstance.close($scope.submitData);
            // $state.go('app.agentIndex');

        }
}]);