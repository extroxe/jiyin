/**
 * Created by sailwish009 on 2016/12/30.
 */
angular.module('app')
    .controller('orderList', ['$rootScope','$scope', 'ajax', function ($rootScope,$scope, ajax) {
        $scope.orderAll = [];
        $scope.subOrder = [];
        $scope.orderStatus = [];
        $scope.orderAll = [];
        $scope.subOrder = [];
        $scope.status_id = '';
        $scope.ajax_url = 'order/get_order_by_page/';
        // 微信支付JSAPI参数
        $scope.js_api_parameters = {};

        $scope.url = window.location.href;

        //页面初始化时，status_id等于order_list，这是默认获取所有订单
        $scope.status_id = $scope.url.substring($scope.url.lastIndexOf('/') + 1);
        if ($scope.status_id == 'order_list') {
            $scope.status_id = 0;
        }
        if($scope.status_id == '90'){
            $scope.status_id = '';
            $scope.ajax_url = 'order/get_can_evaluate_order_by_page/';
        }
        $scope.not_paid = false;
        $scope.paid = false;
        $scope.delivered = false;
        $scope.sentback = false;
        $scope.assaying = false;
        $scope.finished = false;
        $scope.refunding = false;
        $scope.refunded = false;
        $scope.can_evaluate = false;
        if($scope.status_id == '0'){
            document.getElementById('all').scrollIntoView();
            $scope.all = true;
        }else if($scope.status_id == '10'){
            document.getElementById('not_paid').scrollIntoView();
            $scope.not_paid = true;
        }
        else if($scope.status_id == '20'){
            document.getElementById('delivered').scrollIntoView();
            $scope.paid = true;
        }
        else if($scope.status_id == '30'){
            document.getElementById('sentback').scrollIntoView();
            $scope.delivered = true;
        }
        else if($scope.status_id == '40'){
            document.getElementById('assaying').scrollIntoView();
            $scope.sentback = true;
        }
        else if($scope.status_id == '50'){
            document.getElementById('finished').scrollIntoView();
            $scope.assaying = true;
        }
        else if($scope.status_id == '60'){
            document.getElementById('refunded').scrollIntoView();
            $scope.finished = true;
        }
        else if($scope.status_id == '70'){
            document.getElementById('can_evaluate').scrollIntoView();
            $scope.refunding = true;
        }else if($scope.status_id == '80-90'){
            document.getElementById('can_evaluate').scrollIntoView();
            $scope.refunded = true;
        }else{
            document.getElementById('can_evaluate').scrollIntoView();
            $('#can_evaluate1').css('margin-right','100px');
            $scope.can_evaluate = true;
        }

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

        /**
         * 加载数据
         * @param status_id
         */
        $scope.loadData = function (url, status_id, me) {
            ajax.req('POST', url + status_id, {
                page: $scope.page,
                page_size: 10
            }, true)
                .then(function (data) {
                    if(data.data){
                        $scope.commodity_total_num = 0; //子订单商品总计
                        $scope.total_price = 0; //子订单商品总价

                        $scope.subOrder = data.data;
                        angular.forEach($scope.subOrder, function (data, index, array) {
                            array[index]['total_amount'] = 0;
                            angular.forEach(data['sub_orders'], function (sub_data, sub_index, sub_arrray) {
                                array[index]['total_amount'] += parseInt(sub_data['amount']);
                                if(data.payment_id == '4'){
                                    data.total_price = parseInt(sub_data['price']);
                                }
                            })
                        });
                        
                        angular.forEach($scope.subOrder, function (orderlist) {
                            if(orderlist.sub_orders != null){
                                $scope.orderAll.push(orderlist);
                            }
                        });
                        if(data.data.length < 10 &&  data.data.length > 0){
                            // 锁定
                            me.lock();

                            setTimeout(function(){
                                $('.dropload-down').css('display', 'none');
                                $('.load_done').css('display', 'block');
                            },1000);
                            // 无数据
                            me.noData();
                        }else if(data.success == false){
                            $('.dropload-down').css('display', 'block');
                            $('.load_done').css('display', 'none');
                            // 无数据
                            me.noData();
                            // 锁定
                            me.lock();
                        }
                        setTimeout(function(){
                            // 每次数据加载完，必须重置
                            me.resetload();
                        },1000);
                    }else{
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

        /**
         * 下拉刷新
         */
        $scope.page =  1;
        $scope.init = function (url,status_id) {
            setTimeout(function(){
                // 每次数据加载完，必须重置
                $('.content').dropload({
                    scrollArea : window,
                    loadUpFn: function (me) {
                        if(url == 'order/get_order_by_page/'){
                            $scope.loadData('order/get_order_by_page/', status_id, me);
                        }else{
                            $scope.loadData('order/get_can_evaluate_order_by_page/', status_id, me);
                        }

                    },
                    loadDownFn : function(me){
                        if(url == 'order/get_order_by_page/'){
                            $scope.loadData('order/get_order_by_page/', status_id, me);
                        }else{
                            $scope.loadData('order/get_can_evaluate_order_by_page/', status_id, me);
                        }
                    }
                });
            },1);

        };

        $scope.init($scope.ajax_url, $scope.status_id);

        /**
         * 根据订单状态显示对应订单数量
         */
        $scope.get_nav_num = function () {
            ajax.req('POST', 'order/get_order_list_nav')
                .then(function (response) {
                    if(response){
                        $scope.orderStatus = response;
                    }
                });
        };
        $scope.get_nav_num();

        $scope.get_order_list = function (status) {
            $scope.orderAll = [];
            $scope.subOrder = [];
            $('.dropload-down').remove();
            $('.load_done').css('display', 'none');
            $scope.page = 1;
            switch (status){
                case 'all':
                    $scope.status_id = '0';
                    break;
                case 'not_paid':
                    $scope.status_id = 10;
                    break;
                case 'paid':
                    $scope.status_id = 20;
                    break;
                case 'delivered':
                    $scope.status_id = 30;
                    break;
                case 'sentback':
                    $scope.status_id = 40;
                    break;
                case 'assaying':
                    $scope.status_id = 50;
                    break;
                case 'finished':
                    $scope.status_id = 60;
                    break;
                case 'refunding':
                    $scope.status_id = 70;
                    break;
                case 'refunded':
                    $scope.status_id = '80-90';
                    break;
                case 'can_evaluate':
                    $scope.status_id = '';
                    break;
            }

            if(status == 'can_evaluate'){
                $scope.init('order/get_can_evaluate_order_by_page/', $scope.status_id);
                $scope.status_id = 90;
            }else{
                $scope.init('order/get_order_by_page/', $scope.status_id);
            }
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
         * 再次兑换
         * @param commodity_id
         */
        $scope.exchange_again = function(commodity_id) {
            window.location.href = SITE_URL + 'weixin/index/commodity_detail/' + commodity_id;
        };

        /**
         * 去付款
         */
        $scope.ids = [];
        $scope.pay = function (order_id) {
            $scope.order_detail(order_id);
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
                                $scope.get_nav_num();
                                $scope.get_order_list('all');
                            }
                            $scope.pop(data.msg);
                            e.hide();
                        });
                },
                onClickCancel: function (e) {
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
                        $scope.get_order_list('paid');
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
                        $scope.get_order_list('paid');
                        $scope.pop('申请退款成功');
                        history.back();
                    }else{
                        $scope.pop(result.msg);
                    }
                })

        };

        //定义exmobi返回
        $scope.back = function () {
            history.go(-1);
        };

        /**
         * 导航
         */
        [].slice.call(document.querySelectorAll('.tabbar')).forEach(function(tabbar){
            tabbar.onclick=function(e){
                [].slice.call(tabbar.querySelectorAll('.tab')).forEach(function(tab) {
                    tab.classList.remove("active");
                });
                e.target.classList.add("active");
            }
        });

        /**
         * 查看商品详情
         */
        $scope.commodity_detail = function (commodity_id, commodity_specification_id) {
            window.location.href = SITE_URL + 'weixin/index/commodity_detail/'+ commodity_id + '/' + commodity_specification_id + '/' + $scope.status_id;
        };

        /**
         * 查看订单详情
         */
        $scope.order_detail = function(order_id){
            window.location.href = SITE_URL + 'weixin/index/order_detail/'+ order_id + '/' + $scope.status_id;
        };

        /**
         * 查看物流信息
         */
        $scope.check_logistics = function(order_id){
            window.location.href = SITE_URL + 'weixin/index/logistics_details/'+ order_id;
        };

        /**
         * 评价晒单
         */
        $scope.evaluate_order = function (orderlist_id) {
            window.location.href = SITE_URL + 'weixin/user/evaluation/'+orderlist_id + '/' + $scope.status_id;
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
                        console.log(e.activeOptions);
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
