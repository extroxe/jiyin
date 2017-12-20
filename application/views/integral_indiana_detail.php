<div class="single-page" id="page-content" data-id="<?php echo $commodity['id']; ?>">
    <div class="container">
        <div style="margin-top: 20px; width: 1140px">
            <div style="background-color: #fff; width: 360.5px; height: 386px; margin-left: 0; float: left;margin-left: 20px">
                <!--放大镜-->
                <div class="box">
                    <div class="tb-booth tb-pic tb-s310">
                        <a href="<?php echo site_url('commodity/index/'.$commodity['id'].'/'.$commodity['specification_id']); ?>">
                            <img src="<?php echo site_url($commodity_thumbnail[0]['path']); ?>" alt="<?php echo $commodity['commodity_name']; ?>" rel="<?php echo site_url($commodity_thumbnail[0]['path']); ?>" class="jqzoom" />
                        </a>
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
                    <h3 class="eclipse name">
                        <a href="<?php echo site_url('commodity/index/'.$commodity['id'].'/'.$commodity['specification_id']); ?>">
                            <span style="color: #666;">【<?php echo $commodity['type']; ?>】</span>
                            <?php echo $commodity['commodity_name']; ?>
                        </a>
                    </h3>
                    <div class="commodity_desc eclipse"><?php echo $commodity['introduce']; ?></div>
                    <?php if (isset($integral_indiana) && !empty($integral_indiana)): ?>
                        <div class="progressbox-content">
                            <div class="progress radius40">
                                <span class="progress-bar <?php echo $integral_indiana['amount_bet'] * $integral_indiana['current_bet'] >= $integral_indiana['total_points'] ? 'bg-complete' : 'bg-not-complete'; ?> theme-1" style="max-width:100%;width: <?php echo $integral_indiana['current_bet'] ? ($integral_indiana['amount_bet'] * $integral_indiana['current_bet']) / $integral_indiana['total_points'] * 100 : 0; ?>%"></span>
                            </div>
                        </div>
                        <p class="state">
                            当前参与
                            <span><?php  echo $integral_indiana['current_bet'] ? ($integral_indiana['amount_bet'] * $integral_indiana['current_bet'] > $integral_indiana['total_points']) ? $integral_indiana['total_points'] : $integral_indiana['amount_bet'] * $integral_indiana['current_bet'] : 0; ?></span>
                            积分 / 总共需
                            <span><?php echo $integral_indiana['total_points']; ?></span>
                            积分
                        </p>
                    <?php endif; ?>
                    <br>
                    <div class="purchase">
                        <div class="purchase_row">
                            <div class="span3" style="margin-left: 0">
                                <?php if ($integral_indiana['amount_bet'] * $integral_indiana['current_bet'] < $integral_indiana['total_points']): ?>
                                <button class="btn btn-link btn-2" id="indiana-now" data-id="<?php echo $integral_indiana['id']; ?>" data-point="<?php  echo $integral_indiana['amount_bet']; ?>">
                                    <span>立即夺宝</span>
                                </button>
                                <?php else: ?>
                                <button class="btn btn-link btn-2" disabled="disabled" style="background: #848484;">
                                    <span>已结束</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rule-wrapper">
                    <?php if (!empty($integral_indiana['user_total_point']) && !empty($integral_indiana['user_expenditure']) && !empty($integral_indiana['register_start_time']) && !empty($integral_indiana['register_end_time'])): ?>
                    <p>参与条件：</p>
                    <?php if (!empty($integral_indiana['user_total_point']) && !is_null($integral_indiana['user_total_point']) && $integral_indiana['user_total_point'] != ''): ?>
                    <p>● 参与者总积分必须大于 <?php echo $integral_indiana['user_total_point']; ?> 积分</p>
                    <?php endif; ?>
                    <?php if (!empty($integral_indiana['user_expenditure']) && !is_null($integral_indiana['user_expenditure']) && $integral_indiana['user_expenditure'] != ''): ?>
                    <p>● 参与者消费额必须大于 <?php echo $integral_indiana['user_expenditure']; ?> 元</p>
                    <?php endif; ?>
                    <?php if (!empty($integral_indiana['register_start_time']) && !is_null($integral_indiana['register_start_time']) && $integral_indiana['register_start_time'] != '' && !empty($integral_indiana['register_end_time']) && !is_null($integral_indiana['register_end_time']) && $integral_indiana['register_end_time'] != ''): ?>
                    <p>● 参与者账号注册时间在 <?php echo $integral_indiana['register_start_time']; ?> -- <?php echo $integral_indiana['register_end_time']; ?> 之间 </p>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <hr>
        <div id="_content">
            <div id="hot_sale" >
                <div class="title">
                    <h6>热卖推荐</h6>
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
                                            <?php echo $row['commodity_name']; ?>
                                        </a>
                                    </div>
                                    <p>¥<?php echo $row['price']; ?></p>
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
                        <li><a href="#tab2" data-toggle="tab"><strong>商品评价</strong></a></li>
                    </ul>
                    <div class="tab-content" >
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
                                    <li class="active"><a href="#_content" id="all-evaluation"></a></li>
                                    <li><a href="#_content" id="good-evaluation"></a></li>
                                    <li><a href="#_content" id="mid-evaluation"></a></li>
                                    <li><a href="#_content" id="bad-evaluation"></a></li>
                                </ul>
                                <div class="review_content" id="evaluation" style="padding-left: 20px">
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