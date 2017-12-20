<div id="page-content" class="home-page" style="padding-bottom: 60px" ng-controller="shoppingCartCtrl">
    <div class="titlebar" style="background-color: #117d94">
        <a class="titlebar-button" ng-click="back()">
            <i class="icon size16 icon-arrowleft back_btn"></i>
        </a>
        <h1 style="text-align: center;color: #fff;margin-left: 42px;"><?php echo $title; ?></h1>
        <a class="titlebar-button" style="margin-right: 12px">
            <span ng-click="cancel(shopping_cart, $event)" style="color: #FFFFFF;">编辑</span>
            <span ng-click="done(shopping_cart, $event)" style="display: none">完成</span>
        </a>
    </div>

    <div class="table-box table_lists">
        <div>
            <table>
                <tbody>
                <tr ng-repeat="shopping_cart in shopping_carts">
                    <td>
                        <div class="input_img">
                            <input type="checkbox" class="input-checkbox" ng-model="shopping_cart.checked" ng-change="selectOne()">
                            <a href="{{SITE_URL + 'weixin/index/commodity_detail/' + shopping_cart.commodity_id + '/' + shopping_cart.specification_id}}">
                                <img ng-if="shopping_cart.path !== null" ng-src="{{ SITE_URL + shopping_cart.path }}">
                                <img ng-if="shopping_cart.path === null" ng-src="{{ SITE_URL + 'source/mobile/img/photo.jpg' }}">
                            </a>
                        </div>
                    </td>
                    <td>
                        <p class="commodity_name" ng-click="gotoDetail(shopping_cart.commodity_id, shopping_cart.specification_id )">
                            {{shopping_cart.commodity_specification_name}}
                        </p>
                        <span class="price" style="display: inline-block;">¥ {{ shopping_cart.flash_sale_price ? shopping_cart.flash_sale_price : shopping_cart.price}}</span>
                        <span style="font-size: 12px;color: #999;text-decoration: line-through;vertical-align: sub;" ng-if="shopping_cart.flash_sale_price">¥ {{ shopping_cart.price }}</span>
                        <div class="numbox bordered margin8" style="width: 80px;">
                            <input type="button" id="sub_btn" class="button" value="-" ng-click="decrement_num(shopping_cart, $event)"/>
                            <input type="number" ng-model="shopping_cart.amount" ng-change="valIsChange(shopping_cart)" class="input-text"/>
                            <input type="button" id="plus_btn" class="button" value="+" ng-click="increment_num(shopping_cart, $event)"/>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="cart_footer" id="pay">
        <ul class="grid" data-col="3">
            <li>
                <input type="checkbox" class="input-checkbox" ng-model="select_all" ng-change="selectAll()">
                <span>全选</span>
            </li>
            <li  style=" padding-right: 18px;text-align: right; {{ agent_flag != null ? 'padding-top:15px;' : ''}}">
                <span style="font-size: 16px">总计：</span>
                <span style="color: #f23030; vertical-align: text-top;">¥ {{total_price | number:2 }}</span>
                <span ng-if="agent_flag == null" style="display: block">购买可获得 {{total_points}} 积分</span>
            </li>
            <li>
                <a class="button Irpadding8 cancel" ng-click="settlement()" style="background-color: #117d94">去结算</a>
            </li>
        </ul>
    </div>
    <div class="cart_footer" id="cancel_product" style="display: none">
        <ul class="grid" data-col="2">
            <li>
                <input type="checkbox" class="input-checkbox" ng-model="select_all" ng-change="selectAll()">
                <span>全选</span>
            </li>
            <li style="float: right; padding:0">
                <a class="button Irpadding8 cancel cancel_btn" ng-click="cancel_shopping_cart()">删 除</a>
            </li>
        </ul>
    </div>
</div>