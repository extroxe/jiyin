/**
 * Created by sailwish009 on 2017/2/16.
 */
$(function () {
    var page = 1;
    var page_size = 5;
    var total_pages = 0;
    var flag = true;
    var total_focus = 0;

    var url = window.location.href;
    var user_id = url.substring(url.lastIndexOf('/') + 1);



    /**
     * 获取粉丝
     */
    function get_fans(page){
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'post_bar/get_fans/' + page + '/' +page_size,
            data:{
                user_id: user_id,
                flag: true
            },
            success:function (response) {
                if(response.success){
                    $('.personal_info .follows span').text(response.fans);
                    total_focus = response.total_focus;
                    total_pages = response.total_page;
                    if(page == response.total_page){
                        $('#Next_page').parent().addClass('disabled');
                        flag = false;
                    }else{
                        $('#Next_page').parent().removeClass('disabled');
                        flag = true;
                    };
                    var tpl = document.getElementById('focus_lists_tpl').innerHTML;
                    $('#focus_lists').html(template(tpl, {data:response.data}));
                    $('#total_pages').text(response.total_page);
                }else{
                    $('#Next_page').parent().addClass('disabled');
                    flag = false;
                    page = 1;
                    $('#total_pages').html(1);
                };
                $('#page_num').val(page);
                if(parseInt($('#page_num').val()) == 1){
                    $('#Prev_page').parent().addClass('disabled');
                }else{
                    $('#Prev_page').parent().removeClass('disabled');
                };
            }
        });
    };
    get_fans(page);

    /**
     * 获取关注
     */
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: SITE_URL + 'post_bar/get_focus_user/' + page + '/' +page_size,
        data:{
            user_id: user_id,
            flag: true
        },
        success:function (response) {
            if(response.success){
                $('.personal_info .focus span').text(response.total_focus);
            }
        }
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
     * 返回用户
     */
    $('.personal_info .nickname a').click(function () {
        window.location.href = SITE_URL + 'my_city/visit/' + user_id;
    });

    /**
     * 关注用户
     */
    $('.focused').hide();
    $(document).on('click', '.focus_btn', function () {
        var self = $(this);
        var focus_user_id = $(this).data('focus-id');
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'user/focus_user',
            data: {
                focus_id: focus_user_id
            },
            success:function (response) {
                if(response.success){
                    get_fans(1);
                    alert(response.msg);
                }else {
                    alert(response.msg);
                }
            }
        });
    });
    /**
     * 取消关注
     */
    $(document).on('click', '.focused_btn', function () {
        var self = $(this);
        var focus_user_id = $(this).data('focus-id');
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'user/cancel_focus_user',
            data: {
                focus_id: focus_user_id
            },
            success:function (response) {
                if(response.success){
                    get_fans(1);
                    alert(response.msg);
                }else {
                    alert(response.msg);
                }
            }
        });
    });

    //上一页
    $('#Prev_page').click(function () {
        page--;
        if(page < 1){
            page = 1;
        }else{
            get_fans(page);
        };
    });
    //下一页
    $('#Next_page').click(function () {
        if(flag == true){
            page++;
            get_fans(page);
        }
    });
    //跳转页面
    $('#jump_to_page').click(function () {
        page = parseInt($(this).siblings('#page_num').val());
        get_fans(page);
    });
})
