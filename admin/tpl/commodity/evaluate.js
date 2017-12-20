/**
 * Created by sailwish001 on 2016/12/2.
 */
app.controller('evaluateCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', function ($scope, _jiyin, dataToURL, $stateParams) {
    $scope.evaluateList = {};
    $scope.inputPage = 1;
    $scope.url = '';
    console.log($stateParams);
    if($stateParams.type == 'com'){
        $scope.flag = true;
        if($stateParams.specification_id && $stateParams.specification_id != ''){
            $scope.specification_eva = true;
            $scope.url = 'admin/commodity_admin/evaluation_paginate/'+$scope.inputPage+'/10/'+$stateParams.commodity_id+'/' + $stateParams.specification_id;
        }else{
            $scope.url = 'admin/commodity_admin/evaluation_paginate/'+$scope.inputPage+'/10/'+$stateParams.commodity_id;
            $scope.specification_eva = false;
        }
    }else if($stateParams.type == 'int'){
        $scope.flag = false;
        if($stateParams.specification_id && $stateParams.specification_id != ''){
            $scope.specification_eva = true;
            $scope.url = 'admin/commodity_admin/evaluation_paginate/'+$scope.inputPage+'/10/'+$stateParams.commodity_id+'/' + $stateParams.specification_id;
        }else{
            $scope.url = 'admin/commodity_admin/evaluation_paginate/'+$scope.inputPage+'/10/'+$stateParams.commodity_id;
            $scope.specification_eva = false;
        }
    }

    //返回上一页
    $scope.goBack = function () {
        history.go(-1);
    };
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataGet($scope.url)
            .then(function(result){
                if(result.success){
                    if(result.total_page === false){
                        $scope.totalPage = 1;
                    }else{
                        $scope.totalPage = result.total_page;
                    }
                    $scope.evaluateList = result.data;
                }else{
                    _jiyin.msg('e', result.msg);
                }
            });
    };
    $scope.getData();
    /**
     * 审核、回复评论
     */
    $scope.review_evaluate  = function (id) {
        $scope.title = '回复评论';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-replyEvaluation.html',
            tempCtrl : 'modalReplyContentCtrl',
            ok : $scope.reply,
            size : 'lg',
            params : {
                title: $scope.title,
                id: id,
                ael: 'add'
            }
        });
    };

    $scope.reply = function (data) {
        _jiyin.dataPost('/admin/Commodity_admin/review_evaluation', dataToURL(data))
            .then(function(result){
                if(result.success) {
                    _jiyin.msg('s', result.msg);
                    $scope.getData();
                }else {
                    _jiyin.msg('e', result.msg);
                }
            });
    };

    /**
     * 删除评论
     */
    $scope.delete_evaluate = function (evaluate_id) {
        if(confirm('确定删除这条评论吗？')){
            _jiyin.dataPost('/admin/Commodity_admin/delete_evaluation', dataToURL({id:evaluate_id}))
                .then(function(result){
                    if(result.success) {
                        _jiyin.msg('s', result.msg);
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