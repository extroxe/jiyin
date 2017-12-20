<div id="page-content" class="home-page">
    <div class="container">
        <div class="row-fluid">
            <div class="span12">
                <ul class="title">
                    <li>订单详情</li>
                </ul>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <ul class="breadcrumb">
                    <li><a href="<?php echo site_url(); ?>">商城首頁</a> <span class="divider"> > </span></li>
                    <li><a href="<?php echo site_url('order/order_list'); ?>">订单中心</a> <span class="divider"> > </span></li>
                    <li class="active">订单详情</li>
                </ul>
            </div>
        </div>
        <div class="row-fluid" style="display: none;">
            <div class="span12">
                <div id="progress_bar">
                    <ul>
                        <li>
                            <div style="width: 100px; display: inline-block">
                                <p class="operate">提交订单</p>
                                <?php if ($order['status_id'] >= jys_system_code::ORDER_STATUS_NOT_PAID): ?>
                                <span class="operate_img fa fa-file-text-o active" aria-hidden="true"></span>
                                <?php endif; ?>
                                <p class="current_time"><?php echo $order['create_time']; ?></p>
                            </div>
                            <div style="display: inline-block; vertical-align: top; margin-left: 20px">
                                <span style="font-size: 19px; color: #117d94">· · · · · · · · · · > </span>
                            </div>
                        </li>
                        <li>
                            <div style="width: 100px; display: inline-block">
                                <p class="operate">付款成功</p>
                                <?php if ($order['status_id'] >= jys_system_code::ORDER_STATUS_PAID): ?>
                                <span class="operate_img fa fa-money active" aria-hidden="true"></span>
                                <?php else: ?>
                                <span class="operate_img fa fa-money" aria-hidden="true"></span>
                                <?php endif; ?>
                                <p class="current_time"><?php echo $order['payment_time']; ?></p>
                            </div>
                            <div style="display: inline-block; vertical-align: top; margin-left: 20px">
                                <span style="font-size: 19px; color: #117d94">· · · · · · · · · · > </span>
                            </div>
                        </li>
                        <li>
                            <div style="width: 100px; display: inline-block">
                                <p class="operate">卖家发货</p>
                                <?php if ($order['status_id'] >= jys_system_code::ORDER_STATUS_DELIVERED): ?>
                                <span class="operate_img fa fa-truck active" aria-hidden="true"></span>
                                <?php else: ?>
                                <span class="operate_img fa fa-truck" aria-hidden="true"></span>
                                <?php endif; ?>
                                <p class="current_time"><?php echo $order['delivered_time']; ?></p>
                            </div>
                            <div style="display: inline-block; vertical-align: top; margin-left: 20px">
                                <span style="font-size: 19px">· · · · · · · · · · > </span>
                            </div>
                        </li>
                        <?php if ($order['status_id'] == jys_system_code::ORDER_STATUS_SENT_BACK): ?>
                        <li>
                            <div style="width: 100px; display: inline-block">
                                <p class="operate">买家寄回</p>
                                <?php if ($order['status_id'] >= jys_system_code::ORDER_STATUS_SENT_BACK): ?>
                                    <span class="operate_img fa fa-truck active" aria-hidden="true"></span>
                                <?php else: ?>
                                    <span class="operate_img fa fa-truck" aria-hidden="true"></span>
                                <?php endif; ?>
                                <p class="current_time"><?php echo $order['delivered_time']; ?></p>
                            </div>
                            <div style="display: inline-block; vertical-align: top; margin-left: 20px">
                                <span style="font-size: 19px">· · · · · · · · · · > </span>
                            </div>
                        </li>
                        <?php endif; ?>
                        <li>
                            <div style="width: 100px; display: inline-block">
                                <p class="operate">订单完成</p>
                                <?php if ($order['status_id'] >= jys_system_code::ORDER_STATUS_FINISHED): ?>
                                <span class="operate_img fa fa-check-square-o active" aria-hidden="true"></span>
                                <?php else: ?>
                                <span class="operate_img fa fa-check-square-o" aria-hidden="true"></span>
                                <?php endif; ?>
                                <p class="current_time"><?php echo $order['finnished_time']; ?></p>
                            </div>
                            <div style="display: inline-block; vertical-align: top; margin-left: 20px">
                                <span style="font-size: 19px">· · · · · · · · · · > </span>
                            </div>
                        </li>
                        <!--<li>
                            <div style="width: 100px; display: inline-block">
                                <p class="operate">寄回产品</p>
                                <span class="operate_img fa fa-truck" style="font-size: 30px; color: #117d94" aria-hidden="true"></span>
                                <p class="current_time"></p>
                            </div>
                            <div style="display: inline-block; vertical-align: top; margin-left: 20px">
                                <span style="line-height: 140px;">. . . . . . . . . . . . > </span>
                            </div>
                        </li>
                        <li>
                            <div style="width: 100px; display: inline-block">
                                <p class="operate">生成报告</p>
                                <span class="operate_img fa fa-file-text-o" style="font-size: 30px; color: #117d94" aria-hidden="true"></span>
                                <p class="current_time"></p>
                            </div>
                            <div style="display: inline-block; vertical-align: top; margin-left: 20px">
                                <span style="line-height: 140px;">. . . . . . . . . . . . > </span>
                            </div>
                        </li>-->
                        <li style="width: 100px">
                            <div style="width: 100px; display: inline-block">
                                <p class="operate">评价</p>
                                <span class="operate_img fa fa-pencil-square-o" aria-hidden="true"></span>
                                <p class="current_time"></p>
                            </div>

                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row-fluid" id="receving_info">
            <div class="span12">
                <div class="recving_info_list">
                    <ul>
                        <li>
                            <p style="color: #117d94">收货人信息</p>
                            <label >收货人：</label><span><?php echo $order['address']['name']; ?></php></span><br>
                            <div  class="add">
                                <label>地址：</label>
                                <p><?php echo $order['address']['province'].' '.$order['address']['city'].' '.$order['address']['district'].' '.$order['address']['address']; ?></p>
                            </div>
                            <br>
                            <label >手机号码：</label><span><?php echo $order['address']['phone']; ?></span>
                        </li>
                        <li>
                            <p style="color: #117d94">配送信息</p>
                            <label >配送方式：</label><span><?php echo $order['express_company_name'] ? $order['express_company_name'] : '普通快递' ; ?></span><br>
                            <label >运费：</label><span>0</span>
                        </li>
                        <li>
                            <p style="color: #117d94">付款信息</p>
                            <label >付款方式：</label><span><?php echo $order['payment_type_name']; ?></span><br>
                            <?php if (!is_null($order['payment_time'])) :?>
                            <label >付款时间：</label><span><?php echo $order['payment_time'];?></span><br>
                            <?php endif;?>
                            <?php if (isset($order['payment_id']) && $order['payment_id'] != 4): ?>
                                <label >订单总额：</label><span>¥<?php echo sprintf('%.2f', $order['total_price']); ?></span><br>
                                <label >应支付金额：</label><span>¥<?php echo sprintf('%.2f', ($order['total_price']-$order['discount_coupon_privilege']));?></span><br>
                                <label >优惠券抵扣：</label><span>¥<?php echo sprintf('%.2f', $order['discount_coupon_privilege']); ?></span><br>
                            <?php elseif (isset($order['payment_id']) && $order['payment_id'] == 4): ?>
                                <label >订单积分：</label><span><?php echo sprintf('%.2f', $order['total_price']); ?>积分</span><br>
                                <label >应支付积分：</label><span><?php echo sprintf('%.2f', ($order['total_price']));?>积分</span><br>
                            <?php endif;?>
                            <?php
                            $total_points = 0;
                            if (intval($order['payment_id']) != jys_system_code::PAYMENT_POINTPAY) {
                                foreach ($order['sub_orders'] as $sub_order){
                                    $total_points += intval($sub_order['points']);
                                }
                            }
                            ?>
                            <label >可获得积分：</label><span><?php echo $total_points; ?></span>
                        </li>
                        <li>
                            <p style="color: #117d94">订单状态</p>
                            <label >下单时间：</label><span><?php echo $order['create_time'];?></span><br>
                            <label >订单状态：</label><span><?php echo $order['order_status_name'];?></span><br>
                            <?php if (intval($order['status_id']) == jys_system_code::ORDER_STATUS_NOT_PAID):?>
                            <a href="<?php echo site_url('order/wechat_pay').'/'.$order['id'];?>" class="btn btn-link pay-directly">立即支付</a>
                            <?php endif;?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <table class="cart_table" style="width: 100%; margin-top: 30px">
                    <thead style="line-height: 50px">
                    <tr>
                        <th style="width: 40%; text-align: left">名称</th>
                        <th>交易状态</th>
                        <th>
                            单价
                            <?php
                                if ($order['payment_id'] != 4){
                                    echo "(元)";
                                }elseif ($order['payment_id'] == 4){
                                    echo "(积分)";
                                }
                            ?>
                        </th>
                        <th>数量</th>
                        <th>
                            实付
                            <?php
                                if ($order['payment_id'] != 4){
                                    echo "(元)";
                                }elseif ($order['payment_id'] == 4){
                                    echo "(积分)";
                                }
                            ?>
                        </th>
                        <th>商品操作</th>
                    </tr>
                    </thead>
                    <tbody style="border: 1px solid #7AC5CD">
                    <tr>
                        <td style="color: #444;padding-left: 10px">订单编号：<span><?php echo $order['number']; ?></span></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php foreach ($order['sub_orders'] as $sub_order) : ?>
                    <tr>
                        <td>
                            <div class="cart_img">
                                <?php if (intval($sub_order['type_id']) != 3):?>
                                <input type="checkbox" data-sub-order-id="<?php echo $sub_order['id']; ?>" data-sub-order-type-id="<?php echo $sub_order['type_id'];?>">
                                <?php else:?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                <?php endif;?>
                                <a href="<?php echo site_url('commodity/index/'.$sub_order['commodity_id'].'/'.$sub_order['commodity_specification_id']); ?>">
                                    <img src="<?php echo site_url($sub_order['thumbnail_path']); ?>">
                                </a>
                            </div>
                            <div style="display: inline-block;">
                                <a class="commodity-name" href="<?php echo site_url('commodity/index/'.$sub_order['commodity_id'].'/'.$sub_order['commodity_specification_id']); ?>"><?php echo $sub_order['commodity_name']; ?>
                                    <span class="label label-primary"><?php echo $sub_order['commodity_center_name']; ?>
                                        <?php if(!empty($sub_order['package_type_name'])):?>
                                            - <?php echo $sub_order['package_type_name']; ?>
                                        <?php endif;?>
                                </span>
                                </a>
                                <?php if (intval($sub_order['type_id']) == 1):?>
                                <span class="label label-success">基因商品</span>
                                <?php elseif (intval($sub_order['type_id']) == 2):?>
                                <span class="label label-info">实物商品</span>
                                <?php elseif (intval($sub_order['type_id']) == 3):?>
                                <span class="label label-warning">会员商品</span>
                                <?php endif;?>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($sub_order['express_number']) && !empty($sub_order['express_company_id'])): ?>
                            <p style="margin: 0; font-size: 13px; font-family: '微软雅黑'">已寄回</p>
                            <?php else: ?>
                            <p style="margin: 0; font-size: 13px; font-family: '微软雅黑'"><?php echo $order['order_status_name']; ?></p>
                            <?php endif; ?>
                        </td>
                        <?php if ($sub_order['is_point'] == 0) : ?>
                        <td>
                            <span style="font-size: 14px; margin-right: 2px">¥</span><span class="price"><?php echo sprintf('%.2f', $sub_order['price']); ?></span>
                        </td>
                        <?php elseif ($sub_order['is_point'] == 1):?>
                        <td>
                            <span style="font-size: 14px; margin-right: 2px"></span><span class="price"><?php echo sprintf('%.2f', $sub_order['price']); ?></span>
                        </td>
                        <?php endif;?>
                        <td>
                            <span style="font-size: 14px; margin-right: 2px"><?php echo $sub_order['amount']; ?></span>
                        </td>
                        <?php if ($sub_order['is_point'] == 0) : ?>
                        <td style="width: 128px">
                            <span style="color: red; font-size: 14px; margin-right: 2px">¥</span><span class="total_price" style="color: red;"><?php echo sprintf('%.2f', $sub_order['total_price']); ?></span>
                        </td>
                        <?php elseif ($sub_order['is_point'] == 1):?>
                        <td style="width: 128px">
                            <span style="color: red; font-size: 14px; margin-right: 2px"></span><span class="total_price" style="color: red;"><?php echo sprintf('%.2f', $sub_order['total_price']); ?></span>
                        </td>
                        <?php endif;?>
                        <?php if (($order['status_id'] == 20) && ($order['payment_id'] != 4)): ?>
                        <td>
                            <a class="delect_click" data-id="<?php echo $order['id']; ?>" href="javascript:void(0)" style="font-family: '微软雅黑'; font-size: 13px">退款</a>
                        </td>
                        <?php elseif(($order['status_id'] == 60) && ($order['payment_id'] != 4)): ?>
                        <td>
                            <a class="evaluate_click" data-id="<?php echo $sub_order['id']; ?>" href="javascript:void(0)" style="font-family: '微软雅黑'; font-size: 13px">评价</a>
                        </td>
                        <?php elseif($order['payment_id'] == 4): ?>
                        <td>
                            <a href="javascript:void(0)" style="font-family: '微软雅黑'; font-size: 13px">已兑换</a>
                        </td>
                        <?php else: ?>
                        <td></td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (intval($order['sub_orders'][0]['type_id']) != 3 && $order['status_id'] == Jys_system_code::ORDER_STATUS_DELIVERED):?>
                <?php if (isset($sent_back_flag) && $sent_back_flag): ?>
                    <button class="btn btn-2 btn-link pull-right" style="margin: 10px 0; padding-right: 10px" id="send_back_btn">已 寄 回</button>
                <?php else: ?>
                    <button class="btn btn-2 btn-link pull-right" style="margin: 10px 0; padding-right: 10px" id="send_back_btn">寄 回</button>
                <?php endif; ?>
                <?php endif;?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12" style=" background-color: #f2f7ff;">
                <div id="bottom_box">
                    <?php if ($order['payment_id'] != 4): ?>
                    <label>商品总额：</label><p>¥<?php echo sprintf('%.2f', $order['total_price']); ?></p><br>
                    <label>返&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;现：</label><p>- ¥<?php echo sprintf('%.2f', $order['discount_coupon_privilege']); ?></p><br>
                    <label>运&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;费：</label><p>+ ¥0.00</p><br>
                    <label>实付总额：</label><p style="color: red; font-size: 17px">¥<?php echo sprintf('%.2f', ($order['total_price']-$order['discount_coupon_privilege'])); ?></p>
                    <?php elseif ($order['payment_id'] == 4): ?>
                    <label>总积分：</label><p><?php echo sprintf('%.2f', $order['total_price']); ?>积分</p><br>
                    <label>实付积分：</label><p style="color: red; font-size: 17px; width: 90px;"><?php echo sprintf('%.2f', ($order['total_price'])); ?>积分</p>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 寄回信息填写模态框 -->
