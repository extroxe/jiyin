angular.module('app')
    .controller('myCityCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        $scope.page = 1;
        $scope.page_size = 8;
        $scope.post_infos = [];
        $scope.bar_page = 1;
        $scope.focus_person_page = 1;
        $scope.fans_page = 1;
        $scope.receive_page = 1;
        $scope.send_page = 1;

        $scope.$watch('focus_bars', function(nv, ov){
            if (nv && nv.length < 4){
                $('.focus-bar .expand-all').hide();
            }
        }, true);

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
        /*获取关注贴吧*/
        ajax.req('POST', 'post_bar/get_focus_post_bar/1/4')
            .then(function (data) {
                if (data.success){
                    $scope.focus_bars = data.data;
                    $scope.bar_page++;
                    if (data.total_page <= 1){
                        $('.focus-bar .expand-all').hide();
                    }
                }else{
                    $scope.focus_bars = null;
                }
            });
        /*获取推荐贴吧*/
        ajax.req('POST', 'post_bar/get_recommend_post_bar')
            .then(function (data) {
                if (data.success){
                    $scope.recommend_bars = data.data;
                }else{
                    $scope.show_popToast(data.msg);
                }
            });
        /*关注*/
        $scope.focus_bar = function ($event, recommend_bar) {
            ajax.req('POST', 'post_bar/focus_post_bar', {post_bar_id : recommend_bar.id})
                .then(function (data) {
                    if (data.success){
                        if ($scope.focus_bars == null){
                            var arr = new Array();
                            arr[0] = recommend_bar;
                            $scope.focus_bars = arr;
                        }else {
                            $scope.focus_bars.push(recommend_bar);
                        }
                        recommend_bar.is_focused = true;
                        $scope.show_popToast(data.msg);
                    }else{
                        $scope.show_popToast(data.msg);
                    }
                })
        };
        /*取消关注*/
        $scope.cancel_focus_bar = function ($event, recommend_bar) {
            ajax.req('POST', 'post_bar/cancel_focus_post_bar', {post_bar_id : recommend_bar.id})
                .then(function (data) {
                    if (data.success){
                        $scope.focus_bars.pop(recommend_bar);
                        if ($scope.focus_bars.length == 0){
                            $scope.focus_bars = null;
                        }
                        recommend_bar.is_focused = false;
                        $scope.show_popToast(data.msg);
                    }else{
                        $scope.show_popToast(data.msg);
                    }
                })
        };
        /*换一换*/
        $scope.change = function () {
            ajax.req('POST', 'post_bar/get_recommend_post_bar')
                .then(function (data) {
                    if (data.success){
                        $scope.recommend_bars = data.data;
                    }
                })
        };


        //查看贴吧
        $scope.post = function (post_bar_id, $event) {
            window.location.href = SITE_URL + 'weixin/user/post/' + post_bar_id;
        };

        //查看更多关注的帖吧
        $scope.expend_bar = function(){
            ajax.req('POST', 'post_bar/get_focus_post_bar/'+$scope.bar_page+'/4')
                .then(function (response) {
                    if (response.success){
                        angular.forEach(response.data, function(bar, index){
                            $scope.focus_bars.push(bar);
                        });
                        if (response.data.length < 4){
                            $('.focus-bar .expand-all').hide();
                        }
                        $scope.bar_page++;
                    }else{
                        $('.focus-bar .expand-all').hide();
                    }
                });
        };
        $scope.block = 'post';
        $scope.selectTab = function(block) {
            $scope.block = block;
        };
    }])
    .filter('timefilter', function () {
        return function (time) {
            return time.substring(0, 10);
        }
    });
