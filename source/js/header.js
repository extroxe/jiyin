/**
 * Created by sailwish009 on 2016/11/23.
 */
$(document).ready(function(){
    get_all_shopping_cart();
    isIconZero();


    //导航栏设计
    $('#menu .nav li').click(function () {
        $(this).addClass('active').siblings().removeClass('active');
        $('#nav-sort-list .item-list .subitem dt a').css('background-color', '#fff');
    });
    $('#nav-sort-list .item-list .subitem dt a').css('background-color', '#fff');

    //顶部购物车下拉框
    items_num_price();

    $('#cart').hover(function () {
        items_num_price();
        $('.cart_box').stop(true,true).slideDown(200);
        $('#cart a').css({'border-bottom-color':'#fff'});
        isEmpty();
    },function () {
        $('.cart_box').stop(true,true).slideUp(200);
        $('#cart a').css('border-bottom-color', '#117d94');
        $('#empty_cart').stop(true, true).slideUp(200);
    });

    /**
     * 所有分类
     */
    $('#all-catagray').hover(function () {
        $('#nav-list').css('display', 'block');
    },function(){
        $('#nav-list').css('display', 'none');
    });

    //分类点击链接
    $('.category').on('click', function(){
        var category_id = $(this).attr('data-id') || 0;
        var category_name = $(this).text();
        window.location.href = SITE_URL+'index/search?category='+category_id+'&result='+category_name;
    });

    $('.all-sort-list > .item').hover(function(){
        var eq = $('.all-sort-list > .item').index(this),	//获取当前滑过是第几个元素
            h = $('.all-sort-list').offset().top,			//获取当前下拉菜单距离窗口顶部多少像素
            s = $(window).scrollTop(),						//获取浏览器滚动了多少高度
            i = $(this).offset().top,						//当前元素滑过距离窗口多少像素
            item = $(this).children('.item-list').height(),	//下拉菜单子类内容容器的高度
            sort = $('.all-sort-list').height();			//父类分类列表容器的高度

        if ( item < sort ){			//如果子类的高度小于父类的高度
            if ( eq == 0 ){
                $(this).children('.item-list').css('top', (i-h)-1);
            } else {
                $(this).children('.item-list').css('top', (i-h)-1);
            }
        } else {
            if ( s > h ) {			//判断子类的显示位置，如果滚动的高度大于所有分类列表容器的高度
                if ( i-s > 0 ){		//则 继续判断当前滑过容器的位置 是否有一半超出窗口一半在窗口内显示的Bug,
                    $(this).children('.item-list').css('top', (s-h)+2 );
                } else {
                    $(this).children('.item-list').css('top', (s-h)-(-(i-s))+2 );
                }
            } else {
                $(this).children('.item-list').css('top', 3 );
            }
        }

        // $(this).addClass('item_hover');
        $(this).css('border', '1px solid #117d94');
        $(this).siblings('.item1').css('border', '1px solid #222');
        $(this).children('.item-list').css('display','block');
    },function(){
        // $(this).removeClass('item_hover');
        $(this).css('border', '1px solid #fff');
        $(this).siblings('.item1').css('border', '1px solid #222');
        $(this).children('.item-list').css('display','none');
    });

    /**
     * 搜索按钮
     */
    $('#search-btn').click(function(){
        var words = $('.search-query').val();
        if (words != '' && words != null){
            window.location.href = SITE_URL+'index/search?key_words='+words;
        }
    });

    /**
     * 搜索输入框回车响应
     */
    $('.search-query').keyup(function(event) {
        var words = $('.search-query').val();
        if (event.keyCode == 13 && words != '' && words != null) {
            window.location.href = SITE_URL+'index/search?key_words='+words;
            window.event.returnValue=false;
        }
    })
});

//下拉购物车计算数量和总价

function items_num_price() {
    var items_num = 0;
    var items_price = 0;
    $('#items_list li').each(function () {
        items_num += parseInt($(this).find('.num').text());
    });
    $('#items_list li .price').each(function () {
        items_price += parseFloat($(this).text()) * parseInt($(this).parent('div').find('.num').text());
    });
    $('#items_num').html(items_num);
    $('#items_price').html(items_price.toFixed(2));
}


