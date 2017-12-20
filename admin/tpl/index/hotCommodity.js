/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('hotCommonCtrl', ['$scope', '_jiyin', 'dataToURL', function ($scope, _jiyin, dataToURL) {
    $scope.hotCommonList = {};
    $scope.inputPage = 1;
    $scope.keywords = '';

    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/commodity_admin/recommend_paginate/' + $scope.inputPage + '/10', dataToURL({
            is_point: 1,
            type_id: 2,
            agent_id:'admin',
            keywords: $scope.keywords
        })).then(function(result){
            if (result.success) {
                $scope.hotCommonList = result.data;
                $scope.totalPage = result.total_page;
            } else {
                $scope.hotCommonList = [];
                $scope.totalPage = 1;
                _jiyin.msg('e', result.msg);
            }
        });
    };
    $scope.getData();

    //搜索回车监听
    $("#search").keydown(function (e) {
        if(e.keyCode==13) {
            $scope.getData();
        }
    });

    $scope.search = function () {
        $scope.getData();
    }

    //获取当前时间
    function getNowFormatDate() {
        var date = new Date();
        var seperator1 = "-";
        var seperator2 = ":";
        var month = date.getMonth() + 1;
        var strDate = date.getDate();
        if (month >= 1 && month <= 9) {
            month = "0" + month;
        }
        if (strDate >= 0 && strDate <= 9) {
            strDate = "0" + strDate;
        }
        var currentDate = date.getFullYear() + seperator1 + month + seperator1 + strDate
            + " " + date.getHours() + seperator2 + date.getMinutes()
            + seperator2 + date.getSeconds();
        return currentDate;
    }
    $scope.date = getNowFormatDate();

    /**
     * 增加
     */
    $scope.addList = function () {
        $scope.title = '添加热换商品';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-recommond.html',
            tempCtrl : 'modalRecomCtrl',
            ok : $scope.add,
            size : 'lg',
            params : {
                title: $scope.title,
                infoList: {},
                ael: 'add',
                isPoint: 1
            }
        });
    };
    $scope.add = function (list) {
        _jiyin.dataPost('admin/commodity_admin/add_recommend',dataToURL(list))
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
        $scope.title = '编辑热换商品';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-recommond.html',
            tempCtrl : 'modalRecomCtrl',
            ok : $scope.edit,
            size : 'lg',
            params : {
                title: $scope.title,
                infoList: data,
                ael: 'edit',
                isPoint: true
            }
        });
    };
    $scope.edit = function (list) {
        _jiyin.dataPost('admin/commodity_admin/update_recommend',dataToURL(list))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s','修改成功');
                    $scope.getData();
                }else{
                    _jiyin.msg('e', result.msg);
                }
            });
    };
    /**
     * 删除信息
     * @param data
     */
    $scope.deleteData = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.dataPost('admin/commodity_admin/delete_recommend',dataToURL({ id: data.id}))
                .then(function(result){
                    if(result.success == true) {
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