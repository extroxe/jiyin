angular.module('app')
    .controller('confirmOrderCtrl', ['$rootScope', '$scope', 'ajax', '$filter', function ($rootScope, $scope, ajax, $filter) {
        //初始化
        var singlePage = new Page({
            "onLoad": function (e) {
                if (e.isRoot) {

                } else {

                }
            }
        });

        // $scope.url = window.location.href;
        // $scope.ids = $scope.url.substring($scope.url.lastIndexOf('/') + 1);

        var prompt=new Prompt();
        $scope.show_promt = function (msg) {
            prompt.setText(msg);
            prompt.show();
        };

        $scope.discount = [];
        $scope.total_price = 0;
        $scope.settlement = [];
        // 微信支付JSAPI参数
        $scope.js_api_parameters = {};
        // 下单成功之后的订单ID
        $scope.order_id = 0;
        $scope.total_price = 0;
        $scope.postage = 0; //邮费
        $scope.agent_flag = false;
        $scope.postage_flag = false;


        //监听购物车IDS，获取数据
        $scope.$watch('ids', function (nv, ov) {
            var url = '';
            if ($scope.is_point == 1) {
                url = 'weixin/index/get_point_commodity';
            } else {
                url = 'weixin/index/get_order_settlement';
            }
            ajax.req('POST', url, {ids: nv})
                .then(function (response) {
                    if (response.success) {
                        if (response.postage && (response.postage > 0)) {
                            $scope.postage_flag = true;
                            $scope.postage = response.postage;
                        }
                        if (response.agent_id && response.agent_id > 0) {
                            $scope.agent_flag = true;
                        }
                        $scope.settlement = response.data;
                        $scope.total_price = $filter('sum_price')($scope.settlement);
                    } else {
                        window.location.href = SITE_URL + 'weixin/user/order_list';
                    }
                });
        });

        //获取地址信息
        $scope.default_address = [];
        $scope.address_infos = [];
        $scope.get_address = function () {
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

                        //获取邮费
                        $scope.get_postage($scope.default_address.id);
                    }else{
                        var popConfirm=new Alert("您还没有填写地址信息，请到个人中心-收货地址添加地址信息",{
                            onClickOk:function(e){
                                window.location.href = SITE_URL+"weixin/user/receipt_address";
                            },onClickCancel:function(e){
                                $scope.back()
                                e.hide();
                            }
                        });
                        $('.alert-handler > a:nth-child(1)').text('自己去');
                        $('.alert-handler > a:nth-child(2)').text('直接去');
                        popConfirm.show();
                    }
                });
        }
        $scope.get_address();

        //显示地址
        $scope.show_address = function (id) {
            $('.item-footer').css('display','block');
            $('#order_header').hide();
            singlePage.open(id);
        };

        //选择地址
        $scope.selected_address = false;
        $scope.select_address = function(address_info, event) {
            $('.item-footer').css('display', 'none');
            $('.sel_address').css('color', '#444');
            $('.input-radio').prop('checked', false);
            if ($(event.target)[0].className == 'item') {
                $(event.target).siblings('.item').find('.sel_address').css('color', '#117d94');
                $(event.target).siblings('.item').find('.input-radio').prop('checked', true);
            } else {
                $(event.target).parent().next().css('color', '#117d94');
                $(event.target).prop('checked', true);
            }
            $scope.default_address = address_info;

            //获取邮费
            $scope.get_postage($scope.default_address.id);
            singlePage.close('#page_address');
            $('#order_header').show();
        };

        $scope.closePage = function (id) {
            singlePage.close(id);
            if (id == '#page_address') {
                $('.item-footer').hide();
                $('#order_header').show();
            }
            if(id == '#page_modify'){
                $('.item-footer').show();
            }

        };

        //选择设置为默认地址
        $scope.select_default = function($event){
            if ($($event.target).siblings('input[name=switch_default]').val() == 'ok'){
                $scope.address_row.default = 1;
            }else{
                $scope.address_row.default = 0;
            }
        };

        //获取邮费
        $scope.get_postage = function (address_id) {
            ajax.req('POST', 'order/get_postage_by_order', {
                address_id: address_id,
                shopping_cart_ids: $scope.ids,
                terminal_type: 2
            })
                .then(function (response) {
                    if (response.success) {
                        $scope.freight = response.data;
                    } else {
                        $scope.show_popToast(response.msg);
                    }
                })
        };


        //初始化switch
        var switch_view = {
            _initFormControls:function(){
                this.formControls=new Formcontrols();
            },
            _initPlugin:function(){
                this._initFormControls();
            },
            _attach:function(e){
                var self=this;
            },
            _onLoad:function(){
                var self=this;
                this._initPlugin();
                this._attach();
            }
        };

        switch_view._onLoad();

        //添加地址
        $scope.add = function(id, target){
            var openType = target || "";
            var province = $('#province');
            var city = $('#city').find("option[class='city']");
            var district = $('#district').find("option[class='district']");
            singlePage.open(id, openType);

            $scope.type = 'add';
            $scope.address_row = [];
            province.val('');
            if(city.length == 0){
                $('#city').find("option[value='']").prop("selected",true)
            }else{
                $('#city').find("option[class='city']").prop("selected",true)
            }
            if(district.length == 0){
                $('#district').find("option:first-child").prop("selected",true)
            }else{
                $('#district').find("option[class='district']").prop("selected",true)
            }
        };

        //完成添加地址
        $scope.complete_add = function(){
            if ($scope.address_row.name == undefined || $scope.address_row.name == ''){
                $scope.show_promt("请填写姓名");
            }else if ($scope.address_row.phone == undefined || $scope.address_row.phone == ''){
                $scope.show_promt("请填写联系电话");
            }else if($scope.address_row.province == undefined){
                $scope.show_promt("请选择省份");
            }else if($scope.address_row.city == undefined){
                $scope.show_promt("请选择城市");
            }else if (!($scope.address_row.city_code == "120200" || $scope.address_row.city_code == "310200" || $scope.address_row.city_code == "500200") && $scope.address_row.district == undefined){
                $scope.show_promt("请选择地区");
            }else if ($scope.address_row.address == undefined || $scope.address_row.address == ''){
                $scope.show_promt("请填写详细地址");
            }else {
                ajax.req('POST', 'user/add_address', $scope.address_row)
                    .then(function(response){
                        if (response.success){
                            singlePage.close('.page_modify');
                            $('.item-footer').css('display', 'block');
                            $scope.get_address();
                        }else{
                            $scope.show_promt(response.msg);
                        }
                    })
            }
        };

        //显示我的优惠券
        $scope.show_discount = function (id) {
            $('#order_header').hide();
            singlePage.open(id);
            document.body.scrollTop = document.documentElement.scrollTop = 0;
        };

        //选择优惠券
        $scope.select_discount = function (discount) {
            if (discount.condition <= $scope.total_price) {
                $scope.discount = discount;
                singlePage.close('#page_discount');
            }
        };

        //关闭优惠券
        $scope.backToOrder = function () {
            history.go(-1);
            $('#order_header').show();
        };
        //弹窗提示函数
        var popToast;
        $scope.show_popToast = function (msg) {
            if (!popToast || popToast && !popToast.toastBox) {
                popToast = new Toast(msg, {
                    "onHid": function (e) {
                        e.destroy();
                    }
                });
            }
            popToast.show();
        };
        //付款
        $scope.pay = function (ids) {
            if ($scope.order_id != 0 && $scope.settlement[0].is_point != 1) {
                // 已经下单，直接调用支付
                $scope.get_wechat_pay_js_api_parameters();
            } else {
                // 还未下单
                if (!ids) {
                    return false;
                }
                var data = {
                    address_id: $scope.default_address.id,
                    ids: ids,
                    message: $scope.message || '',
                    is_point_flag: $scope.settlement[0].is_point,
                    payment_id: 1,
                    user_discount_coupon_id: $scope.discount.id,
                    terminal_type: 2
                };
                ajax.req('POST', 'order/add', data)
                    .then(function (response) {
                        if (response.success) {
                            if (data.is_point_flag == 1) {
                                $scope.show_popToast("兑换成功");
                                window.location.href = SITE_URL + "weixin/index/pay_status/" + response.insert_id;
                            } else if (data.is_point_flag == 0) {
                                $scope.show_popToast("创建订单成功，正在调起微信支付");
                                $scope.order_id = response.insert_id;
                                $scope.get_wechat_pay_js_api_parameters();
                            } else {
                                $scope.show_popToast("服务器异常，请到订单中心完成支付");
                            }
                        } else {
                            $scope.order_id = 0;
                            $scope.show_popToast(response.msg);
                        }
                    });
            }
        };

        //关闭page页面
        $scope.close_page = function (id, animation) {
            singlePage.close(id, animation);
            $('#order_header').show();
        };

        // 获取微信支付参数
        $scope.get_wechat_pay_js_api_parameters = function () {
            ajax.req('POST', 'order/get_wechat_pay_js_api_parameters', {order_id: $scope.order_id})
                .then(function (response) {
                    if (response.success) {
                        // 调起微信支付
                        $scope.js_api_parameters = response.js_api_parameters;
                        $scope.callpay();
                    } else {
                        $scope.show_popToast(response.msg);
                    }
                });
        };

        // 调起微信支付
        $scope.js_api_call = function () {
            var js_params = JSON.parse($scope.js_api_parameters);
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                {
                    'appId': js_params.appId,
                    'timeStamp': js_params.timeStamp,
                    'nonceStr': js_params.nonceStr,
                    'package': js_params.package,
                    'signType': js_params.signType,
                    'paySign': js_params.paySign
                },
                function (res) {
                    var popToast;
                    if (res.err_msg == "get_brand_wcpay_request:ok") {
                        // 跳转到订单详情页面

                        // 如果是代理商，跳转回代理商指定的页面，否则跳转到订单详情

                        window.location.href = SITE_URL + "weixin/index/pay_status/" + $scope.order_id;
                    } else if (res.err_msg == "get_brand_wcpay_request:cancel") {
                        // 用户取消支付
                        $scope.show_popToast('您已取消支付，请尽快完成订单支付');
                    } else {
                        // 支付失败
                        $scope.show_popToast('系统繁忙，支付失败');
                    }
                }
            );
        };
        $scope.callpay = function () {
            if (typeof WeixinJSBridge == "undefined") {
                if (document.addEventListener) {
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                } else if (document.attachEvent) {
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            } else {
                $scope.js_api_call();
            }
        };

        // 重写$rootScope中的返回按钮事件
        $scope.back = function () {
            if ($scope.order_id != 0) {
                // 已经下单
                window.location.href = SITE_URL + "weixin/index/order_detail/" + $scope.order_id;
            } else if($scope.settlement.length == 1){
                window.location.href = SITE_URL + "weixin/index/commodity_detail/" + $scope.settlement[0].commodity_id + '/' + $scope.settlement[0].specification_id;
            }else {
                history.go(-1);
            }
            $('#order_header').show();
        };

        //获取优惠券
        $scope.discount_coupons = [];
        ajax.req('POST', 'user/get_discount_coupon_by_user_id')
            .then(function (data) {
                if (data.success) {
                    $scope.discount_coupons = data.data;
                }
            });


        //选择地址信息
        var district = new AMap.DistrictSearch({
            level: 'country',
            showbiz: false,
            subdistrict: 1
        });
        /**
         * 初始化省市区选择控件
         */
        $scope.initAddress = function() {
            district.search('中国', function(status, result) {
                if(status=='complete'){
                    if (result.districtList.length > 0) {
                        $scope.getAdministrativeRegion(result.districtList[0]);
                    }else {
                        console.log('获取省级行政区失败');
                    }
                }
            });
        };
        /**
         * 解析省市区信息
         * @param data
         */
        $scope.getAdministrativeRegion = function(data, city_code, district_code) {
            var subList = data.districtList;
            var level = data.level;
            if (subList.length > 0) {
                $('#' + subList[0].level).empty();
            }

            var contentSub;
            if (level === 'province') {
                nextLevel = 'city';
                $("#city").innerHTML = '';
                $('#district').innerHTML = '';
                $("#city").empty();
                $("#city").val("");
                $('#district').empty();
                $('#district').val("");
            } else if (level === 'city') {
                nextLevel = 'district';
                $('#district').innerHTML = '';
                $('#district').empty();
                $('#district').val("");
            }
            if (subList) {
            if (level == 'province') {
                contentSub = new Option('-- 市 --');
            } else if (level == 'city') {
                contentSub = new Option('-- 区 --');
            } else {
                contentSub = new Option('-- 省 --');
            }

            contentSub.setAttribute("value", "");
            for (var i = 0, l = subList.length; i < l; i++) {
                var name = subList[i].name;
                var value = subList[i].adcode;
                var levelSub = subList[i].level;
                var cityCode = subList[i].citycode;

                if (i == 0) {
                    document.querySelector('#' + levelSub).add(contentSub);
                    document.querySelector('#' + levelSub).removeAttribute('disabled');
                }
                contentSub = new Option(name);
                contentSub.setAttribute("value", value);
                contentSub.center = subList[i].center;
                contentSub.adcode = subList[i].adcode;

                document.querySelector('#' + levelSub).add(contentSub);
            }
            if (typeof(city_code) != 'undefined' && city_code != "" && levelSub == "city") {
                $('#' + levelSub).val(city_code);
                $scope.searchNextLevel($('#' + levelSub)[0], city_code, district_code);
            } else if (typeof(district_code) != 'undefined' && district_code != "" && levelSub == "district") {
                $('#' + levelSub).val(district_code);
            }
        }else {
                if (level == "province") {
                    // 将市级、县级下拉列表置为不可用
                    $("#city").attr('disabled', 'disabled');
                    $("#district").attr('disabled', 'disabled');
                }else if (level == "city") {
                    // 将县级下拉列表置为不可用
                    $("#district").attr('disabled', 'disabled');
                }
            }
        };
        /**
         * 根据当前所选省市搜索下级行政区域列表
         * @param obj
         * @param city_code 城市代码，编辑地址时初始化控件使用
         * @param district_code 区县代码，编辑地址时初始化控件使用
         */
        $scope.searchNextLevel = function(obj, city_code, district_code) {
            var option = obj[obj.options.selectedIndex];
            var keyword = option.text; //关键字
            var adcode = option.adcode;
            city_code = city_code || '';
            district_code = district_code || '';
            district.setLevel(option.value); //行政区级别
            //行政区查询
            //按照adcode进行查询可以保证数据返回的唯一性
            district.search(adcode, function(status, result) {
                if(status === 'complete'){
                    $scope.getAdministrativeRegion(result.districtList[0], city_code, district_code);
                }
            });
        };

        $scope.initAddress();

        //监听地址选择事件
        $('#province')[0].addEventListener('change', function(){
            var obj = this;
            $scope.searchNextLevel(obj);
            $scope.address_row.province = obj[obj.options.selectedIndex].text;
            $scope.address_row.province_code = obj[obj.options.selectedIndex].value;
        }, false);

        $('#city')[0].addEventListener('change', function(){
            var obj = this;
            $scope.searchNextLevel(obj);
            $scope.address_row.city = obj[obj.options.selectedIndex].text;
            $scope.address_row.city_code = obj[obj.options.selectedIndex].value;
        }, false);

        $('#district')[0].addEventListener('change', function(){
            var obj = this;
            $scope.searchNextLevel(obj);
            $scope.address_row.district = obj[obj.options.selectedIndex].text;
            $scope.address_row.district_code = obj[obj.options.selectedIndex].value;
        }, false);

    }]);
