$(function(){
    var person_tab_flag = true;
    get_follow_person();

    $('.nav-item').click(function(){
        var tab_name = $(this).find('a').attr('href');
        if ((person_tab_flag && (tab_name == '#follow_person')) || (!person_tab_flag && (tab_name == '#follow_post'))){
            return;
        }
        person_tab_flag = (tab_name == '#follow_person') || false;
        if (person_tab_flag){
            get_follow_person();
        }else{
            get_follow_post_bar();
        }
    });

    //显示、隐藏取消关注操作
    $(document).on('mouseover mouseout', '.follow-item', function(event){
        if (event.type == 'mouseover'){
            $(this).find('.cancel').show();
        }else if (event.type == 'mouseout'){
            $(this).find('.cancel').hide();
        }
    });

    //取消关注的用户
    $(document).on('click', '.cancel', function(e){
        e.stopPropagation();
        var focus_id = $(this).data('id');
        var temp = $(this);
        if (confirm('确定要取消关注该用户!')){
            $.ajax({
                url: SITE_URL + 'user/cancel_focus_user',
                type: 'post',
                data: {
                    focus_id: focus_id
                },
                dataType: 'json',
                success: function (result) {
                    if (result.success) {
                        temp.parents('.follow-item').remove();
                        get_follow_person(1);
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

    //查看关注的用户主页
    $(document).on('click', '.follow-item', function () {
        var user_id = $(this).data("user-id");
        window.location.href = SITE_URL + 'my_city/visit/' + user_id;
    })

    //取消关注的贴吧
    $(document).on('click', '.operated', function(){
        var post_bar_id = $(this).data('id');
        var temp = $(this);
        if (confirm('确定要取消关注该贴吧!')){
            $.ajax({
                url: SITE_URL + 'post_bar/cancel_focus_post_bar',
                type: 'post',
                data: {
                    post_bar_id: post_bar_id
                },
                dataType: 'json',
                success: function (result) {
                    if (result.success) {
                        temp.parents('.follow-post-item').parent('li').remove();
                        get_follow_post_bar(1);
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
        if (person_tab_flag){
            get_follow_person(next_page);
        }else{
            get_follow_post_bar(next_page);
        }
    });

    //上一页
    $(document).on('click', '.prev-page', function(){
        var prev_page = parseInt($(this).data('page')) - 1;
        if (person_tab_flag){
            get_follow_person(prev_page);
        }else{
            get_follow_post_bar(prev_page);
        }
    });

    //首页
    $(document).on('click', '.home', function(){
        if (person_tab_flag){
            get_follow_person();
        }else{
            get_follow_post_bar();
        }
    });
});

//获取关注的人
function get_follow_person(page){
    page = page || 1;
    $('.no-follow-person').hide();
    $.ajax({
        url: SITE_URL + 'user/paginate_focus_user/'+page+'/12',
        type: 'post',
        dataType: 'json',
        success: function (result) {
            if (result.success) {
                var tpl = document.getElementById('follow_person_list').innerHTML;
                $("#follow_person").html(template(tpl, {list: result.data}));

                var page_tpl = document.getElementById('page_list').innerHTML;
                $("#page_content").html(template(page_tpl, {list: {current_page : page, total_page : result.total_page}}));
            }else {
                $('.no-follow-person').show();
            }
        },
        error: function () {
            cosole.log("服务器繁忙，获取地址失败");
        }
    });
}

//获取关注的贴吧
function get_follow_post_bar(page){
    $('.no-follow-post').hide();
    page = page || 1;
    $.ajax({
        url: SITE_URL + 'post_bar/get_focus_post_bar/'+page+'/10',
        type: 'post',
        dataType: 'json',
        success: function (result) {
            if (result.success) {
                var tpl = document.getElementById('follow_post_list').innerHTML;
                $.each(result.data, function(index, item){
                    // result.data[index].create_time = item.create_time.substring(0, 10);
                });
                $("#follow_post_content").html(template(tpl, {list: result.data}));

                var page_tpl = document.getElementById('page_list').innerHTML;
                $("#page_content").html(template(page_tpl, {list: {current_page : page, total_page : result.total_page}}));
            }else {
                $('.no-follow-post').show();
            }
        },
        error: function () {
            cosole.log("服务器繁忙，获取地址失败");
        }
    });
}