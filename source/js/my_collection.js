$(function(){
    get_collect_post();

    //取消收藏
    $(document).on('click', '.operated', function(){
        var post_id = $(this).data('id');
        var temp = $(this);
        if (confirm('确定取消收藏该帖子!')){
            $.ajax({
                url: SITE_URL + 'post/cancel_collect_post',
                type: 'post',
                data: {
                    post_id: post_id
                },
                dataType: 'json',
                success: function (result) {
                    if (result.success) {
                        temp.parents('.collection-item').parent('li').remove();
                    }else {
                        console.log(result.msg);
                    }
                },
                error: function () {
                    cosole.log("服务器繁忙，获取地址失败");
                }
            });
        }
    });

    //跳页
    //下一页
    $(document).on('click', '.next-page', function(){
        var next_page = parseInt($(this).data('page')) + 1;
        get_collect_post(next_page);
    });

    //上一页
    $(document).on('click', '.prev-page', function(){
        var prev_page = parseInt($(this).data('page')) - 1;
        get_collect_post(prev_page);
    });

    //首页
    $(document).on('click', '.home', function(){
        get_collect_post();
    });
});
//获取帖子的收藏列表
function get_collect_post(page){
    page = page || 1;
    $('.no-collection').hide();
    $.ajax({
        url: SITE_URL + 'post/paginate_collect_post/'+page+'/10',
        type: 'post',
        dataType: 'json',
        success: function (result) {
            if (result.success) {
                var tpl = document.getElementById('collection_list').innerHTML;
                $.each(result.data, function(index, item){
                    result.data[index].create_time = item.create_time.substring(0, 10);
                });
                $("#collection_content").html(template(tpl, {list: result.data}));

                var page_tpl = document.getElementById('page_list').innerHTML;
                $("#page_content").html(template(page_tpl, {list: {current_page : page, total_page : result.total_page}}));
            }else {
                $('.no-collection').show();
            }
        },
        error: function () {
            cosole.log("服务器繁忙，获取地址失败");
        }
    });
}