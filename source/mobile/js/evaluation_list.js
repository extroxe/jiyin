angular.module('app')
    .controller('evaluationListCtrl', ['$rootScope','$scope', 'ajax', function ($rootScope,$scope, ajax) {
        $scope.img_src = []; //图片路径
        $scope.flag = true;
        $scope.page = 1;
        $scope.url = window.location.href;
        $scope.specification_id = '';
        $scope.checkFlag = false;
        $scope.level_type = 0;
        $scope.current_pack_id = angular.copy($scope.url.substring($scope.url.lastIndexOf('/') + 1));
        $scope.star = [{num:1, star_src:SITE_URL + 'source/mobile/img/icon/collect.png'},
            {num:2, star_src:SITE_URL + 'source/mobile/img/icon/collect.png'},
            {num:3, star_src:SITE_URL + 'source/mobile/img/icon/collect.png'},
            {num:4, star_src:SITE_URL + 'source/mobile/img/icon/collect.png'},
            {num:5, star_src:SITE_URL + 'source/mobile/img/icon/collect.png'}
        ];
        /**
         * 获取商品id
         */
        $scope.commodity_id = '';
        $scope.$watch('commodity_id', function (nv, ov) {
            $scope.commodity_id = nv;
            if (nv) {
                $scope.order_id = nv;
                init_data($scope.level_type, $scope.specification_id)
                $scope.init_evaluation_nav($scope.commodity_id, '');
            }
        });

    //    只看当前商品

        $scope.preventEvaluation = function () {
            $scope.page = 1;
            $('.dropload-down').remove();
            $scope.commodity = [];
            if(!$scope.checkFlag){
                $scope.specification_id = angular.copy($scope.current_pack_id);
            }else{
                $scope.specification_id = '';
            }
            init_data($scope.level_type, $scope.specification_id);
            $scope.init_evaluation_nav($scope.commodity_id, $scope.specification_id);
        };

        $scope.commodity = [];
        $scope.getEvaluation = function (me,level_type, specification_id) {
            ajax.req('POST', 'commodity/evaluation_paginate/' + $scope.page + '/10/'+$scope.commodity_id+ '/' + level_type + '/' + specification_id)
                .then(function (data) {

                    if (data.success) {
                        $scope.flag =false;
                        angular.forEach(data.data, function (data,index) {
                            $scope.commodity.push(data);
                        });

                        angular.forEach($scope.commodity, function (commodity) {
                            if(commodity.id != null) {
                                $scope.score = parseInt(commodity.score);
                                // commodity.star = $scope.star.slice(0);
                                commodity.star = angular.copy($scope.star);
                                for(var i = 0; i < $scope.score; i++){
                                    commodity.star[i].star_src = SITE_URL + 'source/mobile/img/icon/collected.png'
                                }
                            }
                        });
                        $scope.adeg = data.praise_rate * 100 + '%';

                        setTimeout(function(){
                            // 每次数据加载完，必须重置
                            me.resetload();
                        },1000);
                    }else{
                        $scope.adeg = 0;
                        $scope.flag = false;
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
            $scope.page++;
        };

        //初始化评价导航
        $scope.init_evaluation_nav = function (commodity_id, commodity_specification_id){
            $.ajax({
                type : 'post',
                dataType: "json",
                url : SITE_URL+'commodity/evaluation_nav',
                data : {
                    commodity_id : commodity_id,
                    commodity_specification_id :commodity_specification_id
                },
                success : function(response){
                   $scope.$apply(function () {
                       $scope.all_evaluation = response.all_eva || 0;
                       $scope.good_evaluation = response.good_eva || 0;
                       $scope.mid_evaluation = response.mid_eva || 0;
                       $scope.bad_evaluation = response.bad_eva || 0;
                       $scope.rating_num = response.bad_eva || 0;
                       $('#rating_num').text(response.all_eva || 0);
                   })

                },
                error : function(error){

                }
            });
        };

        $scope.get_prevent_evaluate = function (level_flag) {
            $scope.commodity = [];
            $scope.page = 1;
            $('.dropload-down').remove();
            $scope.level_type = level_flag;
            init_data($scope.level_type, $scope.specification_id)
        };

        function init_data(level_type, specification_id) {
            $('.content').dropload({
                scrollArea : window,
                loadUpFn: function (me) {
                    $scope.getEvaluation(me,level_type, specification_id);
                },
                loadDownFn : function(me){
                    $scope.getEvaluation(me,level_type, specification_id);
                }
            });
        }


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
    }]);
/**
 * Created by sailwish009 on 2017/1/6.
 */
