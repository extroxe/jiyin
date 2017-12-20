/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('postageCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', '$state',
    function ($scope, _jiyin, dataToURL, $stateParams, $state) {
    $scope.couponList = {};
    $scope.inputPage = 1;

    /**
     * 获取邮费规则列表
     */
    $scope.getData = function(){
        _jiyin.dataGet('admin/postage_admin/paginate/'+$scope.inputPage+'/10')
            .then(function(result){
                if(result.total_page == false){
                    $scope.totalPage = 1;
                }else{
                    $scope.totalPage = result.total_page;
                }
                $scope.postageList = result.data;
            });
    };
    $scope.getData();

    /**
     * 增加邮费规则
     */
    $scope.addList = function () {
        $scope.title = '增加邮费规则';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-postage.html',
            tempCtrl : 'modalPostageCtrl',
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
        _jiyin.dataPost('admin/postage_admin/add', dataToURL(list))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s',result.msg);
                    $scope.getData();
                }else{
                    _jiyin.msg('e',result.msg);
                }
            });
    };

    /**
     * 编辑邮费规则
     */
    $scope.editList = function (data) {
        $scope.title = '编辑邮费规则';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-postage.html',
            tempCtrl : 'modalPostageCtrl',
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
        _jiyin.dataPost('admin/postage_admin/update', dataToURL(list))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s',result.msg);
                    $scope.getData();
                }else{
                    _jiyin.msg('e',result.msg);
                }
            });
    };

    /**
     * 删除邮费规则
     * @param data
     */
    $scope.deleteData = function(data){
        if(confirm('确认删除这条邮费规则吗?')){
            _jiyin.dataPost('admin/postage_admin/delete',dataToURL({ id: data.id}))
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
     * 启用规则
     */
    $scope.enableUse = function (data) {
        if(confirm('确定要启用该邮费规则吗?')){
            data.status_id = 1;
            _jiyin.dataPost('admin/postage_admin/update', dataToURL(data))
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

    /**
     * 查看该邮费规则下的商品
     * @param data
     */
    $scope.lookData = function (data) {
        console.log(data);
        $state.go('app.postageset', {'id': data.id});
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