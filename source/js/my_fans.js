$(function(){
    get_fans();

    //跳页
    //下一页
    $(document).on('click', '.next-page', function(){
        var next_page = parseInt($(this).data('page')) + 1;
        get_fans(next_page);
    });

    //上一页
    $(document).on('click', '.prev-page', function(){
        var prev_page = parseInt($(this).data('page')) - 1;
        get_fans(prev_page);
    });

    //首页
    $(document).on('click', '.home', function(){
        get_fans();
    });

    /**
     * 查看我的粉丝主页
     */
    $(document).on("click", '.fans-item', function () {
        var follows_id = $(this).data("follows-id");
        window.location.href = SITE_URL + 'my_city/visit/' + follows_id;
    })
});

//获取我的粉丝
function get_fans(page){
    page = page || 1;
    $('.no-fans').hide();
    $.ajax({
        url: SITE_URL + 'user/paginate_fans/'+page+'/12',
        type: 'post',
        dataType: 'json',
        success: function (result) {
            if (result.success) {
                var tpl = document.getElementById('fans_list').innerHTML;
                $("#fans").html(template(tpl, {list: result.data}));

                var page_tpl = document.getElementById('page_list').innerHTML;
                $("#page_content").html(template(page_tpl, {list: {current_page : page, total_page : result.total_page}}));
            }else {
                $('.no-fans').show();
            }
        },
        error: function () {
            console.log("服务器繁忙，获取地址失败");
        }
    });
}