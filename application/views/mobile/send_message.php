<header>
    <div class="titlebar">
        <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
        <h1 class="text-center" style="margin-right: 40px;"><?php echo $title; ?></h1>
    </div>
</header>
<article ng-controller="sendMessageCtrl" ng-init="user_id = '<?php echo $user_id; ?>'" >
    <div class="receive">
        <div class="title">接受者:</div>
        <div class="name">{{ user.nickname }}</div>
    </div>
    <div class="content">
        <textarea rows="10" ng-model="content"></textarea>
    </div>
    <div class="send-message">
        <div class="send-btn" ng-click="sendMsg()">发 送</div>
    </div>
</article>