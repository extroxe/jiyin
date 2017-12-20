/**
 * Created by sailwish009 on 2017/2/15.
 */
angular.module('app')
    .controller('viewPost',['$scope', 'ajax', function ($scope, ajax) {
        $scope.post_bar_info = [];
        $scope.post_detail = [];
        $scope.replay_lists = [];
        $scope.recommend_bar = [];
        $scope.page = 1;
        $scope.page_size = 2;
        $scope.flag = true;
        $scope.flag_next = true;

        $scope.url= window.location.href;
        $scope.post_bar_id = $scope.url.split('/')[$scope.url.split('/').length-2];

        var singlePage=new Page({
            "onLoad":function(e){
                var targetPageId;
            }
        });
        //popToast弹窗函数
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

        $scope.$watch('post_id', function(nv, ov){
            if(nv){
                $scope.post_id = nv;
                //获取帖子详情
                ajax.req('POST', 'post/get_post_by_id/' + $scope.post_id)
                    .then(function (response) {
                        if(response.success) {
                            $scope.post_detail = response.data;
                        }else {
                            $scope.show_popToast(response.msg);
                        }
                    });
                $scope.get_comment_replay($scope.page, $scope.post_id);

                //上一页
                $scope.prev_page = function () {
                    $scope.page--;
                    if($scope.page <= 1){
                        $scope.flag = true;
                    }else{
                        $scope.flag = false;
                    }
                    if($scope.page <1 ){
                        $scope.page = 1
                    }else{
                        $scope.get_comment_replay($scope.page, $scope.post_id);
                    }
                };
                //下一页
                $scope.next_page = function () {
                    if($scope.flag_next == false){
                        $scope.page++;
                        $scope.get_comment_replay($scope.page, $scope.post_id);
                    }
                };

                //定义exmobi返回
                $scope.back = function () {
                    history.go(-1);
                    $scope.get_comment_replay($scope.page, $scope.post_id);
                };

            }
        });
        //获取帖子详情
        $scope.get_post_detail = function (post_id) {
            ajax.req('POST', 'post/get_post_by_id/' + post_id)
                .then(function (response) {
                    if(response.success) {
                        $scope.post_detail = response.data;
                    }else {
                        $scope.show_popToast(response.msg);
                    }
                });
        };

       

         //根据贴吧id获取贴吧详情
        ajax.req('POST', 'post_bar/get_post_bar_by_id', {id: $scope.post_bar_id})
            .then(function (response) {
                if(response.success) {
                    $scope.post_bar_info = response.data;
                }
            });

         //获取评价和回复详情
        $scope.get_comment_replay = function(page, post_id){
            ajax.req('POST', 'post/paginate_comment/' + page + '/' + $scope.page_size, {post_id:post_id})
                .then(function (response) {
                    if(response.success) {
                        if(page == response.total_page){
                            $scope.flag_next = true;
                        }else{
                            $scope.flag_next = false;
                        }
                        if(page <= 1){
                            $scope.flag = true;
                        }else{
                            $scope.flag = false;
                        }
                        $scope.replay_lists = response.data;
                    }
                });
        };

        //收藏帖子
        $scope.colloect_post = function (post_id) {
            ajax.req('POST', 'post/collect_post', {post_id:post_id})
                .then(function (response) {
                    if(response.success){
                        $scope.info_hinter(response);
                        $scope.get_post_detail(post_id);
                    }else if(response.success == false && response.timeout == true){
                        $scope.show_popToast("您还未登录，请先登录");
                        setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                    }else{
                        $scope.info_hinter(response);
                    }
                })
        };

        //取消收藏
        $scope.cancel_colloect_post = function (post_id) {
            ajax.req('POST', 'post/cancel_collect_post', {post_id:post_id})
                .then(function (response) {
                    if(response.success){
                        $scope.info_hinter(response);
                        $scope.get_post_detail(post_id);
                    }else{
                        $scope.info_hinter(response);
                    }
                })
        };

          //获取推荐的帖子
        ajax.req('POST', 'post_bar/get_recommend_post_bar')
            .then(function (response) {
                if(response.success) {
                    $scope.recommend_bar = response.data;
                }
            });

        //查看贴吧
        $scope.post_bar = function () {
            window.location.href = SITE_URL + 'weixin/user/post/' + $scope.post_bar_id;
        };
        $scope.recommend_post_bar = function (post_bar_id) {
            window.location.href = SITE_URL + 'weixin/user/post/' + post_bar_id;
        };



        //查看吧友主页
        $scope.visit = function (user_id) {
            window.location.href = SITE_URL + 'weixin/user/visit/' + user_id;
        };
        //回我的城
        $scope.home = function () {
            window.location.href = SITE_URL + 'weixin/user/my_city';
        };

        $scope.page_to_publish = function (id, root_comment_id, comment_id, publisher_id, to_user_nickname) {
            var openType="";
            singlePage.open(id,openType);

            $scope.root_comment_id = root_comment_id;
            $scope.comment_id = comment_id;
            $scope.publisher_id = publisher_id;
            $scope.to_user_nickname = to_user_nickname
        };

        //发表评论
        $scope.publish_replay = function (post_id) {
            if($scope.root_comment_id == undefined){
                $scope.root_comment_id = ''
            }
            if($scope.comment_id == undefined){
                $scope.comment_id = ''
            }
            if($scope.publisher_id == undefined){
                $scope.publisher_id = ''
            }
            if($scope.content == undefined){
                $scope.show_popToast('请输入内容');
            }else{
                ajax.req('POST', 'post/add_comment',
                    {
                        post_id:post_id,
                        content:$scope.content,
                        root_comment_id:$scope.root_comment_id,
                        comment_id:$scope.comment_id,
                        to_user_id:$scope.publisher_id
                    })
                    .then(function (response) {
                        if(response.success) {
                            $scope.show_popToast('发表成功');
                            setTimeout(function(){
                                $scope.back();
                            },1000);
                        }else if(response.success == false && response.timeout == true){
                            $scope.show_popToast("您还未登录，请先登录");
                            setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                        }
                    });
                    $scope.content = '';
                }
        };

        $scope.info_hinter = function (response) {
            $scope.show_popToast(response.msg);
        }
    }]);