/**
 * Created by sailwish009 on 2017/1/20.
 */
angular.module('app')
    .controller('forumHealthCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        $scope.page = 1;
        $scope.page_size = 6;
        $scope.subArticle = []
        $scope.articleList = [];
        $scope.back = function () {
            history.go(-1);
        };

        $scope.loadData = function (me) {
            ajax.req('GET', 'article/paginate/' + $scope.page + '/' + $scope.page_size, true)
                .then(function (data) {
                    if(data.data){
                        $scope.subArticle = data.data;
                        angular.forEach($scope.subArticle, function (orderlist) {
                            $scope.articleList.push(orderlist);
                        });
                        if(data.data.length < 6 &&  data.data.length > 0){
                            // 锁定
                            me.lock();
                            setTimeout(function(){
                            },1000);
                            // 无数据
                            me.noData();
                        }else if(data.success == false){
                            // 无数据
                            me.noData();
                            // 锁定
                            me.lock();
                        }
                        setTimeout(function(){
                            // 每次数据加载完，必须重置
                            me.resetload();
                        },1000);
                    }else{
                        // 锁定
                        me.lock();
                        // 无数据
                        me.noData();
                        // 即使加载出错，也得重置
                        me.resetload();
                    }
                });
            $scope.page++;
        };

        $scope.init = function () {
            setTimeout(function(){
                // 每次数据加载完，必须重置
                $('.content').dropload({
                    scrollArea : window,
                    loadUpFn: function (me) {
                        $scope.loadData(me);
                    },
                    loadDownFn : function(me){
                        $scope.loadData(me);
                    } 
                });
            },1);
        };

        $scope.init();

        $scope.goToUrl = function (url, id) {
            window.location.href = SITE_URL + 'weixin/index/'+ url + '/' +id;
        }
    }])
