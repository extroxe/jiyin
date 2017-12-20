/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('postCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', function ($scope, _jiyin, dataToURL, $stateParams, $state) {

    $scope.postList = {};
    $scope.inputPage = 1;
    $scope.post_bar_id = 0;
    $scope.infoList = {};
    $scope.keyword = '';

    /*
     搜索
     */
    $scope.search = function () {
        $scope.inputPage = 1;
        _jiyin.dataPost('admin/post_admin/paginate/'+$scope.inputPage+'/10',dataToURL({
            post_bar_id: $scope.post_bar_id,
            keyword: $scope.keyword
        }))
            .then(function(result){
                $scope.postList = result.data;
                $scope.totalPage = result.total_page;
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
        $scope.infoList.is_stickied = 0;
        $("#post").modal('show');
    };
    $scope.edit = function (data) {
        $scope.title = '编辑';
        $scope.infoList = data;
        $scope.add = false;
        $("#post").modal('show');
    };

    $scope.getAll = function () {
        $scope.post_bar_id = 0;
        $scope.getData();
    };
    $scope.getSome = function () {
        $scope.inputPage = 1;
        $("#po").modal('show');
    };
    $scope.getPost = function () {
        _jiyin.dataPost('admin/post_admin/paginate/'+$scope.inputPage+'/10',dataToURL({
            post_bar_id: $scope.post_bar_id,
            keyword: $scope.keyword
        }))
            .then(function(result){
                $scope.postList = result.data;
                $scope.totalPage = result.total_page;
                $("#po").modal('hide');
            });
    };
    /**
     * 获取所有贴吧
     */
    $scope.getBar = function () {
        _jiyin.dataPost('admin/post_bar_admin/get_post_bar/1/999')
            .then(function (result) {
                $scope.postbarList = result.data;
            })
    };
    $scope.getBar();
    /**
     * 获取状态
     */
    $scope.getStatus = function () {
        _jiyin.dataPost('admin/system_code_admin/get_by_type/post_status')
            .then(function (result) {
                $scope.statusList = result;
            })
    };
    $scope.getStatus();

    $scope.ok = function () {
        /*if(!$scope.infoList.title){
            _jiyin.msg('e','帖子标题不能为空');
            return ;
        }*/

        if($scope.add == true){
            _jiyin.dataPost('admin/post_admin/add',dataToURL($scope.infoList))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s','添加成功');
                        $scope.getData();
                        $("#post").modal('hide');
                    }else{
                        _jiyin.msg('e',result.msg + ',带*号为必填，请先填写信息');
                    }
                });
        }else{
            _jiyin.dataPost('admin/post_admin/update',dataToURL($scope.infoList))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s','修改成功');
                        $scope.getData();
                        $("#post").modal('hide');
                    }else{
                        _jiyin.msg('e',result.msg + ',带*号为必填，请先填写信息');
                    }
                });
        }

    };
    $scope.stick = function (data) {
        _jiyin.dataPost('admin/post_admin/stickied', dataToURL({
            id: data.id,
            bar_id: data.post_bar_id,
            is_stickied: data.is_stickied
        }))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s','修改成功');
                    $scope.getData();
                }else{
                    _jiyin.msg('e',result.msg);
                }
            });
    };
    $scope.cancel = function () {
        $("#post").modal('hide');
    };
    /**
     * 查看评论
     */
    $scope.look = function (data) {
        $state.go('app.comment', {id: data.id});
    };
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/post_admin/paginate/'+$scope.inputPage+'/10',dataToURL({
            post_bar_id: $scope.post_bar_id,
            keyword: $scope.keyword
        }))
            .then(function(result){
                $scope.postList = result.data;
                $scope.totalPage = result.total_page;
                $("#po").modal('hide');
            });
    };
    $scope.getData();

    /**
     * 删除信息
     * @param data
     */
    $scope.deleteData = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.dataPost('admin/post_admin/delete',dataToURL({ id: data.id}))
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