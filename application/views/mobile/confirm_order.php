<div ng-controller="confirmOrderCtrl" ng-init="ids = '<?php echo isset($ids) ? $ids : NULL; ?>';is_point = '<?php echo $is_point; ?>'">
    <header id="order_header">
        <div class="titlebar">
            <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
            <h1 class="text-center" style="margin-right: 40px;"><?php echo $title; ?></h1>
        </div>
    </header>
    <article style="padding-bottom: 55px;">
        <div class="address-info" style="margin-top: 12px;">
            <p class="address-user-info">
                <span>收货人:</span>
                <span>{{default_address.name}}</span>
                <span>{{default_address.phone}}</span>
            </p>
            <hr style="margin: 0 15px;">
            <p class="address-place">
                <span><img ng-src="{{ SITE_URL + 'source/mobile/img/icon/address.png' }}" alt=""></span>
                <span>{{default_address.province}}{{default_address.city}}{{default_address.district}}{{default_address.address}}</span>
                <span><i class="icon size18 icon-arrowright"></i></span>
            </p>
        </div>
        <a class="page-link-address" href="javascript:void(0)" ng-click="show_address('#page_address')" data-toggle="page"></a>
        <div class="card">
            <hr>
            <div class="inputbox">
                <span>商品列表</span>
            </div>
            <hr>
            <div class="inputbox" style="display: flex;" ng-repeat="row in settlement">
                <div>
                    <a href="{{ SITE_URL + 'weixin/index/commodity_detail/' + row.commodity_id + '/' + row.specification_id }}">
                        <img ng-if="row.path != null" ng-src="{{ SITE_URL + row.path }}" alt="">
                        <img ng-if="row.path == null" ng-src="{{ SITE_URL + 'source/mobile/img/photo.jpg' }}" alt="">
                    </a>
                </div>
                <div class="info-box">
                    <div class="info-title">
                        <p>
                            <a href="{{ SITE_URL + 'weixin/index/commodity_detail/' + row.commodity_id + '/' + row.specification_id }}">{{row.commodity_specification_name}}</a>
                        </p>
                    </div>
                    <div class="info-price" ng-if="row.is_point == 1">
                        <span ng-bind="row.price | trans_int"></span>
                        <span style="font-size: 16px;">积分</span>
                    </div>
                    <div class="info-price" ng-if="row.is_point == 0">
                        <span>¥ </span>
                        <span ng-bind="row.flash_sale_price ? row.flash_sale_price : row.price"></span>
                        <span class="old-price" ng-if="row.market_price != null" ng-bind="'¥' + row.market_price"></span>
                    </div>
                </div>
                <div class="item-num">
                    <span>X</span>
                    <span style="font-size: 20px;" ng-bind="row.amount" ng-if="row.is_point == 0"></span>
                    <span style="font-size: 20px;"  ng-if="row.is_point == 1">1</span>
                </div>
            </div>
            <hr style="margin: 0 15px;">
            <div class="inputbox message">
                <label class="inputbox">留言：</label>
                <div class="inputbox-right inputbox">
                    <input type="text" class="input-text" placeholder="选填(对本次交易的说明)，建议填写" ng-model="message"/>
                </div>
            </div>
            <hr style="margin: 0 15px;">
            <div class="inputbox">
                <span>配送</span>
                <span class="item-span-right">顺丰快递</span>
            </div>
            <hr style="margin: 0 15px;">
            <div class="inputbox">
                <span>邮费：</span>
                <span class="item-span-right">{{default_address.province}} {{default_address.city}} {{default_address.district}} {{freight > 0 ? '运费￥' + freight : '免邮'}}</span>
            </div>
            <hr style="margin: 0 15px;">
            <?php if ($show_discount_coupon):?>
            <div class="inputbox" ng-if="settlement[0].is_point == 0" ng-click="show_discount('#page_discount')">
                <span>优惠</span>
                <span class="item-span-right">
                    <span ng-if="discount.id != undefined">满{{ discount.condition }}减{{ discount.privilege }}</span>
                    <i class="icon size18 icon-arrowright"></i>
                </span>
            </div>
            <?php endif;?>
            <hr style="margin: 0 15px;">
            <div class="inputbox text-right item-info">
                <p>共{{ settlement | total_num }}件商品</p>
                <p>
                    <span>合计：</span>
                    <span class="total_price" ng-if="settlement[0].is_point == 1">{{ settlement | sum_price }} 积分</span>
                    <span class="total_price" ng-if="settlement[0].is_point == 0">¥ {{ settlement | sum_price : discount.privilege | total_price : freight | number : 2}}</span>
                </p>
            </div>
        </div>
    </article>
    <div class="confirm-order-footer">
        <div class="item-total" style="{{agent_flag == true ? 'padding-top:18px;' : ''}}">
            <div style="{{ settlement[0].is_point == 1 ? 'margin-top:12px;' : '' }}">
                <span>总计：</span>
                <span style="color: #F6BF00;vertical-align: bottom;" class="total_price" ng-if="settlement[0].is_point == 1">{{ settlement | sum_price }} 积分</span>
                <span style="color: #F6BF00;vertical-align: middle;" class="total_price" ng-if="settlement[0].is_point == 0">¥ {{ settlement | sum_price : discount.privilege | total_price : freight |number : 2}}</span>
            </div>
            <div ng-if="settlement[0].is_point == 0 && !agent_flag">
                <span style="font-size: 13px;">已优惠：</span>
                <span style="color: #F6BF00;margin-top: 3px;">¥ {{ discount.privilege ? discount.privilege : 0 }}</span>
            </div>
        </div>
        <div class="item-pay" ng-click="pay('<?php echo isset($ids) ? $ids : NULL; ?>')">
            <label>{{ settlement[0].is_point == 1 ? '去兑换' : '去付款' }}</label>
        </div>
    </div>
    <section id="page_discount" data-animation="slideRight" class="page" style="background-color:#F9F9F9;height: 150%;">
        <header>
            <div class="titlebar">
                <a class="titlebar-button" ng-click="backToOrder()"><i class="icon size16 icon-arrowleft"></i></a>
                <h1 class="text-center">我的优惠券</h1>
            </div>
        </header>
        <article>
            <div class="address-item {{ discount_coupon.condition > total_price  ? 'unused' : '' }}" ng-repeat="discount_coupon in discount_coupons" ng-click="select_discount(discount_coupon)">
                <div class="discount-privilege">¥ <span>{{discount_coupon.privilege}}</span></div>
                <div class="discount-condition">
                    <p>满{{discount_coupon.condition}}使用</p>
                    <p>优惠券</p>
                </div>
                <div class="discount-bottom">使用时间 : {{discount_coupon.start_time | substring : 0 : 10}} 至 {{discount_coupon.end_time | substring : 0 : 10}}</div>
                <hr>
            </div>
        </article>
    </section>
    <section id="page_address" data-animation="slideRight" class="page" style="background-color:#F9F9F9;height: 150%;">
        <header  id="address_header">
            <div class="titlebar">
                <a class="titlebar-button" ng-click="closePage('#page_address')"><i class="icon size16 icon-arrowleft"></i></a>
                <h1 class="text-center">收货地址</h1>
                <a href="{{ SITE_URL + 'weixin/user/receipt_address' }}" style="color:#fff;">编辑</a>
            </div>
        </header>
        <article>
            <div>
                <div class="address-item" ng-repeat="address_info in address_infos">
                    <div class="item" ng-click="select_address(address_info, $event);$event.stopPropagation();">
                        <div class="item-title">
                            <span>{{address_info.name}}</span>
                            <span>{{address_info.phone}}</span>
                        </div>
                        <div class="item-info">
                            <span>{{address_info.province}}{{address_info.city}}{{address_info.district}}{{address_info.address}}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="item">
                        <div class="item-operation">
                        <span ng-click="select_address(address_info, $event)">
                            <input type="radio" class="input-radio" ng-if="address_info.default == 1" ng-checked="true" name="address">
                            <input type="radio" class="input-radio" ng-if="address_info.default == 0" ng-checked="false" name="address">
