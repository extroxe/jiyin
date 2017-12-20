
<div id="page-content" class="home-page">
    <div class="container">
        <div class="row-fluid">
            <div class="span12">
                <div id="order_list_box">
                    <div class="row-fluid">
                        <div class="span12">
                            <ul id="operation">
                                <li class="active"  id="order_list_all" data-status=""><a href="javascript:void(0)">所有订单<span style="font-size: 10px" id="all-order"></span></a></li>
                                <li data-status="10"  id="order_list_not_paid"><a href="javascript:void(0)">待付款<span style="font-size: 10px" id="not-paid-order"></span></a></li>
                                <li data-status="10"  id="order_list_paid"><a href="javascript:void(0)">待发货<span style="font-size: 10px" id="paid-order"></span></a></li>
                                <li data-status="10"  id="order_list_delivered"><a href="javascript:void(0)">待收货<span style="font-size: 10px" id="delivered-order"></span></a></li>
                                <li data-status="10"  id="order_list_sentback"><a href="javascript:void(0)">已寄回<span style="font-size: 10px" id="sentback-order"></span></a></li>
                                <li data-status="30"  id="order_list_assaying"><a href="javascript:void(0)">检测中<span style="font-size: 10px" id="assaying-order"></span></a></li>
                                <li data-status="60" id="order_list_finished"><a href="javascript:void(0)">已完成<span style="font-size: 10px" id="finished-order"></span></a></li>
                                <li data-status="" id="order_list_not_refunding"><a href="javascript:void(0)">退款中<span style="font-size: 10px" id="refunding-order"></span></a></li>
                                <li data-status="" id="order_list_not_refunded"><a href="javascript:void(0)">已退款<span style="font-size: 10px" id="refunded-order"></span></a></li>
                                <li data-status="" id="order_list_can_evaluate"><a href="javascript:void(0)">待评价<span style="font-size: 10px" id="can-evaluate-order"></span></a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="row-fluid">
                        <div class="span12">
                            <table class="cart_table" id="order_list_container" style="width: 100%">

                            </table>
                            <div id="empty_order">暂无相关信息！！！</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <div class="pagination pull-right">
                    <ul>
                        <li><a href="javascript:void(0)" id="Prev_page">上一页</a></li>
                        <li>共<span id="total_pages" style="color: #117d94"></span>页 &nbsp;
                            第<input id="page_num" value="1" type="text">页
                            <a href="javascript:void(0)" style="cursor: pointer" id="jump_to_page">跳转</a>
                        </li>
                        <li><a href="javascript:void(0)" id="Next_page">下一页</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="modal fade" id="refundModal" style="display: none" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">退款信息</h4>
                    </div>
                    <div class="modal-body">
                        <div class="control-group">
                            <label class="prev_label">退款商品<span style="color: red; margin-left: 5px"></span></label>
                            <textarea style="float: inherit; width: 330px; height: 60px;" type="text" id="refund_commodity_name" disabled/>
                            </textarea>
                        </div>
                        <input type="hidden" id="order_commodity_id">
                        <div class="control-group">
                            <label class="prev_label">下单价格<span style="color: red; margin-left: 5px"></span></label>
                            <input style="float: inherit" type="text" id="order_price" disabled/>
                        </div>
                        <div class="control-group">
                            <label class="prev_label">可退款数量<span style="color: red; margin-left: 5px"></span></label>
                            <input style="float: inherit" type="text" id="refund_available" disabled/>
                        </div>
                        <div class="control-group">
                            <label class="prev_label">退款数量<span style="color: red; margin-left: 5px">*</span></label>
                            <input style="float: inherit" type="text" value="" id="refund_amount"/>
                            <span style="color: red; display: none; font-size: 13px;">退款数量超过可退款数量</span>
                        </div>
                        <div class="control-group" id="reason">
                            <label class="prev_label">退款理由<span style="color: red; margin-left: 5px">*</span></label>
                            <select name="" type="" >
                                <option value="">选择退款理由</option>
                                <option value="下错单，重新下单">下错单，重新下单</option>
                                <option value="收货人信息有误">收货人信息有误</option>
                                <option value="送货时间太长">送货时间太长</option>
                                <option value="其他原因">其他原因</option>
                            </select>
                        </div>
