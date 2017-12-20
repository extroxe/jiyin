/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('couponPayCtrl', ['$scope', '_jiyin', 'dataToURL', function ($scope, _jiyin, dataToURL) {
    $scope.couponPayList = {};
    $scope.inputPage = 1;
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.data('',dataToURL({ page: $scope.inputPage}))
            .then(function(result){
                $scope.couponPayList = result.data;
                $scope.totalPage = result.total_page;
            });
    };
    //$scope.getData();
    /**
     * 删除信息
     * @param data
     */
    $scope.deleteData = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.data('',dataToURL({ id: data.id}))
                .then(function(result){
                    _jiyin.msg('s', '删除成功');
                    $scope.getData();
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