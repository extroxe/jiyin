<header class="container">
    <div class="row-fluid">
        <div style="height: 100px; float: left;">
            <div id="logo">
                <h2>
                    <a href="<?php echo site_url(); ?>" style="text-decoration: none;">
                        <img style="width: 251px" src="<?=site_url('source/img/u2.png'); ?>">
                    </a>
                </h2>
            </div>
        </div>
        <div class="input_search">
            <div class="form-search">
                <input type="text" class="input-medium" id="key_words" placeholder="请输入关键字搜索" value="">
                <a class="btn btn-link btn-3" id="search-btn-post" target="_blank">进入贴吧</a>
            </div>
        </div>
    </div>
</header>

<div id="page-content" style="<?php echo empty($shopping_carts) ? '' : 'display:block;' ?>" class="home-page">
    <div class="container">
        <div class="row-fluid">
            <div class="personal_info">
                <div class="personal_box" style="padding: 16px 12px">
                    <span>个人信息</span>
                    <div class="head_portrait" style="margin-top: 10px">
                        <img src="<?=site_url($user['avatar_path']); ?>">
                        <div class="personal-detail">
                            <div class="name"><?php echo $user['nickname']; ?></div>
                            <div class="gender"><?php echo $user['gender'] == 1 ? '男' : '女'; ?></div>
                            <div class="fans">粉丝：<a href="<?php echo site_url('my_city/home/my_fans'); ?>"><?php echo $fans_num; ?></a></div>
                            <div class="follow">关注：<a href="<?php echo site_url('my_city/home/my_follow'); ?>"><?php echo $focus_num; ?></a></div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="menu">
                    <ul>
                        <li class="menu-item <?php echo $right_side == 'my_post' ? 'active' : ''; ?>">
                            <a href="<?php echo site_url('my_city/home'); ?>">我的帖子</a>
                        </li>
                        <li class="menu-item <?php echo $right_side == 'my_follow' ? 'active' : ''; ?>">
                            <a href="<?php echo site_url('my_city/home/my_follow'); ?>">我的关注</a>
                        </li>
                        <li class="menu-item <?php echo $right_side == 'my_fans' ? 'active' : ''; ?>">
                            <a href="<?php echo site_url('my_city/home/my_fans'); ?>">我的粉丝</a>
                        </li>
                        <li class="menu-item <?php echo $right_side == 'my_collection' ? 'active' : ''; ?>">
                            <a href="<?php echo site_url('my_city/home/my_collection'); ?>">我的收藏</a>
                        </li>
                        <li class="menu-item <?php echo $right_side == 'my_message' ? 'active' : ''; ?>">
                            <a href="<?php echo site_url('my_city/home/my_message'); ?>">站内信</a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php
                $this->load->view($right_side);
            ?>
        </div>
    </div>
</div>