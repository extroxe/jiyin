<article class="post_bar" ng-controller="post" style="padding-bottom: 60px;" >
    <div class="titlebar" style="background-color: #117d94">
        <a class="titlebar-button" ng-click="back()">
            <i style="color: #eee" class="icon size16 icon-arrowleft back_btn"></i>
        </a>
        <h1 class="title"><?php echo $title; ?></h1>
    </div>
    <div class="group inputbox title">
        <span>话题</span>
        <input class="input-text" type="text" ng-model="title">
    </div>
    <div class="group content">
        <span>内容</span>
        <textarea class="input-text" ng-model="content"></textarea>
        <a href="javascript:void(0)" ng-click="publish_post()">发表</a>
    </div>
</article>