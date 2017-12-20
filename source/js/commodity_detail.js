var is_favorite_flag = false;
$(document).ready(function(){
    var show_img_id = 0;

    $('#nav-list').css('margin-top', '-1px');

    var commodity_id = $('#page-content').attr('data-id');
    var commodity_specification_id = $('#page-content').attr('data-specification-id');
    var specification_pack_id = $('#page-content').data('specification-id');
    var commodity_pack_id = '';
    var evaluation_level = 0;
    evaluation_paginate(1, 10, commodity_id, 0, '');
    init_evaluation_nav(commodity_id, '');
    // checkUserFavorite(commodity_id, commodity_specification_id);
    /*$('div.zoomMask').css({
        "background": "url("+SITE_URL+'source/img/mask.png'+")",
        "background-repeat": "repeat",
        "background-scroll": 'scroll',
        "background-origin": "0 0",
        "background-color": "transparent"
    });*/

	$('#delt').click(function(){
		var qut = parseInt($('#appendedInputButtons').val()) || 0;
		qut--;
		qut = qut < 1 ? 1 : qut;
		$('#appendedInputButtons').val(qut);
        isDisabled(qut)
	});

	$('#add').click(function(){
		var qut = parseInt($('#appendedInputButtons').val()) || 0;
		qut++;
		$('#appendedInputButtons').val(qut);
        isDisabled(qut);
	});

    //获取包装和规格
    var specification_pack = [];
    $.ajax({
        type: 'get',
        dataType: 'json',
        url:SITE_URL+'commodity/commodity_specification_by_id/' + commodity_id,
        success: function (response) {
            if(response.success){
                specification_pack = response.data;
                var tpl = document.getElementById('specification_tpl').innerHTML;
                $("#specification_container").html(template(tpl, {list: response.data}));
                
                if(specification_pack_id && specification_pack_id != ''){
                    for(var i = 0; i<specification_pack.length; i++){
                        for(var j = 0; j<specification_pack[i].length; j++){
                            if(specification_pack[i][j].id == specification_pack_id){
                                $('.select-specification').each(function () {
                                    if($(this).data('specification-id') == specification_pack[i][j].commodity_center_id){
                                        select_specification($(this), $(this).data('specification-id'));
                                        return;
                                    }
                                });

                                if(specification_pack[i].length == 2){
                                    if(specification_pack[i][j].package_type_name == '精装'){
                                        select_pack($('.jinz'), '精装');
                                    }else{
                                        select_pack($('.jianz'), '简装');
                                    }
                                    $('.select-pack').removeClass('disabled').prop('disabled', false);
                                }else if(specification_pack[i].length == 1 && specification_pack[i][0].package_type_name == '精装'){
                                    select_pack($('.jinz'), '精装');
                                    $('.jianz').addClass('disabled').prop('disabled', true);
                                }else if(specification_pack[i].length == 1 && specification_pack[i][0].package_type_name == '简装'){
                                    select_pack($('.jianz'), '简装');
                                    $('.jinz').addClass('disabled').prop('disabled', true);
                                }else if(specification_pack[i].length == 0 || (specification_pack[i].length == 1 && (specification_pack[i][0].package_type_name == '' || !specification_pack[i][0].package_type_name || specification_pack[i][0].package_type_name == null))){
                                    $('.select-pack').addClass('disabled').prop('disabled', true);
                                }
                            }
                        }
                    }

                    get_price();
                }else{
                    $('.select-specification').removeClass('active disabled').prop('disabled', false);
                    $('.select-pack').removeClass('active disabled').prop('disabled', false);
                }
            }else{

            }
        }
    });

    //选择商品规格和包装
    $(document).on('click', '#specification_container .select-specification', function () {
        var this_specification = $(this);
        var specification_id = $(this).data('specification-id');
        select_specification(this_specification, specification_id);
        get_price();
        if($('#prevent_evaluation').prop('checked')){
            evaluation_paginate(1, 10, commodity_id, 0, commodity_pack_id);
            init_evaluation_nav(commodity_id, commodity_pack_id);
        }else{
            evaluation_paginate(1, 10, commodity_id, 0, '');
            init_evaluation_nav(commodity_id, '');
        }
    });

    $(document).on('click', '#pack_container .select-pack', function () {
        var this_pack = $(this);
        var pack_name = $(this).text();
        select_pack(this_pack, pack_name);
        get_price();
        if($('#prevent_evaluation').prop('checked')){
            evaluation_paginate(1, 10, commodity_id, 0, commodity_pack_id);
            init_evaluation_nav(commodity_id, commodity_pack_id);
        }else{
            evaluation_paginate(1, 10, commodity_id, 0, '');
            init_evaluation_nav(commodity_id, '');
        }
    });

    //选择规格
    function select_specification(_this, specification_id) {
        if(_this.hasClass('active')){
            _this.removeClass('active');
            $('.select-pack').removeClass('disabled').prop('disabled', false);
            commodity_pack_id = ''
        }else{
            for(var i = 0; i<specification_pack.length; i++){
                if(specification_pack[i][0].commodity_center_id == specification_id){
                    if(specification_pack[i].length == 2){
                        $('.select-pack').removeClass('disabled').prop('disabled',false);
                    }else if(specification_pack[i].length == 1 && specification_pack[i][0].package_type_name == '精装'){
                        $('button.jianz').addClass('disabled').prop('disabled', true);
                        $('button.jinz').removeClass('disabled').prop('disabled', false);
                    }else if(specification_pack[i].length == 1 && specification_pack[i][0].package_type_name == '简装'){
                        $('button.jinz').addClass('disabled').prop('disabled', true);
                        $('button.jianz').removeClass('disabled').prop('disabled', false);
                    }else if(specification_pack[i].length == 0 || specification_pack[i].length == 1 && (specification_pack[i][0].package_type_name == '' || specification_pack[i][0].package_type_name == null || !specification_pack[i][0].package_type_name)){
                        $('.select-pack').addClass('disabled').prop('disabled',true);
                    }
                }
            }

            _this.addClass('active').siblings().removeClass('active');
        }
    }

    //选择包装
    function select_pack(_this, pack_text) {
        if(_this.hasClass('active')){
            _this.removeClass('active');
            $('.select-specification').removeClass('disabled').prop('disabled', false);
            commodity_pack_id = ''
        }else{
            for(var i = 0; i<specification_pack.length; i++){
                $('.select-specification').each(function () {
                    var commodity_center_id = $(this).data('specification-id');
                    if(specification_pack[i][0].commodity_center_id == commodity_center_id){
                        if(specification_pack[i].length == 2){
                            $(this).removeClass('disabled').prop('disabled', false);
                        }else if(specification_pack[i].length == 1 && specification_pack[i][0].package_type_name != pack_text){
                            $(this).addClass('disabled').prop('disabled', true);
                        }else if(specification_pack[i].length == 1 && specification_pack[i][0].package_type_name == pack_text){
                            $(this).removeClass('disabled').prop('disabled', false);
                        }
                    }
                })
            }
            _this.addClass('active').siblings().removeClass('active');
        }
    }

    //计算选择规格后价格
    var end_time = '';
    var timerSec = 0;
    function get_price () {
        var pack_text = '';
        var specification_id_this = '';
        commodity_pack_id = '';
        $('.select-pack').each(function () {
            if($(this).hasClass('active')){
                pack_text = $(this).text();
                return;
            }else{
                $('#count_down').text('');
                clearInterval(timerSec);
            }
        });

        $('.select-specification').each(function () {
            if($(this).hasClass('active')){
                specification_id_this = $(this).data('specification-id');
                return;
            }else{
                $('#count_down').text('');
                clearInterval(timerSec);
            }
        });
        for(var i = 0; i<specification_pack.length; i++){
            if(specification_pack[i][0].commodity_center_id == specification_id_this){
                for(var j = 0; j<specification_pack[i].length; j++){
                    if(specification_pack[i][j].package_type_name == pack_text) {

                        commodity_pack_id = specification_pack[i][j].id;
                        if(specification_pack[i][j].flash_sale_price && specification_pack[i][j].flash_sale_price != null){
                            clearInterval(timerSec);
                            $('#price').text(specification_pack[i][j].flash_sale_price);
                            end_time = specification_pack[i][j].end_time;
                            timerSec = setInterval(setInterval_count_down(end_time),1000);
                        }else{
                            clearInterval(timerSec);
                            $('#count_down').text('');
                            $('#price').text(specification_pack[i][j].selling_price);
                        }
                        checkUserFavorite(commodity_id, commodity_pack_id);
                        return;
                    }else if(specification_pack[i][j].package_type_name == null || !specification_pack[i][j].package_type_name || specification_pack[i][j].package_type_name == ''){
                        commodity_pack_id = specification_pack[i][0].id;
                        if(specification_pack[i][j].flash_sale_price && specification_pack[i][j].flash_sale_price != null){
                            $('#price').text(specification_pack[i][j].flash_sale_price);
                        }else{
                            $('#price').text(specification_pack[i][j].selling_price);
                        }
                        checkUserFavorite(commodity_id, commodity_pack_id);
                    }
                }
            }else if(pack_text == '' || specification_id_this == ''){
                checkUserFavorite(commodity_id, commodity_pack_id);
            }
        }
    }
    var text= '';

    function timer(end_time) {
        var now_time = Math.round(new Date().getTime());
        var unlinx_time = Date.parse(new Date(end_time));
        var count_time = unlinx_time - now_time;
        var leftsecond = parseInt(count_time/1000);
        var d=Math.floor(leftsecond/(60*60*24));
        var hour=Math.floor((leftsecond-d*24*60*60)/3600);
        var min=Math.floor((leftsecond-d*24*60*60-hour*3600)/60);
        var sec=Math.floor(leftsecond-d*24*60*60-hour*3600-min*60);

        d = checkTime(d);
        hour = checkTime(hour);
        min = checkTime(min);
        sec = checkTime(sec);
        text= d + '天 ' + hour + ':' + min + ':' + sec;
        $('#count_down').text('限时折扣，距结束：' + text);

        console.log(text);
    }
    function setInterval_count_down(end_time) {
        return function() {
            timer(end_time)
        }
    }

    function checkTime(i){ //将0-9的数字前面加上0，例1变为01
        if(i<10)
        {
            i = "0" + i;
        }
        return i;
    }

	//减按钮可用/不可用
	function isDisabled(qut) {
        if(qut == 1){
            $('#delt').css({'opacity':'0.4', 'cursor':'default', 'border-color':'#ddd'});
        }else{
            $('#delt').css({'opacity':'1', 'cursor':'pointer','border-color':'#eee'});
        }
    }
    $('#appendedInputButtons').keyup(function () {
        this.value = this.value.replace(/[^\d]/g, '');
        if(this.value == '' || this.value == 0) {
            this.value = 1;
        }
    });

    $(document).ready(function(){
        $(".jqzoom").imagezoom();

        $("#thumblist li a").click(function(){
            $(this).parents("li").addClass("tb-selected").siblings().removeClass("tb-selected");
            $(".jqzoom").attr('src',$(this).find("img").attr("mid"));
            $(".jqzoom").attr('rel',$(this).find("img").attr("big"));
        });
    });

    /**
     * 加入购物车
     */
    $("#addCart").click(function () {
        if(commodity_pack_id == '' || !commodity_pack_id || commodity_pack_id == undefined){
            swal("", "请先选择规格包装", "warning")
            return;
        }
        var commodity_id = $('#page-content').attr('data-id');
        var amount = parseInt($('#appendedInputButtons').val()) || 1;
        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL+'shopping_cart/add',
            data : {
                commodity_id : commodity_id,
                specification_id: commodity_pack_id,
                amount : amount
            },
            success : function(response){
                if (response.success){
                    get_all_shopping_cart();
                    swal({
                            title: "",
                            text: response.msg,
                            type: "success",
                            showCancelButton: true,
                            cancelButtonText: "继续购物",
                            confirmButtonColor: "#AEDEF4",
                            confirmButtonText: "去结算",
                            closeOnConfirm: false
                        },
                        function(){
                            window.location.href = SITE_URL + 'shopping_cart';
                        });
                    ani();
                }else{
                    swal("", response.msg, "error")
                }

            },
            error : function(){
                console.log('系统繁忙，请稍后再试');
            }
        });
    });
    //添加加入购物车动画
    function ani() {
        $('#icon_cart').addClass('icon_animate1');
        $('#cart_to_cart').addClass('animation1');
        setTimeout(function () {
            $('#icon_cart').removeClass('icon_animate1');
            $('#cart_to_cart').removeClass('animation1')
        },1000);
    }


    /**
     * 立即购买
     */
    $('#buy-now').click(function(){
        var commodity_id = $('#page-content').attr('data-id');
        var amount = parseInt($('#appendedInputButtons').val()) || 1;

        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL+'shopping_cart/add',
            data : {
                commodity_id : commodity_id,
                amount : amount,
                specification_id: commodity_pack_id,
                is_buy_now  :   1
            },
            success : function(response){
                if (response.success){
                    window.location.href = SITE_URL+'shopping_cart/buy_now/' + response.insert_id;
                }else{
                    swal("", response.msg, "error")
                }
            },
            error : function(){
                console.log('系统繁忙，请稍后再试');
            }

        });
    });

    /**
     * 立即兑换
     */
    $('#exchange-now').click(function(){
        var commodity_id = $('#page-content').attr('data-id');
        var amount = parseInt($('#appendedInputButtons').val()) || 1;

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
                    swal("", response.msg, "error")
                }
            },
            error : function(){
                console.log('系统繁忙，请稍后再试');
            }
        });
    });

    var prevent = '';
    $(document).on('click', '#prevent_evaluation', function () {
        get_price();
        if($(this).prop('checked')){
            if(commodity_pack_id == ''){
                swal("", "请先选择规格", "warning")
                return;
            }
            prevent = commodity_pack_id;
            evaluation_paginate(1, 10, commodity_id, evaluation_level, prevent);
            init_evaluation_nav(commodity_id, prevent);
        }else{
            prevent = '';
            evaluation_paginate(1, 10, commodity_id, evaluation_level, prevent)
            init_evaluation_nav(commodity_id, prevent);
        }
    });

    /**
     * 查看评价图片
     */
    $(document).on('click', '.review-img-min', function(){
        var id = $(this).data('id');
        var path = $(this).attr('src');
        var img = $(this).parent().next().find('img');
        if (show_img_id != id){
            show_img_id = id;
            img.attr('src', path);
            img.show();
        }else{
            show_img_id = 0;
            img.attr('src', '#');
            img.hide();
        }

    });

    /**
     * 上一页
     */
    $(document).on('click', '#prev-page', function(){
        var now_page = $(this).closest('ul').find('#now-page').attr('data-page');
        evaluation_paginate(parseInt(now_page) - 1, 10, commodity_id, evaluation_level, prevent);
    });

    /**
     * 下一页
     */
    $(document).on('click', '#next-page', function(){
        var now_page = $(this).closest('ul').find('#now-page').attr('data-page');
        evaluation_paginate(parseInt(now_page) + 1, 10, commodity_id, evaluation_level, prevent);
    });

    /**
     * 跳页
     */
    $(document).on('click', '#jump-page', function(){
        var target_page = $(this).closest('ul').find('input').val();
        evaluation_paginate(target_page, 10, commodity_id, evaluation_level, prevent);
    });

    /**
     * 全部评价
     */
    $('#all-evaluation').click(function(){
        evaluation_paginate(1, 10, commodity_id, 0, prevent);
        evaluation_level = 0;
    });

    /**
     * 好评
     */
    $('#good-evaluation').click(function(){
        evaluation_paginate(1, 10, commodity_id, 1,prevent);
        evaluation_level = 1;
    });

    /**
     * 中评
     */
    $('#mid-evaluation').click(function(){
        evaluation_paginate(1, 10, commodity_id, 2,prevent);
        evaluation_level = 2;
    });

    /**
     * 差评
     */
    $('#bad-evaluation').click(function(){
        evaluation_paginate(1, 10, commodity_id, 3,prevent);
        evaluation_level = 3;
    });

    $('.breadcrumb li:not(.last-li)').click(function(){
        var this_li = $(this);
        this_li.addClass('active');
        this_li.siblings('li').removeClass('active');
    });

    /**
     * 收藏
     */
    $(document).on('click', '#collect', function () {
        var favorite_span = $(this).find('span:first-child');
        if(!commodity_specification_id || commodity_specification_id == '' || !specification_pack_id || specification_pack_id == ''){
            alert('请选择规格');
            return;
        }
        if (is_favorite_flag){
            $.ajax({
                type : 'post',
                dataType: "json",
                url : SITE_URL+'favorite/delete_by_commodity_id',
                data : {
                    commodity_id : commodity_id,
                    commodity_specification_id : commodity_pack_id
                },
                success : function(response){
                    if (response.success){
                        is_favorite_flag = !is_favorite_flag;
                        favorite_span.removeClass().addClass('fa fa-star-o');
                        alert('取消收藏成功');
                    }
                },
                error: function(){
                    console.log('系统繁忙，请稍后再试');
                }
            });
        }else{
            $.ajax({
                type : 'post',
                dataType: "json",
                url : SITE_URL+'favorite/add',
                data : {
                    commodity_id : commodity_id,
                    commodity_specification_id : commodity_pack_id
                },
                success : function(response){
                    if (response.success){
                        is_favorite_flag = !is_favorite_flag;
                        favorite_span.removeClass().addClass('collect_color fa fa-star');
                        alert('收藏成功');
                    }else if (response.timeout) {
                        swal("", response.msg, "warning")
                    }
                },
                error: function(){
                    console.log('系统繁忙，请稍后再试');
                }
            });
        }

    });
    // 判断用户是否已收藏当前规格商品
    function checkUserFavorite(commodity_id, commodity_specification_id) {
        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL + 'favorite/check_favorite_by_commodity_id',
            data : {
                commodity_id : commodity_id,
                commodity_specification_id: commodity_specification_id
            },
            success : function(response){
                if (response.success){
                    $('#collect').find('span:first-child').removeClass().addClass('collect_color fa fa-star');
                    is_favorite_flag = true;
                }else{
                    $('#collect').find('span:first-child').removeClass().addClass('fa fa-star-o');
                    is_favorite_flag = false;
                }
            },
            error: function(error){
                console.log('系统繁忙，请稍后再试');
            }
        });
    }


});



