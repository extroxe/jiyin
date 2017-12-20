/**
 * Created by sailwish009 on 2017/2/15.
 */
angular.module('app')
    .controller('post',['$scope', 'ajax', function ($scope, ajax) {
        $scope.url = window.location.href;
        $scope.post_bar_id = $scope.url.substring($scope.url.lastIndexOf('/') + 1);
        $scope.page = 1;
        $scope.page_size = 4;
        $scope.post_bar_info = [];
        $scope.post_lists = [];

        var popToast;
        $scope.show_popToast = function (msg) {
            if(!popToast || popToast&&!popToast.toastBox){
                popToast=new Toast(msg,{
                    "onHid":function(e){
                        e.destroy();
                    }
                });
            }
            popToast.show();
        };
        /**
         * 根据贴吧id获取贴吧信息
         */
        $scope.get_post_bar = function () {
            ajax.req('POST', 'post_bar/get_post_bar_by_id', {id: $scope.post_bar_id})
                .then(function (response) {
                    if(response.success) {
                        $scope.post_bar_info = response.data;
                    }else {
                        $scope.show_popToast(response.msg);
                    }
                });
        };
        $scope.get_post_bar();
        /**
         * 根据贴吧id获取帖子列表
         */

        $scope.post_list = function (me) {
            ajax.req('POST', 'post/paginate/' + $scope.page + '/' + $scope.page_size, {post_bar_id: $scope.post_bar_id})
                .then(function (response) {
                    if(response.success) {
                        angular.forEach(response.data, function (post, index) {
                            if(post.publish_time == null){
                                post.publish_time = "未知时间";
                            }
                            $scope.post_lists.push(post);
                        });

                        setTimeout(function(){
                            // 每次数据加载完，必须重置
                            me.resetload();
                        },1000);
                    }else {
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


        $('.content').dropload({
            scrollArea : window,
            loadUpFn: function (me) {
                $scope.post_list(me);
            },
            loadDownFn : function(me){
                $scope.post_list(me);
            }
        });

        //关注贴吧
        $scope.add_focus = function () {
            ajax.req('POST', 'post_bar/focus_post_bar',{post_bar_id:$scope.post_bar_id})
                .then(function (response) {
                    if(response.success) {
                        $scope.show_popToast("关注成功");
                        $scope.get_post_bar();
                    }else if(response.success == false && response.timeout == true){
                        $scope.show_popToast("您还未登录，请先登录");
                        setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                    }else {
                        $scope.show_popToast(response.msg);
                    }
                });
        };

        //取消关注
        $scope.cancel_focus = function () {
            ajax.req('POST', 'post_bar/cancel_focus_post_bar', {post_bar_id: $scope.post_bar_id})
                .then(function (response) {
                    var popToast;
                    if(response.success) {
                        $scope.show_popToast("取消关注成功");
                        $scope.get_post_bar();
                    }else {
                        $scope.show_popToast(response.msg);
                    }
                });
        };
        /**
         * 查看帖子详情
         */
        $scope.view_post = function (post_bar_id, post_id) {
            window.location.href = SITE_URL + 'weixin/user/view_post/' + post_bar_id + '/' + post_id;
        };

        //返回主页
        $scope.home = function () {
            window.location.href = SITE_URL + 'weixin/user/my_city';
        }


        //定义exmobi返回
        $scope.back = function () {
            history.go(-1);
        };
    }]);