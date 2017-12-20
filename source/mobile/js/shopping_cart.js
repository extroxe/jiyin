angular.module('app')
    .controller('shoppingCartCtrl', ['$scope', '$timeout', '$location', 'ajax', function ($scope,$location, $timeout, ajax) {
        //弹窗提示
        var popToast;
        $scope.pop = function (msg) {
            if(!popToast || popToast && !popToast.toastBox){
                popToast = new Toast(msg, {
                    "onHid": function(e){
                        e.destroy();
                    }
                });
            }
            popToast.show();
        }

        //获取购物车信息
        $scope.shopping_carts = [];
        $scope.shopping_cart = [];
        $scope.agent_flag = '';
        ajax.req('POST', 'shopping_cart/all')
            .then(function (data) {
                if (data.success){
                    $scope.agent_flag = data.agent_id;
                    $scope.shopping_carts = data.data;
                    angular.forEach($scope.shopping_carts, function(shopping_cart, index){
                        $scope.shopping_carts[index].amount = parseInt(shopping_cart.amount);
                    });
                    $scope.select_all = true;
                    $scope.selectAll();
                }
            });

        //购物车商品数量+1
        $scope.increment_num = function (shopping_cart, $event) {
            $scope.shopping_cart = shopping_cart;
            $scope.shopping_cart.amount++;
            if(!shopping_cart.points || shopping_cart.points == null || shopping_cart.points == ''){
               shopping_cart.points = 0
            }
            $($event.target).siblings('#sub_btn').removeClass('active');
            if ($scope.shopping_cart.amount > 10){
                $($event.target).addClass('active').removeClass('success');
                $scope.shopping_cart.amount = 10;
                $scope.pop('当前商品数量为10，无法继续增加');
            }else{
                ajax.req('POST', 'shopping_cart/increment_num', {id : shopping_cart.id}, true)
                    .then(function (response) {
                        if (response){
                            if ($scope.shopping_cart.checked){
                                $scope.total_price += parseFloat(shopping_cart.flash_sale_price ? shopping_cart.flash_sale_price : shopping_cart.price);
                                $scope.total_points += parseInt(shopping_cart.points);
                            }
                        }else{
                            $scope.pop(response.msg);
                        }
                    })
            }
        };

        //购物车商品数量-1
        $scope.decrement_num = function (shopping_cart, $event) {
            $scope.shopping_cart = shopping_cart;
            $scope.shopping_cart.amount--;
            if(!shopping_cart.points || shopping_cart.points == null || shopping_cart.points == ''){
               shopping_cart.points = 0
            }
            if($scope.shopping_cart.amount < 1 ){
                $($event.target).addClass('active').removeClass('success');
                $scope.shopping_cart.amount = 1;
                $scope.pop('当前商品数量为1，无法继续减少');
            }else{
                $($event.target).siblings('#plus_btn').removeClass('active');
                $scope.flag = false;
                ajax.req('POST', 'shopping_cart/decrement_num', {id : shopping_cart.id}, true)
                    .then(function (data) {
                        if (data.success){
                            if ($scope.shopping_cart.checked){
                                $scope.total_price -= parseFloat(shopping_cart.flash_sale_price ? shopping_cart.flash_sale_price : shopping_cart.price);
                                $scope.total_points -= parseInt(shopping_cart.points);
                            }
                        }else{
                            $scope.pop(data.msg);
                        }
                    })
            }
        };

        //input输入数量时变化
        $scope.valIsChange = function (shopping_cart) {
            $scope.shopping_cart = shopping_cart;
            var amount = $scope.shopping_cart.amount;
            if(amount > 10){
                amount = 10;
                $scope.pop('商品数量最大为10');
            }else if(amount < 1 || amount == ''){
                amount = 1;
                $scope.pop('商品数量最小为1');
            }
            $scope.shopping_cart.amount = amount;
            total_price();
            ajax.req('POST', 'shopping_cart/update', {
                id : $scope.shopping_cart.id,
                amount: $scope.shopping_cart.amount
            })
        };

        //全选，单选， 反选
        $scope.checked = [];
        $scope.total_price = 0; //总价
        $scope.total_points = 0; //总积分
        //全选
        $scope.selectAll = function () {
            if($scope.select_all) {
                $scope.checked = [];
                $scope.total_price = 0; //总价
                $scope.total_points = 0; //总积分
                angular.forEach($scope.shopping_carts, function (shopping_cart) {
                    shopping_cart.checked = true;
                    $scope.checked.push(shopping_cart.id);
                })
            }else {
                angular.forEach($scope.shopping_carts, function (shopping_cart) {
                    shopping_cart.checked = false;
                    $scope.checked = [];
                    $scope.total_price = 0; //总价
                    $scope.total_points = 0; //总积分
                })
            }
            total_price();
        };

        //单选
        $scope.selectOne = function () {
            angular.forEach($scope.shopping_carts , function (shopping_cart) {
                var index = $scope.checked.indexOf(shopping_cart.id);
                if(shopping_cart.checked && index === -1) {
                    $scope.checked.push(shopping_cart.id);
                    total_price();
                } else if (!shopping_cart.checked && index !== -1){
                    $scope.checked.splice(index, 1);
                    total_price();
                }
            });

            if ($scope.shopping_carts.length === $scope.checked.length) {
                $scope.select_all = true;
                total_price();
            } else {
                $scope.select_all = false;
                total_price();
            }
        };

    //    计算总价
        function total_price() {
            var total_price = 0;
            var total_points = 0;
            var price = 0;
            var points = 0;

            angular.forEach($scope.shopping_carts, function (shopping_cart) {
                if(shopping_cart.checked){
                    price = parseFloat(shopping_cart.flash_sale_price ? shopping_cart.flash_sale_price : shopping_cart.price) * shopping_cart.amount;
                    if (shopping_cart.points != null) {
                        points = parseInt(shopping_cart.points) * shopping_cart.amount;
                    } else {
                        points = 0;
                    }
                    total_price =  total_price + price;
                    total_points += points;
                }
            });

            $scope.total_price = total_price;
            $scope.total_points = total_points;
        }

    //    编辑购物车按钮
        $scope.cancel = function (shopping_cart, $event) {
            $scope.select_all = false;
            $scope.selectAll();
            $($event.target).siblings('span').css('display', 'block').parents('.titlebar').siblings('#cancel_product').css('display', 'block');
            $($event.target).css('display', 'none').parents('.titlebar').siblings('#pay').css('display', 'none');
        };
        $scope.done = function (shopping_cart, $event) {
            total_price();
            $($event.target).siblings('span').css('display', 'block').parents('.titlebar').siblings('#cancel_product').css('display', 'none');
            $($event.target).css('display', 'none').parents('.titlebar').siblings('#pay').css('display', 'block');
        };

    //    删除购物车
        $scope.checked_ids = '';
        $scope.indexs = [];
        $scope.cancel_shopping_cart = function () {
                if($scope.checked){
                    var popConfirm = new Alert("确定要删除吗？",{
                        onClickOk: function(e){
                            ajax.req('POST', 'shopping_cart/delete', {id : $scope.checked})
                                .then(function (data) {
                                    if (data.timeout != undefined && data.timeout == true){
                                        $scope.pop(data.msg);
                                    }else if(data.success == undefined && data){
                                        if($scope.checked.length == $scope.shopping_carts.length){
                                            $scope.shopping_carts.splice(0, $scope.shopping_carts.length);
                                        }

                                        $scope.carts = angular.copy($scope.shopping_carts);
                                        angular.forEach($scope.carts, function (cart_item,index) {
                                            if($scope.checked.indexOf(cart_item.id) != -1){
                                                $scope.shopping_carts.splice(index,1);
                                                $scope.checked.splice($scope.checked.indexOf(cart_item.id), 1);
                                            }
                                        });

                                        if($scope.checked.length == 0){
                                            $scope.select_all = false;
                                        }
                                        $scope.pop('删除成功');
                                    }else{
                                        $scope.pop('删除成功');
                                    }
                                });
                            e.hide();
                        },
                        onClickCancel: function(e){
                            e.hide();
                        }
                    });
                    popConfirm.show();
                }
        };

        /**
         * 结算
         */
        $scope.ids = [];
        $scope.settlement = function () {
            angular.forEach($scope.checked, function (checked, index) {
                if($scope.checked.length == 1){
                    $scope.ids = checked;
                }else{
                    if (index == 0){
                        $scope.ids = $scope.checked[index];
                    }else{
                        $scope.ids += '-' + $scope.checked[index];
                    }
                }
            });

            if ($scope.ids == ''){
                $scope.pop('请选择商品');
            }else{
                window.location.href = SITE_URL + 'weixin/index/confirm_order/' + $scope.ids;
            }
        };

        //查看详情
        $scope.gotoDetail = function (commodity_id, specification_id) {
            window.location.href = SITE_URL + 'weixin/index/commodity_detail/' + commodity_id + '/' + specification_id;
        };
    }]);