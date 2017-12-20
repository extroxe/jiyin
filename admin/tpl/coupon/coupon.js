/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('couponCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', function ($scope, _jiyin, dataToURL, $stateParams, $state) {
    $scope.couponList = {};
    $scope.inputPage = 1;
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataGet('admin/coupon_admin/paginate/' + $scope.inputPage + '/10')
            .then(function(result){
                if(result.success){
                    $scope.couponList = result.data;
                }else{
                    $scope.couponList = [];
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
        $scope.title = '添加优惠券';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-coupon.html',
            tempCtrl : 'modalCouponCtrl',
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
        _jiyin.dataPost('admin/coupon_admin/add', dataToURL(list))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s',result.msg);
                    $scope.getData();
                }else{
                    _jiyin.msg('e',result.msg);
                }
            })
    };

    /**
     * 编辑
     */
    $scope.editList = function (data) {
        $scope.title = '编辑优惠券';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-coupon.html',
            tempCtrl : 'modalCouponCtrl',
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
        _jiyin.dataPost('admin/coupon_admin/update', dataToURL(list))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s',result.msg);
                    $scope.getData();
                }else{
                    _jiyin.msg('e',result.msg);
                }
            })
    };

    /**
     * 删除信息
     * @param data
     */
    $scope.deleteData = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.dataPost('admin/coupon_admin/delete',dataToURL({ id: data.id}))
                .then(function(result){
                    if(result.success == true){
                        _jiyin.msg('s', '删除成功');
                        $scope.getData();
                    }else {
                        _jiyin.msg('e', result.msg);
                    }
                });
        }
    };

    /**
     * 发放
     */
    $scope.grant = function (data) {
        if(confirm('确定要发放该优惠券吗?')){
            data.status_id = 1;
            _jiyin.dataPost('admin/coupon_admin/update', dataToURL(data))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s',result.msg);
                        $scope.getData();
                    }else{
                        _jiyin.msg('e',result.msg);
                    }
                })
        }
    };

    $scope.lookData = function (data) {
        $state.go('app.couponUser', {id: data.id});
    };

    /**
     * 下一页
     */
    $scope.nextPage = function(){
        if($scope.inputPage < $scope.totalPage){
            $scope.inputPage++;
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
            $scope.inputPage--;
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