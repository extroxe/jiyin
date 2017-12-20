<div class="head">
    <div class="head-wrapper">
        <img src="<?php echo site_url('source/img/u2.png') ?>" alt="">
    </div>
    
</div>
<div class="article-wrapper">
    <div class="title"><?php echo $article['title']; ?></div>
    <div style="text-align: center;">
        <!--<div class="share">
            <span>分享到：</span>
            <img src="<?php /*echo site_url('source/img/hot.png'); */?>" alt="">
        </div>-->
        <div class="date"><?php echo substr($article['create_time'], 0, 10); ?></div>
    </div>
    <div class="content">
        <?php echo $article['content']; ?>
    </div>
</div>