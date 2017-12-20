/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('discountCtrl', ['$scope', '_jiyin', 'dataToURL', function ($scope, _jiyin, dataToURL) {
    $scope.discountList = [];
    $scope.inputPage = 1;
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/commodity_admin/flash_sale_paginate/'+$scope.inputPage+'/10',dataToURL({agent_id:'admin'}))
            .then(function(result){
                if (result.success) {
                    $scope.discountList = result.data;
                } else {
                    $scope.discountList = [];
                    _jiyin.msg('e', result.msg);
                }
                $scope.totalPage = result.total_page;
            });
    };
    $scope.getData();
    /**
     * 增加
     */
    $scope.addList = function () {
        $scope.title = '增加数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-discount.html',
            tempCtrl : 'modalDisCtrl',
            ok : $scope.add,
            size : 'lg',
            params : {
                title: $scope.title,
                infoList: {},
                ael: 'add'
            }
        });
    };
    $scope.add = function (list) {
        _jiyin.dataPost('admin/commodity_admin/add_flash_sale',dataToURL(list))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s','添加成功');
                    $scope.getData();
                }else{
                    _jiyin.msg('e', result.msg);
                }
            });
    };
    /**
     * 编辑
     */
    $scope.editList = function (data) {
        $scope.title = '编辑数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-discount.html',
            tempCtrl : 'modalDisCtrl',
            ok : $scope.edit,
            size : 'lg',
            params : {
                title: $scope.title,
                infoList: data,
                ael: 'edit'
            }
        });
    };
    $scope.edit = function (list) {
        _jiyin.dataPost('admin/commodity_admin/update_flash_sale',dataToURL(list))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s','修改成功');
                    $scope.getData();
                }else{
                    _jiyin.msg('e','修改失败');
                }
            });
    };
    /**
     * 删除信息
     * @param data
     */
    $scope.deleteData = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.dataPost('admin/commodity_admin/delete_flash_sale',dataToURL({ id: data.id}))
                .then(function(result){
                    if(result){
                        _jiyin.msg('s', '删除成功');
                        $scope.getData();
                    }else {
                        _jiyin.msg('e', result.msg);
                    }
                });
        }
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
    }
}]);