<!--<div class="nav_head">
    <div>
        <ul class="breadcrumb">
            <li>
                <p class="triangle"></p>
            </li>
            <li><a href="#">基因商品</a> <span class="divider"> > </span></li>
            <li><a href="#">疾病综合</a> <span class="divider"> > </span></li>
            <li class="active">胶囊</li>
        </ul>
    </div>
</div>-->
<div class="single-page" id="page-content" data-id="<?php echo $commodity['id']; ?>" data-specification-id = "<?php echo $commodity['specification_id_']; ?>" >
    <div class="container">
        <div style="margin-top: 20px; width: 1140px">
            <div style="background-color: #fff; width: 360.5px; height: 386px; margin-left: 0; float: left;margin-left: 20px">
                <!--放大镜-->
                <div class="box">
                    <div class="tb-booth tb-pic tb-s310">
                        <a href="<?php echo site_url($commodity_thumbnail[0]['path']); ?>"><img src="<?php echo site_url($commodity_thumbnail[0]['path']); ?>" alt="<?php echo $commodity['commodity_specification_name']; ?>" rel="<?php echo site_url($commodity_thumbnail[0]['path']); ?>" class="jqzoom" /></a>
                    </div>
                    <ul class="tb-thumb" id="thumblist" style="height: 44px;width: 251px;">
                        <?php if (!empty($commodity_thumbnail)): ?>
                        <?php foreach ($commodity_thumbnail as $key => $row) : ?>
                            <li class="<?php if ($key == 0){
                                echo 'tb-selected';
                            } ?>">
                                <div class="tb-pic tb-s40">
                                    <a href="javascript:void(0)">
                                        <img src="<?php echo site_url($row['path']); ?>" mid="<?php echo site_url($row['path']); ?>" big="<?php echo site_url($row['path']); ?>">
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <!--放大镜-->
            </div>
            <div class="caption_info" style="width: 739.25px; height: 323px; float: left; margin-left: 20px">
                <div class="caption">
                    <h3 class="eclipse name"><span style="color: #666;">【<?php echo $commodity['type']; ?>】</span><?php echo $commodity['commodity_specification_name']; ?></h3>
                    <div class="commodity_desc eclipse"><?php echo $commodity['introduce']; ?></div>
                    <div class="operation">
                        <div class="row-fluid">
                            <div class="span12 price_row">
                                <?php echo $commodity['is_point'] ? '<span>积分：</span>' : '<span>价格：</span><span>¥</span>'; ?>
                                <span id="price">
                                    <?php echo !empty($commodity['flash_sale_price']) ? $commodity['flash_sale_price'] : $commodity['price']  ?>
                                </span>
                                <span style="font-size: 16px; color: #999; text-decoration: line-through;">
                                    <?php if (!$commodity['is_point'] && !empty($commodity['market_price'])): ?>
                                        ¥<?php echo $commodity['market_price']; ?>
                                    <?php endif; ?>
                                </span>
                                <span id="count_down"></span>
                                <ul class="collection">
                                    <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && $_SESSION['role_id'] == Jys_system_code::ROLE_USER): ?>
                                    <li id="collect">
                                        <span class="fa fa-star-o"></span>
                                        <span>收藏</span>
                                    </li>
                                    <?php else: ?>
                                    <li>
                                        <a href="<?php echo site_url('index/sign_in'); ?>">
                                            <span class="fa fa-star-o"></span>
                                            <span>收藏</span>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    <li>
                                        <a href="#_content">
                                            <span id="rating_num"></span>
                                            <span>评价数</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="specification">
                            <span>规格：</span>
                            <span id="specification_container">
                            </span>
                        </div>
                        <script type="text/html" id="specification_tpl">
                            <% for(var i = 0; i < list.length; i++) { %>
                                <button data-specification-id="<%=list[i][0].commodity_center_id%>" class="select-specification detail"><%=list[i][0].name || list[i][0].commodity_specification_name%></button>
                            <% } %>
                        </script>
                        <div class="pack specification">
                            <span>包装：</span>
                            <span id="pack_container">
                                <button class="detail jinz select-pack">精装</button>
                                <button class="detail jianz select-pack">简装</button>
                            </span>
                        </div>
                        <div class="quantity">
                            <div class="row-fluid">
                                <div class="span12">
                                    <span>购买数量：</span>
                                    <div class="num_selected" style="display: inline-block">
                                        <?php if ($commodity['type_id'] != 3) : ?>
                                        <input type="text" value="1" id="appendedInputButtons">
                                        <div class="puls_min">
                                            <div id="add"></div>
                                            <div id="delt"></div>
                                        </div>
                                        <?php else: ?>
                                        <input type="text" value="1" readonly>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="purchase">
                        <div class="purchase_row">
                            <?php if ($commodity['is_point']): ?>
                                <div class="span3" style="margin-left: 0">
                                    <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && $_SESSION['role_id'] == Jys_system_code::ROLE_USER): ?>
                                    <button class="btn btn-link btn-2" id="exchange-now"><span class="fa fa-tag" style="margin-right: 10px"></span>
                                        <span>立即兑换</span>
                                    </button>
                                    <?php else: ?>
                                    <a class="btn btn-link btn-2" href="<?php echo site_url('index/sign_in'); ?>"><span class="fa fa-tag" style="margin-right: 10px"></span>
                                        <span>立即兑换</span>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <?php if ($commodity['type_id'] != 3): ?>
                                <div style="float: left">
                                    <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && $_SESSION['role_id'] == Jys_system_code::ROLE_USER): ?>
                                    <button class="btn btn-link btn-2 add-cart" style="color: #117d94" id="addCart">
                                        <span style=" z-index:-1" id="cart_to_cart" class="fa fa-shopping-cart "></span>
                                        <span >加入购物车</span>
                                    </button>
                                    <?php else: ?>
                                    <a class="btn btn-link btn-2 add-cart" href="<?php echo site_url('index/sign_in'); ?>">
                                        <span style=" z-index:-1" class="fa fa-shopping-cart "></span>
                                        <span >加入购物车</span>
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <div style="margin-left: 0; float: left">
                                    <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && $_SESSION['role_id'] == Jys_system_code::ROLE_USER): ?>
                                    <button class="btn btn-link btn-2 buy-now" id="buy-now" style="<?php echo $commodity['type_id'] == 3 ? 'margin-left:0;' : ''; ?>">
                                        <span>立即购买</span>
                                    </button>
                                    <?php else: ?>
                                    <a href="<?php echo site_url('index/sign_in'); ?>" class="btn btn-link btn-2 buy-now" style="<?php echo $commodity['type_id'] == 3 ? 'margin-left:0;' : ''; ?>">
                                        <span>立即购买</span>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div id="_content">
            <div id="hot_sale" >
                <div class="title">
                    <?php echo $commodity['is_point'] == 1 ? '<h6>热换推荐</h6>' : '<h6>热卖推荐</h6>'; ?>
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
                                        <a href="<?php echo site_url('commodity/index/'.$row['commodity_id'].'/'.$row['commodity_specification_id']); ?>"><?php echo $row['commodity_specification_name']; ?></a>
                                    </div>
                                    <p>
                                        <?php if ($commodity['is_point']): ?>
                                            <?php echo intval($row['price']).'积分'; ?>
                                        <?php else: ?>
                                            ¥<?php echo !empty($commodity['flash_sale_price']) ? $commodity['flash_sale_price'] : $commodity['price']  ?>
                                            <?php if (!empty($row['market_price'])): ?>
                                                <span style="font-size: 12px; color: #999; text-decoration: line-through;"> ¥<?php echo $row['market_price']; ?></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </p>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="detail_evaluation">
                <div class="tabbable">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab1" data-toggle="tab"><strong>商品详情</strong></a></li>
                        <li><a href="#tab2" data-toggle="tab"><strong>商品评价 <span id="evaluate_num"></span></strong></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab1">
                            <div class="detail" style="padding: 0 50px;">
                                <?php echo $commodity['detail']; ?>
                            </div>
                            <hr>
                            <div class="detail_img">
                                <ul>
                                    <li><img data-original="img/slider1.jpg" /></li>
                                    <li><img data-original="img/slider2.jpg" /></li>
                                    <li><img data-original="img/slider3.jpg" /></li>
                                    <li><img data-original="img/slider1.jpg" /></li>
                                    <li><img data-original="img/slider2.jpg" /></li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab2">
                            <div class="review_score" style="padding: 0 30px;">
                                <div class="row-fluid">
                                    <h6>好评度：</h6>
                                    <h4 style="color: red; height: 40px; line-height: 40px" id="praise"></h4>
                                </div>
                            </div>
                            <hr>

                            <div class="user_review">
                                <ul class="breadcrumb" style="padding: 3px 20px; border:1px solid #eee">
                                    <li class="active"><a href="javascript:void(0)" id="all-evaluation"></a></li>
                                    <li><a href="javascript:void(0)" id="good-evaluation"></a></li>
                                    <li><a href="javascript:void(0)" id="mid-evaluation"></a></li>
                                    <li><a href="javascript:void(0)" id="bad-evaluation"></a></li>
                                    <li class="last-li"><input style="margin: 0 5px 2px;" type="checkbox" id="prevent_evaluation">只查看当前商品评价</li>
                                </ul>

                                <div class="review_content" id="evaluation" style="padding-left: 20px;">
                                </div>
                                <br>
                                <div class="row-fluid">
                                    <div class="span12">
                                        <div class="pagination pull-right" id="evaluation-paginate">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
    </div>
