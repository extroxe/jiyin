<div id="page-content" style="<?php echo empty($shopping_carts) ? '' : 'display:block;' ?>" class="home-page">
    <div class="container">
        <div class="row-fluid" style="margin-top: -20px">
            <div class="span12">
                <img data-original="img/购物车/cart_banner.PNG"/>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <hr>
                <ul class="title">
                    <li>全部商品</li>
                </ul>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12" style="min-height: 200px;">
                <table class="cart_table" style="width: 100%">
                    <thead>
                    <tr style="text-align: center;">
                        <th style="width: 40%;">商品名称</th>
                        <th style="width: 20%;"></th>
                        <th >价格</th>
                        <th>数量</th>
                        <th>小计</th>
                        <th>编辑</th>
                    </tr>
                    </thead>
                    <tbody id="cart-content">
                    <?php if(!empty($shopping_carts)) : ?>
                    <?php foreach ($shopping_carts as $shopping_cart) : ?>
                    <tr class="item_list" data-cart-id="<?php echo $shopping_cart['id']; ?>" data-buy="directly">
                        <td>
                            <div style="width:20px; display: inline-block">
                                <input type="checkbox" class="son" name="item_list" data-cart="<?php echo $shopping_cart['id']; ?>">
                            </div>
                            <div class="cart_img">
                                <img src="<?php echo site_url($shopping_cart['path']); ?>">
                            </div>
                            <div class="item-title">
                                <a href="<?php echo site_url('commodity/index/'.$shopping_cart['commodity_id'].'/'.$shopping_cart['specification_id']) ?>" target="_blank" style="font-family: '微软雅黑'; font-size: 15px"><?php echo $shopping_cart['commodity_specification_name']; ?></a>
                            </div>
                        </td>
                        <td>
                        </td>
                        <td class="price">
                            ￥<span> <?php echo empty($shopping_cart['flash_sale_price']) ? $shopping_cart['price'] : $shopping_cart['flash_sale_price']; ?></span>
                            <?php if (!empty($shopping_cart['market_price'])): ?>
                                <span style="font-size: 12px;color: #999;text-decoration: line-through;">￥<?php echo $shopping_cart['market_price']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($commodity_type_id == jys_system_code::COMMODITY_TYPE_MEMBER): ?>
                            <span class="price"><?php echo $shopping_cart['amount']; ?></span>
                            <?php else: ?>
                            <div class="operation">
                                <div class="input-prepend input-append" style="vertical-align: middle; margin: 0;font-size: 0;">
                                    <input class="delt" data-count="<?php echo $shopping_cart['amount']; ?>" data-id="<?php echo $shopping_cart['id']; ?>" value="-" readonly>
                                    <input  value="<?php echo $shopping_cart['amount']; ?>" class="appendedPrependedInput" type="text">
                                    <input class="add" data-count="<?php echo $shopping_cart['amount']; ?>" data-id="<?php echo $shopping_cart['id']; ?>" value="+" readonly>
                                </div>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td style="width: 128px; color: #f6bf00;font-weight: bold;">
                            <span>￥</span>
                            <?php if (empty($shopping_cart['flash_sale_price'])): ?>
                                <span class="total_price"><?php echo sprintf('%.2f', $shopping_cart['price'] * $shopping_cart['amount']); ?></span>
                            <?php else:?>
                                <span class="total_price"><?php echo sprintf('%.2f', $shopping_cart['flash_sale_price'] * $shopping_cart['amount']); ?></span>
                            <?php endif;?>
                        </td>
                        <td>
                            <a href="javascript:void(0)" style="font-family: '微软雅黑'; font-size: 13px" onclick="delete_cart(<?php echo $shopping_cart['id']; ?>)">删除</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row-fluid table-bottom">
            <div class="span6">
                <div class="selectAll">
                    <input id="selectAll" style="margin-top: -3px" type="checkbox">
                    全选
                </div>
            </div>
            <div class="span6 pull-right">
                <div class="pull-right">
                    <span>合计：</span>
                    <span class="sum-price">￥</span>
                    <span class="sum-price" id="total_money">0.00</span>
                    <span id="settlement-btn">结算</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="empty-cart" style="display: <?php echo empty($shopping_carts) ? 'block' : 'none'; ?>">
    <div class="cart-img">
        <img src="<?php echo site_url('source/img/empty-cart.png'); ?>" alt="">
    </div>
    <div class="content">
        <span>购物车空了，快去 <a href="<?php echo site_url(); ?>">商城</a> 逛逛吧！</span>
    </div>
</div>