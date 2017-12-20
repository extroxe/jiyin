/**
 * Created by sailwish001 on 2016/11/18.
 */
app.controller('bannerCtrl', ['$rootScope', '$scope', '_jiyin', 'dataToURL', function ($rootScope, $scope, _jiyin, dataToURL) {
    $scope.is_admin = $rootScope.is_admin;
    $scope.bannerList = {};
    $scope.inputPage = 1;
    $scope.keyword = '';
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/banner_admin/get_page_info',
            dataToURL({
                page: $scope.inputPage,
                page_size: 10,
                keyword: $scope.keyword
        }))
            .then(function(result){
                $scope.bannerList = result.data;
                $scope.totalPage = result.total_page;
            });
    };
    $scope.getData();
    /**
     * 增加
     */
    $scope.addBannerList = function () {
        $scope.title = '增加数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-banner.html',
            tempCtrl : 'modalBannerCtrl',
            ok : $scope.addList,
            size : 'lg',
            params : {
                title: $scope.title,
                infoList: {},
                ael: 'add'
            }
        });
    };
    $scope.addList = function (list) {
        _jiyin.dataPost('admin/banner_admin/add', dataToURL(list))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s',result.msg);
                    $scope.getData();
                }else{
                    _jiyin.msg('e',result.msg);
                }
            })
    };

    /*
    搜索
     */
    $scope.search = function () {
        _jiyin.dataPost('admin/banner_admin/get_page_info', 
            dataToURL({
                page: $scope.inputPage,
                page_size: 10,
                keyword: $scope.keyword
        }))
            .then(function(result){
                $scope.bannerList = result.data;
                $scope.totalPage = result.total_page;
            });
    }

    $("#filter").keydown(function (e) {
        if (e.keyCode == 13) {
            $scope.search();
        }
    });

    /**
     * 编辑
     */
    $scope.editBannerList = function (data) {
        $scope.title = '编辑数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-banner.html',
            tempCtrl : 'modalBannerCtrl',
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
        _jiyin.dataPost('admin/banner_admin/update', dataToURL(list))
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
            _jiyin.dataPost('admin/banner_admin/delete',dataToURL({ id: data.id}))
                .then(function(result){
                    _jiyin.msg('s', '删除成功');
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
