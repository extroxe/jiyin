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
                <input type="text" class="input-medium" id="key_words" placeholder="请输入关键字搜索" value="<?php echo $key_words; ?>">
                <a class="btn btn-link btn-3" id="search-btn-post" target="_blank">搜索贴吧</a>
            </div>
        </div>
    </div>
</header>

<div id="page-content" style="<?php echo empty($shopping_carts) ? '' : 'display:block;' ?>" class="home-page">
    <div class="container">
        <div class="row-fluid">
            <div class="post_bar">
                <div class="personal_info" >
                    <div class="personal_box" style="padding: 10px 12px">
                        <span>我在贴吧</span>
                        <div class="head_portrait" style="margin-top: 10px">
                            <?php if (isset($_SESSION['avatar_path']) && isset($_SESSION['nickname'])): ?>
                                <img src="<?=site_url($_SESSION['avatar_path']); ?>">
                                <span style="margin-left: 10px"><?php echo $_SESSION['nickname'];?></span>
                            <?php else: ?>
                                <img src="<?=site_url('source/img/05.jpg'); ?>">
                                <span style="margin-left: 10px">游客</span>
                            <?php endif;?>
                        </div>
                    </div>
                    <?php if (isset($_SESSION['avatar_path']) && isset($_SESSION['nickname'])): ?>
                        <div class="focused_bar_box" style="padding: 12px">
                            <span style="font-weight: bold; font-family: '黑体'">关注的吧</span>
                            <ul>
                            </ul>
                            <a class="more" id="look_more" href="javascript:void(0)">查看更多</a>
                        </div>
                    <?php else: ?>
                        <span class="tourist">游客请先<a class="sign_in" href="<?php echo site_url('index/sign_in'); ?>">登录</a></span>
                    <?php endif;?>
                </div>
                <div class="post_bar_content">
                    <div class="post_list_box">
                        <h5>搜索结果</h5>
                        <table>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>