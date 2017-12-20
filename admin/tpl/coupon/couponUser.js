/**
 * Created by sailwish001 on 2016/12/23.
 */
app.controller('couponuserCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', function ($scope, _jiyin, dataToURL, $stateParams) {
    $scope.couponuserList = {};
    $scope.inputPage = 1;
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/coupon_admin/get_distribution_by_coupon_id', dataToURL({id: $stateParams.id}))
            .then(function(result){
                $scope.couponuserList = result.data;
                $scope.totalPage = result.total_page;
            });
    };
    $scope.getData();

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