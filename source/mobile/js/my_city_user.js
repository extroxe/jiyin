
angular.module('app')
    .controller('myCityCtrl', ['$scope', 'ajax', function ($scope, ajax) {
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

        $scope.page = 1;
        $scope.page_size = 8;
        $scope.bar_page = 1;
        $scope.focus_person_page = 1;
        $scope.fans_page = 1;
        $scope.receive_page = 1;
        $scope.send_page = 1;

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

        //获取我的帖子
        $scope.get_my_post = function(){
            ajax.req('POST', 'my_city/get_my_post/' + $scope.page + '/' + $scope.page_size)
                .then(function (response) {
                    if(response.success) {
                        $scope.post_infos = response.data;
                    }
                });
        };

        /*获取关注贴吧*/
        $scope.get_focus_bar = function(){
            ajax.req('POST', 'post_bar/get_focus_post_bar/1/4')
                .then(function (data) {
                    if (data.success){
                        $scope.focus_bars = data.data;
                        $scope.bar_page++;
                        if (data.total_page <= 1){
                            $('.focus-content .expand-all').hide();
                        }
                    }else{
                        $scope.focus_bars = null;
                    }
                });
        };

        /*获取帖子*/
        $('.post-content').dropload({
            scrollArea : window,
            loadDownFn : function(me){
                $scope.page++;
                ajax.req('POST', 'my_city/get_my_post/' + $scope.page + '/' + $scope.page_size)
                    .then(function (response) {
                        if(response.success) {
                            angular.forEach(response.data, function (post, index) {
                                if(post.publish_time == null || post.publish_time == ''){
                                    post.publish_time = "未知时间";
                                }
                                $scope.post_infos.push(post);
                            });
                            if (response.total_page < $scope.page){
                                // 锁定
                                me.lock();
                                // 无数据
                                me.noData();
                                // 即使加载出错，也得重置
                                me.resetload();
                            }else{
                                setTimeout(function(){
                                    // 每次数据加载完，必须重置
                                    me.resetload();
                                },1000);
                            }
                        }else {
                            // 锁定
                            me.lock();
                            // 无数据
                            me.noData();
                            // 即使加载出错，也得重置
                            me.resetload();
                        }
                    });
            }
        });

        /*获取关注用户*/
        $scope.get_focus_user = function(){
            ajax.req('POST', 'user/paginate_focus_user/1/6')
                .then(function (data) {
                    if (data.success){
                        $scope.focus_infos = data.data;
                        $scope.focus_person_page++;
                        if (data.total_page == 1){
                            $('.person .expand-all').hide();
                        }
                    }
                });
        };

        /*获取粉丝*/
        $scope.get_my_fans = function(){
            ajax.req('POST', 'user/paginate_fans/1/6')
                .then(function (data) {
                    if (data.success){
                        $scope.fans_infos = data.data;
                        $scope.fans_page++;
                        if (data.total_page == 1){
                            $('.fans-content .expand-all').hide();
                        }
                    }
                });
        };

        /*获取收藏*/
        $scope.get_my_collection = function(){
            ajax.req('POST', 'post/paginate_collect_post/1/10')
                .then(function (data) {
                    if (data.success){
                        $scope.collect_infos = data.data;
                        $scope.collect_page++;
                        if (data.total_page == 1){
                            $('.collect-content .expand-all').hide();
                        }
                    }
                });
        };

        /*获取站内信发发信*/
        $scope.get_send_message = function(){
            ajax.req('POST', 'user/paginate_send_message/1/5')
                .then(function (data) {
                    if (data.success){
                        $scope.send_message_infos = data.data;
                        $scope.send_page++;
                        if (data.total_page == 1){
                            $('.send .expand-all').hide();
                        }
                    }
                });
        };

        /*获取站内信收信*/
        $scope.get_receive_message = function(){
            ajax.req('POST', 'user/paginate_receive_message/1/5')
                .then(function (data) {
                    if (data.success){
                        $scope.receive_message_infos = data.data;
                        $scope.receive_page++;
                        if (data.total_page == 1){
                            $('.receive .expand-all').hide();
                        }
                    }
                });
        };

        /*查看信息*/
        $scope.readMsg = function(id){
            window.location.href = SITE_URL + 'weixin/user/read_message/' + id;
        };
        //查看帖子
        $scope.view_post = function(bar_id, id) {
            window.location.href = SITE_URL + 'weixin/user/view_post/' + bar_id + '/' + id;
        };
        //查看贴吧
        $scope.post = function (post_bar_id) {
            window.location.href = SITE_URL + 'weixin/user/post/' + post_bar_id;
        };
        //查看其它用户主页
        $scope.visit = function(user_id){
            window.location.href = SITE_URL + 'weixin/user/visit/' + user_id;
        };
        //查看更多关注的帖吧
        $scope.expend_focus_bar = function(){
            ajax.req('POST', 'post_bar/get_focus_post_bar/'+$scope.bar_page+'/4')
                .then(function (response) {
                    if (response.success){
                        angular.forEach(response.data, function(bar, index){
                            $scope.focus_bars.push(bar);
                        });
                        if (response.data.length < 4 || $scope.bar_page == response.total_page){
                            $('.focus-content .expand-all').hide();
                        }
                        $scope.bar_page++;
                    }else{
                        $('.focus-content .expand-all').hide();
                    }
                });
        };
        //查看更多关注的人
        $scope.expend_focus_person = function(){
            ajax.req('POST', 'user/paginate_focus_user/'+$scope.bar_page+'/6')
                .then(function (response) {
                    if (response.success){
                        angular.forEach(response.data, function(person, index){
                            $scope.focus_infos.push(person);
                        });
                        if (response.data.length < 6 || $scope.focus_person_page == response.total_page){
                            $('.person .expand-all').hide();
                        }
                        $scope.focus_person_page++;
                    }else{
                        $('.person .expand-all').hide();
                    }
                });
        };
        //查看更多的粉丝
        $scope.expend_fans = function(){
            ajax.req('POST', 'user/paginate_fans/'+$scope.fans_page+'/6')
                .then(function (response) {
                    if (response.success){
                        angular.forEach(response.data, function(person, index){
                            $scope.fans_infos.push(person);
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
        //查看更多的收藏
        $scope.expend_collect = function(){
            ajax.req('POST', 'post/paginate_collect_post/'+$scope.collect_page+'/10')
                .then(function (response) {
                    if (response.success){
                        angular.forEach(response.data, function(collect, index){
                            $scope.collect_infos.push(collect);
                        });
                        if (response.data.length < 10 || $scope.collect_page == response.total_page){
                            $('.collect-content .expand-all').hide();
                        }
                        $scope.collect_page++;
                    }else{
                        $('.collect-content .expand-all').hide();
                    }
                });
        };
        //查看更多收件箱
        $scope.expend_receive = function(){
            ajax.req('POST', 'user/paginate_receive_message/'+$scope.receive_page+'/5')
                .then(function (response) {
                    if (response.success){
                        angular.forEach(response.data, function(receive, index){
                            $scope.receive_message_infos.push(receive);
                        });
                        if (response.data.length < 5 || $scope.receive_page == response.total_page){
                            $('.receive .expand-all').hide();
                        }
                        $scope.receive_page++;
                    }else{
                        $('.receive .expand-all').hide();
                    }
                });
        };
        //查看更多发件箱
        $scope.expend_send = function(){
            ajax.req('POST', 'user/paginate_send_message/'+$scope.send_page+'/5')
                .then(function (response) {
                    if (response.success){
                        angular.forEach(response.data, function(send, index){
                            $scope.send_message_infos.push(send);
                        });
                        if (response.data.length < 5 || $scope.send_page == response.total_page){
                            $('.send .expand-all').hide();
                        }
                        $scope.send_page++;
                    }else{
                        $('.send .expand-all').hide();
                    }
                });
        };

        $scope.block = 'post';
        $scope.selectTab = function(block) {
            $scope.block = block;
        };

        //刷新数据
        $scope.$watch('block', function(nv, ov){
            switch (nv){
                case 'post':
                    $scope.get_my_post();
                    break;
                case 'focus':
                    $scope.get_focus_bar();
                    $scope.get_focus_user();
                    break;
                case 'fans':
                    $scope.get_my_fans();
                    break;
                case 'collect':
                    $scope.get_my_collection();
                    break;
                case 'message':
                    $scope.get_send_message();
                    $scope.get_receive_message();
                    break;
            }
        });
    }])
    .filter('timefilter', function () {
        return function (time) {
            return time.substring(0, 10);
        }
    });
