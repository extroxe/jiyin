/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('commodityCategoryCtrl', ['$scope','$modalInstance', '_jiyin', 'dataToURL', '$stateParams', '$state', 'params', function ($scope,$modalInstance, _jiyin, dataToURL, $stateParams, $state, params) {
    $scope.commonclassList = {};
    $scope.list = [];
    $scope.commodity_details = angular.copy(params.infoList);
    $scope.inputPage = 1;
    $scope.open = false;
    $scope.ael = angular.copy(params.ael);
    $scope.selectFlag = angular.copy(params.select);
    $scope.agent_id = angular.copy(params.agent_id);

    $scope.keywords = '';

    /**
     * 获取商品数据
     */
    $scope.getData = function(){
        var count = 0
        _jiyin.dataPost('admin/agent_admin/agent_paginate_by_id/' + $scope.inputPage + '/10', dataToURL({
            agent_id: $scope.agent_id,
            keywords: $scope.keywords,
        }))
            .then(function(result){
                if(result.success){
                    angular.forEach(result.data, function (data, index) {
                        if($scope.ids.indexOf(data.id) >= 0){
                            data.checked = true;
                            count++;
                        }
                    });
                    if(count == result.data.length){
                        $('.select-all').prop('checked', true);
                    }else{
                        $('.select-all').prop('checked', false);
                    }
                    $scope.commonclassList = result.data;
                }else{
                    $scope.commonclassList = [];
                    _jiyin.msg('e', result.msg);
                    $('.select-all').prop('checked', false);
                }
                $scope.total_num = result.total_num;
                $scope.totalPage = result.total_page;
            });
    };
    $scope.getData();

    /*
     搜索
     */
    $scope.search = function () {
        $scope.getData();
    };

    $("#search").keydown(function (e) {
        if(e.keyCode==13) {
            $scope.getData();
        }
    });

    //全选
    $scope.ids = [];
    $scope.selectAll = function (e) {
        if($(e.target)[0].checked){
            angular.forEach($scope.commonclassList, function (singleData, sIndex) {
                if($scope.ids.indexOf(singleData.id) < 0){
                    $scope.ids.push(singleData.id);
                    $scope.list.push(singleData);
                }
            });
            $('.single-check1').prop('checked', true);
        }else{
            for(var i = 0; i<$scope.commonclassList.length; i++){
                var index = $scope.ids.indexOf($scope.commonclassList[i].id);
                if(index>=0){
                    $scope.ids.splice(index, 1);
                    $scope.list.splice(index,1);
                }
            }
            $('.single-check1').prop('checked', false);
        }
    };
    
    //单选
    $scope.selectSingle = function (e, commodity) {
        if($(e.target)[0].checked && $scope.ids.indexOf(commodity.id) == -1){
            $scope.ids.push(commodity.id);
            $scope.list.push(commodity);
            if($scope.ids.length == $scope.commonclassList.length){
                $('.select-all').prop('checked', true);
            }
        }else{
            for(var i = 0; i<$scope.ids.length; i++){
                if($scope.ids[i] == commodity.id){
                    $scope.ids.splice(i, 1);
                    $scope.list.splice(i,1);
                    i--;
                }
            }
            $('.select-all').prop('checked', false);
        }
    };

    //关闭窗口
    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    };

    /**
     * 下一页
     */
    $scope.nextPage = function(){
        if($scope.inputPage < $scope.totalPage){
            $scope.inputPage ++;
            $scope.getData();
        }else{
            _jiyin.msg('e', '当前是最后一页');
        }
    };
    /**
     * 上一页
     */
    $scope.previousPage = function(){
        if($scope.inputPage > 1){
            $scope.inputPage --;
            $scope.getData();
        }else{
            _jiyin.msg('e', '当前是第一页');
        }
    };
    /**
     * 第一页
     */
    $scope.firstPage = function () {
        $scope.inputPage = 1;
        $scope.getData();
    };
    /**
     * 最后一页
     */
    $scope.lastPage = function () {
        $scope.inputPage = $scope.totalPage;
        $scope.getData();
    };
    $scope.selectPage = function (page) {
        $scope.inputPage = page;
        $scope.getData();
    };

    $scope.batch_add = function(){
        $modalInstance.close($scope.list);
    }
}]);