<!--                            <input type="radio" class="input-radio" ng-model="selected_address" ng-checked="address_row.default == 1" name="address">-->
                        </span>
                            <span class="sel_address">选择地址</span>
                            <span><a href="{{ SITE_URL + 'weixin/user/receipt_address' }}"></a></span>
                        </div>
                    </div>
                </div>
            </div>
        </article>
        <div class="item-footer" style="position: fixed;bottom: 0;">
            <button class="button" ng-click="add('#page_modify')">添加新地址</button>
        </div>
    </section>
    <section id="page_modify" data-animation="slideRight" class="page" style="background-color:#F9F9F9;position: fixed;">
        <header>
            <div class="titlebar">
                <a class="titlebar-button" ng-click="closePage('#page_modify')"><i class="icon size16 icon-arrowleft"></i></a>
                <h1 class="text-center">{{ type == 'edit' ? '编辑地址' : '新增地址' }}</h1>
            </div>
        </header>
        <article>
            <div class="inputbox underline">
                <label class="inputbox-left">姓名</label>
                <div class="inputbox-right inputbox">
                    <input type="text" class="input-text" placeholder="建议填写真实姓名" name="name" ng-model="address_row.name"/>
                </div>
            </div>
            <div class="inputbox">
                <label class="inputbox-left">联系电话</label>
                <div class="inputbox-right inputbox">
                    <input type="number" class="input-text" placeholder="请输入您的手机号码" name="username" ng-model="address_row.phone"/>
                </div>
            </div>
            <div class="inputbox underline" style="margin-top: 12px;">
                <label class="inputbox-left" style="width: 22%;">所在地区</label>
                <div class="inputbox-right inputbox">
                    <select id="province" class="input-text">
                        <option class="province">-- 省 --</option>
                    </select>
                    <select id="city" class="input-text">
                        <option class="city">-- 市 --</option>
                    </select>
                    <select id="district" class="input-text" style="padding-right: 0;">
                        <option class="district">-- 区 --</option>
                    </select>
                </div>
            </div>
            <div class="inputbox">
                <label class="inputbox-left">详细地址</label>
                <div class="inputbox-right inputbox">
                    <input type="text" class="input-text" placeholder="建议填写详细收货地址" name="username" ng-model="address_row.address"/>
                </div>
            </div>
            <div class="inputbox" style="margin-top: 12px;padding: 5px 15px;">
                <div class="box-flex-1">设置默认地址</div>
                <div class="switch notext {{ address_row.default == 1 ? 'active' : '' }}" data-name="switch_default" data-on-value="ok" data-off-value="off" ng-click="select_default($event)">
                    <div class="switch-handle"></div>
                </div>
            </div>
            <div class="sub-btn">
                <button class="button" ng-click="complete_add()" ng-if="type == 'add'">添&nbsp;&nbsp;&nbsp;&nbsp;加</button>
                <button class="button" ng-click="complete_edit()" ng-if="type == 'edit'">保&nbsp;&nbsp;&nbsp;&nbsp;存</button>
            </div>
        </article>
    </section>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
</div>