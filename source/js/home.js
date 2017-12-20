$(function(){
    /**
     * 搜索贴吧
     */

    $('#search-btn-post').click(function(){
        var key_words = $('#key_words').val();
        if (key_words != '' && key_words != null){
            window.location.href = SITE_URL+'my_city/post_bar_search?key_words='+key_words;
        }
    });
});
