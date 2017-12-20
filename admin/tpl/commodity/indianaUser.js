/**
 * Created by sailwish001 on 2017/3/7.
 */
app.controller('indianaUserCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', function ($scope, _jiyin, dataToURL, $stateParams, $state) {
    $scope.indianaList = {};
    $scope.list = {};
    $scope.inputPage = 1;
    $scope.isAudit = false;
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataGet('admin/integral_indiana_admin/indiana_bet_info/'+$stateParams.id+'')
            .then(function(result){
                $scope.indianaList = result.data;
                $scope.totalPage = result.total_page;
                angular.forEach(result.data, function (data, index, arr) {
                   if(data.integral_indiana_result_status > 0){
                       $scope.isAudit = true;
                   }
                });
            });
    };
    $scope.getData();
    /**
     * 审核
     */
    $scope.check = function (data) {
        _jiyin.dataGet('admin/integral_indiana_admin/operate_result/'+data.result_id+'')
            .then(function(result){
                if(result.success == true){
                    _jiyin.msg('s', result.msg);
                    $scope.getData();
                }else {
                    _jiyin.msg('e', result.msg);
                }
            });
    };
    /**
     * 编辑
     */
    $scope.edit = function (data) {
        _jiyin.dataPost('admin/integral_indiana_admin/modify_winner', dataToURL({
            indiana_id: data.integral_indiana_id,
            integral_indiana_bet_id: data.bet_id,
            user_id: data.user_id

        }))
            .then(function(result){
                if(result.success == true){
                    _jiyin.msg('s', result.msg);
                    $scope.getData();
                }else {
                    _jiyin.msg('e', result.msg);
                }
            });
    };
    /*$scope.ok = function () {
        $scope.list = data;
        $('#edit').modal('show');
    };*/
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