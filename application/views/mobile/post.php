<article class="post_bar" ng-controller="post" style="padding-bottom: 60px;" >
    <div class="titlebar" style="background-color: #117d94">
        <a class="titlebar-button" ng-click="back()">
            <i style="color: #eee" class="icon size16 icon-arrowleft back_btn"></i>
        </a>
        <h1 class="title"><?php echo $title; ?></h1>
        <i class="icon icon-home" style=" color: #fff;right: 8px;" ng-click="home()"></i>
    </div>
    
    <div class="group post_bar_info">
        <div class="post_bar_detail">
            <span class="post_bar_name">{{post_bar_info.name}}</span>
            <span ng-if="post_bar_info.description" class="post_description">{{post_bar_info.description}}</span>
            <span class="focus_num">关注 <span>{{post_bar_info.focus_count}}</span>  &nbsp; 帖子 <span>{{post_bar_info.post_count}}</span></span>
            <a ng-if="!post_bar_info.is_focused" class="btn add_focus focus" ng-click="add_focus()"><i class="icon icon-plus"></i>关注</a>
            <a ng-if="post_bar_info.is_focused" class="btn add_focus focused" ng-click="cancel_focus()" style="background-color: #f25e5e"><i class="icon icon-hook"></i>已关注</a>
        </div>
        <div style="clear: both"></div>
    </div>

    <div class="content">
        <div class="group post_item" ng-repeat="post in post_lists" ng-click="view_post(post.post_bar_id, post.id)">
            <ul>
                <li><span class="post_title">{{post.title}}</span></li>
                <li><span class="post_content">{{post.content}}</span></li>
                <li>
                    <a class="publisher">{{post.nickname}}</a>
                    <span class="publish_time">{{post.publish_time}}</span>
                </li>
            </ul>
        </div>
    </div>
    <div class="group publish_post">
        <a ng-href="<?php echo site_url('weixin/user/publish_post'); ?>/{{post_bar_id}}">发帖</a>
    </div>
</article>