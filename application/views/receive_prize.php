<header id="top_title">
    <div class="container">
        <div class="row-fluid top">
            <div>领取奖品</div>
        </div>
        <div class="row-fluid" style="margin-top: 40px">
            <div class="span2">
                <strong>确认收货地址</strong>
            </div>
            <div class="span8"></div>
            <div class="span2">
                <a class=" pull-right" style="color: #117d94" href="##">管理收货地址</a>
            </div>
        </div>
        <div class="row-fluid">
            <div style="border: 1px solid #bbb"></div>
        </div>
        <div class="row-fluid" style="margin-top: 30px;" id="address_list_container">
        </div>
        <br>
        <div class="row-fluid" style="margin-top: -20px">
            <div class="span12">
                <a class="btn btn-2 btn-link" href="#" id="add_address">使用新地址</a>
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
                        <th>单价(元)</th>
                        <th>数量</th>
                        <th>总计</th>

                    </tr>
                    </thead>
                    <tbody style=" background-color: #fbfcff">
                    <?php if (isset($commodity) && !empty($commodity)): ?>
                        <tr class="item_list order-settlement" data-id="<?php echo $commodity['id']; ?>">
                            <td>
                                <div class="cart_img">
                                    <img data-original="<?php echo site_url($commodity['path']); ?>">
                                </div>
                                <div style="display: inline-block;">
                                    <a href="javascript:void(0)" style="font-family: '微软雅黑'; font-size: 13px"><?php echo $commodity['name']; ?></a>
                                </div>
                            </td>
                            <td>
                                <span>¥<?php echo $commodity['price']; ?></span>
                            </td>

                            <td>
                                <span style="font-size: 14px; margin-right: 2px">1</span>
                            </td>
                            <td style="width: 128px">
                                    <span style="color: red; font-size: 14px; margin-right: 2px">¥</span><span class="total_price" style="color: red;"><?php echo sprintf('%.2f', $commodity['price'] * 1); ?></span>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pay_money" style="margin-top: 40px;">
            <div class="row-fluid" id="order_info">
            </div>
            <div class="row-fluid" style="margin-top: 25px">
                <div class="span10"></div>
                <div class="span2">
                    <a class="btn btn-2 btn-link pull-right" data-id="<?php echo isset($id) ? $id : 0;?>" data-commodity-id="<?php echo isset($commodity['id']) ? $commodity['id'] : 0;?>" data-is-indiana="<?php echo isset($is_indiana) && $is_indiana ? 1 : 0; ?>" id="submit-order" style="padding:4px 18px">提交订单</a>
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
        <div class="span3 order-address <%:=is_selected%>" style="margin-left: 0;" data-address-id="<%:=list[i].id%>">
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
            <p style="    color: #ffbe04;font-size: 18px;">地址</p>
            <p>寄送至: <%:=data.province%> <%:=data.city%> <%:=data.district%> <%:=data.address%></p>
            <p>收货人: <%:=data.name%> <%:=data.phone%></p>
        </div>
    </script>
</header>