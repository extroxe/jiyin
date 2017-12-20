<div id="integral_detail"  class="model_box">
    <div class="control-group" style=" border: none">
        <h5>积分详情</h5>
    </div>
    <div class="control-group">
        <label style="display: inline-block; margin-right: 20px">当前积分：</label><span><?php echo $user['current_point']; ?></span>
        <label style="display: inline-block; margin-right: 20px; margin-left: 100px">总积分：</label><span><?php echo $user['total_point']; ?></span>
        <div class="integral-activity">
            <div class="integral-activity-item">
                <a href="<?php echo site_url('integral_indiana') ?>" class="item-img-1">
                    <img src="<?php echo site_url('source/mobile/img/u1010.png') ?>" alt="" width="80" height="80">
                </a>
                <span>积分夺宝</span>
            </div>
            <div class="integral-activity-item">
                <a href="<?php echo site_url('sweepstakes_commodity/integral_draw') ?>" class="item-img-2">
                    <img src="<?php echo site_url('source/mobile/img/u1012.png') ?>" alt="" width="80" height="80">
                </a>
                <span>积分抽奖</span>
            </div>
        </div>
    </div>
</div>