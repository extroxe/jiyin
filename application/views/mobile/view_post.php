<article class="post_bar" ng-controller="viewPost" style="padding-bottom: 60px;" ng-init="post_id = '<?php echo $post_id; ?>'">
    <div class="titlebar" style="background-color: #117d94">
        <a class="titlebar-button"  ng-click="back()">
            <i class="icon size16 icon-arrowleft back_btn"></i>
        </a>
        <h1 class="title"><?php echo $title; ?></h1>
        <i class="icon icon-home" style=" right:8px;color: #fff;" ng-click="home()"></i>
    </div>

    <div class="group post_bar" ng-click="post_bar()" style="line-height: 39px;">
        <span class="bar_name">{{post_bar_info.name}}</span>
        <span class="focus">关注 <span>{{post_bar_info.focus_count}}</span></span>
        <span class="focus">帖子 <span>{{post_bar_info.post_count}}</span></span>
        <i class="icon icon-arrowright"></i>
    </div>

    <div class="group post_info" style="margin-bottom: 0;  overflow: hidden">
        <ul>
            <li>
                <span class="post_content" >{{post_detail.title}}</span>
                <span class="icon icon-heart-fill" ng-if="!post_detail.is_collected" ng-click="colloect_post(post_detail.id)"></span>
                <span class="icon icon-heart-fill" ng-if="post_detail.is_collected" style="color: #f25e5e" ng-click="cancel_colloect_post(post_detail.id)"></span>
            </li>
            <li style="margin: 5px 0 10px;">
                <img class="publisher_portrait" ng-src="{{ SITE_URL + (post_detail.avatar_path ? post_detail.avatar_path : 'source/img/default-user.png' ) }}">
                <div class="publisher_info">
                    <a href="javascript:void(0)" ng-click="visit(post_detail.user_id)">{{post_detail.nickname}}</a>
                    <span>1楼</span>
                    <span>{{post_detail.publish_time}}</span>
                </div>
                <div style="clear: both"></div>
            </li>
            <li>
                <p class="content" ng-bind-html="post_detail.content | to_trusted">
<!--                    {{}}-->
                </p>
            </li>
        </ul>
    </div>
    <div class="group evaluations post_info" style="margin-top: 0;">
        <ul class="evaluation_content">
            <li ng-repeat="replay in replay_lists">
                <img class="portrait" ng-src="{{ SITE_URL + (replay.avatar_path ? replay.avatar_path : 'source/img/default-user.png') }}">
                <div class="publisher_info">
                    <a href="javascript:void(0)" ng-click="visit(replay.publisher_id)">{{replay.nickname}}</a>
                    <span>{{ $index + page * page_size }}楼</span>
                    <span>{{replay.create_time}}</span>
                </div>
                <div style="clear: both"></div>
                <p class="evaluate_content">{{replay.content}}</p>
                <a class="replay_btn" href="javascript:void(0)" data-toggle="page" ng-click="page_to_publish('#replayPage', replay.id, replay.id, replay.publisher_id, replay.nickname)">回复</a>
                <ul class="evaluate_lists" ng-if="replay.replies">
                    <li ng-repeat="sub_replay in replay.replies" >
                        <span class="replay_to_replay">
                            <a href="javascript: void(0)" ng-click="visit(sub_replay.publisher_id)">{{sub_replay.nickname}} </a> 回复
                            <a href="javascript: void(0)" ng-click="visit(sub_replay.to_user_id)">{{sub_replay.to_user_nickname}}：</a>
                            <span ng-click="page_to_publish('#replayPage', sub_replay.root_comment_id, sub_replay.comment_id, sub_replay.publisher_id, sub_replay.nickname)" style="display:inline;">{{sub_replay.content}}</span>
                            <span class="publish_time">{{sub_replay.create_time}}</span>
                        </span>
                    </li>
                </ul>
                <div style="clear: both"></div>
            </li>
        </ul>
        <div class="pagenation">
            <a class="button btn-primary prev_page" ng-disabled="flag == true" ng-click="prev_page()">上一页</a>
            <a class="button btn-primary next_page" ng-disabled="flag_next == true" ng-click="next_page()">下一页</a>
        </div>
    </div>
    <div class="group recommend_post">
        <li>
            <span>热门推荐</span>
        </li>
        <li ng-repeat="recommend in recommend_bar" ng-click="recommend_post_bar(recommend.id)">
            <p class="post_name">{{recommend.name}}</p>
            <p ng-if="recommend.description" class="description">{{recommend.description}}</p>

            <div style="margin-left: 13px;margin-top: 10px;color: #999;">
                <span>关注  {{recommend.focus_count}}</span>&nbsp;
                <span>帖子  {{recommend.post_count}}</span>
            </div>
            <div style="clear: both;"></div>
        </li>
    </div>

    <div class="group replay">
        <span ng-click = "page_to_publish('#replayPage','','','', post_detail.nickname)">发表评论</span>
    </div>
    <!--page1-->
    <section id="replayPage" data-animation="slideRight" class="page" style="background-color:#f4f4f4;">
        <header>
            <div class="titlebar" style="background-color: #117d94">
                <a class="titlebar-button" ng-click="back()">
                    <i class="icon icon-arrowleft back_btn" ></i>
                </a>
                <h1 class="title"><?php echo $title; ?></h1>
                <i class="icon icon-home" style=" margin-top: 13px;color: #fff;" ng-click="home()"></i>
            </div>
        </header>
        <!--        <article>-->
        <div class="group replay_page">
            <span class="to_user_name">回复：<a ng-href="<?php echo site_url('weixin/user/visit'); ?>/{{publisher_id}}">{{to_user_nickname}}</a></span>
            <textarea class="content" ng-model="content" placeholder="我说...."></textarea>
            <a class="btn publish_replay" href="javascript:void(0)" ng-click="publish_replay(post_detail.id)">发表</a>
            <div style="clear: both"></div>
        </div>
        <!--        </article>-->
    </section>
</article>
