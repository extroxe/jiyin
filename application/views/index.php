<div id="bing_phone" style="display: none">
    <div class="body-content">
        <div class="tabbar">
            <div class="bind-phone common active">
                <span>1、绑定手机</span>
            </div>
            <div class="sign-up-username common">
                <span>2、注册用户名</span>
            </div>
            <div class="complete common">
                <span>3、完成</span>
            </div>
        </div>
        <div class="content">
            <div class="bind-phone" style="display: block">
                <span style="display: block">
                    <img class="img-icon" src="<?php echo site_url('source/img/icon/mobilephone.png');?>" alt="">
                    <input type="text" id="phone" placeholder="请输入手机号">
                    <span class="hinter">请输入手机号</span>
                </span>
                <span style="display: block">
                    <img class="img-icon safe-code" src="<?php echo site_url('source/img/icon/code.png');?>" alt="">
                    <input type="text" id="code" placeholder="请输入验证码" style="    width: 43%;margin: 10px 0;">
                    <button class="get-code">获取验证码</button>
                </span>
                <button class="check-directly btn-common">立即验证</button>
                <span class="remark">注：一个手机号码只能绑定一个账号，绑定手机不收取任何费用，请您放心使用！</span>
            </div>
            <div class="sign-up-username" style="display: none">
                <span class="parent">
                    <img class="img-icon" src="<?php echo site_url('source/img/icon/people.png');?>" alt="">
                    <input type="text" id="username" placeholder="请输入用户名">
                    <span class="username-hinter hinter">请输入用户名</span>
                </span>
                <span class="parent">
                    <img class="img-icon" src="<?php echo site_url('source/img/icon/lock.png');?>" alt="">
                    <input type="password" id="password" placeholder="设置密码">
                    <span class="password-hinter  hinter">请设置密码</span>
                </span>
                <span class="parent">
                    <img class="img-icon" src="<?php echo site_url('source/img/icon/lock.png');?>" alt="">
                    <input type="password" id="conform_password" placeholder="确认密码">
                    <span class="confirm-hinter1 hinter">请输入确认密码</span>
                    <span class="confirm-hinter2 hinter">两次密码不一致</span>
                </span>
                <button class="sign-confirm btn-common">确认</button>
            </div>
            <div class="complete" style="display: none">
                <img class="success-img" src="<?php echo site_url('source/img/icon/checked.png');?>" alt="">
                手机绑定成功 !
            </div>
        </div>
    </div>
