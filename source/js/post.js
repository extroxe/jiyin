/**
 * Created by sailwish009 on 2017/2/9.
 */
$(function () {
    var recommend_post = [];
    var url= window.location.href;
    var post_bar_id = url.substring(url.lastIndexOf('/') + 1);
    var flag = true;




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
                    $('.post_name').text(response.data.name)
                    $('.breadcrumb .post_bar_name').text(response.data.name);
                    $('.body .focus_num span').text(response.data.focus_count);
                    $('.body .post_num span').text(response.data.post_count);
                    if (response.data.description == null){
                        $('#des_content').parent().hide();
                    }else{
                        $('#des_content').parent().show();
                        $('#des_content').text(response.data.description);
                    }
                    if(response.data.is_focused == true){
                        $('#focus_post_bar').hide();
                        $('#cancel_focus_post_bar').show();
                    }else{
                        $('#focus_post_bar').show();
                        $('#cancel_focus_post_bar').hide();
                    }

                }else{
                    alert(response.msg);
                }
            }
        });
    };
    get_post_bar_info(post_bar_id);

    /**
     * 关注贴吧
     */
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
                get_post_bar_info(post_bar_id);
                if(response.success){
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
    /**
     * 取消关注
     */
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
                get_post_bar_info(post_bar_id);
                if(response.success){
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
     * 获取推荐的吧
     */
    function init_recommend(){
        recommend_post = [];
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'post_bar/get_recommend_post_bar',
            data:{
                num: 3
            },
            success:function (response) {
                if(response.success){
                    recommend_post = response.data;
                    for(var i = 0; i<recommend_post.length;i++){
                        $('.hot_post ul').append('<li>\
                    <span class="background">' + parseInt(i+1) + '</span>\
                    <a href="'+ SITE_URL + 'my_city/post/' + recommend_post[i].id +'">' + recommend_post[i].name +'</a>\
                </li>');
                    }
                    console.log(response.msg);
                }else{
                    alert(response.msg)
                }
            }
        });
    };
    init_recommend();
    /**
     * 换一批
     */
    $('.exchange').click(function () {
        $('.hot_post ul').html('');
        init_recommend();
    });

    /**
     * 发帖
     */
    $('.publish_post_btn').click(function () {
        window.location.href = SITE_URL + 'my_city/publish_post/' + post_bar_id;
    });

    /**
     * 分页获取帖子列表
     */
    var page = 1, page_size = 5;
    function init_data(page) {
        $('.empty-post').hide();
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'post/paginate/' + page + '/' + page_size,
            data:{
                post_bar_id:post_bar_id
            },
            success:function (response) {
                if(response.success){
                    $('table tbody').html('');
                    if(page == response.total_page){
                        $('#Next_page').parent().addClass('disabled');
                        flag = false;
                    }else{
                        $('#Next_page').parent().removeClass('disabled');
                        flag = true;
                    }
                    
                    var tpl = document.getElementById('post_lists_tpl').innerHTML;
                    $('table tbody').html(template(tpl,{data:response.data}));
                    $('#total_pages').text(response.total_page);


                    $.each(response.data, function (index, post) {
                        $('.post_content').each(function () {
                            if($(this).data('id') == post.id){
                                $(this).html(post.content);
                            }
                        });
                    });
                    $('.personal_info').css('height',$('.post_list_box').height());
                }else{
                    $('#Next_page').parent().addClass('disabled');
                    $('.empty-post').show();
                    flag = false;
                    page = 1;
                    $('#total_pages').html(1);
                };

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
    }
    init_data(page);

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

    /**
     * 查看帖子详情
     */
    $(document).on('click', '.post_head', function () {
        var post_id = $(this).data('post-id');
        window.location.href = SITE_URL +'my_city/view_post/' +post_bar_id + '/' + post_id;
    });

});

function search_result(key_words) {
    if (key_words != '' && key_words != null){
        window.location.href = SITE_URL+'my_city/post_bar_search?key_words='+key_words;
    }
}

