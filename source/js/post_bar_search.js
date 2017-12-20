/**
 * Created by sailwish009 on 2017/2/9.
 */
$(function () {
    var page = 1;
    var page_size = 6;
    var view_more = true;

    /**
     * 获取贴吧数据
     */
    var key_words = $('#key_words').val();
    get_search_par(key_words);

    /**
     * 获取关注的贴吧
     */
    function init_post_bar(page){
        $.ajax({
            type:'get',
            dataType:'json',
            url: SITE_URL + 'post_bar/get_focus_post_bar/' + page + '/' + page_size,
            success:function (response) {
                if(response.success){
                    if(response.data.length <= 4){
                        $('#look_more').css("display", 'none');
                    }else{
                        $('#look_more').css("display", 'block');
                    }
                    for(var i = 0; i<response.data.length; i++){
                        $('.focused_bar_box ul').append('<li class="focused_bar"> <a href="'+ SITE_URL + 'my_city/post/' + response.data[i].post_bar_id +'">' + response.data[i].name + ' </a></li>');
                    }
                }else{
                    view_more = false;
                }
            },
            error:function () {
            }
        });
    };
    init_post_bar(page);

    /**
     * 查看更多吧
     */
    $('#look_more').click(function () {
        page++;
        if(view_more == true){
            init_post_bar(page);
        }else{
            alert("暂无更多的把");
        }
    });

    /**
     * 搜索贴吧
     */

    $('#search-btn-post').click(function(){
        key_words = $('#key_words').val();
        if (key_words != '' && key_words != null){
            get_search_par(key_words);
        }
    });
    $('#key_words').bind('keypress',function(event){
        key_words = $(this).val();
        get_search_par(key_words);
    });

    /**
     * 查看贴吧帖子
     */
    $(document).on('click', '.post_head', function () {
        var post_bar_id = $(this).data('post-bar-id');
        console.log(post_bar_id);
        window.location.href = SITE_URL + 'my_city/post/' + post_bar_id;
    });
});

//获取搜索的吧的列表
function init_post(data) {
    $('table tbody').html("");
    for(var i = 0; i<data.length; i++){
        if(data[i].description == null){
            data[i].description = '';
        }
        $('table tbody').append('<tr>\
                        <td class="head_img">\
                        <div>\
                        <img src="<?=site_url("  + source/img/forum_new.gif ); ?">\
                        </div>\
                        </td>\
                        <td class="body_content">\
                        <a href="javascript:void(0)" data-post-bar-id = "'+ data[i].id +'" class="post_head">'+ data[i].name +'</a>\
                        <span style="">帖数：'+ data[i].post_count +'</span>\
                        <span style="">时间：'+ data[i].create_time +'</span>\
                    <a href="javascript:void(0)">'+ data[i].description +'</a>\
                        </td>\
                        </tr>');
    }
}

//获取搜索的贴吧
function get_search_par(key_words) {
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: SITE_URL +'post_bar/search_post_bar?key_words='+key_words,
        success:function (response) {
            if(response.success){
                init_post(response.data);
                console.log(response.msg);
            }else{
                $('table tbody').html('');
                $('table tbody').html('<div class="no-post-info">\
                                            <img style="width: 100px" src="' +SITE_URL+ 'source/img/warning.png">\
                                            没有此贴吧信息`~`</div>')
            }
        },
        error:function () {

        }
    });
}

