<div id="page-content" class="home-page">
    <div class="container">
        <div class="row-fluid">
            <div class="span12">
                <div id="check_img">
                    <span class="fa fa-check-circle"></span>
                    <label id="hinter_info" style="font-size: 20px"><?php echo $is_point_flag == 0 ? '支付成功' : '兑换成功'; ?></label>
                    <span id="timer">3</span>
                    <span class="turn_to_orderdetail">秒后跳转至
                        <a href="<?php echo site_url('order/order_list'); ?>">订单详情</a>页
                    </span>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <div class="text_box">
                    <p>一个人，一座健康城</p>
                </div>
            </div>
        </div>
    </div>
</div>