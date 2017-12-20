<header>
    <div class="titlebar">
        <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
        <h1 class="text-center" style="margin-right: 40px;"><?php echo $title; ?></h1>
    </div>
</header>
<article ng-controller="myCityCtrl">
    <?php if ($block == 'bar'): ?>
    <div id="bar_hook">
        <div class="content-wrapper">
        <div class="focus-bar">
            <div class="header">
                <div class="title">我关注的吧</div>
            </div>
            <div ng-if="focus_bars == null" style="margin: 14px; padding-bottom: 14px;">
                暂无关注贴吧 去<a>关注</a>
            </div>
            <ul class="bar-wrapper" ng-if="focus_bars != null">
                <li class="bar-item" ng-repeat="focus_bar in focus_bars" ng-click="post(focus_bar.post_bar_id)">
                    <div class="bar-content">
                        <div class="name">{{ focus_bar.name }}</div>
                        <div class="detail">关注 {{ focus_bar.focus_count }}</div>
                    </div>
                </li>
            </ul>
            <div class="expand-all" ng-click="expend_bar()" ng-show="focus_bars">
                <i class="icon size12 icon-arrowdown"></i>
                <p>展开更多</p>
            </div>
        </div>
        <div class="recommend-bar">
            <div class="header">
                <div class="title">
                    推荐的吧
                </div>
                <div class="operate" ng-click="change()">
                    换一换
                </div>
            </div>
            <ul>
                <li class="bar" ng-repeat="recommend_bar in recommend_bars">
                    <div class="bar-wrapper">
                        <div class="bar-item" ng-click="post(recommend_bar.id, $event)">
                            <div class="title" >
                                {{ recommend_bar.name }}
                            </div>
                            <div class="detail">
                                <span class="focus">关注 {{ recommend_bar.focus_count }}</span>
                                <span class="post">帖子 {{ recommend_bar.post_count }}</span>
                            </div>
                        </div>
                        <div class="operate">
                            <div class="focus-btn" ng-if="recommend_bar.is_focused == false" ng-click="focus_bar($event, recommend_bar)">关注</div>
                            <div class="focused-btn" ng-if="recommend_bar.is_focused == true" ng-click="cancel_focus_bar($event, recommend_bar)">取消关注</div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        </div>
    </div>
    <?php elseif ($block == 'user'): ?>
    <div id="user_hook">
        <div class="user-wrapper">
            <div class="header">
                <div class="avatar-wrapper">
                    <img src="<?php echo site_url($_SESSION['avatar_path'] ? $_SESSION['avatar_path'] : 'source/img/default-user.png'); ?>" alt="" class="avatar">
                </div>
                <div class="name"><?php echo $_SESSION['nickname']; ?></div>
            </div>
            <div class="nav-wrapper">
                <ul class="tabbar tabbar-line animated">
                    <li class="tab active" ng-click="selectTab('post')">
                        <label class="tab-label">帖子</label>
                    </li>
                    <li class="tab" ng-click="selectTab('focus')">
                        <label class="tab-label">关注</label>
                    </li>
                    <li class="tab" ng-click="selectTab('fans')">
                        <label class="tab-label">粉丝</label>
                    </li>
                    <li class="tab" ng-click="selectTab('collect')">
                        <label class="tab-label">收藏</label>
                    </li>
                    <li class="tab" ng-click="selectTab('message')">
                        <label class="tab-label">站内信</label>
                    </li>
                </ul>
            </div>
            <div class="post-content" ng-show="block == 'post'">
                <ul ng-show="post_infos">
                    <li class="list-item" ng-repeat="post_info in post_infos" ng-click="view_post(post_info.post_bar_id, post_info.id)">
                        <div class="title">{{ post_info.title }}</div>
                        <div class="content" ng-bind-html="post_info.content | to_trusted">
