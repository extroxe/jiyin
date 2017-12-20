var shopping_cart_ids = '';
$(function(){
    init_address();
    settle_price();

    $('#orderPayType li').click(function () {
        $(this).css('border-color','#117d94').siblings().css('border-color','transparent');
        $(this).children('input').prop('checked',true);
    });

    //购物车ids
    shopping_cart_ids = $('#address_list_container').data('shopping_cart_ids');

    /**
     * 选择地址
     */
    $(document).on('click', '.order-address', function(){
        var id = $(this).data('address-id');
        var div = $(this).children();
        var name = div.children(".address_item_city").data('name');
        var province = div.children(".address_item_city").data('province');
        var province_code = div.children(".address_item_city").data('province-code');
        var city = div.children(".address_item_city").data('city');
        var city_code = div.children(".address_item_city").data('city-code');
        var district = div.children(".address_item_city").data('district');
        var district_code = div.children(".address_item_city").data('district-code');
        var address = div.children(".address_item_address").data('address');
        var phone = div.children(".address_item_phone").data('phone');
        var is_default = div.data('default');

        $(this).addClass('selected');
        $(this).siblings().removeClass('selected');
        $(this).find('.address-bg').css('background-image', 'url("' + SITE_URL + 'source/img/add_01.png")');
        $(this).siblings().find('.address-bg').css('background-image', 'url("' + SITE_URL + 'source/img/add_02.png")');

        var address_item = {
            id: id,
            name: name,
            province: province,
            province_code: province_code,
            city: city,
            city_code: city_code,
            district: district,
            district_code: district_code,
            address: address,
            phone: phone,
            is_default: is_default
        };
        var tpl = document.getElementById('order_info_tpl').innerHTML;
        $("#order_info").html(template(tpl, {data: address_item}));

        //重新获取邮费
        get_postage(id, shopping_cart_ids, 1, province + city + district);

        select_discount($('#discount_coupon option:selected'));
    });

    /**
     * 添加新地址
     */
    $('#add_address').click(function(){
        initGaodeAddress();
        $('#address_modal').modal('show');
    });

    /**
     * 修改地址
     */
    $(document).on("click", ".edit_address", function () {
        var id = $(this).data('id');
        var div = $(this).parent();
        var name = div.children(".address_item_city").data('name');
        var province = div.children(".address_item_city").data('province');
        var province_code = div.children(".address_item_city").data('province-code');
        var city = div.children(".address_item_city").data('city');
        var city_code = div.children(".address_item_city").data('city-code');
        var district = div.children(".address_item_city").data('district');
        var district_code = div.children(".address_item_city").data('district-code');
        var address = div.children(".address_item_address").data('address');
        var phone = div.children(".address_item_phone").data('phone');
        var is_default = div.data('default');

        $('#address_modal').modal('show');
        $("#add_or_update_address_title").html("编辑");
        $("#add_or_update_address_title").data("id", id);
        $("#consignee_address").val(address);
        $("#consignee_name").val(name);
        $("#consignee_phone").val(phone);
        if (is_default == 1) {
            $("#consignee_default_addr")[0].checked = true;
        }else {
            $("#consignee_default_addr")[0].checked = false;
        }

        // 填充省市区选择控件
        $("#consignee_province_select").val(province_code);
        searchNextLevel($("#consignee_province_select")[0], city_code, district_code);
    });

    /**
     * 显示全部地址/收起地址
     */
    $('#address-link').click(function(){
        if ($(this).data('pack-up') == 'false'){
            $(this).data('pack-up', 'true');
            $(this).text('显示全部地址');
            init_address();
        }else{
            $(this).data('pack-up', 'false');
            $(this).text('收起地址列表');
            init_address();
        }
    });

    /**
     * 下拉选择响应事件
     */
    $('#discount_coupon').change(function(){
        var option_selected = $(this).find('option:selected');
        select_discount(option_selected);
    });

    /**
     * 提交订单
     */
    $('#submit-order').click(function(){
        //获取购物车ids
        var ids = '';
        $('.order-settlement').each(function(index, item){
            if (index == 0){
                ids += $(item).attr('data-id');
            }else{
                ids += '-'+$(item).attr('data-id');
            }
        });

        //积分商品标志
        var is_point_flag = $('#top_title').data('point-flag');

        //获取选择的地址id
        var address_id = $('.order-address.selected').attr('data-address-id');
        //获取买家留言
        var message = $('#buyer_message').val();
        //优惠券
        var user_discount_coupon_id = parseInt($('#discount_coupon').select().val()) || '';
        //获取选择的支付方式id
        var payment_id = $('#orderPayType li input:checked').val();
        
        $(this).prop('disabled', true);
        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL+'order/add',
            data : {
                address_id : address_id,
                ids : ids,
                message : message,
                user_discount_coupon_id : user_discount_coupon_id,
                is_point_flag : is_point_flag,
                payment_id: payment_id
            },
            success : function(response){
                if (response.success){
                    if (payment_id == 1) {
                        // 微信支付
                        window.location.href = SITE_URL + 'order/wechat_pay/' + response.insert_id;
                    }else if (payment_id == 2) {
                        // 支付宝支付

                    }else if (payment_id == 3) {
                        // 银联支付

                    }else if (is_point_flag){
                        window.location.href = SITE_URL + 'order/status/1';

                    } else {
                        swal("", "您选择的支付方式有误", "info")
                    }
                    // $('#submit-order').prop('disabled', false);
                }else{
                    swal("", response.error, "info")
                }

            },
            error : function(error){
                $('#submit-order').prop('disabled', false);
            }

        });
    });

    $("#save_address").click(function () {
        var verification = true;

        // 地址ID
        var id = $("#add_or_update_address_title").data("id");
        // 详细地址
        var address = $("#consignee_address").val();
        if (typeof(address) == 'undefined' || address == "") {
            verification = false;
            $("#consignee_address_error").text("请填写详细地址");
        }else {
            $("#consignee_address_error").text("");
        }

        // 收货人姓名
        var name = $("#consignee_name").val();
        if (typeof(name) == 'undefined' || name == "") {
            verification = false;
            $("#consignee_name_error").text("请填写收货人姓名");
        }else {
            $("#consignee_name_error").text("");
        }

        // 手机号码
        var phone = $("#consignee_phone").val();
        var pattern_phone = /^1(3|4|5|7|8)\d{9}$/;
        if (typeof(phone) == 'undefined' || phone == "") {
            verification = false;
            $("#consignee_phone_error").text("请填写收货人手机号码");
        }else if (!pattern_phone.exec(phone)) {
            verification = false;
            $("#consignee_phone_error").text("请填写正确的手机号码");
        }else {
            $("#consignee_phone_error").text("");
        }

        // 是否默认地址
        var default_addr = false;
        if ($("#consignee_default_addr")[0].checked) {
            default_addr = true;
        }else {
            default_addr = false;
        }

        // 省市区信息
        var province = document.getElementById('consignee_province_select');
        var city = document.getElementById('consignee_city_select');
        var district = document.getElementById('consignee_district_select');
        var province_text, province_code, city_text, city_code, district_text, district_code;

        if (typeof(province.value) == 'undefined' || province.value == "" || typeof(province.options[province.selectedIndex].text) == "undefined" || province.options[province.selectedIndex].text == "") {
            verification = false;
            $("#district_error").text("请选择所在省份");
        }else if (typeof(city.value) == 'undefined' || city.value == "" || typeof(city.options[city.selectedIndex].text) == "undefined" || city.options[city.selectedIndex].text == "") {
            verification = false;
            $("#district_error").text("请选择所在城市");
        }else {
            if (city.value == "120200" || city.value == "310200" || city.value == "500200") {
                // 没有县级行政区信息
                $("#district_error").text("");
                province_text = province.options[province.selectedIndex].text;
                province_code = province.value;
                city_text = city.options[city.selectedIndex].text;
                city_code = city.value;
                district_text = "";
                district_code = "";
            }else {
                // 有县级行政区信息
                if (typeof(district.value) == 'undefined' || district.value == "" || typeof(district.options[district.selectedIndex].text) == "undefined" || district.options[district.selectedIndex].text == "") {
                    verification = false;
                    $("#district_error").text("请选择所在区县");
                }else {
                    province_text = province.options[province.selectedIndex].text;
                    province_code = province.value;
                    city_text = city.options[city.selectedIndex].text;
                    city_code = city.value;
                    district_text = district.options[district.selectedIndex].text;
                    district_code = district.value;
                    $("#district_error").text("");
                }

            }
        }

        if (verification) {
            // 验证通过
            if (typeof(id) != 'undefined' && parseInt(id) > 0) {
                // 修改地址
                $.ajax({
                    url : SITE_URL + 'user/update_address',
                    type : 'POST',
                    dataType : 'json',
                    data : {
                        id : id,
                        name : name,
                        phone : phone,
                        province : province_text,
                        province_code : province_code,
                        city : city_text,
                        city_code : city_code,
                        district : district_text,
                        district_code : district_code,
                        address : address,
                        default : default_addr
                    },
                    success : function (result) {
                        if (result.success) {
                            init_address();
                            reset_form();
                        }else {
                            swal("", result.msg, "info");
                        }
                    },
                    error : function () {
                        console.log('服务器繁忙，请稍后重试');
                    }
                });
            }else {
                // 新增地址
                $.ajax({
                    url : SITE_URL + 'user/add_address',
                    type : 'POST',
                    dataType : 'json',
                    data : {
                        name : name,
                        phone : phone,
                        province : province_text,
                        province_code : province_code,
                        city : city_text,
                        city_code : city_code,
                        district : district_text,
                        district_code : district_code,
                        address : address,
                        default : default_addr
                    },
                    success : function (result) {
                        if (result.success) {
                            init_address();
                            reset_form();
                        }else {
                            swal("", result.msg, "info");
                        }
                    },
                    error : function () {
                        console.log('服务器繁忙，请稍后重试');
                    }
                });
            }
        }
    });
});

