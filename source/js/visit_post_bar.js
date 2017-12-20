/**
 * Created by sailwish009 on 2017/2/16.
 */
$(function () {
    var page = 1;
    var page_size = 5;
    var total_pages = 0;
    var flag = true;

    var url = window.location.href;
    var user_id = url.substring(url.lastIndexOf('/') + 1);

    /**
     * 获取吧友的关注的贴吧和发表的帖子信息
     */
    function init_post_lists(page) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'post_bar/get_user_post_info/' + page + '/' + page_size,
            data: {
                user_id: user_id
            },
            success:function (response) {
                if(response.success){
                    $('.pagination').show();
                    $.each(response.data, function (index, data) {
                        if(data.publish_time == null){
                            data.publish_time = '未知';
                        }
                    });
                    total_pages = response.total_page;
                    if(page == response.total_page){
                        $('#Next_page').parent().addClass('disabled');
                        flag = false;
                    }else{
                        $('#Next_page').parent().removeClass('disabled');
                        flag = true;
                    };
                    response.data.page = page;
                    var tpl = document.getElementById('post_list_tpl').innerHTML;
                    $('#post_lists').html(template(tpl, {post_list:response.data}));
                    $('#total_pages').text(response.total_page);
                }else {
                    $('.pagination').hide();
                    $('#Next_page').parent().addClass('disabled');
                    flag = false;
                    page = 1;
                    $('#total_pages').html(1);
                    // console.log(response.msg);
                }
                $('.post_nums').text(response.total_post);

                $('#page_num').val(page);
                if(parseInt($('#page_num').val()) == 1){
                    $('#Prev_page').parent().addClass('disabled');
                }else{
                    $('#Prev_page').parent().removeClass('disabled');
                }
            },
            error:function () {
                
            }
        });
    };
    init_post_lists(page);

    //获取用户信息
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: SITE_URL + 'user/get_personal_info',
        data:{
            id:user_id
        },
        success:function (response) {
            if(response.success){
                $('#to_user_letter').text(response.data.nickname)
;            }else{
                console.log(response.msg);
            }
        }
    });

    /**
     * 获取关注的人信息
     */

    $.ajax({
        type: 'post',
        dataType: 'json',
        url: SITE_URL + 'post_bar/get_focus_user/' + page + '/' + page_size,
        data: {
            user_id: user_id
        },
        success:function (response) {
            if(response.success){
                for(var i = 0 ;i<response.data.length; i++){
                    $('.focus_num').text('(' +response.total_focus+')');
                    $('.focus_lists').append('<li><img class="portrait" src="' + SITE_URL + response.data[i].avatar_path + '"></li>');
                }
            }
        }
    });

    /**
     * 查看关注的人
     */
    $('.focus_num').click(function () {
        window.location.href = SITE_URL + 'my_city/focus_lists/' + user_id;
    });
    $('.fallows_num').click(function () {
        window.location.href = SITE_URL + 'my_city/follow_lists/' + user_id;
    });

    /**
     * 搜索贴吧
     */

    $('#search-btn-post').click(function(){
        var key_words = $('#key_words').val();
        if (key_words != '' && key_words != null){
            window.location.href = SITE_URL+'my_city/post_bar_search?key_words='+key_words;
        }
    });

    /**
     *  关注
     */
    $(document).on('click', '#focus', function () {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'user/focus_user',
            data:{
                focus_id: user_id
            },
            success:function (response) {
                if (response.success){
                    $('#focus').text('取消关注');
                    $('#focus').removeClass('focus').addClass('focused').attr('id', 'focused');
                }else{
                    console.log(response.msg);
                }
            }
        });
    });
    /**
     * 取消关注
     */
    $(document).on('click', '#focused', function () {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'user/cancel_focus_user',
            data:{
                focus_id: user_id
            },
            success:function (response) {
                if (response.success){
                    $('#focused').text('关注');
                    $('#focused').removeClass('focused').addClass('focus').attr('id', 'focus');
                }else{
                    console.log(response.msg);
                }
            }
        });
    });
    /**
     * 获取粉丝信息
     */

    $.ajax({
        type: 'post',
        dataType: 'json',
        url: SITE_URL + 'post_bar/get_fans/' + page + '/' + page_size,
        data: {
            user_id: user_id
        },
        success:function (response) {
            if(response.success){
                for(var i = 0 ;i<response.data.length; i++){
                    $('.fallows_num').text('(' +response.fans+')');
                    $('.follow_lists').append('<li><img class="portrait" src="' + SITE_URL + response.data[i].avatar_path + '"></li>');
                }
            }else {
                // console.log(response.msg);
            }
        }
    });


    /**
     * 发送信息
     */
    $(document).on("click", ".send_msg", function () {
        var content = $(".modal-body textarea").val();
        add_message(user_id, content);
    });

    //上一页
    $('#Prev_page').click(function () {
        page--;
        if(page < 1){
            page = 1;
        }else{
            init_post_lists(page);
        };
    });
    //下一页
    $('#Next_page').click(function () {
        if(flag == true){
            page++;
            init_post_lists(page);
        }
    });
    //跳转页面
    $('#jump_to_page').click(function () {
        page = parseInt($(this).siblings('#page_num').val());
        init_post_lists(page);
    });
});

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
                console.log('发送成功');
                $('#myModal').removeClass("in");
                $(".modal-backdrop").removeClass("in").css("display", 'none');
            }else {
                console.log(result.error);
            }
        },
        error: function () {
            console.log("服务器繁忙，获取地址失败");
        }
    });
}