<!--                        <div class="control-group" id="enquiry_create_time_container">-->
<!--                            <label class="prev_label">上传照片<span style="color: red; margin-left: 5px">*</span></label>-->
<!--                            <div class="img_upload">-->
<!--                                <p class="imghead upload_img_btn" onclick="$(this).siblings('.previewImg').click()"></p>-->
<!--                                <input type="file" data-commodity-id = '<%:= data[i].commodity_id %>' style="display: none;" class="previewImg">-->
<!--                                    <span class="describle">共-->
<!--                                        <span class="upload_img_num" style="color:#eb3c3f"> 0 </span>张，还可以上传-->
<!--                                        <span class="all_img_num" style="color:#eb3c3f"> 5 </span> 张-->
<!--                                    </span>-->
<!--                            </div>-->
<!--                        </div>-->
                    </div>
                    <div class="modal-footer">
                        <div style="margin: 10px 30px" class="pull-right">
                            <a type="button" class="btn btn-link" href="javascript:void(0)" style="padding: 2px 12px; color: #4ac590" data-dismiss="modal">取消</a>
                            <a type="button" class="btn btn-2 btn-link" href="javascript:void(0)" style="padding: 2px 12px; background-color: #4ac590; color: #fff; border-radius: 0" id="refund">确认</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <!-- 订单列表 -->
    <script type="text/html" id="order_lists_tpl">
        <thead style="line-height: 50px">
        <tr>
            <th style="width: 47%; text-align: left; ">名称</th>
            <th>交易状态</th>
            <th>单价</th>
            <th>数量</th>
            <th>实付款</th>
            <th>商品操作</th>
        </tr>
        </thead>
        <% for (var i = 0; i < data.length; i++) { %>
        <tbody style="border: 1px solid #117d94; margin-bottom: 50px">
            <tr>
                <td style="color: #f1f1f1;padding-left: 10px">订单编号：<span><%:=data[i].number%></span>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><a style="color: #fff;" href="<?php echo site_url('order/detail')?>/<%:=data[i].id%>">订单详情</a></td>
            </tr>
            <!-- 订单子列表 -->
            <% if( data[i].sub_orders == null ){ %>
            <tr class="item_list">
                <td>
                    <div class="cart_img">
                        <img data-original="<?php echo site_url('source/img/indiana_icon.png'); ?>">
                    </div>
                    <div style="display: inline-block;">
                        <a href="javascript:void(0)" style="font-family: '微软雅黑'; font-size: 13px">积分商品</a>
                    </div>
                </td>
                <td>
                    <p style="margin: 0; font-size: 13px; font-family: '微软雅黑'"><%:=data[i].order_status_name%></p>
                    <div class="check_logistics_box" style="height: 41px" data-order-id='<%:=data[i].id%>' data-order-number='<%:=data[i].number%>'>
                        <a href="javascript:void(0)">查看物流</a>
                        <div class="check_logistics" >
                            <div class="_up"></div>
                            <div class="check_logistics_detail" style="min-height: 200px;text-align: center;">
                                <img src="<?=site_url('source/img/loading.gif'); ?>">
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="price"><%:=data[i].total_price%> 积分</span>
                </td>
                <td>
                    <span style="font-size: 14px; margin-right: 2px">1</span>
                </td>
                <td style="width: 128px">
                    <span
                        class="total_price" style="color: red;"><%:=data[i].total_price%> 积分
                    </span>
                </td>
                <td>
                    <a id="cancel_order" data-order-id='<%:=data[i].id%>' data-order-status-id='<%:=data[i].status_id%>' href="javascript:void(0)" style="font-family: '微软雅黑'; font-size: 13px"><%:=data[i].order_operate%></a>
                </td>
            </tr>
            <% }else{
            for (var j = 0; j < data[i].sub_orders.length; j++) { %>
            <tr class="item_list">
                <td>
                    <div class="cart_img">
                        <a href="<?php echo site_url('commodity/index/');?>/<%:=data[i].sub_orders[j].commodity_id%>/<%:=data[i].sub_orders[j].commodity_specification_id%>">
                            <img src="<%=SITE_URL + data[i].sub_orders[j].thumbnail_path%>">
                        </a>
                    </div>
                    <div style="display: inline-block;" style="font-family: '微软雅黑'; font-size: 13px">
                        <a href="<?php echo site_url('commodity/index/');?>/<%:=data[i].sub_orders[j].commodity_id%>/<%:=data[i].sub_orders[j].commodity_specification_id%>"><%:=data[i].sub_orders[j].specification_name%>
                        </a>
                        <span class="label label-success"><%:=data[i].sub_orders[j].type_name%></span>
                    </div>
                </td>
                <td>
                    <p style="margin: 0; font-size: 13px; font-family: '微软雅黑'"><%:=data[i].order_status_name%></p>
                    <div class="check_logistics_box" style="height: 41px" data-order-id='<%:=data[i].id%>' data-order-number='<%:=data[i].number%>'>
                        <a href="javascript:void(0)">查看物流</a>
                        <div class="check_logistics" >
                            <div class="_up"></div>
                            <div class="check_logistics_detail" style="min-height: 200px;text-align: center;">
                                <img src="<?=site_url('source/img/loading.gif'); ?>">
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="price"><%:=data[i].sub_orders[j].price%></span>
                </td>
                <td>
                    <span style="font-size: 14px; margin-right: 2px"><%:=data[i].sub_orders[j].amount%></span>
                </td>
                <td style="width: 128px">
                    <span
                        class="total_price" style="color: red;"><%:=data[i].sub_orders[j].total_prices%>
                    </span>
                </td>
                <td>
                    <% if (data[i].status_id == 60 && data[i].sub_orders[j].commodity_evaluation_id != null) { %>
                    <a id="cancel_order" data-order-id='<%:=data[i].id%>' data-sub-order-id='<%:=data[i].sub_orders[j].id%>' data-order-status-id='<%:=data[i].status_id%>' href="javascript:void(0)" style="font-family: '微软雅黑'; font-size: 13px">已评价</a>
                    <% } else { %>
                    <a id="cancel_order" data-order-id='<%:=data[i].id%>' data-sub-order-id='<%:=data[i].sub_orders[j].id%>' data-commodity-id = "<%:=data[i].sub_orders[j].commodity_id%>" data-order-status-id='<%:=data[i].status_id%>' href="javascript:void(0)" style="font-family: '微软雅黑'; font-size: 13px"><%:=data[i].order_operate%></a>
                    <% } %>
                </td>
            </tr>
            <%}}%>
        </tbody>
        <tbody style="border: 1px solid #fff">
        <tr>
            <td style="height: 20px; background-color: #fff"></td>
        </tr>

        </tbody>
        <tbody style="border: 1px solid #fff">
        <tr>
            <td style="height: 20px; background-color: #fff"></td>
        </tr>

        </tbody>
        <%}%>
        
    </script>
    <!--查看物流-->
    <script type="text/html" id="check_logistics_tpl">
        <p style="font-size: 16px;"><%:= data.express_company_name %>速递:<span><%:= data.express_number %></span></p>
        <ul>
            <% for (var j = data.Traces.length; j > 0; j--) { %>
            <li>
                <p style=""><%:= data.Traces[j].AcceptStation %></p>
                <span><%:= data.Traces[j].AcceptTime %></span>
            </li>
            <% } %>
            <li style="margin-left: 6px;"><a href="<?php echo site_url('order/detail')?>/<%:=data.id%>">查看全部物流信息</a></li>
        </ul>
    </script>
</div>