/**
 * 初始化地址数据
 */
function init_address(){
    $('#address_modal').modal('hide');
    if ($("#address-link").data('pack-up') == true || $("#address-link").data('pack-up') == "true") {
        get_address(4);
        $("#address-link").text("显示全部地址");
    }else {
        get_address();
        $("#address-link").text("收起地址列表");
    }
    reset_form();
}

/**
 * 获取地址数据
 */
function get_address(limit = false){
    $.ajax({
        type : 'post',
        dataType: "json",
        url : SITE_URL + 'user/show_address/' + limit,
        success : function(response){
            if (response.success){
                push_address(response.data);
                $.each(response.data, function (index, data) {
                    if(data.default == 1){
                        //计算邮费
                        var address = data.province + data.city + data.district;
                        get_postage(data.id, shopping_cart_ids, 1, address);
                    }
                })
            }
        },
        error : function(error){
            console.log("系统繁忙，请稍后再试");
        }
    });
}

var freight = 0;
//计算邮费规则
function get_postage(address_id, cart_ids, terminal_type, address) {
    $.ajax({
        url : SITE_URL + 'order/get_postage_by_order',
        type : 'POST',
        dataType : 'json',
        data : {
            address_id : address_id,
            shopping_cart_ids : cart_ids,
            terminal_type : terminal_type
        },
        success : function (result) {
            if (result.success && parseFloat(result.data) == 0) {
                freight = result.data;
                settle_price();
                $('.freight').html(address + '邮费<span style="color: #f53434;margin-left: 5px;font-weight: bold;">免邮</span>');
            } else if (result.success && parseFloat(result.data) > 0) {
                freight = result.data;
                settle_price();
                $('.freight').html(address + '邮费<span style="color: #f53434;margin-left: 5px;font-weight: bold;">￥' +  parseInt(result.data).toFixed(2) + '</span>');
            } else {
                swal("", result.msg, "info")
            }
        },
        error : function () {
            console.log('服务器繁忙，请稍后重试');
        }
    })
}

