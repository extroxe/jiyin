$(function(){
    var tab_flag = 1;
    get_receive_message();

    $('.nav-item').click(function(){
        var tab_name = $(this).find('a').attr('href');
        back();
        if ((tab_flag == 1 && (tab_name == '#inbox')) || (tab_flag== 2 && (tab_name == '#outbox')) || (tab_flag == 3 && tab_name == '#follow')){
            return;
        }
        if (tab_name == '#inbox'){
            tab_flag = 1;
            get_receive_message();
        }else if (tab_name == '#outbox'){
            tab_flag = 2;
            get_send_message();
        }else {
            tab_flag = 3;
            get_follow();
        }
    });

    //点击查看信息详情
    $(document).on('click', '.content-wrapper', function(){
        var message_id = $(this).data('id');
        get_message_by_id(message_id, $(this));
    });

    //发信息面板
    $(document).on('click', '.send-message', function(){
        var user_id = $(this).data('id');
        get_user_info(user_id)
    });

    //发送信息
    $(document).on('click', '.send-btn', function(){
        var content = $('#message_content').val();
        var receive_user_id = $(this).data('id');
        add_message(receive_user_id, content);
    });

    //返回
    $(document).on('click', '.back', function(){
        back();
    });

    //跳页
    //下一页
    $(document).on('click', '.next-page', function(){
        var next_page = parseInt($(this).data('page')) + 1;
        if (tab_flag == 1){
            get_receive_message(next_page);
        }else if (tab_flag == 2){
            get_send_message(next_page);
        }else {
            get_follow(next_page);
        }
    });

    //上一页
    $(document).on('click', '.prev-page', function(){
        var prev_page = parseInt($(this).data('page')) - 1;
        if (tab_flag == 1){
            get_receive_message(prev_page);
        }else if (tab_flag == 2){
            get_send_message(prev_page);
        }else {
            get_follow(prev_page);
        }
    });

    //首页
    $(document).on('click', '.home', function(){
        if (tab_flag == 1){
            get_receive_message();
        }else if (tab_flag == 2){
            get_send_message();
        }else {
            get_follow();
        }
    });
});
//获取收件箱的信息
function get_receive_message(page){
    page = page || 1;
    $.ajax({
        url: SITE_URL + 'user/paginate_receive_message/'+page+'/12',
        type: 'post',
        dataType: 'json',
        success: function (result) {
            if (result.success) {
                var tpl = document.getElementById('inbox_list').innerHTML;
                $("#inbox").html(template(tpl, {list: result.data}));

                var page_tpl = document.getElementById('page_list').innerHTML;
                $("#page_content").html(template(page_tpl, {list: {current_page : page, total_page : result.total_page}}));
            }else {
                console.log(result.msg);
            }
        },
        error: function () {
            console.log("服务器繁忙，获取地址失败");
        }
    });
}

//获取发件箱的数据
function get_send_message(page){
    page = page || 1;
    $.ajax({
        url: SITE_URL + 'user/paginate_send_message/'+page+'/12',
        type: 'post',
        dataType: 'json',
        success: function (result) {
            if (result.success) {
                var tpl = document.getElementById('outbox_list').innerHTML;
                $("#outbox").html(template(tpl, {list: result.data}));

                var page_tpl = document.getElementById('page_list').innerHTML;
                $("#page_content").html(template(page_tpl, {list: {current_page : page, total_page : result.total_page}}));
            }else {
                console.log(result.msg);
            }
        },
        error: function () {
            console.log("服务器繁忙，获取地址失败");
        }
    });
}

//获取关注的人
function get_follow(page){
    page = page || 1;
    $.ajax({
        url: SITE_URL + 'user/paginate_focus_user/'+page+'/12',
        type: 'post',
        dataType: 'json',
        success: function (result) {
            if (result.success) {
                var tpl = document.getElementById('follow_list').innerHTML;
                $("#follow").html(template(tpl, {list: result.data}));

                var page_tpl = document.getElementById('page_list').innerHTML;
                $("#page_content").html(template(page_tpl, {list: {current_page : page, total_page : result.total_page}}));
            }else {
                console.log(result.msg);
            }
        },
        error: function () {
            console.log("服务器繁忙，获取地址失败");
        }
    });
}

//根据ID获取站内信详情
function get_message_by_id(id, temp){
    $.ajax({
        url: SITE_URL + 'user/get_message_by_id',
        type: 'post',
        data: {
            message_id: id
        },
        dataType: 'json',
        success: function (result) {
            if (result.success) {
                read_message(id, temp);
                $('.message-content').show();
                $('.tab-content').hide();
                var tpl = document.getElementById('msg_wrapper').innerHTML;
                $("#msg").html(template(tpl, {data: result.data}));
            }else {
                console.log(result.msg);
            }
        },
        error: function () {
            console.log("服务器繁忙，获取地址失败");
        }
    });
}
//修改信息阅读状态
function read_message(id, temp){
    $.ajax({
        url: SITE_URL + 'user/read_message',
        type: 'post',
        data: {
            message_id: id
        },
        dataType: 'json',
        success: function (result) {
            if (result.success) {
                temp.parent('.item').find('.operated').text('已读');
            }else {
                console.log(result.msg);
            }
        },
        error: function () {
            console.log("服务器繁忙，获取地址失败");
        }
    });
}

//根据ID获取用户信息
function get_user_info(user_id){
    $.ajax({
        url: SITE_URL + 'user/get_receive_msg_user_info',
        type: 'post',
        data: {
            user_id: user_id
        },
        dataType: 'json',
        success: function (result) {
            if (result.success) {
                $('.send-content').show();
                $('.tab-content').hide();
                var tpl = document.getElementById('send_wrapper').innerHTML;
                $("#send").html(template(tpl, {data: result.data}));
            }else {
                console.log(result.msg);
            }
        },
        error: function () {
            console.log("服务器繁忙，获取地址失败");
        }
    });
}

//发送信息
function add_message(receive_user_id, content){
    $.ajax({
        url: SITE_URL + 'user/add_message',
        type: 'post',
        data: {
            receive_user_id: receive_user_id,
            content: content
        },
        dataType: 'json',
        success: function (result) {
            if (result.success) {
                $('#message_content').val('');
                alert('发送成功');
            }else {
                console.log(result.msg);
            }
        },
        error: function () {
            console.log("服务器繁忙，获取地址失败");
        }
    });
}

//返回
function back(){
    $('.tab-content').show();
    $('.message-content').hide();
    $('.send-content').hide();
}