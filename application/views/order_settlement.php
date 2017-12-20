<header id="top_title" data-point-flag="<?php echo $settlement[0]['is_point']; ?>" data-user-id="<?php echo $_SESSION['user_id'];?>">
    <div class="container">
        <div class="row-fluid">
            <div class="span2">
                <h3>基因商城</h3>
            </div>
            <div class="span10">
                <ul class="breadcrumb">
                    <li><a href="<?php echo site_url('shopping_cart')?>">查看购物车</a> <span class="divider">></span></li>
                    <li class="active">拍下商品 <span class="divider">></span></li>
                    <li class="active">付款 <span class="divider">></span></li>
                    <li class="active">确认收货 <span class="divider">></span></li>
                    <li class="active">评价</li>
                </ul>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span2">
                <strong>确认收货地址</strong>
            </div>
            <div class="span8"></div>
            <div class="span2">
                <a class=" pull-right" style="color: #117d94" href="<?php echo site_url('user/user_center/receiving_info'); ?>">管理收货地址</a>
            </div>
        </div>
        <div class="row-fluid">
            <div style="border: 1px solid #bbb"></div>
        </div>
        <div class="row-fluid" style="margin-top: 30px;" id="address_list_container" data-shopping_cart_ids="<?php echo $ids; ?>">
        </div>
        <br>
        <div class="row-fluid" style="margin-top: -20px">
            <div class="span12">
                <a class="btn btn-2 btn-link" href="javascript:void(0)" id="add_address">使用新地址</a>
                <a class="address-link" id="address-link" data-pack-up="true">显示全部地址</a>
            </div>
        </div>
        <br>
        <div class="row-fluid">
            <div class="span2">
                <strong>确认订单信息</strong>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <table class="cart_table" style="width: 100%;">
                    <thead style="line-height: 50px">
                    <tr>
                        <th style="width: 40%; text-align: left">商品属性</th>
                        <th><?php echo $settlement[0]['is_point'] == 1 ? '单价' : '单价(元)'?></th>
                        <th>数量</th>
                        <th>总计</th>
                    </tr>
                    </thead>
                    <tbody style=" background-color: #fbfcff">
                    <?php foreach ($settlement as $row) : ?>
                    <tr class="item_list order-settlement" data-commodity-id="<?php echo $row['commodity_id']; ?>" data-id="<?php echo $row['id']; ?>">
                        <td>
                            <div class="cart_img">
                                <a href="<?php echo site_url('commodity/index/'.$row['commodity_id'].'/'.$row['specification_id']); ?>">
                                    <img data-original="<?php echo site_url($row['path']); ?>" src="<?php echo site_url($row['path']); ?>">
                                </a>
                            </div>
                            <div style="display: inline-block;">
                                <a href="<?php echo site_url('commodity/index/'.$row['commodity_id'].'/'.$row['specification_id']); ?>" style="font-family: '微软雅黑'; font-size: 13px"><?php echo $row['commodity_specification_name']; ?></a>
                            </div>
                        </td>
                        <td>
                            <?php if ($row['is_point']): ?>
                                <span><?php echo $row['price'] < 1 ? 1 : $row['price']; ?>积分</span>
                            <?php else: ?>
                                <span>¥<?php echo empty($row['flash_sale_price']) ? $row['price'] : $row['flash_sale_price'] ?></span>
                                <?php if (!empty($row['market_price'])): ?>
                                    <span style="font-size: 13px;color: #999;text-decoration: line-through;">￥<?php echo $row['market_price']; ?></span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="commodity-num" style="font-size: 14px; margin-right: 2px"><?php echo isset($row['amount']) ? $row['amount'] : 1; ?></span>
                        </td>
                        <td style="width: 128px">
                            <?php if ($row['is_point']): ?>
                                <span class="total_price" style="color: red;"><?php echo $row['price'] < 1 ? 1 : $row['price']; ?></span><span style="color: red; font-size: 14px; margin-right: 2px">积分</span>
                            <?php else: ?>
                                <span style="color: red; font-size: 14px; margin-right: 2px">¥</span><span class="total_price" style="color: red;"><?php echo sprintf('%.2f', (empty($row['flash_sale_price']) ? $row['price'] : $row['flash_sale_price']) * (isset($row['amount']) ? $row['amount'] : 1)); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <table style=" width: 100%">
                    <tbody style="background-color: #f2f7ff;">
                    <tr>
                        <td style="width: 50%; border: 1px solid #fff">
                            <div>
                                <p>给卖家留言：</p>
                                <textarea id="buyer_message"></textarea>
                            </div>
                        </td>
                        <td style="text-align: left; border: 1px solid #fff">
                            <p style="padding: 5px 0; border-bottom: 1px solid #fff">配送方式:  普通配送快递 </p>
                            <p style="padding: 5px 0;">邮费: <span class="freight"></span></p>
                            <?php if (!$settlement[0]['is_point']): ?>
                            <p style="border-color: #fff; border-width: 1px 0; border-style: solid">商品优惠:
                                <select style="margin: 10px" id="discount_coupon">
                                    <option>不使用优惠券</option>
                                    <?php foreach ($discount as $row) : ?>
                                    <option value="<?php echo $row['id']; ?>" data-condition="<?php echo $row['condition']; ?>" data-privilege="<?php echo $row['privilege']; ?>" ><?php echo $row['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </p>
                            <?php endif; ?>
                            <p>发货时间:  卖家承诺订单在买家付款后，72小时内发货</p>
                        </td>
                    </tr>
                    <tr style="height: 80px; border: 1px solid #fff;">
                        <td></td>
                        <?php if ($settlement[0]['is_point']): ?>
                        <td class="pull-right" style="padding-right: 30px">
                            <span>店铺合计：</span>
                            <span><?php echo $settlement[0]['price'] < 1 ? 1 : $settlement[0]['price']; ?>积分</span>
                        </td>
                        <?php else: ?>
                        <td class="pull-right" style="padding-right: 30px" id="shop_detail"></td>
                        <?php endif; ?>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (!$settlement[0]['is_point']): ?>
        <div class="row-fluid">
            <div class="span12"  style="border-bottom: 2px solid #aaa; padding-bottom: 10px">
                <strong>请选择支付方式:</strong>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <ul id="orderPayType">
                    <?php if (isset($payments) && !empty($payments)) : ?>
                    <?php foreach ($payments as $key => $payment) : ?>
                    <?php if ($payment['value'] != jys_system_code::PAYMENT_POINTPAY && $payment['value'] != jys_system_code::PAYMENT_INTEGRAL_INDIANA && $payment['value'] != jys_system_code::PAYMENT_INTEGRAL_SWEEPSTAKES && $payment['value'] == 1): ?>
                    <li>
                        <input style="margin: 0 4px" type="radio" value="<?php echo $payment['value']; ?>" name="payType" <?php echo ($key == 0) ? 'checked' : NULL; ?>>
                        <span><?php echo $payment['name']; ?></span>
                    </li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
        <div class="pay_money" style="margin-top: 40px;">
            <div class="row-fluid" id="order_info">
            </div>
            <div class="row-fluid" style="margin-top: 25px">
                <div class="span8"></div>
                <div class="span2">
                    <?php if ($settlement[0]['is_point']): ?>
                    <span class="pull-right"><a href="<?php echo site_url('commodity/index/'.$settlement[0]['id']) ?>">返回商品详情页</a></span>
                    <?php else: ?>
                    <span class="pull-right"><a href="<?php echo site_url('shopping_cart'); ?>">返回购物车</a></span>
                    <?php endif; ?>
                </div>
                <div class="span2">
                    <button class="btn btn-2 btn-link pull-right" id="submit-order" style="padding:4px 18px">提交订单</button>
                </div>
            </div>
        </div>
    </div>
       <!-- Address Modal -->
     <div class="modal fade" id="address_modal" tabindex="-1" role="dialog" aria-labelledby="addressModal" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><span id="add_or_update_address_title" data-id="0">新增</span>收货地址</h4>
                    </div>
                    <div class="modal-body">
                        <div id="receiving_info"  class="model_box">

                            <div class="control-group">
                                <label class="prev_label">所在地区<span style="color: red; margin-left: 5px">*</span></label>
                                <select id="consignee_province_select" class="border_radius" style="width: 180px; border-radius: 0" onchange="searchNextLevel(this)">
                                    <option>--省--</option>
                                </select>
                                <select id="consignee_city_select" class="border_radius" style="width: 180px; border-radius: 0" onchange="searchNextLevel(this)">
                                    <option>--市--</option>
                                </select>
                                <select id="consignee_district_select" class="border_radius" style="width: 180px; border-radius: 0" onchange="searchNextLevel(this)">
                                    <option>--区--</option>
                                </select>
                                <span id="consignee_district_error" class="help-inline error"></span>
                            </div>
                            <div class="control-group">
                                <label class="prev_label">详细地址<span style="color: red; margin-left: 5px">*</span></label>
                                <textarea class="border_radius" id="consignee_address" placeholder="建议如实填写详细收货地址"></textarea>
                                <span id="consignee_address_error" class="help-inline error"></span>
                            </div>
                            <div class="control-group">
                                <label class="prev_label">收货人姓名<span style="color: red; margin-left: 5px">*</span></label>
                                <input class="input_text" id="consignee_name" type="text" placeholder="长度不超过25个字">
                                <span id="consignee_name_error" class="help-inline error"></span>
                            </div>
                            <div class="control-group">
                                <label class="prev_label">手机号码<span style="color: red; margin-left: 5px">*</span></label>
                                <input class="input_text" id="consignee_phone" type="tel" >
                                <span id="consignee_phone_error" class="help-inline error"></span>
                            </div>
                            <div class="control-group">
                                <input id="consignee_default_addr" type="checkbox" style="margin: 0">
                                <label style="display: inline-block; margin-left: 5px; color: #117d94;" for="consignee_default_addr">设置为默认收货地址</label>
                            </div>
                        </div>
                    </div>
                    <div style="margin: 10px 30px" class="pull-right">
                        <a type="button" class="btn btn-link" style="padding: 2px 12px" data-dismiss="modal">取消</a>
                        <a type="button" class="btn btn-2 btn-link" style="padding: 2px 12px" id="save_address">保存</a>
                    </div>
                </div>
            </div>
        </div>
    <!--地址列表模版-->
    <script type="text/html" id="address_item_tpl">
        <% for (var i = 0; i < list.length; i++) { %>
        <%
        if (list[i].default == 1) {
            is_selected = "selected";
        }else {
            is_selected = "";
        }
        if (list[i].district == null ) {
            list[i].district = "";
            list[i].district_code = "";
        }
        %>
        <div class="span3 order-address <%:=is_selected%>" style="margin-left: 0;" data-address-id="<%:=list[i].id%>" data-district-code="<%:=list[i].district_code%>">
            <div class="address-bg address_item_default" data-default="<%:=list[i].default%>">
                <p class="address_item_city" style="margin: 5px 0 0 0;font-size: 10px; border-bottom: 1px solid #eee; width: 210px;" data-province="<%:=list[i].province%>" data-province-code="<%:=list[i].province_code%>" data-city="<%:=list[i].city%>" data-city-code="<%:=list[i].city_code%>" data-district="<%:=list[i].district%>" data-district-code="<%:=list[i].district_code%>" data-name="<%:=list[i].name%>"><%:=list[i].province%> <%:=list[i].city%> (<%:=list[i].name%> 收)</p>
                <span class="address_item_address" style="font-size: 13px" data-address="<%:=list[i].address%>"><%:=list[i].district%> <%:=list[i].address%></span>
                <span class="address_item_phone" style="margin: 0;font-size: 10px; display: block" data-phone="<%:=list[i].phone%>"><%:=list[i].phone%></span>
                <a style="font-size: 13px; display: block; width: 30px; color: #117d94; margin-top: -5px" class="edit_address" data-id="<%:=list[i].id%>">修改</a>
            </div>
        </div>
        <%}%>
    </script>
    <!--结算信息模版-->
    <script type="text/html" id="order_info_tpl">
        <div class="span8 offset4" style="border: 2px solid #117d94; padding: 10px 0 10px 20px">
            <p id="actual_payment"></p>
            <p>寄送至: <%:=data.province%> <%:=data.city%> <%:=data.district%> <%:=data.address%></p>
            <p>收货人: <%:=data.name%> <%:=data.phone%></p>
        </div>
    </script>
</header>