/**
 * 填充地址数据
 */
function push_address(addresses){
    $('#address').html('');
    var tpl = document.getElementById('address_item_tpl').innerHTML;
    $("#address_list_container").html(template(tpl, {list: addresses}));

    $.each(addresses, function(index, address){
        if (address['default'] == 1){
            var tpl = document.getElementById('order_info_tpl').innerHTML;
            $("#order_info").html(template(tpl, {data: address}));
            return;
        }
    });
    settle_price();
}

/**
 * 计算总价
 */
var totalcost = 0;
function settle_price(){
    var total_price = 0;
    var is_point_flag = $('#top_title').data('point-flag');
    $('.total_price').each(function(index, item){
        total_price += parseFloat($(item).text());
    });
    total_price = total_price + parseFloat(freight);
    if (total_price >= condition && condition != 0){
        var present_price = total_price - parseFloat(privilege);
        $('#shop_detail').html('\
        <span>店铺总计: </span><span style="color: red;"> ¥ '+total_price.toFixed(2)+'</span>\
        <br>\
        <span>优惠: </span><span style="color: red;">-￥' + parseFloat(privilege).toFixed(2) + '</span>\
        <br>\
        <span>合计: </span><span id="shop_total" style="color: red;"> ¥ '+present_price.toFixed(2)+'</span>');

        //实际付款
        if (is_point_flag){
            $('#actual_payment').html('实际付款: '+present_price+'积分');
        }else{
            $('#actual_payment').html('实际付款:'+'<span style="color: red;">￥'+present_price.toFixed(2)+'</span>');
        }
    }else{
        $('#shop_detail').html('<span>店铺合计:</span><span id="shop_total" style="color: red;"></span>');
        //店铺合计
        $('#shop_total').text('￥'+total_price.toFixed(2));
        //实际付款
        if (is_point_flag){
            $('#actual_payment').text('实际付款: '+total_price+'积分');
        }else{
            $('#actual_payment').html('实际付款:'+'<span style="color: red;">￥'+total_price.toFixed(2)+'</span>');
        }
    }

    totalcost = total_price.toFixed(2);
}

