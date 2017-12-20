$(function(){
    //选择分类
    $('[data-category-id]').click(function() {
        var category_id = $(this).attr('data-category-id');
        var category_name = $(this).text();
        var url = remove_url_parameter('result');
        url = remove_url_parameter('category', url);
        window.location.href = url + 'category=' + category_id + '&result=' + category_name;
    });
    //选择类型
    $('[data-type-id]').click(function() {
        var type_id = $(this).attr('data-type-id');
        var type_name = $(this).text();
        var url = remove_url_parameter('result');
        url = remove_url_parameter('type', url);
        window.location.href = url + 'type=' + type_id + '&result=' + type_name;
    });
    //选择价格
    $('.select-price').click(function(){
        var price_str = $(this).data('price');
        var url = remove_url_parameter('price');
        window.location.href = url + 'price=' +price_str;
    });

    /**
     * 上一页
     */
    $(document).on('click', '.prev-page', function(){
        var url = remove_url_parameter('page');
        $current_page = $('.current-page').text();
        window.location.href = url + 'page=' + (parseInt($current_page) - 1) + '#search-list';
    });

    /**
     * 下一页
     */
    $(document).on('click', '.next-page', function(){
        var url = remove_url_parameter('page');
        $current_page = $('.current-page').text();
        window.location.href = url + 'page=' + (parseInt($current_page) + 1) + '#search-list';
    });

    /**
     * 选择页数
     */
    $(document).on('click', '.point-page', function(){
        var url = remove_url_parameter('page');
        $page = $(this).text();
        $('#search-list').scroll();
        window.location.href = url + 'page=' + $page + '#search-list';
    });

    /**
     * 跳页
     */
    $(document).on('click', '.skip-btn', function(){
        var url = remove_url_parameter('page');
        $page = $('.skip').find('input').val();
        window.location.href = url + 'page=' + $page + '#search-list';
    });

    /**
     * 立即购买
     */
    $('.buy_directly').click(function(){
        var commodity_id = $(this).data('commodity-id');
        var specification_id = $(this).data('specification-id');
        var amount = 1;

        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL+'shopping_cart/add',
            data : {
                commodity_id : commodity_id,
                amount : amount,
                specification_id : specification_id,
                is_buy_now  :   1
            },
            success : function(response){
                if (response.success){
                    window.location.href = SITE_URL+'shopping_cart/buy_now/'+response.insert_id;
                }else{
                    alert(response.msg);
                }
            },
            error : function(error){
                alert('系统繁忙，请稍后重试');
            }

        });
    });

    /**
     * 立即兑换
     */
    $('.exchange').click(function(){
        var commodity_id = $('.exchange').data('commodity-id');
        var amount = 1;

        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL+'commodity/check_point_enough',
            data : {
                commodity_id : commodity_id,
                amount : amount
            },
            success : function(response){
                if (response.success){
                    window.location.href = SITE_URL+'order/settlement/'+commodity_id+'/1';
                }else{
                    alert(response.msg);
                }
            },
            error : function(error){

            }
        });
    });

    /**
     * 加入购物车
     */

    $(".addCart").click(function () {
        var commodity_id = $(this).data('commodity-id');
        var specification_id = $(this).data('specification-id');
        var amount = 1;
        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL+'shopping_cart/add',
            data : {
                commodity_id : commodity_id,
                specification_id : specification_id,
                amount : amount
            },
            success : function(response){
                // alert(response.msg);
                if (response.success){
                    get_all_shopping_cart();
                    ani();
                }else{
                    alert(response.msg);
                }

            },
            error : function(error){

            }

        });
    });
    //添加加入购物车动画
    function ani() {
        $('.icon_cart').addClass('icon_animate1');
        $('.cart_to_cart').addClass('animation1');
        setTimeout(function () {
            $('.icon_cart').removeClass('icon_animate1');
            $('.cart_to_cart').removeClass('animation1')
        },1000);
    }

    /**
     * 评论导航点击效果
     */
    $('.evaluation li').click(function () {
        $(this).addClass('active');
        $(this).siblings().removeClass('active')
    })


});

/**
 * 去除url中的参数
 *
 * @param name 参数名
 */
function remove_url_parameter(name, path){
    path = path || null;
    var url_arr = path ? path.split('?')[1].split('&') :window.location.search.substr(1).split('&');
    var url = SITE_URL + 'index/search?';
    $.each(url_arr, function(index, item){
        if(item != "" && item.substr(0, name.length) != name){
            url += item + '&';
        }
    });

    return url;
}
