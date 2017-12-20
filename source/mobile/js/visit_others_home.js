angular.module('app')
    .controller('visitOthersHome', ['$scope', 'ajax', function ($scope, ajax) {
        $scope.url = window.location.href;
        $scope.page = 1;
        $scope.post_page = 1;
        $scope.focus_page = 1;
        $scope.fans_page = 1;
        $scope.user_info = [];
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

        $scope.$watch('user_id', function(nv, ov){
            if (nv){
                $scope.user_id = nv;
                //查看帖子列表
                ajax.req('POST', 'post_bar/get_user_post_info', {user_id: $scope.user_id})
                    .then(function (response) {
                        if(response.success) {
                            $scope.post_lists = response.data;
                            $scope.post_page++;
                        }
                    });

                //查看关注列表
                ajax.req('POST', 'post_bar/get_focus_user', {user_id: $scope.user_id})
                    .then(function (response) {
                        if(response.success) {
                            $scope.focus_lists = response.data;
                            $scope.focus_page++;
                            if (response.total_page == 1){
                                $('.focus-content .expand-all').hide();
                            }
                        }else {
                            $('.focus-content .expand-all').hide();
                        }
                    });

                //获取粉丝列表
                ajax.req('POST', 'post_bar/get_fans', {user_id: $scope.user_id})
                    .then(function (response) {
                        if(response.success) {
                            $scope.fans_lists = response.data;
                            $scope.fans_page++;
                            if (response.total_page == 1){
                                $('.fans-content .expand-all').hide();
                            }
                        }else {
                            $('.fans-content .expand-all').hide();
                        }
                    });
            }
        });

        /*上拉获取帖子*/
        $('.post-content').dropload({
            scrollArea : window,
            loadUpFn: function (me) {
                // $scope.page = 1;
            },
            loadDownFn : function(me){
                if ($scope.post_page != 1){
                    ajax.req('POST', 'post_bar/get_user_post_info/' + $scope.post_page + '/10', {user_id: $scope.user_id})
                        .then(function (response) {
                            if(response.success) {
                                angular.forEach(response.data, function (post, index) {
                                    $scope.post_lists.push(post);
                                });
                                $scope.post_page++;
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
                }else{
                    setTimeout(function(){
                        // 每次数据加载完，必须重置
                        me.resetload();
                    },1000);
                }
            }
        });

        //查看更多关注的人
        $scope.expend_focus_person = function(){
            ajax.req('POST', 'post_bar/get_focus_user/'+$scope.focus_page+'/6', {user_id: $scope.user_id})
                .then(function (response) {
                    if (response.success){
                        angular.forEach(response.data, function(person, index){
                            $scope.focus_lists.push(person);
                        });
                        if (response.data.length < 6 || $scope.focus_page == response.total_page){
                            $('.person .expand-all').hide();
                        }
                        $scope.focus_page++;
                    }else{
                        $('.person .expand-all').hide();
                    }
                });
        };
        //查看更多的粉丝
        $scope.expend_fans = function(){
            ajax.req('POST', 'post_bar/get_fans/'+$scope.focus_page+'/6', {user_id: $scope.user_id})
                .then(function (response) {
                    if (response.success){
                        angular.forEach(response.data, function(person, index){
                            $scope.fans_lists.push(person);
                        });
                        if (response.data.length < 6 || $scope.fans_page == response.total_page){
                            $('.fans-content .expand-all').hide();
                        }
                        $scope.fans_page++;
                    }else{
                        $('.fans-content .expand-all').hide();
                    }
                });
        };

        //关注、取消关注
        $scope.focus = function(event){
            if ($(event.target).text() == '关注'){
                ajax.req('POST', 'user/focus_user', {focus_id: $scope.user_id})
                    .then(function(response){
                        if (response.success){
                            $('.focus').addClass('focus-ed').removeClass('focus').find('a').text('已关注');
                        }else{
                            $scope.show_popToast('操作失败');
                        }
                    })
            }else{
                ajax.req('POST', 'user/cancel_focus_user', {focus_id: $scope.user_id})
                    .then(function(response){
                        if (response.success){
                            $('.focus-ed').addClass('focus').removeClass('focus-ed').find('a').text('关注');
                        }else{
                            $scope.show_popToast('操作失败');
                        }
                    })
            }
        };


        //查看贴吧
        /*$scope.post = function (post_bar_id) {
            window.location.href = SITE_URL + 'weixin/user/post/' + post_bar_id;
        };*/
        
        //查看帖子
        $scope.view_post = function (post_bar_id, post_id) {
            window.location.href = SITE_URL + 'weixin/user/view_post/' + post_bar_id + '/' + post_id;
        };
        
        //查看关注和粉丝列表
        $scope.view_friends = function (user_id) {
            window.location.href = SITE_URL + 'weixin/user/visit/' + user_id;
        };

        //返回主页
        $scope.home = function () {
            window.location.href = SITE_URL + 'weixin/user/my_city';
        };

        $scope.block = 'post';

        $scope.selectTab = function(block) {
            $scope.block = block;
        };

        window.addEventListener("load",function(e){
            [].slice.call(document.querySelectorAll('.tabbar')).forEach(function(tabbar){
                tabbar.onclick=function(e){
                    [].slice.call(tabbar.querySelectorAll('.tab')).forEach(function(tab) {
                        tab.classList.remove("active");
                    });
                    e.target.classList.add("active");
                }
            });
        },false);

    }])
    .filter('timefilter', function () {
        return function (time) {
            if (time == null || time == ''){
                return '未知时间';
            }else{
                return time.substring(0, 10);
            }
        }
    });