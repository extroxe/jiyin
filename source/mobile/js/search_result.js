angular.module('app')
    .controller('searchResultCtrl', ['$scope', 'ajax', function($scope, ajax){
        $scope.page = 1;
        $scope.url = window.location.href;
        $scope.key_words = $scope.url.substring($scope.url.lastIndexOf('=') + 1);
        /*$scope.$watch('searchfor.flag', function (nv) {
            if (nv == 'key_words'){
                $scope.words = true;
                $scope.search_data = {
                    key_words : $scope.searchfor.key_words,
                    page : 1,
                    page_size : 3
                };
            }else if (nv == 'category'){
                $scope.words = false;
                $scope.search_data = {
                    category : $scope.searchfor.category,
                    page : 1,
                    page_size : 3
                };
            }
            ajax.req('POST', 'weixin/index/search', $scope.search_data).then(function(response){
                if (response.success){
                    $scope.commodities = response.data;
                }else{
                    var popToast;
                    if(!popToast || popToast&&!popToast.toastBox){
                        popToast=new Toast("没有此类商品",{
                            "onHid":function(e){
                                e.destroy();
                            }
                        });
                    }
                    popToast.show();
                }
            })
        });*/
        /*获取用户类型*/
        $scope.getUserType = function () {
            ajax.req('GET', 'user/get_personal_info')
                .then(function(response){
                    if(response.success){
                        $scope.userType = response.data.uid;
                    }
                });
        };
        $scope.getUserType();
        $scope.commodities = [];

        $scope.search = function(me){
            if(angular.isNumber(parseInt($scope.key_words)) && !isNaN(parseInt($scope.key_words))){
                $scope.data = {
                    category : $scope.key_words,
                    page : $scope.page,
                    page_size : 5
                }
            }else{
                $scope.data = {
                    key_words : $scope.key_words,
                    page : $scope.page,
                    page_size : 5
                }
            }
            ajax.req('POST', 'weixin/index/search', $scope.data).then(function(response){
                if (response.success && $scope.page <= response.total_page){
                    angular.forEach(response.data, function (data) {
                        $scope.commodities.push(data);
                    });

                    $scope.page++;
                    setTimeout(function(){
                        // 每次数据加载完，必须重置
                        me.resetload();
                    },1000);
                }else{
                    var popToast;
                    if(!popToast || popToast&&!popToast.toastBox){
                        popToast=new Toast("暂无更多数据",{
                            "onHid":function(e){
                                e.destroy();
                            }
                        });
                    }
                    popToast.show();
                    // 锁定
                    me.lock();
                    // 无数据
                    me.noData();
                    // 即使加载出错，也得重置
                    me.resetload();
                }
            });
        };

        $('.content').dropload({
            scrollArea : window,
            loadUpFn: function (me) {
                $scope.search(me);
            },
            loadDownFn : function(me){
                $scope.search(me);
            }
        });
        
        // 打开商品详情页面
        $scope.open_commodity = function (commodity_id, specification_id) {
            window.location.href = SITE_URL + 'weixin/index/commodity_detail/' + commodity_id + '/' + specification_id;
        };

        $('#empty_content').click(function () {
            $("#search_box").val("");
        });


    }]);