</div>
<div id="page-content" class="home-page" data-is-weixin="<?php echo $is_weixin;?>">
    <div class="container">
        <!-- nav-left -->
        <div style="position: relative">
            <div style="width: 263px; height: 407px; float: left">
                <div class="all-sort-list" style="margin: 0 auto">
                    <div class="item item1" style="background-color: #222;">
                        <h2 style="    font-size: 23px;
    font-family: '微软雅黑';
    font-weight: normal;">所有商品分类</h2>
                    </div>
                    <?php foreach($collection as $row) : ?>
                    <div class="item">
                        <div class="list_nav" style="">
                            <h5 style=" font-family: '微软雅黑'"><?php echo $row['type_name']; ?><span> > </span></h5>
                            <ul>
                                <?php foreach($row['category'] as $key => $category) : ?>
                                <?php if ($key <= 3): ?>
                                <li class="category" data-id="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></li>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="item-list clearfix" style="border: 1px solid #117d94">
                            <div class="subitem">
                                <?php foreach ($row['category'] as $category): ?>
                                <dl class="fore">
                                    <dt><a href="<?php echo site_url('index/search?category='.$category['id'].'&result='.$category['name']); ?>"><?php echo $category['name']; ?></a></dt>
                                    <dd>
                                        <?php foreach($category['children_category'] as $child_category) : ?>
                                        <em><a href="#" class="category" data-id="<?php echo $child_category['id']; ?>"><?php echo $child_category['name']; ?></a></em>
                                        <?php endforeach; ?>
                                    </dd>
                                </dl>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div style="width: 870px; height: 416px;margin-left: 285px;">
                <div class="row-fluid">
                    <div id="myCarousel" class="carousel slide">
                        <ol class="carousel-indicators" style="bottom: 10px;  margin-left: 50%;">
                            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                            <li data-target="#myCarousel" data-slide-to="1"></li>
                            <li data-target="#myCarousel" data-slide-to="2"></li>
                        </ol>
                        <!-- Carousel items -->
                        <div class="carousel-inner">
                            <?php foreach ($banner as $key => $row) : ?>
                            <div class="<?php if ($key == 0){
                                echo 'active';
                            } ?> item">
                                <a href="<?php echo $row['url']; ?>"><img src="<?php echo site_url($row['path']); ?>"></a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- 今日上新 -->
        <?php if (!empty($new_recent)) : ?>
        <div class="row-fluid">
            <div class="span12">
                <div class="totaynew">
                    <img data-original="<?php echo site_url('source/img/new-flag.png'); ?>">
                    <strong style="position: absolute;bottom: 7px;">今日上新</strong>
                </div>
            </div>
        </div>
        <div class="totaynew_commodity" id="today_new" >
            <?php foreach ($new_recent as $row) : ?>
            <div class="box">
                <a href="<?php echo site_url('commodity/index/'.$row['id'].'/'.$row['specification_id']); ?>"><img class="img_detail" data-original="<?php echo site_url($row['path']); ?>"></a>
                <div class="disc">
                    <a href="<?php echo site_url('commodity/index/'.$row['id'].'/'.$row['specification_id']); ?>"><?php echo '【'.$row['type'].'】'.$row['commodity_specification_name']; ?></a>

                    <p class="price">
                        <span style="color: #f6bf00; font-size: 11px">￥</span>
                        <span style="color: #f6bf00"><?php echo empty($row['flash_sale_price']) ? $row['price'] : $row['flash_sale_price'] ?></span>
                        <?php if (!empty($row['market_price'])) : ?>
                        <span style="font-size: 14px;color: #999; text-decoration: line-through;">
                            ￥<?php echo $row['market_price']; ?>
                        </span>
                        <?php endif; ?>
                    </p>
                    <a class="btn btn-2 btn-link pull-right" href="<?php echo site_url('commodity/index/'.$row['id'].'/'.$row['specification_id']); ?>">购买</a>
                </div>
            </div>
            <?php endforeach; ?>
            <br style="clear:both;" />
        </div>
        <?php endif; ?>

        <!-- 限时折扣 -->
        <?php if (!empty($flash_sale)): ?>
        <div class="row-fluid">
            <div class="span12">
                <div class="limited_discount_head">
                    <img data-original="<?php echo site_url('source/img/explosion_flag.png'); ?>">
                    <strong style="color: #117d94; margin-left: 10px; font-size: 16px; position: absolute;bottom: 7px;">限时折扣</strong>
                    <?php if (count($flash_sale) >= 3): ?>
                    <a class="more" href="<?php echo site_url('index/search?flash_sale=true&page_size=9'); ?>">更多></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="limited_discount">
            <div class="box_first" id="flash_sale">
                <a href="<?php echo site_url('commodity/index/'.$flash_sale[0]['commodity_id']); ?>">
                    <img class="img_detail discount_img cover-image" data-original="<?php echo site_url($flash_sale[0]['path']); ?>">
                </a>
                <div class="mask">
                    <div>
                        <h3>限时折扣</h3>
                        <p>立即进入>></p>
                    </div>
                </div>
            </div>

            <?php foreach ($flash_sale as $row): ?>
            <div class="box">
                <p class="time_head" style="font-size: 12px;" >活动结束时间 <span class="count-time" style="display: inline-block"> </span></p>
                <a href="<?php echo site_url('commodity/index/'.$row['commodity_id'].'/'.$row['specification_id']); ?>">
                    <img class="img_detail discount_img" data-original="<?php echo site_url($row['path']); ?>">
                </a>
                <div class="disc">
                    <a href="<?php echo site_url('commodity/index/'.$row['commodity_id'].'/'.$row['specification_id']); ?>"><?php echo '【'.$row['type'].'】'.$row['commodity_specification_name']; ?></a><br>
                    <p class="price">
                        <span style="color: #f6bf00; font-size: 10px">￥</span>
                        <span style="color: #f6bf00"><?php echo $row['flash_sale_price']; ?></span>
                        <span style="font-size: 12px;color: #999;text-decoration: line-through;"> <?php echo '¥'.$row['price']; ?></span>
                    </p>
                    <a class="btn btn-2 btn-link pull-right" href="<?php echo site_url('commodity/index/'.$row['commodity_id'].'/'.$row['specification_id']); ?>">购买</a>
                </div>
            </div>
            <?php endforeach; ?>
            <br style="clear:both;" />
        </div>
        <?php endif; ?>

        <!--热卖商品-->
        <?php if (!empty($recommend_hot_sale)): ?>
        <div class="row-fluid">
            <div class="span12">
                <div class="limited_discount_head hot_sale">
                    <img data-original="<?php echo site_url('source/img/hot.png'); ?>">
                    <strong>热卖商品</strong>
                </div>
            </div>
        </div>
        <div class="sale">
            <div class="hot-cover-image">
                <img data-original="<?php echo site_url(isset($recommend_hot_sale_cover['value']) ? $recommend_hot_sale_cover['value'] : 'source/img/06.png'); ?>">
            </div>
            <?php foreach ($recommend_hot_sale as $key => $row): ?>
            <?php if ($key <= 2): ?>
            <div class="box hot_change">
                <a href="<?php echo site_url('commodity/index/'.$row['commodity_id'].'/'.$row['commodity_specification_id']); ?>">
                    <img class="img_detail border_right" data-original="<?php echo site_url($row['path']); ?>">
                </a>
                <div class="disc mark">
                    <a style="height: 25px;" href="<?php echo site_url('commodity/index/'.$row['commodity_id'].'/'.$row['commodity_specification_id']); ?>"><?php echo '【'.$row['type'].'】'.$row['commodity_specification_name']; ?></a>
                    <br>
                    <p class="price">
                        <span style="color: #f6bf00; font-size: 10px">￥</span>
                        <span style="color: #f6bf00"><?php echo empty($row['flash_sale_price']) ? $row['price'] : $row['flash_sale_price']; ?></span>
                        <?php if (!empty($row['market_price'])) : ?>
                            <span style="font-size: 12px;color: #e4e4e4;text-decoration: line-through;"> <?php echo $row['market_price']; ?></span>
                        <?php endif; ?>
                    </p>
                    <a class="btn btn-2 btn-link pull-right" href="<?php echo site_url('commodity/index/'.$row['commodity_id'].'/'.$row['commodity_specification_id']); ?>">购买</a>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
            <div class="box hot_change" id="hot_sale">
                <a href="<?php echo site_url('index/search?hot_sale=true&page_size=10'); ?>" style="display: block;">
                    <img class="img_detail discount_img border_right" data-original="<?php echo site_url($recommend_hot_sale[count($recommend_hot_sale) - 1]['path']); ?>">
                </a>
                <div>
                    <div class="mask">
                        <h3>HOT</h3>
                        <p>立即进入></p>
                    </div>
                </div>
            </div>
