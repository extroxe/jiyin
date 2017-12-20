app.controller('packagesCtrl', ['$rootScope', '$scope', '_jiyin', 'dataToURL', '$stateParams', '$state', '$filter', function ($rootScope, $scope, _jiyin, dataToURL, $stateParams, $state, $filter) {
    $scope.is_admin = $rootScope.is_admin;
    $scope.inputPage = 1;
    $scope.keyword = '';
    $scope.register_start_time = '';
    $scope.register_end_time = '';
    $scope.name = '';

    /**
     * 获取数据
     */
    $scope.getData = function () {
        _jiyin.dataPost('admin/Agent_admin/get_all_category_for_agent',
            dataToURL({
                page: $scope.inputPage,
                page_size: 10,
                keyword: $scope.keyword,
                start_time: $scope.register_start_time,
                end_time: $scope.register_end_time,
                name: $scope.name
            })).then(function (result) {
                if (result.success) {
                    $scope.packagesList = result.data;
                    $scope.role_id = result.role_id;
                } else {
                    $scope.packagesList = [];
                    _jiyin.msg('e', result.msg);
                }
                $scope.total_num = result.total;
                $scope.totalPage = result.total_page;
        });
    };
    $scope.getData();

    $scope.getAgent = function () {
            $scope.title = '添加代理商';
            _jiyin.modal({
                tempUrl : '/source/admin/tpl/modal/modal-agentAddCommodity.html',
                tempCtrl : 'agentAddCommodityCtrl',
                ok : $scope.addAgent,
                size : 'lg',
                params : {
                    title: $scope.title,
                    ael: 'add'
                }
            });
        };
        $scope.addAgent = function (data) {
            $scope.inputPage = 1;
            $scope.agentName = data.name;
            $scope.infoList.agent_id = data.id;
            $scope.name = $scope.agentName;
            $scope.getData();
        };

    /*
    搜索
     */
    $scope.search = function () {
        $scope.inputPage = 1;
        $scope.getData();
    };

    /**
     * 删除信息
     * @param data
     */
    $scope.deleteData = function(id){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.dataPost('admin/Agent_admin/delete_agent_index',dataToURL({ id: id}))
                .then(function(result){
                    if(result.success){
                        _jiyin.msg('s', result.msg);
                        $scope.getData();
                    }else {
                        _jiyin.msg('e', result.msg);
                    }
                });
        }
    };

    $scope.add = function () {
        $state.go('app.editCommodityCategory', {operate: 'add'});
    };
    $scope.view_detail = function (data) {
        $state.go('app.editCommodityCategory', {operate: 'edit', category: data.index_name, id: data.id, agent_id: data.agent_id, color: data.color, agent_name: data.agent_name});
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
