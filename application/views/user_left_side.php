<div id="person_info_shange" style=" width: 202px; margin-left: 0; float: left">
    <div class="personal_list">
        <p ><span class="fa fa-user" style="color: #777"></span>账户管理</p>
        <ul id="_menu">
            <li><a href="<?php echo site_url('user/user_center/personal_info'); ?>" class="<?php echo $main_content == 'personal_info' ? 'active' : ''; ?>">编辑个人信息</a></li>
            <li><a href="<?php echo site_url('user/user_center/change_password'); ?>" class="<?php echo $main_content == 'change_password' ? 'active' : ''; ?>">修改密码</a></li>
            <li><a href="<?php echo site_url('user/user_center/account_contact'); ?>" class="<?php echo $main_content == 'account_contact' ? 'active' : ''; ?>">账户关联</a></li>
            <li><a href="<?php echo site_url('user/user_center/my_report'); ?>" class="<?php echo $main_content == 'my_report' ? 'active' : ''; ?>">我的报告</a></li>
            <li><a href="<?php echo site_url('user/user_center/receiving_info'); ?>" class="<?php echo $main_content == 'receiving_info' ? 'active' : ''; ?>">收货信息</a></li>
            <li><a href="<?php echo site_url('user/user_center/integral_detail'); ?>" class="<?php echo $main_content == 'integral_detail' ? 'active' : ''; ?>">积分详情</a></li>
            <li><a href="<?php echo site_url('user/user_center/my_indiana'); ?>" class="<?php echo $main_content == 'my_indiana' ? 'active' : ''; ?>">我的夺宝</a></li>
            <li><a href="<?php echo site_url('user/user_center/my_sweepstake'); ?>" class="<?php echo $main_content == 'my_sweepstake' ? 'active' : ''; ?>">我的抽奖</a></li>
            <li><a href="<?php echo site_url('user/user_center/commodity_collection'); ?>" class="<?php echo $main_content == 'commodity_collection' ? 'active' : ''; ?>">我的收藏</a></li>
            <li><a href="<?php echo site_url('user/user_center/my_discount'); ?>" class="<?php echo $main_content == 'my_discount' ? 'active' : ''; ?>">我的优惠券</a></li>
            <li><a href="<?php echo site_url('user/user_center/member_center'); ?>" class="<?php echo $main_content == 'member_center' ? 'active' : ''; ?>">会员中心</a></li>
            <li ><a id="get_family_info" href="<?php echo site_url('user/user_center/my_family'); ?>" class="<?php echo $main_content == 'my_family' ? 'active' : ''; ?>">我的家族</a></li>
        </ul>
    </div>
</div>