<?php if (isset($isset_footer) && $isset_footer == FALSE): ?>
<?php else : ?>
<?php if (isset($simple_footer) && $simple_footer) : ?>
<footer id="sign_in" style="margin-top: 20px">
    <div class="container">
        <div class="row-fluid">
            <div class="span12">
                <ul class="foot_label">
                    <li><a href="javascript:void(0)">关于我们</a></li>
                    <li><a href="javascript:void(0)">联系我们</a></li>
                    <li><a href="javascript:void(0)">手机商城</a></li>
                    <li><a href="javascript:void(0)">我的贴吧</a></li>
                    <li><a href="javascript:void(0)">我的城</a></li>
                </ul>
            </div>
        </div>
        <br>

<!--        <div class="copyright">-->
<!--            <div class="container">-->
<!--                <div class="row-fluid">-->
<!--                    <div class="span12" style="color:#aaa; text-align: center">-->
<!--                        Copyright &copy; 2016. All rights reserved.-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->

    </div>
</footer>
<?php else : ?>
<footer style="    min-width: 1140px;">
    <div class="container-fluid" style="background-color: #117d94;">
        <div class="wrap-footer">
            <div class="row-fluid">
                <div class="span4 col-footer footer-1" style=" text-align: center;">
                    <img style="width: 251px" data-original="<?=site_url('source/img/u2.png'); ?>">
                    <div style="color:#69cbd8; font-size:18px; line-height:20px; font-family: '微软雅黑'">赛安医学基因城  </div>
                    <span style="color:#69cbd8; font-size:12px; line-height:40px; font-family: '微软雅黑'">一个人， 一座健康城</span>
                </div>
                <div class="span4 col-footer footer-2">
                    <div class="heading"><h4>服务保证</h4></div>
                    <ul>
                        <li>正品保证</li>
                        <li>7天无理由退换</li>
                        <li>退货返运费</li>
                    </ul>
                </div>
                <div class="span4 col-footer footer-3">
                    <div class="heading"><h4>联系我们</h4></div>
                    <ul>
                        <li><a href="#">
                                <img style="width:25px; margin-right: 10px" data-original="<?=site_url('source/img/email.png'); ?>">
                                <strong>saian@shineschina.com</strong>
                            </a></li>
                        <li><a href="#">
                                <img style="width:20px; margin-right: 10px" data-original="<?=site_url('source/img/phone.png'); ?>">
                                <strong>400-821-0621</strong>
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<!--    <div class="container-fluid" style="background-color: #393939;">-->
<!--        <div class="copyright">-->
<!--            <div class="container-fluid" style="background-color: #444">-->
<!--                <div class="row-fluid">-->
<!--                    <div class="span6" style="color:#aaa">-->
<!--                        Copyright &copy; 2016. All rights reserved.-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
</footer>
<!--<div id="backTop" style="position: fixed;right: 0;bottom: 0">-->
<!--    <a href="#" style="color: red;">回到顶部</a>-->
<!--</div>-->
<?php endif; ?>
<?php endif; ?>