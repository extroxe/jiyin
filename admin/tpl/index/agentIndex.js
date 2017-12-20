app.controller('agentCommodityCtrl', ['$scope', '_jiyin', 'dataToURL', function ($scope, _jiyin, dataToURL) {
    $scope.bannerList = {};
    $scope.inputPage = 1;
    $scope.keyword = '';
    $scope.name = '';

    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/agent_admin/get_agent_commodity_page?page='+$scope.inputPage+'&page_size=10', dataToURL({
            keyword: $scope.keyword,
            name: $scope.name
        })).then(function(result){
            if (result.success) {
                $scope.bannerList = result.data;
            } else {
                _jiyin.msg('e', result.msg);
                $scope.bannerList = [];
            }
            $scope.totalPage = result.total_page;
            $scope.role_id = result.role_id;
        });
    };
    $scope.getData();

    /*
     搜索
     */
    $scope.search = function () {
        $scope.inputPage = 1;
        $scope.getData();
    };

    $("#filter").keydown(function (e) {
        if(e.keyCode==13) {
            $scope.getData();
        }
    });

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

    /**
     * 增加
     */
    $scope.addList = function () {
        $scope.title = '添加代理商商品';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-agentIndex.html',
            tempCtrl : 'modalAgentCommodityCtrl',
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
        _jiyin.dataPost('admin/agent_admin/add_agent_commodity', dataToURL({commodity_category:JSON.stringify(list)}))
            .then(function (result) {
                if(result.success == true){
                    $scope.getData();
                    _jiyin.msg('s',result.msg);
                }else{
                    _jiyin.msg('e',result.msg);
                }
            })
    };

    /**
     * 编辑
     */
    $scope.editBannerList = function (data) {
        $scope.title = '编辑代理商商品';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-agentCommodity.html',
            tempCtrl : 'modalAgentCtrl',
            ok : $scope.editList,
            size : 'lg',
            params : {
                title: $scope.title,
                infoList: data,
                ael: 'edit'
            }
        });
    };
    $scope.editList = function (list) {
        _jiyin.dataPost('admin/agent_admin/update_agent_commodity', dataToURL(list))
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
            _jiyin.dataPost('admin/agent_admin/delete_agent_commodity',dataToURL({ id: data.id}))
                .then(function(result){
                    if (result.success) {
                        _jiyin.msg('s', result.msg);
                        $scope.getData();
                    } else {
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
