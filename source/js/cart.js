$(document).ready(function() {
	var url_arr = window.location.pathname.split('/');

	/*if (url_arr[2] == undefined || url_arr[2] == null || url_arr[3] == undefined || url_arr[3] == null){
		init();
	}*/

	var buy_directly_flag = $('#cart-content .item_list').data('buy');
	if (buy_directly_flag != undefined && buy_directly_flag == 'directly'){
		$('#selectAll').prop('checked', true);
		$(".son").prop('checked', true);
		total_money();
	}

	$('.operation').each(function(){
		if ($(this).find('input[class*=appendedPrependedInput]').val() > 1){
			$(this).find('.delt').css({'opacity':'1', 'cursor':'pointer'});
		}
	});

       //实时监听input变化
    $(document).on('keyup', '.appendedPrependedInput', function (){
		$(this).each(function () {
            var cart_id = $(this).prev().attr('data-id'); //购物车id
            var _count = $(this).val();//输入框数量
            var temp = $(this);

            if(this.value.length == 1){
            	this.value = this.value.replace(/[^1-9]/g,'')
            }else{
            	this.value = this.value.replace(/\D/g,'')
            }
            // this.value = this.value.replace(/[^1-9]/g, '');
            if(this.value == '' || this.value == 0) {
                this.value = 1;
            }

            if (parseInt(_count) > 10) {
				$(this).val(10);
				swal("", "每件商品每单最多购买10件", "warning")
				return ;
			}

            //计算单件商品金额
            setTimeout(function () {
                btn_show();
                var this_money = 0;
                var price = parseFloat(temp.parents('tr').find('.price span:nth-child(1)').text()); //当前商品单价
                this_money +=  price * (parseInt(temp.parent().find('input[class*=appendedPrependedInput]').val())); //计算当前商品总价

                temp.parents('tr').find('span[class*=total_price]').text(this_money.toFixed(2));
                product_update(cart_id, temp, _count);
            },600)
        })
    });

    $(document).on('change', '.appendedPrependedInput', function (){
        $(this).each(function () {
        	btn_show();
            if(this.value.length == 1){
                this.value = this.value.replace(/[^1-9]/g,'')
            }else{
                this.value = this.value.replace(/\D/g,'')
            }
        })
	});

	// 购买数量增加
	$(document).on('click', '.add', function() {
		$(this).each(function () {
            var t = $(this).parent().find('input[class*=appendedPrependedInput]');
			var _count = t.val();
			_count++;
            if (t.val() == 1) {
                $(this).parent().find('.delt').css({'opacity':'0.5', 'cursor':'default'});
            } else {
                $(this).parent().find('.delt').css({'opacity':'1', 'cursor':'pointer'});
            }

            //加法计算单件商品金额
            var temp = $(this);

            var count = temp.attr('data-count');
            count = parseInt(count)+1;
			if (count > 10) {
				count = 10;
				temp.attr('data-count', count);
				swal("", "每件商品每单最多购买10件", "warning")
				return ;
			}
			t.val(_count);
            temp.attr('data-count', count);

            setTimeout(function () {
            	if(count == temp.attr('data-count')){
                    var money = 0;
                    var price = parseFloat(temp.parents('tr').find('.price span:nth-child(1)').text()); //当前商品单价
                    money +=  price * (parseInt(temp.parent().find('input[class*=appendedPrependedInput]').val())); //计算当前商品总价
                    temp.parents('tr').find('span[class*=total_price]').html(money.toFixed(2));//当前商品总价

                    var cart_id = temp.attr('data-id');
                    product_update(cart_id, temp, _count);
				}
            }, 600)
        })
	});

    //数量减少按钮显示效果
    function btn_show() {
		$('.appendedPrependedInput').each(function () {
            if ($(this).val() == 1 ) {
                $(this).parent().find('.delt').css({'opacity':'0.5', 'cursor':'default'});
            } else {
                $(this).parent().find('.delt').css({'opacity':'1', 'cursor':'pointer'});
            }
        })
    }

	// 购买数量减少
	$(document).on('click', '.delt', function() {
		var t = $(this).parent().find('input[class*=appendedPrependedInput]');
		var _count = parseInt(t.val());
		_count--;
		_count = _count < 1 ? 1 : _count;
		t.val(_count);
		if (parseInt(t.val()) <= 1) {
			t.val(1);
            $("#btn").attr("disabled", true);
        }
		if (t.val() == 1) {
			$(this).css({'opacity':'0.5', 'cursor':'default'});
		} else {
			$(this).css({'opacity':'1', 'cursor':'pointer'});
		}

        //计算单件商品金额
		var temp = $(this);
		var count = temp.attr('data-count');
        count = parseInt(count)+1;
        temp.attr('data-count', count);

        setTimeout(function () {
        	if (temp.attr('data-count') == count){
                var price = parseFloat(temp.parents('tr').find('.price span:nth-child(1)').text());
                var money;
                money = price * _count;
                money = money < price  ? price : money;
                temp.parents('tr').find('span[class*=total_price]').text(money.toFixed(2));//当前商品总价

                var cart_id = temp.attr('data-id');
                product_update(cart_id, temp, _count);
			}
        }, 1000)
	});

	// 商品全選
	// 点击单件商品选择按钮
	$(document).on('click', '.son', function (){
		var goods = $('#cart-content').find(".son"); //获取购物车的所有商品
		var goodsC = $('#cart-content').find(".son:checked"); //获取购物车所有被选中的商品
		var Shops = $('#selectAll');
		if (goods.length == goodsC.length) { //如果选中的商品等于所有商品
			Shops.prop('checked', true); //购物车全选按钮被选中
			total_money()
		} else { //如果选中的商品不等于所有商品
			Shops.prop('checked', false); //购物车全选按钮不被选中
			// 计算
			total_money();
			// 计算
		}
	});

	// 点击全选按钮
	$(document).on('click', '#selectAll', function (){
		if ($(this).prop("checked") == true) { //如果全选按钮被选中
			$(".son").prop('checked', true); //所有按钮都被选中
			total_money();
		} else {
			$(".son").prop('checked', false); //else所有按钮不全选
			total_money();
		}
	});

	/**
	 * 跳转结算页面
	 */
	$(document).on('click', '#settlement-btn', function(){
		var ids = '';
		var i = 0;
		$('.cart_table input[type=checkbox]').each(function(index, item){
			if ($(item).is(':checked')){
				if (i == 0){
					ids += $(item).attr('data-cart');
				}else{
					ids += '-'+$(item).attr('data-cart');
				}
				i++;
			}
		});

		if (ids == ''){
			swal("", "请先选择商品再结算", "warning");
		}else{
			window.location.href = SITE_URL+'order/settlement/'+ids;
		}
	});
});

