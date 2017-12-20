angular.module('app')
    //定义过滤器，返回字符串的第一个字符
    .filter('namefilter', function () {
        return function (name) {
            var names = name.split("");
            return names[0];
        } 
    })
    .controller('categoryCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        $scope.first_active_flag = false;
        $scope.open = false;
        //获取一级分类
        $scope.categorys = [];
        $scope.all_categorys = [];
        // $scope.child_categorys = [];
        //获取所有分类
        ajax.req('get', 'weixin/index/get_category')
            .then(function (data) {
                    if (data.success){
                        $scope.all_categorys = data.data;
                        $scope.child_categorys = data.data[1];
                    }
                }
            );
        $scope.get_child = function (father_index, event) {
            // $scope.child_categorys = [];
            $(event.target).addClass('active').siblings().removeClass('active');
            angular.forEach($scope.all_categorys, function (data, index) {
                if(father_index == index){
                    $scope.child_categorys = data;
                }
            })
        };

        //二级分类商品
        $scope.gotoCommodityList = function (id) {
            window.location.href = $scope.SITE_URL + 'weixin/index/search_result?category=' + id;
                $('details ul, details hr').hide();
        };
        $scope.openChildCategory = function (e) {
            e.stopPropagation();
            $(e.target).parents('details').find('ul, hr').show();
            if($(event.target).hasClass("icon-arrowright")){
                $(event.target).removeClass('icon-arrowright').addClass("icon-arrowdown")
            }else{
                $(event.target).removeClass('icon-arrowdown').addClass("icon-arrowright")
            }
        };


        ajax.req('POST', 'category/get_father_category')
            .then(function (data) {
                    if (data.success){
                        $scope.categorys = data.data;
                        if ($scope.first_active_flag){
                            $scope.parent.id = $scope.categorys[0].id;
                        }
                    }
                }
            );
        //监视
       /* $scope.$watch('parent.id', function (nv, ov) {
            if (nv){
                ajax.req('GET', 'category/get_child_category_by_id/' + nv)
                    .then(function (data) {
                        if (data.success){
                            $scope.child_categorys = data.data;
                            $scope.first_active_flag = false;
                        }
                    });
            }else{
                $scope.first_active_flag = true;
            }
        });*/
        //根据一级分类id获取子类分类
        // $scope.child_categorys = [];
        /*$scope.get_child_category = function (category_id, $event) {
            if ($event) {
                $($event.target).addClass('active').siblings('.tab').removeClass('active');
            }
            ajax.req('GET', 'category/get_child_category_by_id/' + category_id)
                .then(function (data) {
                    if (data.success) {
                        $scope.child_categorys = data.data;
                    }else{
                        $scope.child_categorys = [];
                    }
                });
        };*/
        $scope.recommends = [];
        ajax.req('POST', 'commodity/get_recommend')
            .then(function (data) {
                if (data.success){
                    $scope.recommends = data.data;
                }
            });
        //分类搜索跳转
        $scope.search = function(id){
            ajax.req('POST', 'weixin/index/search', {
                category : id,
                page : 1,
                page_size : 10
            }).then(function(response){
                if (response.success){
                    $scope.commodities = response.data;
                    //window.location.href = SITE_URL + 'weixin/index/search';
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
        }
    }]);
$(function () {
    $('.tab').click(function () {
        $(this).addClass('active').siblings('.tab').removeClass('active');
        $(this).siblings('.tab').addClass('sibborder');
    })
});
