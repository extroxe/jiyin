<article class="post_bar" ng-controller="visitOthersHome" ng-init="user_id = '<?php echo $user_id; ?>';">
    <div class="titlebar" style="background-color: #117d94">
        <a class="titlebar-button" ng-click="back()">
            <i style="color: #eee" class="icon size16 icon-arrowleft back_btn"></i>
        </a>
        <h1 class="head"><?php echo $title; ?></h1>
        <i class="icon icon-home" style=" color: #fff;right: 8px;" ng-click="home()"></i>
    </div>
    <div id="user_hook">
        <div class="user-wrapper">
            <div class="header">
                <?php if (isset($user_info) && is_array($user_info)): ?>
                <div class="avatar-wrapper">
                    <img src="<?php echo site_url($user_info['avatar_path'] ? $user_info['avatar_path'] : 'source/img/default-user.png'); ?>" alt="" class="avatar">
                </div>
                <div class="name"><?php echo $user_info['nickname']; ?></div>
                <?php if (isset($is_focused) && $is_focused): ?>
                <div class="focus-ed" ng-click="focus($event)">
                    <a href="">已关注</a>
                </div>
                <?php elseif(isset($is_focused) && !$is_focused): ?>
                <div class="focus" ng-click="focus($event)">
                    <a href="">关注</a>
                </div>
                <?php endif; ?>
                <div class="send-message">
                    <a href="<?php echo site_url('weixin/user/send_message/'.$user_info['id']); ?>" class="send-btn">
                        <i class="icon size16 icon-chatdot-fill"></i> 去私聊
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <div class="nav-wrapper">
                <ul class="tabbar tabbar-line animated">
                    <li class="tab active" ng-click="selectTab('post')">
                        <label class="tab-label">帖子</label>
                    </li>
                    <li class="tab" ng-click="selectTab('focus')">
                        <label class="tab-label">关注的人</label>
                    </li>
                    <li class="tab" ng-click="selectTab('fans')">
                        <label class="tab-label">粉丝</label>
                    </li>

                </ul>
            </div>
            <div class="post-content" ng-show="block == 'post'">
                <ul ng-show="post_lists">
                    <li class="list-item" ng-repeat="post in post_lists" ng-click="view_post(post.post_bar_id, post.id)">
                        <div class="title">{{post.title}}</div>
                        <div class="info">
                            <span>发表于</span>
                            <span style="color: #ea0707">{{post.bar_name_post}}</span>
                            <span>{{post.publish_time | timefilter}}</span>
                        </div>
                    </li>
                </ul>
                <div class="no-content" ng-show="!post_lists">TA还没有发帖哦</div>
            </div>
            <div class="focus-content" ng-show="block == 'focus'">
                <div class="person">
                    <ul>
                        <li class="person-list" ng-repeat="focus in focus_lists" ng-click="view_friends(focus.focus_user_id)">
                            <div class="avatar">
                                <img ng-src="{{ SITE_URL + focus.avatar_path ? focus.avatar_path : 'source/img/default-user.png' }}" alt="">
                            </div>
                            <div class="name">{{focus.nickname}}</div>
                        </li>
                    </ul>
                    <div class="expand-all" ng-click="expend_focus_person();" ng-show="focus_lists">
                        <i class="icon size12 icon-arrowdown"></i>
                        <p>展开更多</p>
                    </div>
                    <div class="no-content" ng-show="!focus_lists">TA还没有关注的人哦</div>
                </div>
            </div>
            <div class="fans-content" ng-show="block == 'fans'">
                <div class="person">
                    <ul>
                        <li class="person-list" ng-repeat="fans in fans_lists" ng-click="view_friends(fans.fans_user_id)">
                            <div class="avatar">
                                <img ng-src="{{ SITE_URL + fans.avatar_path ? fans.avatar_path : 'source/img/default-user.png' }}" alt="">
                            </div>
                            <div class="name">{{fans.nickname}}</div>
                        </li>
                    </ul>
                    <div class="expand-all" ng-click="expend_fans();" ng-show="fans_lists">
                        <i class="icon size12 icon-arrowdown"></i>
                        <p>展开更多</p>
                    </div>
                    <div class="no-content" ng-show="!fans_lists">TA还没有粉丝哦</div>
                </div>
            </div>
        </div>
    </div>
</article>
