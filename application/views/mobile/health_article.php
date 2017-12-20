<article class="health_article" ng-controller="healthArticleCtrl" style="padding-bottom: 60px;">
    <div class="titlebar" style="background-color: #117d94">
        <a class="titlebar-button" ng-click="back()">
            <i style="color: #eee" class="icon icon-arrowleft back_btn"></i>
        </a>
        <h1 class="title"><?php echo $title; ?></h1>
    </div>
    <div class="group article">
        <h3 class="article_title"><?php echo $article['title']; ?></h3>
        <span class="date"><?php echo $article['update_time']; ?></span>
        <article>
            <?php echo $article['content']; ?>
        </article>
    </div>
</article>