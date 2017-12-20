<!-- Result Page -->
<div id="page-content" class="home-page">
    <div class="header">
        <div class="title">
            <h2>积 分 夺 宝</h2>
        </div>
    </div>
    <div class="container">
        <div style="width: 1140px;display: inline-block;">
            <?php if (isset($indiana_result_info) && !empty($indiana_result_info)): ?>
                <div id="hot_sale" >
                    <div class="title">
                        <h6>最新揭晓</h6>
                    </div>
                    <div class="confirm">
                        <ul>
                            <?php foreach ($indiana_result_info as $key => $row): ?>
                                <?php if ($key < 10): ?>
                                <li>
                                    <span style="color: #2583b1;"><?php echo !empty($row['nickname']) ? $row['nickname'] : $row['username']; ?></span>
                                    获得了
                                    <span style="color: #2583b1;"><?php echo $row['commodity_name']; ?></span>
                                </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
            <div id="search_list" style="margin-left: 20px;width: 895px;float: left;">
                <div id="search-list" style="width: 900px">
                    <ul class="thumbnails">
                        <?php if (isset($all_indiana) && !empty($all_indiana)) : ?>
                        <?php foreach($all_indiana as $row) : ?>
                            <li style="width: 260px; float: left">
                                <div class="thumbnail" style="margin-left: 0">
                                    <div class="content-wrapper">
                                        <a href="<?php echo site_url('integral_indiana/detail/'.$row['commodity_id'].'/'.$row['id']); ?>" style="display: block;">
                                            <img src="<?php echo site_url($row['path']); ?>" alt="">
                                        </a>
                                        <div class="contain">
                                            <p class="commodity_name"><?php echo $row['commodity_name']; ?></p>
                                            <div class="progressbox-content">
                                                <div class="progress">
                                                    <span class="progress-bar <?php echo $row['amount_bet'] * $row['current_bet'] >= $row['total_points'] ? 'bg-complete' : 'bg-not-complete'; ?> theme-1" style="max-width:100%;width: <?php echo $row['current_bet'] ? ($row['amount_bet'] * $row['current_bet']) / $row['total_points'] * 100 : 0; ?>%"></span>
                                                </div>
                                            </div>
                                            <p class="state" style="font-size: 14px;">
                                                当前
                                                <span><?php echo  $row['current_bet'] ? ($row['amount_bet'] * $row['current_bet'] > $row['total_points']) ? $row['total_points'] : $row['amount_bet'] * $row['current_bet'] : 0; ?></span>
                                                积分 / 总共需
                                                <span><?php echo $row['total_points']; ?></span>
                                                积分</p>
                                        </div>
                                    </div>
                                    <div class="purchase">
                                        <ul>
                                            <li class="buy_directly" style="width:100%;text-align:center;">
                                                <a class="<?php echo $row['amount_bet'] * $row['current_bet'] >= $row['total_points'] ? 'end' : '';  ?>" href="<?php echo site_url('integral_indiana/detail/'.$row['commodity_id'].'/'.$row['id']); ?>">
                                                    <?php if ($row['amount_bet'] * $row['current_bet'] >= $row['total_points']) : ?>
                                                    <span>已结束</span>
                                                    <?php else: ?>
                                                    <span>正在进行</span>
                                                    <?php endif; ?>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <div id="paginate-render">
                    </div>
                </div>
            </div>
            <div class="rules">
                <?php if (!empty($rules)): ?>
                <h5>夺宝规则：</h5>
                <p><?php echo $rules; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>