</div>

<script id="evaluation_tpl" type="text/html">
    <% for(var i = 0; i < list.length; i++) { %>
    <div style="margin-bottom: 20px; border-bottom: 1px solid #eee">
        <div class="row-fluid" style="display: flex; align-items: center;">
            <div class="span3" style="border-right: 1px solid #eee">
                <% if (list[i].user_avatar_path != null) { %>
                <a href="#" class="user_img">
                    <img class="img-circle" src="<%=SITE_URL + list[i].user_avatar_path%>">
                </a>
                <% } else { %>
                <a href="#" class="user_img">
                    <img class="img-circle" src="<%=SITE_URL + 'source/mobile/img/Hamster.jpg'%>">
                </a>
                <% } %>
                <span><%=list[i].user_nickname%></span>
                <br>
                <% if (list[i].level_name != null) { %>
                <span style="font-size: 14px; line-height: 40px"><%=list[i].level_name%></span>
                <% } else { %>
                <span style="font-size: 14px; line-height: 40px"></span>
                <% } %>
            </div>
            <div class="span9">
                <div class="review_list">
                    <p style="font-size: 14px"><%=list[i].content%></p>
                    <div>
                        <% if (list[i].evaluation_pic != null) { %>
                        <% for (var n = 0; n < list[i].evaluation_pic.length; n++) { %>
                        <img src="<%=SITE_URL + list[i].evaluation_pic[n].path%>" alt="" class="review-img-min" width="50" height="50" data-id="<%=list[i].evaluation_pic[n].id%>" style="margin-right: 5px;cursor: pointer;">
                        <% } %>
                        <% } %>
                    </div>
                    <div class="review-img" style="margin-top: 12px">
                        <img src="#" alt="" style="display: none;" width="200">
                    </div>
                    <div class="purchase_detail" style="margin-top: 10px">
                        <ul>
                            <li style="float: left; margin-right: 20px;color: #999"><%=list[i].commodity_center_name != null ? list[i].commodity_center_name : list[i].commodity_specification_name%>-<%=list[i].package_type_name%></li>
                            <li style="float: left; margin-right: 20px;color: #999"><%=list[i].create_time%></li>
                            <li style="float: left; margin-right: 20px">
                                <% if (list[i].score >= 4) { %>
                                好评
                                <% } else if (list[i].score == 3) { %>
                                中评
                                <% } else { %>
                                差评
                                <% } %>
                            </li>
                            <li style="float: left; margin-right: 20px">
                                <% for (var j = 0; j < list[i].score; j++) { %>
                                <span class="star fa fa-star active"></span>
                                <% } %>
                                <% for (var k = 0; k < 5 - list[i].score; k++) { %>
                                <span class="star fa fa-star"></span>
                                <% } %>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <span class="reply">回复：<%=list[i].reply_content%></span>
    </div>
    <% } %>
</script>
<style>
    .sp {
        float: left;
        margin-left: 21px;
        margin-bottom: 10px;
        color: #666;
        background-color: #FFF
    }
    .selected{
        border:1px solid #e3393c;
        color: #666;
    }
    .sp a{
        padding: 4px 13px;
        border: 1px solid #ccc;
        color: #666;
    }
    .sp a:hover {
        border:1px solid #e3393c;
        color: #666;
    }
</style>