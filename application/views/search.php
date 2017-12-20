<!-- Result Page -->
<div id="page-content" class="home-page">
    <div class="container">
        <?php if (isset($search_commodity) && !empty($search_commodity)): ?>
        <div class="row-fluid">
            <div class="subitem">
                <?php if(!empty($category)) : ?>
                <dl class="fore">
                    <dt><a href="#">分类</a></dt>
                    <dd>
                        <?php foreach ($category as $row) : ?>
                        <em>
                            <a href="#" data-category-id="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></a>
                        </em>
                        <?php endforeach; ?>
                    </dd>
                </dl>
                <?php endif; ?>
                <?php if(!empty($type)) : ?>
                    <dl class="fore">
                        <dt><a href="#">类型</a></dt>
                        <dd>
                            <?php foreach ($type as $row) : ?>
                                <em>
                                    <a href="#" data-type-id="<?php echo $row['type_id']; ?>"><?php echo $row['type']; ?></a>
                                </em>
                            <?php endforeach; ?>
                            <em>
                                <a href="#" data-type-id="0">全部类型</a>
                            </em>
                        </dd>
                    </dl>
                <?php endif; ?>
                <?php if (!$all_point_flag): ?>
                <dl class="fore">
                    <dt><a href="#">价格</a></dt>
                    <dd><em><a href="#" class="select-price" data-price="0-1000">0-1000</a></em><em><a href="#" class="select-price" data-price="1001-2000">1001-2000</a></em><em><a href="#" class="select-price" data-price="2001-3000">2001-3000</a></em><em><a href="#" class="select-price" data-price="min_p-3001">3000以上</a></em></dd>
                </dl>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <br>
        <div style="width: 1140px">
            <div id="hot_sale" >
                <div class="title">
                    <?php echo $all_point_flag == 1 ? '<h6>热换推荐</h6>': '<h6>热卖推荐</h6>';?>
                </div>
                <div class="confirm">
                    <ul>
                        <?php if (isset($recommend) && !empty($recommend)): ?>
                        <?php foreach ($recommend as $row): ?>
                        <li>
                            <a href="<?php echo site_url('commodity/index/'.$row['commodity_id'].'/'.$row['commodity_specification_id']); ?>">
                                <img src="<?php echo site_url($row['path']); ?>">
                            </a>
                            <div class="desc">
                                <a href="<?php echo site_url('commodity/index/'.$row['commodity_id'].'/'.$row['commodity_specification_id']); ?>">
                                    <span>【<?php echo $row['type']; ?>】</span>
                                    <?php echo $row['commodity_specification_name']; ?>
                                </a>
                            </div>
                            <p>
                                <?php if ($all_point_flag): ?>
                                    <?php echo intval($row['price']).'积分'; ?>
                                <?php else: ?>
                                    ¥<?php echo empty($row['flash_sale_price']) ? $row['price'] : $row['flash_sale_price']; ?>
                                    <?php if (!empty($row['market_price'])) : ?>
                                        <span style="font-size: 12px;color: #999;text-decoration: line-through;"> ¥<?php echo $row['market_price']; ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </p>
                        </li>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div id="search_list" style="margin-left: 20px;width: 895px;float: left;"><!--
                <div class="row-fluid" style="width:900px">
                    <div class="sort">
                        <div class="span12" style="width: 900px">
                            <ul class="breadcrumb evaluation">
                                <li class="active"><a href="#">综合</a></li>
                                <li><a href="#">销量</a></li>
                                <li><a href="#">人气</a></li>
                                <li><a href="#">评论数</a></li>
                            </ul>
                        </div>
                    </div>
                </div>-->
                <div id="search-list" style="width: 900px">
                    <?php if (empty($search_commodity)) : ?>
                    <p class="no-commodity">
                        <img src="<?php echo site_url('source/img/warning.png'); ?>" alt="warning" width="50" height="50">
                        抱歉！没有找到相关的商品...
                    </p>
                    <?php else : ?>
                    <ul class="thumbnails">
                        <?php foreach ($search_commodity as $row) : ?>
                        <li style="width: 301px; float: left">
                            <div class="thumbnail" style="margin-left: 0">
                                <a href="<?php echo site_url('commodity/index/'.$row['id'].'/'.$row['specification_id']); ?>" style="display: block;height: 300.09px; border: 1px solid #eee">
                                    <img src="<?php echo site_url($row['path']); ?>" alt="">
                                </a>
                                <div class="contain">
                                    <?php if ($row['is_point']): ?>
                                        <h6><?php echo intval($row['price']); ?><span style="vertical-align: baseline;font-size: 15px;margin-left: 5px;">积分</span></h6>
                                    <?php else: ?>
                                        <h6>
                                            <span style="font-size: 15px;">¥</span><?php echo !empty($row['flash_sale_price']) ? $row['flash_sale_price'] : $row['price']; ?>
                                            <?php if (!empty($row['market_price'])) : ?>
                                                <span style="font-size: 12px;color: #999;text-decoration: line-through;"> ¥<?php echo $row['market_price']; ?></span>
                                            <?php endif; ?>
                                        </h6>
                                    <?php endif; ?>
                                    <p class="commodity_name">
                                        <a href="<?php echo site_url('commodity/index/'.$row['id'].'/'.$row['specification_id']); ?>">
                                            【<?php echo $row['type']; ?>】<?php echo $row['commodity_specification_name']; ?>
                                        </a>
                                    </p>
                                    <p class="already_sale">销量：<span style="color: #222"><?php echo $row['sales_volume']; ?></span></p>
                                    <!--<p class="review">评论：<span style="color: #222">50</span></p>-->
                                </div>
                                <div class="purchase">
                                    <ul class="text-center">
                                        <?php if($row['is_point']): ?>
                                        <li class="exchange" data-commodity-id="<?php echo $row['id']; ?>" style="<?php echo $row['type_id'] == 3 ? 'width:100%;text-align:center;' : ''; ?>">
                                            <a>
                                                <img style="height: 19px;" src="<?=site_url('source/img/label.png'); ?>">
                                                <span style="margin-left: 5px">立即兑换</span>
                                            </a>
                                        </li>
                                        <?php else: ?>
                                        <?php if ($row['type_id'] != 3): ?>
                                        <li class="addCart" data-commodity-id="<?php echo $row['id']; ?>" data-specification-id="<?php echo $row['specification_id']; ?>">
                                            <a>
                                                <img id="cart" style="height: 21px" src="<?=site_url('source/img/shopping-cart-red.png'); ?>">
                                                <span style="margin-left: 28px">加入购物车</span>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <li class="buy_directly" data-commodity-id="<?php echo $row['id']; ?>"  style="<?php echo $row['type_id'] == 3 ? 'width:100%;text-align:center;' : ''; ?>">
                                            <a>
                                                <img style="height: 19px;" src="<?=site_url('source/img/label.png'); ?>">
                                                <span style="margin-left: 28px">立即购买</span>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>

                                </div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <div id="paginate-render">
                    <?php echo $render; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <hr>
    </div>
</div>