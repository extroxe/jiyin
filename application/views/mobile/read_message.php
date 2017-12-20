<header>
    <div class="titlebar">
        <a class="titlebar-button" ng-click="back()"><i class="icon size16 icon-arrowleft back_btn"></i></a>
        <h1 class="text-center" style="margin-right: 40px;"><?php echo $title; ?></h1>
    </div>
</header>
<article ng-controller="readMessageCtrl" ng-init="message_id = '<?php echo $message_id; ?>'" >
    <div class="message-wrapper">
        <div class="name">
            {{ message.status_type == 1 ? '接受者: ' + message.receive_user_username : '发送者: ' + message.send_user_username }}
        </div>
        <div class="content">
            {{ message.content }}
        </div>
        <div class="date">
            {{ message.create_time }}
        </div>
    </div>
</article>