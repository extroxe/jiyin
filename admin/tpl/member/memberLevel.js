/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('levelCtrl', ['$scope', '_jiyin', 'dataToURL', function ($scope, _jiyin, dataToURL) {
    $scope.levelList = {};
    $scope.inputPage = 1;
    $scope.showEdit = true;
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataGet('admin/level_admin/get_level')
            .then(function(result){
                $scope.levelList = result.data;
                $scope.totalPage = result.total_page;
            });
    };
    $scope.getData();
    $scope.changeSort = function (arr, index, up, attr) {
        /*var temp;
        temp = arr[index].rank;
        arr[index][attr] = arr[index + up][attr];
        arr[index + up][attr] = temp;
        return false;*/
        var temp;
        temp = arr[index];
        arr[index] = arr[index + up];
        arr[index + up] = temp;
        var ids = [];
        var ranks = [];
        angular.forEach($scope.levelList, function (data, index) {
            ids[ids.length] = data.id;
            ranks[ranks.length] = data.rank;
        });
        ids = ids.toString();
        _jiyin.dataPost('admin/level_admin/adjust_rank', dataToURL({
            id: ids
        })).then(function (result) {
            if(result.success == true){
                _jiyin.msg('s', '调整成功');
                $scope.getData();
            }else{
                _jiyin.msg('e', result.msg);
            }
        });
        return false;
    };
    /**
     * 增加
     */
    $scope.addList = function () {
        $scope.title = '增加数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-level.html',
            tempCtrl : 'modalLevelCtrl',
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
        _jiyin.dataPost('admin/level_admin/add', dataToURL(list))
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
        $scope.title = '编辑数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-level.html',
            tempCtrl : 'modalLevelCtrl',
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
        _jiyin.dataPost('admin/level_admin/update', dataToURL(list))
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
            _jiyin.dataPost('admin/level_admin/del_level',dataToURL({ id: data.id}))
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