/**
 * 初始评价nav
 */
function init_evaluation_nav(commodity_id, commodity_specification_id){
    $.ajax({
        type : 'post',
        dataType: "json",
        url : SITE_URL+'commodity/evaluation_nav',
        data : {
            commodity_id : commodity_id,
            commodity_specification_id :commodity_specification_id
        },
        success : function(response){
            if(commodity_specification_id == ''){
                if(response.all_eva > 999){
                    $('#evaluate_num').text('(999+)');
                }else{
                    $('#evaluate_num').text('(' + (response.all_eva || 0) + ')');
                }
            }
            $('#all-evaluation').text('全部评价('+(response.all_eva || 0)+')');
            $('#good-evaluation').text('好评('+(response.good_eva || 0)+')');
            $('#mid-evaluation').text('中评('+(response.mid_eva || 0)+')');
            $('#bad-evaluation').text('差评('+(response.bad_eva || 0)+')');
            $('#rating_num').text(response.all_eva || 0);
        },
        error : function(){
            console.log('系统繁忙，请稍后再试');
        }
    });
}

/**
 * 评价分页
 */
function evaluation_paginate(page, page_size, commodity_id, evaluation_level, specification_id){
    if (evaluation_level==undefined){
        evaluation_level = 0;
    }
    $.ajax({
        type : 'post',
        dataType: "json",
        url : SITE_URL+'commodity/evaluation_paginate/'+page+'/'+page_size+'/'+commodity_id+'/'+evaluation_level + '/' + specification_id,
        success : function(response){
            if (response.success){
                $.each(response.data, function(index, row){
                    row.create_time = row.create_time.substr(0, 16);
                });
                push_evaluation(response.data, response.total_page, page);
            }else{
                if (page > response.total_page){
                    page = response.total_page;
                }else{
                    push_evaluation([], response.total_page, page);
                }
            }
            $('#praise').text(response.praise_rate * 100 + '%');
        },
        error : function(){
            console.log('系统繁忙，请稍后再试');
        }
    });
}

/**
 * 填充评价分页数据
 */
function push_evaluation(evaluations, total_page, now_page){
    var tpl = document.getElementById('evaluation_tpl').innerHTML;
    $("#evaluation").html(template(tpl, {list: evaluations}));

    var paginate = '<ul>';

    if (now_page != 1){
        paginate += '<li class="pointer" id="prev-page">上一页 </li>';
    }

    paginate += '<li> 共 '+total_page+' 页 </li>\
                 <li data-page="'+now_page+'" id="now-page">第 <input type="text" value="'+now_page+'" style="width:15px;"> 页</li>\
                 <li class="pointer" id="jump-page">跳转</li>';

    if (now_page != total_page){
        paginate += '<li class="pointer" id="next-page"> 下一页</li>';
    }

    paginate += '</ul>';

    $('#evaluation-paginate').html(paginate);
}