/**
 * 选择优惠
 */
var condition = 0;
var privilege = 0;
function select_discount(option_selected){
    condition = option_selected.attr('data-condition');
    privilege = option_selected.attr('data-privilege');

    settle_price();
}

/**
 * 模态框内部代码
 */
var district;
/**
 * 初始化省市区选择控件
 */
function initGaodeAddress() {
    $("#consignee_city_select").innerHTML = '';
    $("#consignee_city_select").empty();
    $("#consignee_city_select").val("");
    $("#consignee_city_select").removeAttr("disabled");

    $("#consignee_district_select").innerHTML = '';
    $("#consignee_district_select").empty();
    $("#consignee_district_select").val("");
    $("#consignee_district_select").removeAttr("disabled");

    district = new AMap.DistrictSearch({
        level: 'country',
        showbiz: false,
        subdistrict: 1
    });
    district.search('中国', function(status, result) {
        if(status=='complete'){
            if (result.districtList.length > 0) {
                getAdministrativeRegion(result.districtList[0]);
            }else {
                console.log('获取省级行政区失败');
            }
        }
    });
}
/**
 * 解析省市区信息
 * @param data
 */
function getAdministrativeRegion(data, city_code, district_code) {
    var subList = data.districtList;
    var level = data.level;
    //清空下一级别的下拉列表
    if (level === 'province') {
        nextLevel = 'city';
        $("#consignee_city_select").innerHTML = '';
        $("#consignee_district_select").innerHTML = '';
        $("#consignee_city_select").empty();
        $("#consignee_city_select").val("");
        $("#consignee_district_select").empty();
        $("#consignee_district_select").val("");
    } else if (level === 'city') {
        nextLevel = 'consignee_district_select';
        $("#consignee_district_select").innerHTML = '';
        $("#consignee_district_select").empty();
        $("#consignee_district_select").val("");
    }
    if (subList) {
        if (subList.length > 0) {
            $('#consignee_' + subList[0].level+'_select').empty();
        }

        var contentSub = new Option('--请选择--');
        contentSub.setAttribute("value", "");
        for (var i = 0, l = subList.length; i < l; i++) {
            var name = subList[i].name;
            var value = subList[i].adcode;
            var levelSub = "consignee_"+subList[i].level+"_select";
            var cityCode = subList[i].citycode;

            if (i == 0) {
                document.querySelector('#' + levelSub).add(contentSub);
                document.querySelector('#' + levelSub).removeAttribute('disabled');
            }
            contentSub=new Option(name);
            contentSub.setAttribute("value", value);
            contentSub.center = subList[i].center;
            contentSub.adcode = subList[i].adcode;

            document.querySelector('#' + levelSub).add(contentSub);
        }
        if (typeof(city_code) != 'undefined' && city_code != "" && levelSub == "consignee_city_select") {
            $('#' + levelSub).val(city_code);
            searchNextLevel($('#' + levelSub)[0], city_code, district_code);
        }else if (typeof(district_code) != 'undefined' && district_code != "" && levelSub == "consignee_district_select") {
            $('#' + levelSub).val(district_code);
        }
    }else {
        if (level == "province") {
            // 将市级、县级下拉列表置为不可用
            $("#consignee_city_select").attr('disabled', 'disabled');
            $("#consignee_district_select").attr('disabled', 'disabled');
        }else if (level == "city") {
            // 将县级下拉列表置为不可用
            $("#consignee_district_select").attr('disabled', 'disabled');
        }
    }

}
/**
 * 根据当前所选省市搜索下级行政区域列表
 * @param obj
 * @param city_code 城市代码，编辑地址时初始化控件使用
 * @param district_code 区县代码，编辑地址时初始化控件使用
 */
function searchNextLevel(obj, city_code = "", district_code = "") {
    var option = obj[obj.options.selectedIndex];
    var keyword = option.text; //关键字
    var adcode = option.adcode;
    district.setLevel(option.value); //行政区级别
    //行政区查询
    //按照adcode进行查询可以保证数据返回的唯一性
    district.search(adcode, function(status, result) {
        if(status === 'complete'){
            getAdministrativeRegion(result.districtList[0], city_code, district_code);
        }
    });
}
/**
 * 重置收获信息表单
 */
function reset_form() {
    $("#add_or_update_address_title").html("新增");
    $("#add_or_update_address_title").data("id", "0");
    initGaodeAddress();
    $("#consignee_address").val("");
    $("#consignee_name").val("");
    $("#consignee_phone").val("");
    $("#consignee_default_addr")[0].checked = false;
    $("#consignee_district_error").text("");
    $("#consignee_address_error").text("");
    $("#consignee_name_error").text("");
    $("#consignee_phone_error").text("");
}