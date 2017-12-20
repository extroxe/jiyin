<!--home-page-->
<div id="page-content" class="home-page">
    <div class="container">
        <div class="row-fluid">
            <div class="span12">
                <div class="logo">
                    <img src="<?php echo base_url('/source/img/u2.png');?>">
                    <span>收银台</span>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <div id="order_type">
                    <ul>
                        <li>
                            <label>订单编号：</label><span><?php echo $order['number'];?></span>
                        </li>
                        <li>
                            <label>支付方式：</label><span>微信支付</span>
                        </li>
                    </ul>
                    <span class="pay_money">应付金额：¥<?php echo $order['payment_amount']?></span>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <div class="tabbable">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab1" data-toggle="tab">微信支付</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab1">
                            <img class="wePay_logo" src="<?php echo base_url('/source/img/wechat_pay_logo.png');?>">
                            <div class="code_box">
                                <img style="display: block" src="<?php echo site_url("order/show_wechat_pay_qrcode/{$order['id']}");?>">
                                <img src="<?php echo base_url('/source/img/wechat_pay_explain.png');?>">
                                <a href="<?php echo site_url('order/order_list'); ?>" class=" btn-success pay-success">我已支付完成<span id="timer"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>