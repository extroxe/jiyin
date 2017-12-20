/**
 * Created by sailwish009 on 2017/2/9.
 */
$(function () {
    var page = 1;
    var page_size = 6;
    var view_more = false;

    /**
     * 获取关注的贴吧
     */
    function init_post_bar(page){
        var view_more = false;
        $.ajax({
            type:'get',
            dataType:'json',
            url: SITE_URL + 'post_bar/get_focus_post_bar/' + page + '/' + page_size,
            success:function (response) {
                if(response.success){
                    view_more = true;
                    if(response.data.length <= 2){
                        $('#look_more').css("display", 'none');
                    }else{
                        $('#look_more').css("display", 'block');
                    }
                    for(var i = 0; i<response.data.length; i++){
                        $('.focused_bar_box ul').append('<li class="focused_bar"> <a href="'+ SITE_URL + 'my_city/post/' + response.data[i].post_bar_id +'">' + response.data[i].name + ' </a></li>');
                    }
                }else{

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
     * 获取推荐的吧
     */
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: SITE_URL + 'post_bar/get_recommend_post_bar',
        success:function (response) {
            if (response.success){
                for(var i = 0; i<response.data.length; i++){
                    if(response.data[i].description == null){
                        response.data[i].description = '';
                    }
                }
                init_post(response.data);
            }
        }
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
     * 查看贴吧帖子
     */
    $(document).on('click', '.post_head', function () {
        var post_bar_id = $(this).data('post-bar-id');
        console.log(post_bar_id);
        window.location.href = SITE_URL + 'my_city/post/' + post_bar_id;
    });
});

//获取推荐的吧的列表
function init_post(data) {
    for(var i = 0; i<data.length; i++){
        $('.hot_post ul').append('<li>\
                        <span class="number background">'+ parseInt(i+1)+'</span>\
                        <a href="'+ SITE_URL + 'my_city/post/' + data[i].id +'" class="text_head">'+ data[i].name +'</a>\
                        <span class="view_count">'+ data[i].focus_count+'</span>\
                        </li>');

        $('table tbody').append('<tr>\
                        <td class="head_img">\
                        <div>\
                        <img src="<?=site_url("  + source/img/forum_new.gif ); ?">\
                        </div>\
                        </td>\
                        <td class="body_content">\
                        <a href="javascript:void(0)" data-post-bar-id = "'+ data[i].id +'" class="post_head">'+ data[i].name +'</a>\
                        <span style="">帖数：'+ data[i].post_count +'</span>\
                        <span style="">关注：'+ data[i].focus_count +'</span>\
                    <a href="javascript:void(0)">'+ data[i].description +'</a>\
                        </td>\
                        </tr>');
    }
};

function search_result(key_words) {
    if (key_words != '' && key_words != null){
        window.location.href = SITE_URL+'my_city/post_bar_search?key_words='+key_words;
    }
}