<div class="modal fade" style="display: none" id="send_back_modal" tabindex="-1" role="dialog" aria-labelledby="sendBackModal" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">寄回信息</h4>
            </div>
            <div class="modal-body">
                <div class="model_box">
                    <div class="control-group">
                        <label for="" >物流公司 <span style="color:red">*</span></label>
                        <select id="express_company">
                            <option value="">--请选择--</option>
                            <option value="1">顺丰</option>
                            <option value="2">百世快递</option>
                            <option value="3">中通</option>
                            <option value="4">申通</option>
                            <option value="5">圆通</option>
                            <option value="6">韵达</option>
                            <option value="7">邮政平邮</option>
                            <option value="8">EMS</option>
                            <option value="9">天天</option>
                            <option value="11">全峰</option>
                            <option value="12">国通</option>
                            <option value="13">优速</option>
                            <option value="15">快捷</option>
                            <option value="16">亚马逊</option>
                        </select>
                    </div>
                    <div class="control-group">
                        <label for="">运单号 <span style="color:red">*</span></label>
                        <input type="text" class="form-control" id="express_number">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a type="button" class="btn btn-link" data-dismiss="modal">取消</a>
                <a type="button" id="save_send_back_info" class="btn btn-2" style="padding-right: 12px;">确定</a>
            </div>
        </div>
    </div>
</div>