/**
 * 获取所有购物车数据
 */
function icon_num(data) {
    var product_cart_num  = 0;
    //计算购物车所有商品的总数量
    for(var i = 0; i<data.length;i++){
        product_cart_num += parseInt(data[i].amount);
    }
    $('#icon_cart_num').html(product_cart_num);
}

function get_all_shopping_cart(){

    $.ajax({
        type : 'post',
        dataType: "json",
        url : SITE_URL+'shopping_cart/all',
        success : function(response){
            if (response.success) {
                push_shopping_cart_view(response.data);
                icon_num(response.data);
                isIconZero();
                iconNum();
            }
        },
        error : function(error){

        }
    });
}
//icon_cart的显示与隐藏
function isIconZero(){
    if($('#icon_cart_num').text() == ''){
        $('#icon_cart').css('display', 'none');
    }else{
        $('#icon_cart').css('display', 'block');
    }
}

function isEmpty() {
    if($('#items_list li').length == 0){
        $('.cart_box').hide();
        $('#empty_cart').show();
    }else{
        $('#empty_cart').hide();
        $('.cart_box').show();
    }
}

function deleteCart(cart_id){
    if (confirm('确定要删除吗？')){
        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL+'shopping_cart/delete',
            data : {
                id : cart_id
            },
            success : function(response){
                if (response){
                    alert('删除成功');
                    $('[data-shopping-cart-id='+cart_id+']').remove();
                    //当删除所有物品显示空购物车
                    if($('#items_list li').length <= 3){//当购物车数量小于等于3时隐藏滚动条
                        $('#items_list').css('overflow-y', 'inherit');
                        $('.cart_box').css({'width':'260px', 'margin-left':'-128px'});
                    }
                    items_num_price();
                    iconNum();
                }
            },
            error : function(error){

            }
        });
    }
}

//删除购物车时icon_cart数据变化
function iconNum() {
    var total_num = 0;
    $('#shopping-cart-content li').each(function () {
        total_num += parseInt($(this).find('.num').text());
    });
    if(total_num > 99){
        $('#icon_cart_num').text('99+');
    }else{
        $('#icon_cart_num').html(total_num);
    }
}

/**
 * 填充购物车数据
 */
function push_shopping_cart_view(shopping_carts){
    $('#shopping-cart-content').html(' ');
    $.each(shopping_carts, function(index, shopping_cart){
        if(shopping_cart['flash_sale_price'] == null){
            shopping_cart['flash_sale_price'] = shopping_cart['price']
        }
        $('#shopping-cart-content').append('\
        <li data-shopping-cart-id="'+shopping_cart['id']+'">\
            <div style="width:56px;height:60px;">\
                <img src="'+SITE_URL+shopping_cart['path']+'" style="width:56px;height:60px;">\
            </div>\
            <div>\
                <span title="' +shopping_cart['commodity_name'] + ' ' + shopping_cart['name'] + ' ' + shopping_cart['package_type_name'] + '">' +shopping_cart['commodity_name'] + ' ' + shopping_cart['name'] + ' ' + shopping_cart['package_type_name'] + '</span>\
            </div>\
            <div>\
                <span style="font-size: 10px; margin-right: -5px">¥</span>\
                <span style="color: red; font-size: 10px" class="price">'+shopping_cart['flash_sale_price']+'</span>\
                <span style="margin-left: 10px; font-size: 10px">×<span class="num" style="margin-left: 0px; font-size: 10px">'+shopping_cart['amount']+'</span></span>\
            </div>\
            <div>\
                <a class="item_delt" style="font-size: 15px; padding: 0; border: none" href="javascript:void(0)" onclick="deleteCart('+shopping_cart['id']+')">删除</a>\
            </div>\
        </li>');
    });

    var cart_length = $('#items_list li').length;

    if(cart_length <= 3 && cart_length != 0){//当购物车数量小于等于3时隐藏滚动条
        $('#items_list').css('overflow-y', 'inherit');
        $('.cart_box').css({'width':'260px', 'margin-left':'-128px'});
    }
}