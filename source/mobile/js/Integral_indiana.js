angular.module('app')
    .controller('IntegralIndianaCtrl', ['$scope','$interval', 'ajax', function ($scope,$interval, ajax) {
        $scope.awards = [];
        $scope.activity_infos = [];

        // 弹窗提示
        var popToast;
        $scope.pop = function (msg) {
            if (!popToast || popToast && !popToast.toastBox) {
                popToast = new Toast(msg, {
                    "onHid": function (e) {
                        e.destroy();
                    }
                });
            }
            popToast.show();
        };

        //我的夺宝
        ajax.req('get', 'integral_indiana/my_indiana')
            .then(function (response) {
                if(response.success){
                    $scope.my_prizes = response.data;
                }else{
                    $scope.my_prizes = [];
                }
            });

        //获取夺宝规则
        ajax.req('POST', 'system_setting/get_indiana_rules')
            .then(function (response) {
                if (response.success){
                    $scope.indiana_rules = response.data.value;
                } else {
                    $scope.indiana_rules = '';
                }
            });

        //获奖结果
        ajax.req('get', 'integral_indiana/get_result_info')
            .then(function (response) {
                if (response.success) {
                    $scope.awards = response.data;
                } else {
                    $scope.awards = [];
                }
            });

        //夺宝活动信息
        $scope.get_activity_info = function () {
            ajax.req('get', 'integral_indiana/get_all_indiana_info')
                .then(function (response) {
                    if(response.success){
                        $scope.activity_infos = response.data;
                    }else{
                        $scope.activity_infos = [];
                        $scope.pop(response.msg);
                    }
                });
        };
        $scope.get_activity_info();

        //参与夺宝
        $scope.indiana = function (id, point) {
            var txt = '本次活动需' + point + '积分，确定参与吗？';
            var popConfirm = new Alert(txt,{
                onClickOk: function(e){
                    ajax.req('post', 'integral_indiana/join_integral_indiana',
                        {
                            id: id,
                            bet_num: 1
                        }).then(function (response) {
                            if (response.success) {
                                $scope.pop(response.msg);
                                $scope.get_activity_info();
                            } else {
                                $scope.pop(response.msg);
                            }
                            e.hide();
                        });
                },
                onClickCancel: function(e){
                    e.hide();
                }
            });
            popConfirm.show();
        };
        
        //去领奖
        $scope.accept_prize = function (sweepstakes_commodity_id, insert_id) {
            window.location.href = SITE_URL + 'weixin/integral/receive_prize/' + sweepstakes_commodity_id + '/' + insert_id + '/1';
        };
        
        //查看商品详情
        $scope.indiana_detail = function (specification_id) {
            window.location.href = SITE_URL + 'weixin/integral/indiana_detail/' + specification_id;
        };

        //查询夺宝结果
        var singlePage=new Page({
            "onLoad": function (e) {
                var targetPageId;
            }
        });
        $scope.openPage = function(id){
            $('.home-page').hide();
            singlePage.open(id);
        };

        $scope.toPage = function () {
            window.location.href = SITE_URL + 'weixin/index';
        };
        
        setInterval(function(){
                $("#oDiv").find("#oUl").animate({
                    marginTop : "-25px"
                },500,function(){
                    $(this).css({marginTop : "0px"}).find("li:first").appendTo(this);
                });
            },3000);

        $scope.back = function () {
            history.go(-1);
            $('.home-page').show();
        }

        //回顶部
        $(".back-top").css("display","none");
        $(window).scroll(function(){
            var sc=$('html').offset().top - $(window).scrollTop() + 300;
            if(sc < 0){
                $(".back-top").css("display","block");
            }else{
                $(".back-top").css("display","none");
            }
        });
        $scope.back_top = function(){
            $('body,html').animate({scrollTop:0},500);
        };
    }]);