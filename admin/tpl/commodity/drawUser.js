/**
 * Created by sailwish001 on 2017/3/7.
 */
app.controller('drawUserCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', function ($scope, _jiyin, dataToURL, $stateParams, $state) {
    $scope.drawuserList = {};
    $scope.inputPage = 1;
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataGet('admin/sweepstakes_commodity_admin/get_all_prize/' + $stateParams.id + '/' + $scope.inputPage+'/10')
            .then(function(result){
                if (result.success) {
                    $scope.drawuserList = result.data;
                } else {
                    _jiyin.msg('e', result.msg);
                    $scope.drawuserList = [];
                }
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