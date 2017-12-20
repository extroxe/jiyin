$(function(){
    get_post();

    //跳页
    //下一页
    $(document).on('click', '.next-page', function(){
        var next_page = parseInt($(this).data('page')) + 1;
        get_post(next_page);
    });

    //上一页
    $(document).on('click', '.prev-page', function(){
        var prev_page = parseInt($(this).data('page')) - 1;
        get_post(prev_page);
    });

    //首页
    $(document).on('click', '.home', function(){
        get_post();
    });
});

//获取我的帖子
function get_post(page){
    page = page || 1;
    $.ajax({
        url: SITE_URL + 'my_city/get_my_post/'+page+'/10',
        type: 'post',
        dataType: 'json',
        success: function (result) {
            if (result.success) {
                var tpl = document.getElementById('post_list').innerHTML;
                $.each(result.data, function(index, item){
                    result.data[index].create_time = item.create_time.substring(0, 10);
                });
                $("#post_content").html(template(tpl, {list: result.data}));

                var page_tpl = document.getElementById('page_list').innerHTML;
                $("#page_content").html(template(page_tpl, {list: {current_page : page, total_page : result.total_page}}));
            }else {
                console.log(result.msg);
            }
        },
        error: function () {
            cosole.log("服务器繁忙，获取地址失败");
        }
    });
}