<!--            <p style="clear: both"></p>-->
        </div>
        <?php endif; ?>
        <!--热换商品-->
        <?php if(!empty($recommend_hot_exchange)): ?>
        <div class="row-fluid">
            <div class="span12">
                <div class="limited_discount_head hot_sale">
                    <img data-original="<?php echo site_url('source/img/hot_sale_flag.png'); ?>">
                    <strong>热换商品</strong>
                </div>
            </div>
        </div>
        <div class="sale">
            <div class="hot-cover-image">
                <img data-original="<?php echo site_url(isset($recommend_hot_exchange_cover['value']) ? $recommend_hot_exchange_cover['value'] : 'source/img/06.png'); ?>">
            </div>
            <?php foreach ($recommend_hot_exchange as $key => $row): ?>
            <?php if ($key <= 2): ?>
            <div class="box hot_change">
                <a href="<?php echo site_url('commodity/index/'.$row['commodity_id']); ?>">
                    <img class="img_detail border_right" data-original="<?php echo site_url($row['path']); ?>">
                </a>
                <div class="disc mark">
                    <a style="height: 25px;" href="<?php echo site_url('commodity/index/'.$row['commodity_id']); ?>"><?php echo '【'.$row['type'].'】'.$row['commodity_specification_name']; ?></a><br>
                    <p class="price">
                        <span style="color: #f6bf00"><?php echo $row['price']; ?>
                        </span>
                        <span style="color: #f6bf00;">积分</span>
                    </p>
                    <a class="btn btn-2 btn-link pull-right" href="<?php echo site_url('commodity/index/'.$row['commodity_id']); ?>">兑换</a>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
            <div class="box hot_change" id="hot_change">
                <a href="<?php echo site_url('index/search?hot_exchange=true&page_size=10'); ?>" style="display: block;">
                    <img class="img_detail discount_img border_right" data-original="<?php echo site_url($recommend_hot_exchange[count($recommend_hot_exchange) - 1]['path']); ?>">
                </a>
                <div>
                    <div class="mask">
                        <h3>HOT</h3>
                        <p>立即进入></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>