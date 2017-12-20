angular.module('app')
    .controller('receiptPrizeCtrl', ['$scope', 'ajax', function($scope, ajax){
        //初始化
        var singlePage = new Page({
            "onLoad":function(e){
            }
        });
        //弹窗提示函数
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

        //选择地址
        $scope.select_address = function(address_info){
            $scope.default_address = address_info;
            singlePage.close('#page_address');
        };

        //获取地址信息
        $scope.default_address = [];
        $scope.address_infos = [];
        ajax.req('POST', 'user/show_address')
            .then(function (data) {
                if(data.success){
                    $scope.address_infos = data.data;
                    for (var i = 0; i < data.data.length; i++){
                        if (data.data[i].default === '1'){
                            $scope.default_address = data.data[i];
                            break;
                        }
                    }
                }else{
                    var popConfirm=new Alert("您还没有填写地址信息，请到个人中心-收货地址添加地址信息",{
                        onClickOk:function(e){
                            window.location.href = SITE_URL+"weixin/user/center";
                        },onClickCancel:function(e){
                            $scope.back();
                            e.hide();
                        }
                    });
                    $('.alert-handler > a:nth-child(1)').text('自己去');
                    $('.alert-handler > a:nth-child(2)').text('直接去');
                    popConfirm.show();
                }
            });

        //获取商品信息
        $scope.$watch('commodity_id', function(nv, ov){
            ajax.req('POST', 'index/get_commodity_by_id', {commodity_id : nv})
                .then(function(response){
                    if (response){
                        $scope.commodity = response;
                    }
                });
        });

        //提交订单
        $scope.confirm_order = function(){
            var data = {
                address_id : $scope.default_address.id,
                commodity_id : $scope.commodity_id,
                is_indiana : $scope.is_indiana,
                id : $scope.id
            };
            ajax.req('POST', 'weixin/integral/add_prize_order', data)
                .then(function(response){
                    if (response.success){
                        $scope.show_popToast("订单提交成功");
                        window.location.href = SITE_URL + 'weixin/user/order_list';
                    }else {
                        $scope.show_popToast("订单提交失败");
                    }
                });
        }
    }]);
