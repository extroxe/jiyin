/**
 * 编辑代理商商品分类
 * Created by huazq.
 * 2017-7-20 16:03:11
 */
app.controller('editPackagesCtrl', ['$rootScope', '$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', function ($rootScope, $scope, _jiyin, dataToURL, $stateParams, $state) {
    $scope.is_admin = $rootScope.is_admin;
    $scope.infoList = {};
    $scope.role_id = '';
    $scope.operate = angular.copy($stateParams.operate);
    $scope.infoList.name = angular.copy($stateParams.category);
    $scope.infoList.index_id = angular.copy($stateParams.id);
    $scope.infoList.agent_id = angular.copy($stateParams.agent_id);
    $scope.infoList.agent_name = angular.copy($stateParams.agent_name);
    $scope.infoList.color = angular.copy($stateParams.color) ? angular.copy($stateParams.color) :'#fff';
    $('#color').val($scope.infoList.color);

    $scope.$watch('currentUser.role_id', function (nv) {
        if(nv == '30'){
            $scope.infoList.agent_name = $rootScope.currentUser.username;
            $scope.infoList.agent_id = $rootScope.currentUser.id;
            $scope.role_id = $rootScope.currentUser.role_id;
        }
    });
    // if($rootScope.currentUser.role_id == '30'){
    //
    // }
    if ($scope.operate == 'edit'){
        $scope.url = true;
    }else{
        $scope.url = false;
    }


    //获取商品信息
    if ($scope.operate == 'edit'){
        $scope.get_category_by_name = function () {
            _jiyin.dataPost('admin/agent_admin/get_category_by_name', dataToURL({index_id: $scope.infoList.index_id}))
                .then(function (result) {
                    if (result){
                        $scope.selecteCommodityList = result.data;
                        $scope.category = result.data[0].category;
                    }else{
                        _jiyin.msg('e', result.msg);
                        $scope.discount = [];
                    }
                });
        };
        $scope.urlWC = SITE_URL+'weixin/agent/entrance?agent_id=' + $scope.infoList.agent_id + '&uid=xxxxx&cid=' + $scope.infoList.name;

        $scope.get_category_by_name();
    }

    //代理商更新商品价格
    // $scope.update_price = function (commodity) {
    //     if ($scope.operate == 'edit') {
    //         _jiyin.dataPost('admin/agent_admin/update_agent_commodity_price', dataToURL({id: commodity.agent_commodity_id, price: commodity.price}))
    //             .then(function (result) {
    //                 if (result){
    //                 }else{
    //                     _jiyin.msg('e', result.msg);
    //                 }
    //         });
    //     }
    // };

    $('#colorpicker').farbtastic('#color');
    $('#color').focus(function () {
        $('#colorpicker').show();
    });
    $('#color').blur(function () {
        $('#colorpicker').hide();
    });
     //删除代理商商品
    $scope.delete_commodity = function(id, agent_index_id){
        if($scope.operate == 'edit'){
                if(confirm('确定删除吗？')){
                    for(var i = 0; i<$scope.selecteCommodityList.length; i++){
                    if($scope.selecteCommodityList[i].id == id){
                        $scope.selecteCommodityList.splice(i, 1);
                        i--;
                    }
                }
                _jiyin.dataPost('admin/agent_admin/delete_agent_home_by_id', dataToURL({id: id, agent_index_id: agent_index_id}))
                    .then(function (result) {
                        if (result){
                            $scope.get_category_by_name();
                        }else{
                             _jiyin.msg('e',result.msg);
                        }
                    })
                }
        }else{
            //删除添加的商品
            if(confirm('确定删除该商品吗？')){
                for(var i = 0; i<$scope.selecteCommodityList.length; i++){
                    if($scope.selecteCommodityList[i].id == id){
                        $scope.selecteCommodityList.splice(i, 1);
                        $scope.commodity_ids.splice(i, 1);
                        i--;
                    }
                }
                angular.forEach($scope.selecteCommodityList, function (data, index) {
                    data.rank = index + 1;
                });
                 $scope.commodity_list = angular.copy($scope.selecteCommodityList);
                $scope.idStr = $scope.commodity_ids.join(',');
            }
        }
    };

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
        $scope.infoList.agent_name = data.name;
        $scope.infoList.agent_id = data.id;
    };


    //添加代理商商品
    $scope.add_commodity = function () {
        if(!$scope.infoList.agent_id){
            _jiyin.msg('e', '请选择代理商');
            return;
        }
        $scope.title = '添加代理商商品';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-editCommodityCategory.html',
            tempCtrl : 'commodityCategoryCtrl',
            ok : $scope.add,
            size : 'lg',
            params : {
                title: $scope.title,
                infoList: $scope.discount,
                roleList: $scope.roleList,
                ael: 'add',
                agent_id:$scope.infoList.agent_id
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
        if ($scope.operate == 'edit'){
            $scope.ids = [];
            $scope.commodity_ids = [];
            $scope.commodity_list = [];
            $scope.package = {};
            $scope.packages_detail = [];
            angular.forEach(list, function(data, index){
                if($scope.commodity_ids.length == 0 || $scope.commodity_ids.indexOf(data.id) == -1){
                    data.commodity_price = data.cash_price;
                    data.number = 1;
                    $scope.commodity_ids.push(data.id);
                    $scope.commodity_list.push(data);
                }
            });
            var rank_flag = ($scope.selecteCommodityList && $scope.selecteCommodityList != '')  ? $scope.selecteCommodityList.length + 1 : 1;
            if($scope.selecteCommodityList == '' || $scope.selecteCommodityList == undefined){
                $scope.selecteCommodityList = angular.copy($scope.commodity_list);
            }else{
                angular.forEach($scope.selecteCommodityList,function(data , index){
                    var hasData = $scope.commodity_ids.indexOf(data.agent_commodity_id);
                    if(hasData != -1){
                        for(var i = 0; i<$scope.commodity_list.length;i++){
                            if($scope.commodity_list[i].id == data.agent_commodity_id){
                                $scope.commodity_list.splice(i, 1);
                            }
                        }
                        $scope.commodity_ids.splice(hasData, 1);
                    }
                });


                 angular.forEach($scope.commodity_list, function (data, index) {
                    $scope.selecteCommodityList.push(data);
                });
            }
            if($scope.commodity_ids.length == 0){
                _jiyin.msg('e', '已经添加过该商品');
                return;
            }
           
            angular.forEach($scope.selecteCommodityList, function (data, index) {
                $scope.ids.push(data.id);
            });
            angular.forEach($scope.selecteCommodityList, function (data,index) {
                data.rank = index + 1;
            });

            angular.forEach($scope.commodity_list, function(data, index){
                data.rank = angular.copy(rank_flag);
                $scope.package.agent_commodity_id = data.id;
                $scope.package.rank = data.rank;
                $scope.package.agent_index_id = angular.copy($stateParams.id);
                $scope.packages_detail.push(angular.copy($scope.package));
                rank_flag++;
            });
            $scope.packages_detail = JSON.stringify($scope.packages_detail);

            _jiyin.dataPost('admin/agent_admin/add_agent_commodity_category', dataToURL({commodity_category: $scope.packages_detail}))
                .then(function (result) {
                    console.log(result);
                    if (result.success){
                        $scope.get_category_by_name();
                    }else{
                        _jiyin.msg('e', result.msg);
                    }
                });
        }else if ($scope.operate == 'add'){
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

            angular.forEach($scope.selecteCommodityList, function (data,index) {
                data.rank = index + 1;
            });

            $scope.idStr = $scope.commodity_ids.join(',');
        }
    };


    //添加商品分类
    $scope.submit = function () {
        $scope.infoList.color = $('#color').val();
        if(!$scope.infoList.name){
            _jiyin.msg('e', '请填写主页名称名称');
            return;
        }
        var reg= /^[A-Za-z]+$/;
        if (!reg.test($scope.infoList.name)){
            _jiyin.msg('e', '主页名称必须全是英文');
            return;
        }
        if(!$scope.infoList.color){
            _jiyin.msg('e', '请填写主页配色');
            return;
        }
        if($scope.operate == 'add'){
            if(!$scope.selecteCommodityList || $scope.selecteCommodityList == []){
                _jiyin.msg('e', '请添加商品');
                return;
            }
            $scope.submitData = [];
            $scope.submitSub = {};
            //提取需要的商品信息
            angular.forEach($scope.selecteCommodityList, function(data,index){
                $scope.submitSub.commodity_id = data.commodity_id;
                $scope.submitSub.commodity_specification_id = data.id;
                $scope.submitSub.name = $scope.infoList.name;
                $scope.submitSub.color = $scope.infoList.color;
                $scope.submitSub.rank = data.rank;
                $scope.submitSub.agent_commodity_id = data.id;
                $scope.submitSub.agent_id = $scope.infoList.agent_id;
                $scope.submitData.push(angular.copy($scope.submitSub));
            });
            _jiyin.dataPost('admin/agent_admin/add_agent_category', dataToURL({commodity_category:JSON.stringify($scope.submitData)}))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s',result.msg);
                        $state.go('app.agentCommodityCategory');
                    }else{
                        _jiyin.msg('e',result.msg);
                    }
                })
        }else{
            _jiyin.dataPost('admin/agent_admin/update_agent_index', dataToURL({name: $scope.infoList.name, color: $scope.infoList.color, index_id: $scope.infoList.index_id}))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s',result.msg);
                        $state.go('app.agentCommodityCategory');
                    }else{
                        _jiyin.msg('e',result.msg);
                    }
                })
        }


    };

    //调整顺序
    $scope.changeSort = function (arr, index, up, attr) {
        var temp;
        temp = arr[index];
        arr[index] = arr[index + up];
        arr[index + up] = temp;
        var ids = [];
        var ranks = [];
        angular.forEach($scope.selecteCommodityList, function (data, index) {
            ids[ids.length] = data.id;
            ranks[ranks.length] = data.rank;
        });
        ids = ids.toString();
        _jiyin.dataPost('admin/Agent_admin/adjust_rank', dataToURL({
            id: ids
        })).then(function (result) {
            if(result.success == true){
                _jiyin.msg('s', '调整成功');
                $scope.get_category_by_name();
            }else{
                _jiyin.msg('e', result.msg);
            }
        });
        return false;
    };

    $scope.reset = function () {
        $scope.discount = [];
    }
    $scope.back = function () {
        history.go(-1);
    }
}]);
