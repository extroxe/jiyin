/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('alipayCtrl', ['$scope', '_jiyin', 'dataToURL', function ($scope, _jiyin, dataToURL) {
    $scope.alipayList = {};
    $scope.inputPage = 1;
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/refund_admin/paginate', dataToURL({
            payment_id: 2,
            page: $scope.inputPage,
            page_size: 10
        }))
            .then(function(result){
                $scope.alipayList = result.data;
                $scope.totalPage = result.total_page;
            });
    };
    $scope.getData();
    /**
     * 查看
     */
    $scope.look = function(data){
        $scope.title = '查看数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-refund.html',
            tempCtrl : 'modalRefundCtrl',
            ok : $scope.edit,
            size : 'lg',
            params : {
                title: $scope.title,
                infoList: data,
                ael: 'edit'
            }
        });
    };
    /**
     * 同意
     * @param data
     */
    $scope.agree = function(data){
        if(confirm('确认同意这条退款吗?')){
            _jiyin.dataPost('admin/refund_admin/audit_refund',dataToURL({
                id: data.id,
                audit_result: true
            }))
                .then(function(result){
                    _jiyin.msg('s', '操作成功');
                    $scope.getData();
                });
        }
    };
    /**
     * 拒绝
     * @param data
     */
    $scope.refuse = function(data){
        if(confirm('确认拒绝这条退款吗?')){
            _jiyin.dataPost('admin/refund_admin/audit_refund',dataToURL({
                id: data.id,
                audit_result: false
            }))
                .then(function(result){
                    _jiyin.msg('s', '操作成功');
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