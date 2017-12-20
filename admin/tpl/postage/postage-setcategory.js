/**
 * Created by sailwish001 on 2017/09/15.
 */
app.controller('postageSetCtrl', ['$scope', '_jiyin', 'dataToURL', '$stateParams', '$state',
    function ($scope, _jiyin, dataToURL, $stateParams, $state) {
        $scope.categoryList = {};
        $scope.categoryTotalPage = 1;
        $scope.categoryInputPage = 1;

        $scope.commodityList = {};
        $scope.commodityTotalPage = 1;
        $scope.commodityInputPage = 1;

        console.log($stateParams);

        /**
         * 获取对应的分类
         */
        $scope.get_category = function(){
            _jiyin.dataGet('admin/postage_admin/get_category_by_postage_id/'+$stateParams.id)
                .then(function(result){
                    if(result.total_page == false){
                        $scope.categoryTotalPage = 1;
                    }else{
                        $scope.categoryTotalPage = result.total_page;
                    }
                    $scope.categoryList = result.data;
                });
        };
        $scope.get_category();

        /**
         * 获取对应的商品
         */
        $scope.get_commodity = function(){
            _jiyin.dataGet('admin/postage_admin/get_commodities_by_postage_id/'+$stateParams.id)
                .then(function(result){
                    if(result.total_page == false){
                        $scope.commodityTotalPage = 1;
                    }else{
                        $scope.commodityTotalPage = result.total_page;
                    }
                    $scope.commodityList = result.data;
                });
        };
        $scope.get_commodity();

        /**
         *
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
            console.log(dataToURL(list));
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
         * 下一页
         */
        $scope.nextPage = function()
        {

        };

        /**
         * 上一页
         */
        $scope.previousPage = function()
        {

        };
        /**
         * 第一页
         */
        $scope.firstPage = function () {

        };
        /**
         * 最后一页
         */
        $scope.lastPage = function () {

        };
        $scope.selectPage = function (page) {

        }
    }]);