<!--                            {{  }}-->
                        </div>
                        <div class="info">
                            <span>发表于</span>
                            <span style="color: #ea0707">{{ post_info.post_bar_name }}</span>
                            <span>{{ post_info.publish_time | timefilter }}</span>
                        </div>
                    </li>
                </ul>
                <div class="no-content" ng-show="!post_infos">还没有帖子，快去贴吧发帖子吧!</div>
            </div>
            <div class="focus-content" ng-show="block == 'focus'">
                <div class="bar">
                    <div class="title">关注的吧</div>
                    <ul ng-show="focus_bars">
                        <li class="bar-list" ng-repeat="focus_bar in focus_bars" ng-click="post(focus_bar.post_bar_id)">
                            <div class="name">{{ focus_bar.name }}</div>
                            <div class="detail">关注 {{ focus_bar.focus_count }}</div>
                        </li>
                    </ul>
                    <div class="expand-all" ng-click="expend_focus_bar();" ng-show="focus_bars">
                        <i class="icon size12 icon-arrowdown"></i>
                        <p>展开更多</p>
                    </div>
                    <div class="no-content" ng-show="!focus_bars">还没有关注的吧，快去关注吧!</div>
                </div>
                <div class="person">
                    <div class="title">关注的人</div>
                    <ul ng-show="focus_infos">
                        <li class="person-list" ng-repeat="focus_info in focus_infos" ng-click="visit(focus_info.focus_id)">
                            <div class="avatar">
                                <img ng-src="{{ SITE_URL + focus_info.avatar_path ? focus_info.avatar_path : 'source/img/default-user.png' }}" alt="">
                            </div>
                            <div class="name">{{ focus_info.nickname }}</div>
                        </li>
                    </ul>
                    <div class="expand-all" ng-click="expend_focus_person();" ng-show="focus_infos">
                        <i class="icon size12 icon-arrowdown"></i>
                        <p>展开更多</p>
                    </div>
                    <div class="no-content" ng-show="!focus_infos">还没有关注的人，快去关注吧!</div>
                </div>
            </div>
            <div class="fans-content" ng-show="block == 'fans'">
                <div class="person">
                    <ul ng-show="fans_infos">
                        <li class="person-list" ng-repeat="fans_info in fans_infos" ng-click="visit(fans_info.user_id)">
                            <div class="avatar">
                                <img ng-src="{{ SITE_URL + fans_info.avatar_path ? fans_info.avatar_path : 'source/img/default-user.png' }}" alt="">
                            </div>
                            <div class="name">{{ fans_info.nickname }}</div>
                        </li>
                    </ul>
                </div>
                <div class="expand-all" ng-click="expend_fans();" ng-show="fans_infos">
                    <i class="icon size12 icon-arrowdown"></i>
                    <p>展开更多</p>
                </div>
                <div class="no-content" ng-show="!fans_infos">您还没有粉丝...</div>
            </div>
            <div class="collect-content" ng-show="block == 'collect'">
                <div class="bar">
                    <ul ng-show="collect_infos">
                        <li class="bar-list" ng-repeat="collect_info in collect_infos" ng-click="view_post(collect_info.post_bar_id, collect_info.post_id)">
                            <div class="name">{{ collect_info.title }}</div>
                            <div class="detail">浏览量 {{ collect_info.page_view }}</div>
                        </li>
                    </ul>
                    <div class="expand-all" ng-click="expend_collect();" ng-show="collect_infos">
                        <i class="icon size12 icon-arrowdown"></i>
                        <p>展开更多</p>
                    </div>
                    <div class="no-content" ng-show="!collect_infos">还没有收藏，赶快去收藏吧!</div>
                </div>
            </div>
            <div class="message-content" ng-show="block == 'message'">
                <div class="message receive">
                    <div class="title">收件箱</div>
                    <ul ng-show="receive_message_infos">
                        <li class="msg-list" ng-repeat="receive_message in receive_message_infos" ng-click="readMsg(receive_message.id)">
                            <div class="send">发送者: {{ receive_message.send_user_username }}</div>
                            <div class="detail">{{ receive_message.content }}</div>
                            <div class="date">
                                <span class="status" ng-show="receive_message.status_id == 0">未读</span>
                                {{ receive_message.create_time | timefilter }}
                            </div>
                        </li>
                    </ul>
                    <div class="expand-all" ng-click="expend_receive();" ng-show="receive_message_infos">
                        <i class="icon size12 icon-arrowdown"></i>
                        <p>展开更多</p>
                    </div>
                    <div class="no-content" ng-show="!receive_message_infos" >收件箱时空的...</div>
                </div>
                <div class="message send">
                    <div class="title">发件箱</div>
                    <ul ng-show="send_message_infos">
                        <li class="msg-list" ng-repeat="send_message in send_message_infos" ng-click="readMsg(send_message.id)">
                            <div class="send">接收者: {{ send_message.receive_user_username }}</div>
                            <div class="detail">{{ send_message.content }}</div>
                            <div class="date">{{ send_message.create_time | timefilter }}</div>
                        </li>
                    </ul>
                    <div class="expand-all" ng-click="expend_send();" ng-show="send_message_infos">
                        <i class="icon size12 icon-arrowdown"></i>
                        <p>展开更多</p>
                    </div>
                    <div class="no-content" ng-show="!send_message_infos">发件箱是空的，快去发信息吧!</div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="footer-wrapper">
        <div class="background tbpadding8">
            <ul class="tabbar tabbar-rect reverse" style="width:50%;">
                <li class="tab <?php echo $block == 'bar' ? 'active' : ''; ?>">
                    <a href="<?php echo site_url('weixin/user/my_city'); ?>">
                        <label class="tab-label">贴吧</label>
                    </a>

                </li>
                <li class="tab <?php echo $block == 'user' ? 'active' : ''; ?>">
                    <a href="<?php echo site_url('weixin/user/my_city/user'); ?>">
                        <label class="tab-label">我</label>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</article>