//    购物车商品数量更新
function product_update(cart_id, temp, _amount) {
	var amount = _amount;
	var input = temp.parent().find('input[class*=appendedPrependedInput]');
	if (amount < 1){
		input.val(1);
	}else if (amount > 10){
		input.val(10);
	}else{
		$.ajax({
			type:'POST',
			url:SITE_URL + 'shopping_cart/update',
			data:{
				id: cart_id,
				amount: amount
			},
			success:function (response) {
				if(response){
					total_money();
				}else{
					swal("", response.msg, "error");
				}
			},
			error:function () {
				console.log('系统繁忙，请稍后再试');
			}
		});
	}
}

//计算商品总价
function total_money() {
	var total_money = 0;          //合计
	var price = 0; //单价

	$(".son").each(function () {
		if ($(this).is(":checked")) {
			price = parseFloat($(this).parents('tr').find('span[class*=total_price]').text());
			total_money += price;
		}
	});

	$('#total_money').html(total_money.toFixed(2));

	if (total_money > 0){
		$('.sum-price').css({'color':'#f6bf00', 'font-size':'18px', 'font-weight':'bold'});
		$('#settlement-btn').css({'cursor':'pointer', 'background-color':'#117d94'})
	}else{
		$('.sum-price').css({'color':'', 'font-size':'', 'font-weight':''});
		$('#settlement-btn').css({'cursor':'default', 'background-color':''})
	}
}

/**
 * 删除购物车
 */
function delete_cart(cart_id){
	$.ajax({
		type : 'post',
		dataType: "json",
		url : SITE_URL+'shopping_cart/delete',
		data : {
			id : cart_id
		},
		success : function(response){
			if (response){
				swal("", "删除成功", "success");
				$('[data-cart-id='+cart_id+']').remove();
				if ($('#cart-content > tr').length <= 0){
					$('#empty-cart').show();
					$('#page-content').hide();
				}
				total_money();
			}
		},
		error : function(){
			console.log('系统繁忙，请稍后再试');
		}
	});
}

/**
 * 填充购物车数据
 */
function push_shopping_cart(shopping_carts){
	var tpl = document.getElementById('cart_info_tpl').innerHTML;
	$("#cart-content").html(template(tpl, {shopping_carts: shopping_carts}));
}

/**
 * 检验正整数
 */
function check_integer(number){
    var rule = /^[1-9]*[1-9][0-9]*$/;
    return rule.test(number);
}