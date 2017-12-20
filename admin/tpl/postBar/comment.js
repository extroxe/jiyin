/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('commentCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', function ($scope, _jiyin, dataToURL, $stateParams, $state) {

    $scope.commentList = {};
    $scope.inputPage = 1;
    $scope.infoList = {};
    $scope.keyword = '';

    /**
     * 查看回复
     */
    $scope.look = function (data) {
        $("#replyAll").modal('show');
        $scope.replyList = data.replies;
    };
    /*
     搜索
     */
    $scope.search = function () {
        $scope.inputPage = 1;
        _jiyin.dataPost('admin/post_admin/paginate_comment/'+$scope.inputPage+'/10',dataToURL({
            post_bar_id : $stateParams.id,
            keyword : $scope.keyword
        }))
            .then(function(result){
                if(result.success == true){
                    $scope.totalPage = result.total_page;
                    $scope.commentList = result.data;
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
        $scope.infoList.post_id = $stateParams.id;
        $("#comment").modal('show');
    };
    $scope.edit = function (data) {
        $scope.title = '编辑';
        $scope.infoList = data;
        $scope.add = false;
        $("#comment").modal('show');
    };
    $scope.reply = function (data) {
        $scope.infoList = {};
        $scope.infoList.post_id = $stateParams.id;
        $scope.infoList.root_comment_id = data.id;
        $scope.infoList.comment_id = data.id;
        $scope.infoList.to_user_id = data.publisher_id;
        $("#reply").modal('show');
    };
    $scope.replyOk = function () {
        _jiyin.dataPost('admin/post_admin/add_comment',dataToURL($scope.infoList))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s','添加成功');
                    $scope.getData();
                    $("#reply").modal('hide');
                }else{
                    _jiyin.msg('e',result.msg);
                }
            });
    };

    /**
     * 获取帖子
     */
    $scope.getBar = function () {
        _jiyin.dataPost('admin/post_admin/paginate/1/999')
            .then(function (result) {
                $scope.postList = result.data;
            })
    };
    $scope.getBar();
    /**
     * 获取状态
     */
    $scope.getStatus = function () {
        _jiyin.dataPost('admin/system_code_admin/get_by_type/comment_status')
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
            _jiyin.dataPost('admin/post_admin/add_comment',dataToURL($scope.infoList))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s','添加成功');
                        $scope.getData();
                        $("#comment").modal('hide');
                    }else{
                        _jiyin.msg('e',result.msg);
                    }
                });
        }else{
            _jiyin.dataPost('admin/post_admin/update_comment',dataToURL({
                id : $scope.infoList.id,
                status_id : $scope.infoList.status_id
            }))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s','修改成功');
                        $scope.getData();
                        $("#comment").modal('hide');
                    }else{
                        _jiyin.msg('e',result.msg);
                    }
                });
        }

    };
    $scope.cancel = function () {
        $("#comment").modal('hide');
    };
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/post_admin/paginate_comment/'+$scope.inputPage+'/10/',dataToURL({
            post_bar_id : $stateParams.id,
            keyword : $scope.keyword
        }))
            .then(function(result){
                if(result.success == true){
                    $scope.totalPage = result.total_page;
                    $scope.commentList = result.data;
                }
            });
    };
    $scope.getData();

    /**
     * 删除信息
     * @param data
     */
    $scope.delete = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.dataPost('admin/post_admin/delete_comment',dataToURL({ id: data.id}))
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

    $scope.remove = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.dataPost('admin/post_admin/delete_comment',dataToURL({ id: data.id}))
                .then(function(result){
                    if(result.success == true){
                        _jiyin.msg('s', '删除成功');
                        $scope.getData();
                        data.status_id = 2;
                        data.status_name = '已删除（管理员）';
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