/**
 * Created by sailwish009 on 2017/2/9.
 */
$(function () {
    var page = 1;
    var page_size = 10;
    var all_comment = [];
    var comment = [];
    var reply_comment = [];
    var flag = true;
    var total_pages = 1;
    var land_host_id = '';

    var url = window.location.href;
    var post_id = url.substring(url.lastIndexOf('/') + 1);
    var post_bar_id = url.split('/')[url.split('/').length - 2];

    $('.post_collect').hide();
    $('.post_collected').hide();

    /**
     * 根据贴吧id获取贴吧信息
     */

    function get_post_bar_info(post_bar_id){
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'post_bar/get_post_bar_by_id' ,
            data:{
                id:post_bar_id
            },
            success:function (response) {
                if(response.success){
                    $('.post_name').text(response.data.name);
                    $('.body .focus_num span').text(response.data.focus_count);
                    $('.body .post_num span').text(response.data.post_count);
                    if(response.data.is_focused == true){
                        $('#focus_post_bar').hide();
                        $('#cancel_focus_post_bar').show();
                    }else{
                        $('#focus_post_bar').show();
                        $('#cancel_focus_post_bar').hide();
                    }
                }else{
                    console.log(response.msg);
                }
            }
        });
    }
    get_post_bar_info(post_bar_id);

    /**
     * 根据帖子id查看帖子详情
     */
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: SITE_URL + 'post/get_post_by_id/' + post_id ,
        success:function (response) {
            if(response.success){
                $('.post_title').text(response.data.title);
                $('.post_detail_name').text(response.data.nickname);
                $('.land_host').attr("src", response.data.avatar_path);
                $('.post_detail_content').html(response.data.content);
                $('.post_detail_create_time').text(response.data.create_time);
                $('.land_host').attr('src', SITE_URL + response.data.avatar_path);
                land_host_id = response.data.user_id;

                if(response.data.collect_post_id == '0'){
                    $('.post_collect').show();
                    $('.post_collected').hide();
                }else{
                    $('.post_collect').hide();
                    $('.post_collected').show();
                }
            }else{
                alert(response.msg);
            }
        }
    });

    /**
     * 查看用户主页
     */
    $('.land_host').click(function () {
        window.location.href = SITE_URL + 'my_city/visit/' + land_host_id;
    });
    $('.post_detail_name').click(function () {
        window.location.href = SITE_URL + 'my_city/visit/' + land_host_id;
    });


    $(document).on('click', '.check_user', function () {
        var user_id = $(this).data('user-id');
        window.location.href = SITE_URL + 'my_city/visit/' + user_id;
    });

    /**
     * 搜索贴吧
     */

    $('#key_words').bind('keypress',function(event){
        var key_words = $(this).val();
        search_result(key_words);
    });
    $('#search-btn-post').click(function(){
        var key_words = $('#key_words').val();
        search_result(key_words);
    });


    /**
     * 根据帖子id分页查看帖子评论及回复
     */
    init_data(page);
    function init_data(page){
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'post/paginate_comment/' + page + '/' + page_size,
            data:{
                post_id:post_id
            },
            success:function (response) {
                all_comment = [];
                comment = [];
                reply_comment = [];
                if(response.success){
                    total_pages = response.total_page;
                    if(page == response.total_page){
                        $('#Next_page').parent().addClass('disabled');
                        flag = false;
                    }else{
                        $('#Next_page').parent().removeClass('disabled');
                        flag = true;
                    }

                    $('.delt_comment').hide();
                    all_comment = response.data;
                    $('#total_pages').text(response.total_page);
                }else{
                    $('.delt_comment').show();
                    $('#Next_page').parent().addClass('disabled');
                    flag = false;
                    page = 1;
                    $('#total_pages').html(1);
                }

                for(var i = 0; i<all_comment.length; i++){
                    if( land_host_id == all_comment[i].publisher_id){
                        all_comment[i]["land_icon"] = true;
                    }else{
                        all_comment[i]["land_icon"] = false;
                    }
                }
                
                var comment_tpl = document.getElementById('post_comment_tpl').innerHTML;
                $('#comment_cotent').html(template(comment_tpl, {comment_data: all_comment}));

                $('.replay_content_del').hide();
                /*判断是否显示收起回复*/
                for(var i = 0; i<all_comment.length; i++){
                    if(all_comment[i].replies.length == 0){
                        $('.replay_comment_list').each(function () {
                            if($(this).data('id') == parseInt(all_comment[i].id)){
                                $(this).parents('.collapseOne').removeClass('in');
                                $(this).parents('.collapseOne').siblings('.replay_btn').text('回复');
                            }
                        });
                    }
                }
                for(var i = 0; i<all_comment.length; i++){
                    for(var j = 0; j<all_comment[i].replies.length;j++){
                        if(all_comment[i].replies[j].status_id == 4){
                            $('.replay_box').hide();
                            $('.replay_content_del').text('提示：该评论已被(本人)删除');
                            $('.replay_content_del').show();
                        }else if(all_comment[i].replies[j].status_id == 2){
                            $('.replay_box').hide();
                            $('.replay_content_del').text('提示：该评论已被(管理员)删除');
                            $('.replay_content_del').show();
                        }else if(all_comment[i].replies[j].status_id == 3){
                            $('.replay_box').hide();
                            $('.replay_content_del').text('提示：该评论已被(楼主)删除');
                            $('.replay_content_del').show();
                        }else{
                            $('.replay_box').show();
                            $('.replay_content_del').hide();
                        }
                    }
                };

                $('#page_num').val(page);
                if(parseInt($('#page_num').val()) == 1){
                    $('#Prev_page').parent().addClass('disabled');
                }else{
                    $('#Prev_page').parent().removeClass('disabled');
                };
            },
            error:function () {

            }
        });
    }

   // 返回贴吧帖子列表

    $('.post_name').click(function () {
        window.location.href = SITE_URL + 'my_city/post/' + post_bar_id;
    });

      //搜索贴吧
    $('#search-btn-post').click(function(){
        var key_words = $('#key_words').val();
        if (key_words != '' && key_words != null){
            window.location.href = SITE_URL+'my_city/post_bar_search?key_words='+key_words;
        }
    });

     // 关注贴吧

    $('#cancel_focus_post_bar').hide();
    $(document).on('click', '#focus_post_bar', function () {
        var self = $(this);
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'post_bar/focus_post_bar',
            data:{
                post_bar_id: post_bar_id
            },
            success:function (response) {
                if(response.success){
                    get_post_bar_info(post_bar_id);
                    self.hide();
                    $('#cancel_focus_post_bar').show();
                    alert(response.msg)
                }else{
                    alert(response.msg);
                }
            },
            error:function () {

            }
        })
    });

     // 取消关注

    $(document).on('click', '#cancel_focus_post_bar', function () {
        var self = $(this);
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'post_bar/cancel_focus_post_bar',
            data:{
                post_bar_id:post_bar_id
            },
            success:function (response) {
                if(response.success){
                    get_post_bar_info(post_bar_id);
                    self.hide();
                    $('#focus_post_bar').show();
                    alert(response.msg);
                }else{
                    alert(response.msg);
                }
            },
            error:function () {

            }
        })
    });

    //收藏帖子
    $(document).on('click', '.post_collect', function () {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'post/collect_post',
            data:{
                post_id: post_id
            },
            success:function (response) {
                if(response.success){
                    $('.post_collect').hide();
                    $('.post_collected').show();
                    alert(response.msg);
                }else{
                    alert(response.msg);
                }
            },
            error:function () {

            }
        });
    });


     // 取消收藏帖子

    $(document).on('click', '.post_collected', function () {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'post/cancel_collect_post',
            data:{
                post_id: post_id
            },
            success:function (response) {
                if(response.success){
                    $('.post_collect').show();
                    $('.post_collected').hide();
                    alert(response.msg);
                }else{
                    alert(response.msg);
                }
            },
            error:function () {

            }
        });
    });

    function publish_comment(post_id, content, root_comment_id, comment_id, to_user_id) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'post/add_comment',
            data:{
                post_id: post_id,
                content: content,
                root_comment_id: root_comment_id,
                comment_id: comment_id,
                to_user_id: to_user_id
            },
            success:function (response) {
                if(response.success){
                    init_data(1);
                    alert('发表成功');
                }else{
                    alert(response.msg);
                }
            },
            error:function () {

            }
        });
    };



    /**
     * 对评论发表评论
     */
    $('.publish_comment_to_comment').hide();
    $(document).on('click', '.publish_comment_to_comment', function () {
        var content = $(this).siblings('textarea').val();
        var comment_id = $(this).parents('.replay_comment_list').data('id');
        var to_user_id = $(this).parents('.replay_comment_list').data('publisher-id');
        publish_comment(post_id, content, comment_id, comment_id, to_user_id);
    });

    $(document).on('click', '.publish_comment_btn', function () {
        var content = $(this).siblings('.publish_comment').val();
        publish_comment(post_id, content);
    })

    /**
     * 对评论及回复发表回复
     */

    $(document).on('click', '.publish_comment_to_replay', function () {
        var content = $(this).siblings('textarea').val();
        var root_comment_id = $(this).parents('.replay_comment_list').data('id');
        var comment_id = $(this).parents('.replay_comment_list').find('li .replay .reply_comment').data('comment-id');
        var to_user_id = $(this).parents('.replay_comment_list').find('li .replay .reply_comment').data('to-user-id');
        publish_comment(post_id, content, root_comment_id, comment_id, to_user_id);
    });



    $(document).on('click', '.replay_btn', function () {
        $(this).siblings('.collapseOne').toggleClass('in');
        if($(this).html() == '展开回复'){
            $(this).text('收起回复')
        }else{
            $(this).text('展开回复')
        }
    });

    $(document).on('click', '.more_replay', function () {
        $(this).parent('div').hide()
    });

    $(document).on('click', '.my_say, .reply_comment', function () {
        $('.publish_comment_to_replay').hide();
        $('.publish_comment_to_comment').show();
        $(this).parent().next().find('.collapseTwo').toggleClass('in');
    });
    $(document).on('click', '.reply_comment', function () {
        $('.publish_comment_to_replay').show();
        $('.publish_comment_to_comment').hide();
        $(this).parents('.replay_comment_list').find('li:last-child .collapseTwo').toggleClass('in');
    });

    //上一页
    $('#Prev_page').click(function () {
        page--;
        if(page < 1){
            page = 1;
        }else{
            init_data(page);
        };
    });
    //下一页
    $('#Next_page').click(function () {
        if(flag == true){
            page++;
            init_data(page);
        }
    });
    //跳转页面
    $('#jump_to_page').click(function () {
        page = parseInt($(this).siblings('#page_num').val());
        init_data(page);
    });
});

function search_result(key_words) {
    if (key_words != '' && key_words != null){
        window.location.href = SITE_URL+'my_city/post_bar_search?key_words='+key_words;
    }
}