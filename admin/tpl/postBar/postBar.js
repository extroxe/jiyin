/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('postBarCtrl', ['$scope', '_jiyin', 'dataToURL', function ($scope, _jiyin, dataToURL) {

    $scope.postbarList = {};
    $scope.inputPage = 1;
    $scope.infoList = {};
    $scope.keyword = '';

    /*
     搜索
     */
    $scope.search = function () {
        $scope.inputPage = 1;
        _jiyin.dataPost('admin/post_bar_admin/get_post_bar/'+$scope.inputPage+'/10',dataToURL({
            key_words: $scope.keyword
        }))
            .then(function(result){
                if(result.success == true){
                    $scope.totalPage = result.total_page;
                    $scope.postbarList = result.data;
                }
            });
    };

    $("#search").keydown(function (e) {
        if(e.keyCode==13) {
            $scope.search();
        }
    });

    $scope.show=function(){
        $scope.title = '增加';
        $scope.add = true;
        $scope.infoList = {};
        $scope.infoList.is_recommended = 0;
        $("#postBar").modal('show');
    };
    $scope.edit = function (data) {
        $scope.title = '编辑';
        $scope.infoList = data;
        $scope.add = false;
        $("#postBar").modal('show');
    };


    $scope.ok = function () {
        /*if(!$scope.infoList.name){
            _jiyin.msg('e','贴吧标题不能为空');
            return ;
        }*/
        if($scope.add == true){
            _jiyin.dataPost('admin/post_bar_admin/add',dataToURL($scope.infoList))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s','添加成功');
                        $scope.getData();
                        $("#postBar").modal('hide');
                    }else{
                        _jiyin.msg('e',result.msg + ',带*号为必填，请先填写信息');
                    }
                });
        }else{
            _jiyin.dataPost('admin/post_bar_admin/update',dataToURL($scope.infoList))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s','修改成功');
                        $scope.getData();
                        $("#postBar").modal('hide');
                    }else{
                        _jiyin.msg('e',result.msg + ',带*号为必填，请先填写信息');
                    }
                });
        }

    };
    $scope.cancel = function () {
        $("#postBar").modal('hide');
    };
    /**
     * 是否推荐
     */
    $scope.recommend = function (id) {
        _jiyin.dataPost('admin/post_bar_admin/is_recommended_switch',dataToURL({id: id}))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s','修改成功');
                    $scope.getData();
                }else{
                    _jiyin.msg('e',result.msg);
                }
            });
    };
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataGet('admin/post_bar_admin/get_post_bar/'+$scope.inputPage+'/10')
            .then(function(result){
                if(result.success == true){
                    $scope.totalPage = result.total_page;
                    $scope.postbarList = result.data;
                }
            });
    };
    $scope.getData();

    /**
     * 删除信息
     * @param data
     */
    $scope.deleteData = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.dataPost('admin/post_bar_admin/delete',dataToURL({ id: data.id}))
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