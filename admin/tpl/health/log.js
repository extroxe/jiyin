/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('logCtrl', ['$scope', '_jiyin', 'dataToURL', function ($scope, _jiyin, dataToURL) {

    $scope.articleList = {};
    $scope.inputPage = 1;
    $scope.infoList = {};
    $scope.pageSize = 10;
    $scope.keyword = '';
    $scope.state = 0;
    $scope.checkState = ["全部"];
    $scope.register_start_time = '';
    $scope.register_end_time = '';

    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/article_admin/paginate_log/', dataToURL({
            keyword : $scope.keyword,
            interface_name: $scope.state,
            start_create_time :$scope.register_start_time,
            end_create_time : $scope.register_end_time,
            page : $scope.inputPage,
            page_size : $scope.pageSize,
            })).then(function(result){
                if(result.total_page === false){
                    $scope.totalPage = 1;
                }else{
                    $scope.totalPage = result.total_page;
                }
                $scope.articleList = result.data;
            });
    };
    $scope.getData();

    $scope.reset = function(){
        $scope.keyword = '';
        $scope.state = 0;
        $scope.checkState = ["全部"];
        $scope.register_start_time = '';
        $scope.register_end_time = '';
        $scope.getData();
    }

    //获取接口名称
    _jiyin.dataGet('admin/agent_admin/get_all_agent_code')
        .then(function (result) {
            if(result.success){
                $scope.status = result.data;
            }
        });
    $scope.stateFlagSub = function (index) {
        angular.forEach($scope.status, function (status, eq) {
            if(eq == index){
                $scope.state = status.value;
                $scope.checkState[0]=status.name;
            }
        });
    };

    /*
     搜索
     */
    $scope.search = function () {
        $scope.inputPage = 1;
        $scope.getData();
    };

    $("#search").keydown(function (e) {
        if (e.keyCode == 13) {
            $scope.search();
        }
    });

    //查询
    $scope.watch = function (data) {
        $scope.title = '查询';
        $("#article").modal('show');
        $scope.infoList = data;
    };

    $scope.cancel = function () {
        $("#article").modal('hide');
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
