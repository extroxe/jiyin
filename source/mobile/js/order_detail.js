angular.module('app')
    .controller('orderDetailCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        $scope.order_id = 0;
        $scope.order = {};
        $scope.commodity_total_amount = 0;
        $scope.js_api_parameters = {};

        $scope.url = window.location.href;
        $scope.status_id = $scope.url.substring($scope.url.lastIndexOf('/') + 1);

        // 弹窗提示
        var popToast;
        $scope.pop = function (msg) {
            if(!popToast || popToast && !popToast.toastBox){
                popToast = new Toast(msg,{
                    "onHid":function(e){
                        e.destroy();
                    }
                });
            }
            popToast.show();
        }

        $scope.$watch('order_id', function (nv) {
            if (nv) {
                $scope.order_id = nv;
                ajax.req('POST', 'order/get_order_by_id', {id : nv}, true)
                    .then(function (data) {
                        if (data.success){
                            $scope.order = data.data;
                            for (var i = 0; i < data.data.sub_orders.length; i++){
                                $scope.commodity_total_amount += parseInt(data.data.sub_orders[i].amount);
                            }
                        }
                    });
            }else {
                window.location.href = SITE_URL + '/weixin/index/show_404';
            }
        });
        
        $scope.init_data = function () {
            ajax.req('POST', 'order/get_order_by_id', {id : $scope.order_id}, true)
                .then(function (data) {
                    if (data.success){
                        $scope.order = data.data;
                        for (var i = 0; i < data.data.sub_orders.length; i++){
                            $scope.commodity_total_amount += parseInt(data.data.sub_orders[i].amount);
                        }
                    }
                });
        }

        // 评价晒单按钮响应事件
        $scope.evaluate_order = function () {
            window.location.href = SITE_URL + 'weixin/user/evaluation/'+$scope.order_id;
        };

        //再次兑换
        $scope.exchange_again = function (commodity_id) {
            window.location.href = SITE_URL + 'weixin/index/commodity_detail/' + commodity_id;
        };

        /**
         * 查看商品详情
         */
        $scope.commodity_detail = function (commodity_id, commodity_specification_id) {
            window.location.href = SITE_URL + 'weixin/index/commodity_detail/'+ commodity_id + '/' + commodity_specification_id;
        };

        /**
         * 再次购买
         * @param sub_orders 子订单信息
         */
        var addData = [];
        $scope.add_shopping_cart = function (sub_orders) {
            for (var i = 0; i < sub_orders.length; i++) {
                addData[i] = {};
                addData[i].amount = sub_orders[i].amount;
                addData[i].commodity_id = sub_orders[i].commodity_id;
                addData[i].specification_id = sub_orders[i].commodity_specification_id;
            }

            ajax.req('post', 'shopping_cart/add_again', {data: JSON.stringify(addData)})
                .then(function (response) {
                    $scope.pop(response.msg);
                    if (response.success) {
                        window.location.href = SITE_URL + 'weixin/index/shopping_cart';
                    }
                })
        };

        /**
         * 取消订单
         * @param order_id
         */
        $scope.cancel_order = function (order_id) {
            $scope.confirm_info = '确定取消订单吗？';

            var popConfirm = new Alert($scope.confirm_info, {
                onClickOk: function(e){
                    ajax.req('post', 'order/cancel_order', {order_id: order_id})
                        .then(function (data) {
                            if (data.success){
                                angular.forEach($scope.orderAll, function (cart_item, index) {
                                    if(order_id == cart_item.id){
                                        $scope.orderAll.splice(index,1)
                                    }
                                });
                                $scope.init_data();
                            }
                            $scope.pop(data.msg);
                            e.hide();
                        });
                },
                onClickCancel: function () {
                    e.hide();
                }
            });
            popConfirm.show();
        };

        /**
         * 申请退款
         * @param order_id
         */
        $scope.refundCommodity = [];
        var singlePage = new Page();
        $scope.applyRefund = function (order_id, sub_order, e) {
            ajax.req('post', 'refund/get_suborder_refund_info', {order_commodity_id: sub_order.id})
                .then(function (response) {
                    if (response.data.is_refunding == 0 && parseInt(response.data.has_refunded) < parseInt(response.data.amount)) {
                        var commodity_name = response.data.commodity_name;
                        if (response.data.commodity_center_name != null) {
                            commodity_name = commodity_name + ' ' + response.data.commodity_center_name;
                        } else {
                            commodity_name = commodity_name + ' ' + response.data.commodity_specification_name;
                        }
                        commodity_name = commodity_name + ' ' + response.data.package_type_name;
                        $scope.refundCommodity.refundAvailable = response.data.refund_available;
                        $scope.refundCommodity.order_id = response.data.order_id;
                        $scope.refundCommodity.order_commodity_id = sub_order.id
                        $scope.refundCommodity.commodityName = commodity_name;
                        $scope.refundCommodity.commodityPath = sub_order.thumbnail_path;
                        $scope.refundCommodity.price = response.data.price;
                        e.stopPropagation();
                        singlePage.open('#page_2');
                        $('.confirm').prop('disabled', false);
                        $('.confirm').css('opacity', 1);
                    } else if (response.data.is_refunding > 0) {
                        $scope.pop('该商品正在退款，请勿重复申请退款');
                    } else if (parseInt(response.data.has_refunded) == parseInt(response.data.amount)) {
                        $scope.pop('该商品已全部退款，不能重复申请退款');
                    }
                });
        };
        //关闭选择
        $scope.closePage = function () {
            history.go(-1);
        };
        $scope.refundInfo = {};

        //申请退款确认按钮
        $scope.confirm = function () {
            if(!$scope.refundInfo.amount || $scope.refundInfo.amount == ''){
                $scope.pop('请填写退款数量');
                return;
            }
            if($scope.refundInfo.amount > $scope.refundCommodity.refundAvailable){
                $scope.pop('退款数量不能大于可退款数量');
                return;
            }
            if(!$scope.refundInfo.reason || $scope.refundInfo.reason == ''){
                $scope.pop('请选择退款理由');
                return;
            }
            $('.confirm').prop('disabled', true);
            $('.confirm').css('opacity', .5);

            ajax.req('post', 'refund/application_for_refund', {
                order_id: $scope.refundCommodity.order_id,
                order_commodity_id: $scope.refundCommodity.order_commodity_id,
                amount: $scope.refundInfo.amount,
                reason: $scope.refundInfo.reason
            }).then(function (result) {
                if(result.success){
                    $scope.pop('申请退款成功');
                    history.back();
                }else{
                    $scope.pop(result.msg);
                }
            })

        };

        // 立即支付按钮响应事件
        $scope.pay = function () {
            $scope.get_wechat_pay_js_api_parameters();
        };

        // 获取微信支付参数
        $scope.get_wechat_pay_js_api_parameters = function () {
            ajax.req('POST', 'order/get_wechat_pay_js_api_parameters', {order_id: $scope.order_id})
                .then(function (response) {
                    if(response.success) {
                        // 调起微信支付
                        $scope.js_api_parameters = response.js_api_parameters;
                        $scope.callpay();
                    }else {
                        $scope.pop(response.msg);
                    }
                });
        };

        // 调起微信支付
        $scope.js_api_call = function () {
            var js_params = JSON.parse($scope.js_api_parameters);
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                {
                    'appId':js_params.appId,
                    'timeStamp':js_params.timeStamp,
                    'nonceStr':js_params.nonceStr,
                    'package':js_params.package,
                    'signType':js_params.signType,
                    'paySign':js_params.paySign
                },
                function(res){
                    //WeixinJSBridge.log(res);
                    if(res.err_msg == "get_brand_wcpay_request:ok") {
                        // 跳转到订单详情页面
                        window.location.href = SITE_URL+"weixin/index/pay_status/"+$scope.order_id;
                    }else if (res.err_msg == "get_brand_wcpay_request:cancel") {
                        // 用户取消支付
                        $scope.pop('您已取消支付，请尽快完成订单支付');
                    }else {
                        // 支付失败
                        $scope.pop('系统繁忙，支付失败');
                        console.log(res);
                    }
                }
            );
        };
        $scope.callpay = function () {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                $scope.js_api_call();
            }
        };
        $scope.back = function () {
            var last_url = document.referrer.substring(document.referrer.lastIndexOf('/')).replace('/','');
            if(last_url == '/order_list' || angular.isNumber(parseInt(last_url))){
                window.location.href = SITE_URL + 'weixin/user/order_list/'+ $scope.status_id;
            }else{
                history.go(-1);
            }
        };

        var view={
            /*=========================
             Model
             ===========================*/
            initialize:function(){
                /*DOM*/
                this.textSp=document.getElementById("ID-Sp");

                /*Plugin*/
                this.scrollpicker={};
                this.scrollpicker.hasEvent=false;
                this.nums1=[
                    {'key':'下错单，重新下单','value':'下错单，重新下单'},
                    {'key':'收货人信息有误','value':'收货人信息有误'},
                    {'key':'送货时间太长','value':'送货时间太长'},
                    {'key':'其他原因','value':'其他原因'}];
                this.render();
                this._attach();
            },
            render:function(){
                this._initPlugin();
            },
            _initScrollPicker:function(){
                var self=this;
                this.scrollpicker=new Scrollpicker({
                    "parent":"article",
                    "onClickDone":function(e){
                        //获得全部选中项
                        $scope.refundInfo.reason = e.activeOptions[0].value;
                        //打印值
                        var activeText="";
                        e.activeOptions.forEach(function(n,i,a){
                            if(i==e.activeOptions.length-1){
                                activeText+=n["value"];
                            }else{
                                activeText+=n["value"]+"-";
                            }
                        });
                        self.textSp.value=activeText;
                        e.hide();
                    },
                    "onClickCancel":function(e){
                        e.updateSlots();
                        e.hide();
                    },
                });
            },
            _addScrollpickerData:function(){
                this.scrollpicker.addSlot(this.nums1,'','','d');//数据,样式,默认value，默认key
            },
            _initPlugin:function(){
                this._initScrollPicker();
                this._addScrollpickerData();
            },
            _attach:function(e){
                var self=this;
                if(!self.textSp.hasEvent){
                    this.textSp.addEventListener("click",function(e){
                        self._onClickTextSp(e);
                    },false);
                    self.textSp.hasEvent=true;
                }
            },
            _onClickTextSp:function(e){
                this.scrollpicker.show();
            }
        };

        view.initialize();
    }]);