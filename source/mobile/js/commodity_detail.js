angular.module('app')
    .controller('commodityDetailCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        //初始化轮播图
        $scope.commodity_pack_id = '';
        $scope.last_url = document.referrer;
        $scope.url = window.location.href;
        $scope.urlArr = [];
        if($scope.url.indexOf('#page_2') != -1){
            var indexEq = $scope.url.indexOf('#');
            window.location.href = $scope.url.substring(0,indexEq);
        }
        $scope.urlArr = window.location.href.split('/');
        if($scope.last_url.indexOf('order_list') != -1){
            $scope.commodity_id = $scope.urlArr[$scope.urlArr.length - 3];
            $scope.specification_pack_id = $scope.urlArr[$scope.urlArr.length - 2];
            $scope.status_id = $scope.urlArr[$scope.urlArr.length - 1];
        }else{
            $scope.commodity_id = $scope.urlArr[$scope.urlArr.length - 2];
            $scope.specification_pack_id = $scope.urlArr[$scope.urlArr.length - 1];
        }
        $scope.userType_pack_id = angular.copy($scope.specification_pack_id);
        console.log($scope.specification_pack_id);

        var s1 = new Slider("#carousel1", {
            "pagination": ".slider-pagination",
            "loop": true,
            "autoplay": 5000
        });
        var singlePage=new Page();

        // 弹窗提示
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

        var img_height = parseFloat(screen.width);
        $('.slider-container, .slider-container div, .slider-container img').css('height', img_height + 'px');
        $scope.favorite_flag = false;

        /*获取用户类型*/
        $scope.getUserType = function () {
            ajax.req('GET', 'user/get_personal_info')
                .then(function (response) {
                    if (response.success) {
                        $scope.userType = response.data.uid;
                        $scope.agent_name = response.data.agent_name;
                    } else {
                        $scope.userType = null;
                    }
                });
        };
        $scope.getUserType();

        /**
         * 获取缩略图
         */
        //  $scope.getThumbnail = function() {
        //     ajax.req('POST', 'admin/commodity_admin/get_specification_thumbnail', {
        //                 commodity_id: $scope.commodity_id,
        //                 commodity_specification_id: $scope.commodity_pack_id
        //             }).then(function (response) {
        //                 if (response.success) {
        //                     $scope.picList = response.data;
        //                     $('.slider-wrapper').html('');
        //                     angular.forEach($scope.picList, function(data, index){
        //                         $('.slider-wrapper').append(' <div class="slider-slide">\
        //             <img class="slide-banner" src="' + SITE_URL + data.path + '"/>\
        //         </div>');
        //                     })
        //                     // $('.slide-banner')[0].src = $scope.picList.path;
        //                 }
        //             });
        // };
        // $scope.getThumbnail();

        //获取商品详情
        $scope.commodity = [];
        $scope.agent_price = '';
        $scope.commodity_path = '';
        $scope.commodity_price = '';
        // $scope.$watch('commodity.pack_id', function (nv, ov) {
        //     if (nv) {
                // $scope.commodity.pack_id = nv;
                ajax.req('POST', 'index/get_commodity_by_id', {specification_id: $scope.specification_pack_id})
                    .then(function (response) {
                        if (response.success) {
                            $scope.commodity = response.data;
                            $scope.commodity_price = response.data.price;
                            $scope.agent_price = response.data.agent_price;
                            $scope.commodity_path = response.data.path;
                        } else {
                            $scope.show_popToast(response.msg);
                            $scope.commodity = [];
                        }
                    });


            // }
        // });



        // $scope.$watch('commodity_pack_id', function (nv, ov) {
        //     if (nv && nv != '') {
        //         $scope.get_favorite($scope.commodity_id, nv);
        //     }
        // });

        $scope.get_favorite = function (commodity_id, pack_id) {
            ajax.req('POST', 'favorite/check_favorite_by_commodity_id', {commodity_id: commodity_id, commodity_specification_id: pack_id})
                .then(function (response) {
                    if (response.success) {
                        $scope.favorite_flag = true;
                    } else {
                        $scope.favorite_flag = false;
                    }
                });
        };

        //收藏
        $scope.favorite = function () {
            if(($scope.commodity_pack_id == '' || !$scope.commodity_pack_id)) {
                $scope.show_popToast("请选择商品规格");
                return;
            }
            // if($scope.userType != null){
            //         $scope.commodity_pack_id = angular.copy($scope.userType_pack_id);
            //     }
            if ($scope.favorite_flag) {
                ajax.req('POST', 'favorite/delete_by_commodity_id', {
                        commodity_id: $scope.commodity.id,
                        commodity_specification_id: $scope.commodity_pack_id
                    }).then(function (response) {
                        if (response.success) {
                            $scope.favorite_flag = !$scope.favorite_flag;
                            $scope.show_popToast("取消收藏成功");
                        } else {
                            $scope.show_popToast(response.msg);
                        }
                    });
            } else {
                ajax.req('POST', 'favorite/add', {
                    commodity_id: $scope.commodity.id,
                    commodity_specification_id: $scope.commodity_pack_id
                }).then(function (response) {
                        if (response.success) {
                            $scope.favorite_flag = !$scope.favorite_flag;
                            $scope.show_popToast("收藏成功");
                        } else {
                            $scope.show_popToast(response.msg);
                            if (response.timeout) {
                                setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                            }
                        }
                    });
            }
        };

        //获取购物车数量
        $scope.cart_num = 0;
        ajax.req('GET', 'shopping_cart/amount')
            .then(function (response) {
                if (!response.success && response.timeout) {
                    // 购物车为空或未登录
                } else if (response) {
                    setTimeout(function () {
                        $scope.$apply(function () {
                            $scope.cart_num = response;
                        })
                    }, 1)
                }
            });
        $scope.$watch('cart_num', function (nv, ov) {
            if (nv > 99) {
                $('.tip.shopping-cart').text('99+');
            }
        });

        //商品数量
        $scope.num = 1;
        $scope.$watch('num', function (nv, ov) {
            if (nv < 1) {
                $scope.num = 1;
            } else if (nv > 10) {
                $scope.num = 10;
            }
            if (nv > 1 && $scope.commodity.is_point == 1) {
                $scope.num = 1;
                $scope.show_popToast("积分商品每次只能兑换一个");
            }
        });

        //商品数量加1
        $scope.add_num = function () {
            $scope.num += 1;
        };

        //商品数量减1
        $scope.sub_num = function () {
            $scope.num -= 1;
        };
        //获取规格
        $scope.specification = [];
        $scope.pack = [];
        $scope.get_specification_pack = function () {
            ajax.req('GET', 'commodity/commodity_specification_by_id/' + $scope.commodity_id)
                .then(function (response) {
                if (response.success) {
                    $scope.specification = response.data;
                    setTimeout(function () {
                        if($scope.specification_pack_id && $scope.specification_pack_id != ''){
                            for(var i = 0; i < $scope.specification.length; i++){
                                for(var j = 0; j < $scope.specification[i].length; j++){
                                    if($scope.specification[i][j].id == $scope.specification_pack_id){
                                        $scope.select_specification('', $scope.specification[i], $('.specification-btn').eq(i));

                                        $scope.$apply(function () {
                                            if($scope.specification[i].length == 2){
                                                if($scope.specification[i][j].package_type_name == '精装'){
                                                    $scope.select_pack('',$('.jinz'));
                                                }else{
                                                    $scope.select_pack('',$('.jianz'));
                                                }
                                                $scope.hasnojinz = false;
                                                $scope.hasnojianz = false;
                                            }else if($scope.specification[i].length == 1 && $scope.specification[i][0].package_type_name == '精装'){
                                                $scope.select_pack('',$('.jinz'));
                                                $scope.hasnojianz = true;
                                                $scope.hasnojinz = false;
                                            }else if($scope.specification[i].length == 1 && $scope.specification[i][0].package_type_name == '简装'){
                                                $scope.select_pack('',$('.jianz'));
                                                $scope.hasnojinz = true;
                                                $scope.hasnojianz = false;
                                            }else if($scope.specification[i].length == 0 || ($scope.specification[i].length == 1 && ($scope.specification[i][0].package_type_name == '' || !$scope.specification[i][0].package_type_name || $scope.specification[i][0].package_type_name == null))){
                                                $scope.hasnojinz = true;
                                                $scope.hasnojianz = true;
                                            }
                                            if ($scope.specification[i][j].name != null) {
                                                $scope.specification_pack_name = $scope.specification[i][j].name + ' ' + $scope.specification[i][j].package_type_name;
                                            } else if ($scope.specification[i][j].name == null){
                                                $scope.specification_pack_name = $scope.specification[i][j].commodity_specification_name + ' ' + $scope.specification[i][j].package_type_name;
                                            }

                                            if ($scope.specification[i][j].flash_sale_price && $scope.specification[i][j].flash_sale_price != null && $scope.specification[i][j].flash_sale_price != '') {
                                                $scope.specification_pack_price = $scope.specification[i][j].flash_sale_price;
                                                $scope.commodity_price = $scope.specification[i][j].flash_sale_price;
                                            } else {
                                                $scope.specification_pack_price = $scope.specification[i][j].selling_price;
                                                $scope.commodity_price = $scope.specification[i][j].selling_price;
                                            }
                                        })
                                    }
                                }
                            }
                        }else{
                            $scope.hasnojinz = false;
                            $scope.hasnojianz = false;
                        }
                        // $scope.get_price();
                    }, 500);
                } else {
                    $scope.show_popToast(response.msg);
                }
            });

        };

        // //选择规格
        $scope.pack = [];
        $scope.select_specification = function (e, data, ele) {
            var _this = '';
            if(e != '' && ele == ''){
                _this = $(e.target);
            }else if(e == '' && ele != ''){
                _this = ele;
            }
            $scope.pack = angular.copy(data);
            if(_this.hasClass('active')){
                _this.removeClass('active');
                $scope.hasnojinz = false;
                $scope.hasnojianz = false;
            }else{
                if(data.length == 1 && data[0].package_type_name == '精装'){
                    $scope.hasnojianz = true;
                    $scope.hasnojinz = false;
                }else if(data.length == 1 && data[0].package_type_name == '简装'){
                    $scope.hasnojinz = true;
                    $scope.hasnojianz = false;
                }else if(data.length == 0 || (data.length == 1 && (data[0].package_type_name == '' || data[0].package_type_name == null || !data[0].package_type_name))){
                    $scope.hasnojinz = true;
                    $scope.hasnojianz = true;
                }else if(data.length == 2){
                    $scope.hasnojinz = false;
                    $scope.hasnojianz = false;
                }
                _this.addClass('active').siblings().removeClass('active');
            }
            $scope.get_price();
        };


        $scope.select_pack = function (e, ele) {
            var _this = '';
            if(e != '' && ele == ''){
                _this = $(e.target);
            }else if(e == '' && ele != ''){
                _this = ele;
            }
            if(_this.hasClass('active')){
                angular.forEach($scope.specification, function (data, index) {
                    data.hasnopack = false;
                });
                _this.removeClass('active');
            }else{
                angular.forEach($scope.specification, function (data, index) {
                    if(data.length == 2 || (data.length == 1 && data[0].package_type_name == _this.text())){
                        data.hasnopack = false;
                    }else if((data.length == 1 && data[0].package_type_name != _this.text()) || data.length == 0){
                        data.hasnopack = true;
                    }
                });
                _this.addClass('active').siblings().removeClass('active');
            }
            $scope.get_price();
        };


        // //计算选择规格后价格
        $scope.get_price = function () {
            var pack = '';
            var specation = '';
            $('.se-btn').each(function () {
                if($(this).hasClass('active')){
                    pack = $(this).text();
                }
            });
            $('.specification-btn').each(function () {
                if($(this).hasClass('active')){
                    specation = $(this).text();
                }
            });

            angular.forEach($scope.pack, function (item, index) {
                if(specation != '' && pack != ''){
                   if(item.package_type_name == pack) {
                       $scope.commodity_path = item.path;
                       $scope.commodity_pack_id = item.id;
                       if($scope.userType){
                            $scope.agent_price = item.agent_price;
                            $scope.commodity.agent_price = item.agent_price;
                       }else{
                            if (item.flash_sale_price && item.flash_sale_price != null && item.flash_sale_price != '') {
                               $scope.specification_pack_price = item.flash_sale_price;
                               $scope.commodity_price = item.flash_sale_price;
                           } else {
                               $scope.specification_pack_price = item.selling_price;
                               $scope.commodity_price = item.selling_price;
                           }
                       }
                       // if(item.commodity_specification_name && item.commodity_specification_name != ''){
                       //     $scope.specification_pack_name = item.commodity_specification_name + ' ' + item.package_type_name;
                       // }else 
                       if(item.name && item.name != ''){
                           $scope.specification_pack_name = item.name + ' ' + item.package_type_name;
                       }else if(item.package_type_name && item.package_type_name != ''){
                           $scope.specification_pack_name = item.package_type_name;
                       }else{
                           $scope.specification_pack_name = '没有规格';
                       }

                       // $scope.commodity_orignal_name = item.name + ' ' + item.package_type_name;
                       // $scope.commodity_orignal_name = item.commodity_specification_name;s
                       $scope.commodity.commodity_specification_name = item.commodity_specification_name;
                       return;
                   }
                    return;
                }
                $scope.commodity_pack_id = '';
                $scope.specification_pack_id = '';
                $scope.commodity_price = $scope.commodity.price;
                $scope.specification_pack_name = '未选择规格';
                // $scope.commodity_orignal_name = $scope.commodity.commodity_name;
                // $scope.commodity_orignal_name = item.commodity_specification_name;
                $scope.commodity.commodity_specification_name = $scope.commodity.commodity_specification_name;
            });
            $scope.get_favorite($scope.commodity.id, $scope.commodity_pack_id);
            // if($scope.commodity_id && $scope.commodity_pack_id){
            //     $scope.getThumbnail(); 
            // }
           

        };

        //关闭选择
        $scope.closePage = function () {
            history.go(-1);
            $('.parent-detail').css('position','relative')
        };

        $scope.get_favorite($scope.commodity_id, $scope.specification_pack_id);
        $scope.get_specification_pack();
        $scope.confirm_pack = function () {
          if(($scope.commodity_pack_id == '' || !$scope.commodity_pack_id)) {
              $scope.show_popToast('请选择规格')
          }else{
                // if($scope.userType != null){
                //     $scope.commodity_pack_id = angular.copy($scope.userType_pack_id);
                // }
              if(!$scope.buy_directly){
                  history.go(-1);
                  $('.parent-detail').css('position', 'relative');
                  ajax.req('POST', 'shopping_cart/add', {
                      commodity_id: $scope.commodity.id,
                      specification_id: $scope.commodity_pack_id,
                      amount: $scope.num
                  }).then(function (response) {
                      if (response.success) {
                          $scope.show_popToast(response.msg);
                          $scope.cart_num = parseInt($scope.cart_num) + $scope.num;
                      } else {
                          $scope.show_popToast(response.msg);
                          if (response.timeout) {
                              setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                          }
                      }
                  });
              }else{
                  ajax.req('POST', 'shopping_cart/add', {
                      commodity_id: $scope.commodity.id,
                      specification_id: $scope.commodity_pack_id,
                      amount: $scope.num,
                      is_buy_now: 1
                  }).then(function (response) {
                      if (response.success) {
                          window.location.href = SITE_URL + 'weixin/index/confirm_order/' + response.insert_id;
                      } else {
                          $scope.show_popToast(response.msg);
                          if (response.timeout) {
                              setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                          }
                      }
                  });
              }

          }
        };
        //加入购物车
        $scope.openPage = function () {
            singlePage.open('#page_2');
            $scope.selectSpecification = true;
            $scope.buy_directly = false;
            $scope.get_price();
            $('.parent-detail').css('position', 'fixed');
        };
        $scope.add_cart = function () {
            $scope.selectSpecification = true;
            singlePage.open('#page_2');
            $scope.buy_directly = false;
            $('.parent-detail').css('position', 'fixed');
            $scope.get_price();
        };

        //立即购买
        $scope.buy_direct = function () {
            singlePage.open('#page_2');
            $scope.buy_directly = true;
        };

        //兑换积分商品
        $scope.exchange = function () {
            ajax.req('GET', 'user/get_personal_info')
                .then(function (response) {
                    if (response.success) {
                        window.location.href = SITE_URL + 'weixin/index/confirm_order/' + $scope.commodity.id + '/1';
                    } else {
                        $scope.show_popToast(response.msg);
                        if (response.timeout) {
                            setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                        }
                    }
                });
        };

        $scope.goShoppingCart = function () {
            ajax.req('GET', 'user/get_personal_info')
                .then(function (response) {
                    if (response.success) {
                        window.location.href = SITE_URL + 'weixin/index/shopping_cart';
                    } else {
                        $scope.show_popToast('请先登录');
                        if (response.timeout) {
                            setTimeout("location.href = SITE_URL + 'weixin/index/sign_in'", 1000);
                        }
                    }
                });
        };
        //查看商品评价
        $scope.commodity_evaluation = function () {
            if($scope.commodity_pack_id != ''){
                window.location.href = SITE_URL + 'weixin/user/evaluation_list/' + $scope.commodity.id + '/' + $scope.commodity_pack_id;
            }else if($scope.specification_pack_id != ''){
                window.location.href = SITE_URL + 'weixin/user/evaluation_list/' + $scope.commodity.id + '/' + $scope.specification_pack_id;
            }else {
                window.location.href = SITE_URL + 'weixin/user/evaluation_list/' + $scope.commodity.id;
            }
        };

        //定义exmobi返回
        $scope.back = function () {
            var last_url = document.referrer;
            var last_url_falg = document.referrer.substring(document.referrer.lastIndexOf('/')).replace('/', '');
            if (last_url_falg == 'order_list' || (!isNaN(parseInt(last_url_falg)) && angular.isNumber(parseInt(last_url_falg)) && last_url.indexOf('order_list') != -1)) {
                window.location.href = SITE_URL + 'weixin/user/order_list/' + $scope.status_id;
            } else {
                window.parent.history.go(-1)
            }
        }

    }]);
