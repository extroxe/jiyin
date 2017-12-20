/**
 * Created by sailwish001 on 2017/1/5.
 */
angular.module('app')
    .controller('collectionCtrl', ['$scope', 'ajax', function($scope, ajax){
        $scope.collection_list = [];
        $scope.currentPage = 1;

        $(function(){
            $('.content').dropload({
                scrollArea : window,
                loadUpFn: function(me){
                    ajax.req('POST', 'favorite/get_favorite_by_page', {
                        page: 1,
                        page_size: 10
                    }, true)
                        .then(function (data) {
                            if(data.data){
                                $scope.list = data.data;
                                angular.forEach($scope.list, function (list) {
                                    $scope.collection_list.push(list);
                                });
                                if(data.data.length < 10){
                                    // 锁定
                                    me.lock();
                                    // 无数据
                                    me.noData();
                                }
                                setTimeout(function(){
                                    // 每次数据加载完，必须重置
                                    me.resetload();
                                },1000);
                            }else{
                                // 即使加载出错，也得重置
                                me.resetload();
                            }
                        });
                },
                loadDownFn : function(me){
                    ajax.req('POST', 'favorite/get_favorite_by_page', {
                        page: $scope.currentPage,
                        page_size: 10
                    }, true)
                        .then(function (data) {
                            if(data.data){
                                $scope.list = data.data;
                                angular.forEach($scope.list, function (list) {
                                    $scope.collection_list.push(list);
                                });
                                if(data.data.length < 10){
                                    // 锁定
                                    me.lock();
                                    // 无数据
                                    me.noData();
                                }
                                setTimeout(function(){
                                    // 每次数据加载完，必须重置
                                    me.resetload();
                                },1000);
                            }else{
                                // 即使加载出错，也得重置
                                me.resetload();
                            }
                        });
                    $scope.currentPage++;
                }
            });
        });

        $scope.delete = function (data) {
            var popConfirm = new Alert("确认删除此收藏商品吗?",{
                onClickOk: function(e){
                    e.hide();
                    ajax.req('POST' , 'favorite/delete_by_id', {
                        id: data.favorite_id
                    }).then(function(result){
                        if(result.success === true){
                            var popToast;
                            if(!popToast || popToast&&!popToast.toastBox){
                                popToast=new Toast('删除成功',{
                                    "onHid":function(e){
                                        e.destroy();
                                    }
                                });
                            }
                            ajax.req('POST' , 'favorite/get_favorite_by_page', {
                                page: 1,
                                page_size: 10
                            }).then(function(result){
                                if(result.success === true){
                                    $scope.collection_list = result.data;
                                    popToast.show();
                                }else{
                                    $scope.collection_list = [];
                                }
                            });
                        }
                    });
                },
                onClickCancel: function(e){
                    e.hide();
                }
            });
            popConfirm.show();

        };

        $scope.GoCommodityDetail = function (commodity_id, commodity_specification_id) {
            window.location.href = SITE_URL + 'weixin/index/commodity_detail/' + commodity_id + '/' + commodity_specification_id;
        }
    }]);