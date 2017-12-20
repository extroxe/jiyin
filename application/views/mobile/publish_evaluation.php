<article class="post_bar" ng-controller="publishEvaluation" style="padding-bottom: 60px;">
    <div class="titlebar" style="background-color: #117d94">
        <a class="titlebar-button" ng-click="back()">
            <i style="color: #eee" class="icon icon-arrowleft back_btn"></i>
        </a>
        <h1 class="title"><?php echo $title; ?></h1>
    </div>
    <div class="group replay">
        <span>回复：无极剑圣</span>
        <textarea class="content" ng-model="content" placeholder="我说...."></textarea>
        <a class="btn publish_replay" href="javascript:void(0)" ng-click="publish_replay()">发表</a>
        <div style="clear: both"></div>
    </div>
</article>