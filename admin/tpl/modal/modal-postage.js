/**
 * Created by sailwish001 on 2016/12/6.
 */
'use strict';

app.controller('modalPostageCtrl', ['$scope', '$modalInstance', '_jiyin', 'params', 'FileUploader', 'dataToURL',
    function ($scope, $modalInstance, _jiyin, params, FileUploader, dataToURL) {
        $scope.levelIds = [];
        $scope.categoryIds = [];
        $scope.categoryObject = {};

        $scope.commodity_ids = [];
        $scope.commodity_list = [];
        $scope.ids = [];
        $scope.setCommodity = {};
        if(!$scope.set_commodity){
            $scope.set_commodity = [];
        }

        $scope.infoList = {};
        if(params.infoList.order_cost != null && params.infoList.order_cost != 0){
            params.infoList.type = 1;
        }else if(params.infoList.order_commodity_amount != null && params.infoList.order_commodity_amount != 0){
            params.infoList.type = 2;
        }

        $scope.selecteCommodityList = [];
        angular.forEach(params.infoList.commodity_list,function (data, index) {
            data.id = data.option_id;
            data.name = data.option_name;
            $scope.selecteCommodityList.push(data);
        });
        $scope.set_commodity = angular.copy($scope.selecteCommodityList);
        console.log($scope.set_commodity);

        $scope.selecteCommodityListByCategory = [];
        angular.forEach(params.infoList.category_list,function (data, index) {
            data.id = data.option_id;
            data.name = data.option_name;
            $scope.categoryIds.push(data.option_id);
            $scope.selecteCommodityListByCategory.push(data);
        });
        console.log($scope.set_commodity);

        $scope.infoList = angular.copy(params.infoList);
        if($scope.infoList.order_commodity_amount != null && $scope.infoList.order_commodity_amount != 0){
            $scope.infoList.type = '2'
        }else if($scope.infoList.order_cost != null && $scope.infoList.order_cost != 0 || params.ael == 'add'){
            $scope.infoList.type = '1'
        }
        $scope.title = params.title;
        $scope.ael = params.ael;

        if($scope.ael == 'add'){
            $scope.infoList.level_list = [];
            $scope.infoList.terminal_list =[];
            $scope.infoList.level_scope = 1;
            $scope.infoList.terminal_type_scope = 1;
            $scope.allLevelChecked = true;
            $scope.allTerminalChecked = true;
        }else{

        }
        if(!$scope.infoList.commodity_scope){
            $scope.infoList.commodity_scope = 1
        }

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
         * 获取会员等级
         */

        $scope.getLevel = function () {
            _jiyin.dataPost('admin/level_admin/get_level')
                .then(function (result) {
                    angular.forEach(result.data, function (data, index) {
                        if($scope.infoList.level_list.length > 0 && $scope.infoList.level_scope == 0){
                            angular.forEach($scope.infoList.level_list, function (levelData, eq) {
                                if(data.id == levelData.option_id){
                                    data.checked = true;
                                    $scope.levelIds.push(data.id);
                                    return;
                                }
                            });

                            $scope.allLevelChecked = false;
                            $scope.setLevel = angular.copy($scope.infoList.level_list);
                        }else if($scope.infoList.level_scope == 1 || $scope.ael == 'add'){
                            data.checked = true;
                            $scope.allLevelChecked = true;
                            $scope.levelIds.push(data.id);
                            $scope.levelSubList.id = data.id;
                            $scope.levelSubList.name = data.name;
                            $scope.setLevel.push(angular.copy($scope.levelSubList));
                        }

                    });
                    $scope.levelList = result.data;
                    if($scope.infoList.level_list.length == 0 && $scope.infoList.level_scope == 0){
                        $scope.levelIds = [];
                        $scope.setLevel = [];
                        $scope.levelList = [];
                    }
                });
            console.log($scope.levelList)
        };
        $scope.getLevel();

        //获取终端类型
        $scope.terminalIds = [];
        $scope.setTerminal = [];
        $scope.terminalSubList = {};
        $scope.get_terminal_type = function () {
            _jiyin.dataGet('admin/system_code_admin/get_by_type/terminal_type')
                .then(function (result) {
                    if(result){
                        angular.forEach(result, function (data, index) {
                            if($scope.infoList.terminal_list.length > 0 && $scope.infoList.terminal_type_scope == 0){
                                angular.forEach($scope.infoList.terminal_list, function (levelData, eq) {
                                    if(data.id == levelData.option_id){
                                        data.checked = true;
                                        $scope.terminalIds.push(data.id);
                                        return;
                                    }
                                });
                                $scope.allTerminalChecked = false;
                                $scope.setTerminal = angular.copy($scope.infoList.terminal_list);
                            }else if($scope.infoList.terminal_type_scope == 1 || $scope.ael == 'add'){
                                data.checked = true;
                                $scope.allTerminalChecked = true;
                                $scope.terminalIds.push(data.id);
                                $scope.terminalSubList.id = data.id;
                                $scope.terminalSubList.name = data.name;
                                $scope.setTerminal.push(angular.copy($scope.terminalSubList));
                            }

                        });
                        $scope.setTerminalList = result;
                        if($scope.infoList.terminal_list.length == 0 && $scope.infoList.terminal_type_scope == 0){
                            $scope.terminalIds = [];
                            $scope.setTerminal = [];
                            $scope.setTerminalList = [];
                        }
                        console.log('setTerminalListt');
                        console.log( $scope.setTerminalList);
                    }
                })
        };
        $scope.get_terminal_type();
        /**
         * 选择会员
         */
        $scope.levelSubList = {};
        $scope.setLevel = [];
        $scope.selectAllLevel = function () {
            if ($scope.allLevelChecked) {
                $scope.levelIds = [];
                $scope.setLevel = [];
                angular.forEach($scope.levelList, function(value,index) {
                    value.checked = true;
                    $scope.levelIds.push(value.id);
                    $scope.levelSubList.id = value.id;
                    $scope.levelSubList.name = value.name;
                    $scope.setLevel.push(angular.copy($scope.levelSubList));
                });

                $scope.infoList.level_scope = 1;

            } else { // 清空全选
                $scope.levelIds = [];
                angular.forEach($scope.levelList, function(value,index) {
                    value.checked = false;
                });
                $scope.setLevel = [];
                $scope.infoList.level_scope = 0;
            }
            console.log($scope.setLevel);
        };
        $scope.selectLevel = function (data) {
            angular.forEach($scope.levelList, function(item) {
                var localIndex = $scope.levelIds.indexOf(data.id);
                // 选中
                if (localIndex === -1 && item.checked && data.id == item.id) {
                    $scope.levelIds.push(item.id);
                    $scope.levelSubList.id = item.id;
                    $scope.levelSubList.name = item.name;
                    $scope.setLevel.push(angular.copy($scope.levelSubList));
                } else if (localIndex !== -1 && !item.checked && data.id == item.id) { // 取消选中
                    $scope.levelIds.splice(localIndex, 1);
                    $scope.setLevel.splice(localIndex, 1);
                }
            });
            $scope.allLevelChecked = $scope.levelList.length === $scope.levelIds.length;
            $scope.levelList.length === $scope.levelIds.length ? $scope.infoList.level_scope = 1 : $scope.infoList.level_scope = 0;
            console.log($scope.setLevel);
        };

        /**
         * 选择终端
         */
        $scope.selectAllTerminal = function () {
            if ($scope.allTerminalChecked) {
                $scope.terminalIds = [];
                $scope.setTerminal = [];
                angular.forEach($scope.setTerminalList, function(data,index) {
                    data.checked = true;
                    $scope.terminalIds.push(data.id);
                    $scope.terminalSubList.id = data.id;
                    $scope.terminalSubList.name = data.name;
                    $scope.setTerminal.push(angular.copy($scope.terminalSubList));
                });

                $scope.infoList.terminal_type_scope = 1;

            } else { // 清空全选
                $scope.terminalIds = [];
                angular.forEach($scope.setTerminalList, function(value,index) {
                    value.checked = false;
                });
                $scope.setTerminal = [];
                $scope.infoList.terminal_type_scope = 0;
            }
            console.log($scope.setTerminal);
        };
        $scope.selectTerminal = function (data) {
            angular.forEach($scope.setTerminalList, function(item) {
                var localIndex = $scope.terminalIds.indexOf(data.id);
                // 选中
                if (localIndex === -1 && item.checked && data.id == item.id) {
                    $scope.terminalIds.push(data.id);
                    $scope.terminalSubList.id = data.id;
                    $scope.terminalSubList.name = data.name;
                    $scope.setTerminal.push(angular.copy($scope.terminalSubList));
                } else if (localIndex !== -1 && !item.checked && data.id == item.id) { // 取消选中
                    $scope.terminalIds.splice(localIndex, 1);
                    $scope.setTerminal.splice(localIndex, 1);
                }
            });
            $scope.allTerminalChecked = $scope.setTerminalList.length === $scope.terminalIds.length;
            $scope.setTerminalList.length === $scope.terminalIds.length ? $scope.infoList.terminal_type_scope = 1 : $scope.infoList.terminal_type_scope = 0;
            console.log($scope.setTerminal);
        };

        /**
         * 获取商品分类
         */
        if(!$scope.selecteCommodityListByCategory){
            $scope.selecteCommodityListByCategory = [];
        }

        $scope.getCate = function () {
            _jiyin.dataPost('admin/category_admin/get_categories')
                .then(function (result) {
                    $scope.cateList = result.data;
                });
        };
        $scope.getCate();

        $scope.add_commodity_by_category = function () {
            var index = $scope.categoryIds.indexOf($scope.infoList.category_id);
            if(index == -1){
                $scope.categoryObject.id = $scope.infoList.category_id;
                $scope.categoryObject.name = $('#category').find("option:selected").text();
                $scope.categoryIds.push($scope.infoList.category_id);
                $scope.selecteCommodityListByCategory.push(angular.copy($scope.categoryObject));
            }else{
                _jiyin.msg('e', '已经添加过该分类');
            }
            console.log($scope.selecteCommodityListByCategory);
        };

        //删除分类
        $scope.deleteCategory = function (index) {
            $scope.categoryIds.splice(index, 1);
            $scope.selecteCommodityListByCategory.splice(index, 1);
        };

        /**
         * 添加商品
         */
        //添加代理商商品
        $scope.add_commodity = function () {
            $scope.title = '添加免邮规格';
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


        $scope.add = function (list) {
            $scope.set_commodity = [];
            if ($scope.ael == 'edit'){
                $scope.ids = [];
                $scope.commodity_ids = [];
                $scope.commodity_list = [];
                angular.forEach(list, function(data, index){
                    if($scope.commodity_ids.length == 0 || $scope.commodity_ids.indexOf(data.id) == -1){
                        $scope.commodity_ids.push(data.id);
                        $scope.commodity_list.push(data);
                    }
                });
                if($scope.selecteCommodityList == '' || $scope.selecteCommodityList == undefined){
                    $scope.selecteCommodityList = angular.copy($scope.commodity_list);
                }else{
                    angular.forEach($scope.selecteCommodityList,function(data , index){
                        var hasData = $scope.commodity_ids.indexOf(data.id);
                        if(hasData != -1){
                            for(var i = 0; i<$scope.commodity_list.length;i++){
                                if($scope.commodity_list[i].id == data.id){
                                    $scope.commodity_list.splice(i, 1);
                                }
                            }
                            $scope.commodity_ids.splice(hasData, 1);
                        }
                    });
                    if($scope.commodity_ids.length == 0){
                        _jiyin.msg('e', '已经添加过该商品');
                        return;
                    }
                    angular.forEach($scope.commodity_list, function (data, index) {
                        $scope.selecteCommodityList.push(data);
                    });
                }
                angular.forEach($scope.selecteCommodityList, function (data, index) {
                    $scope.ids.push(data.id);
                });
            }else if ($scope.ael == 'add'){
                angular.forEach(list, function(data, index){
                    if($scope.commodity_ids.length == 0 || $scope.commodity_ids.indexOf(data.id) == -1){
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
            }

            angular.forEach($scope.selecteCommodityList, function (data,index) {
                if(data.specification_center_name){
                    data.name = data.commodity_name + '-' + data.specification_center_name + '-' + data.package_type_name;
                }else if(data.specification_name){
                    data.name = data.commodity_name + '-' +  data.specification_name + '-' + data.package_type_name;
                }

                $scope.setCommodity.id = data.id;
                $scope.setCommodity.commodity_name = data.name;
                $scope.set_commodity.push(angular.copy($scope.setCommodity));
            });
            console.log($scope.selecteCommodityList);
            console.log($scope.set_commodity);
        };

        //删除添加的商品
        $scope.cancelCommodity = function (index, data) {
            $scope.commodity_ids.splice(data.id, 1);
            $scope.selecteCommodityList.splice(index, 1);
            $scope.set_commodity.splice(index, 1);
        };


        /**
         * 取消关闭
         */
        // $scope.infoList.type = 1;
        // $scope.couponCommodity = 0;
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };


        $scope.ok = function () {

            $scope.infoList.terminal_list = JSON.stringify($scope.setTerminal);
            if(!$scope.infoList.name){
                _jiyin.msg('e','名称不能为空');
                return ;
            }
            if(!$scope.infoList.role_id){
                _jiyin.msg('e', '角色不能为空');
                return;
            }
            if(($scope.infoList.commodity_scope == 2 && $scope.selecteCommodityListByCategory.length <= 0) || ($scope.infoList.commodity_scope == 3 && $scope.set_commodity.length <= 0)){
                _jiyin.msg('e', '请添加可使用商品');
                return;
            }
            if($scope.infoList.type == 1){
               if(!$scope.infoList.order_cost){
                   _jiyin.msg('e', '请设置包邮规格');
                   return;
               }else if($scope.infoList.order_cost == 0){
                   _jiyin.msg('e', '包邮规格价格不能为0');
                   return;
               }
            }else if($scope.infoList.type == 2){
                    if(!$scope.infoList.order_commodity_amount){
                        _jiyin.msg('e', '请设置包邮规格');
                        return;
                    }else if($scope.infoList.order_commodity_amount == 0){
                        _jiyin.msg('e', '包邮规格商品数量不能为0');
                        return;
                    }
            }

            if($scope.infoList.level_scope != 1 && $scope.infoList.setLevel <= 0){
                _jiyin.msg('e', '请选择添加会员');
                return;
            }
            if($scope.infoList.terminal_type_scope != 1 && $scope.infoList.setTerminal <= 0){
                _jiyin.msg('e', '请选择终端');
                return;
            }
            angular.forEach($scope.selecteCommodityListByCategory, function (data, index) {
                data.option_id = angular.copy(data.id);
                data.option_name = angular.copy(data.name);
                delete data.$$hashKey;
            });
            angular.forEach($scope.selecteCommodityListByCategory, function (data, index) {
                delete data.id;
                delete data.name;
            });
            $scope.infoList.category_list = JSON.stringify($scope.selecteCommodityListByCategory);
            angular.forEach($scope.set_commodity, function (data, index) {
                data.option_id = angular.copy(data.id);
                data.option_name = angular.copy(data.commodity_name ? data.commodity_name : data.name);
            });
            angular.forEach($scope.set_commodity, function (data, index) {
                delete data.id;
                delete data.commodity_name;
                delete data.name;
            });
            $scope.infoList.commodity_list = JSON.stringify($scope.set_commodity);
            angular.forEach($scope.setLevel, function (data, index) {
                data.option_id = angular.copy(data.id ? data.id : data.option_id);
                data.option_name = angular.copy(data.name ? data.name : data.option_name);
            });
            angular.forEach($scope.setLevel, function (data, index) {
                delete data.id;
                delete data.name;
            });
            $scope.infoList.level_list = JSON.stringify($scope.setLevel);
            angular.forEach($scope.setTerminal, function (data, index) {
                data.option_id = angular.copy(data.id ? data.id : data.option_id);
                data.option_name = angular.copy(data.name ? data.name : data.option_name);
            });
            angular.forEach($scope.setTerminal, function (data, index) {
                delete data.id;
                delete data.name;
            });
            $scope.infoList.terminal_list = JSON.stringify($scope.setTerminal);
            $modalInstance.close($scope.infoList);
        }
    }]);