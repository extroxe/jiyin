/**
 * Created by sailwish009 on 2017/2/6.
 */
$(function () {
    var page = 1;
    var page_size = 6;
    var data = [];
    var flag = true;
    function init() {
        flag = false;
        $.ajax({
            type: 'get',
            dataType: 'json',
            url: SITE_URL + 'article/paginate/' + page + '/' + page_size,
            success: function (response) {
                if (response.success) {
                    if(response.data.length == page_size){
                        flag = true;
                        $('.loading').show();
                    }else{
                        $('.loading').hide();
                    }
                    load(response.data);
                    $('.no_data').hide();
                }else{
                    $('.no_data').hide();
                }
            }
        });
    }
    init();

    $(window).scroll(function() {
        if ($(document).scrollTop() >= ($(document).height() - $(window).height() - 500)) {
            if(flag == true){
                 page++;
                 init();
            }
        }
    });

    function load(article_lists) {
        $.each(article_lists, function (index, article_list) {
            article_list['create_time'] = article_list['create_time'].substr(0,16);
            $('.content').append('<div class="item" data-article-id=' +article_list['id'] + '>\
                <img src="'+SITE_URL+article_list['thumbnail_path']+'" alt="">\
                <div class="item-title">'+article_list['title']+'</div>\
                <div class="item-description">'+article_list['abstract']+'</div>\
                <div class="item-date">'+article_list['create_time']+'</div>\
                </div>');
        })

    }

    //查看文章详情
    $(document).on('click', '.item', function () {
        var article_id = $(this).data('article-id');
        window.location.href = SITE_URL + 'article/detail/' + article_id;
    });
});
