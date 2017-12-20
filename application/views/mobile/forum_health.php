<article class="forum_health" ng-controller="forumHealthCtrl" style="padding-bottom: 60px;">
    <div class="titlebar" style="background-color: #117d94">
        <a class="titlebar-button" ng-click="back()">
            <i style="color: #eee" class="icon icon-arrowleft back_btn"></i>
        </a>
        <h1 class="title"><?php echo $title; ?></h1>
    </div>
    <div class="content">
        <div class="group article_list" ng-repeat="article in articleList" ng-click="goToUrl('health_article', article.id)">
            <img ng-src="{{SITE_URL + article.thumbnail_path}}">
            <div class="article">
                <h3>{{article.title}}</h3>
                <span>{{article.abstract}}</span>
            </div>
        </div>
    </div>
</article>