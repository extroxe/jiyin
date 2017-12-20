/**
 * Created by sailwish009 on 2016/12/7.
 */
$(function () {
    cancel_edit();
    // 获取要定位元素距离浏览器顶部的距离
    var navH = $("#person_info_shange").offset().top;    var avatar_url = '';
    if ($('#birthday').attr('data-date') != undefined){
        var birthday = $('#birthday').attr('data-date').split('-');
        var _birthday = new YMDselect('year','month','day', birthday[0], birthday[1], birthday[2]);
    }

    // 滚动条事件
    $(window).scroll(function(){
        //获取滚动条的滑动距离
        var scroH = $(this).scrollTop();
        var items = $("#content").find(".model_box");//model_box为每个子容器的类名
        var menu = $("#_menu");
        var top = scroH;
        var currentId = ""; //滚动条现在所在位置的model_box id
        // 滚动条的滑动距离大于等于定位元素距离浏览器顶部的距离，就固定，反之就不固定
        if(scroH>=navH){
            $("#person_info_shange").css({"position":"fixed","top":'30px'});
        }else if(scroH<navH){
            $("#person_info_shange").css({"position":"static"});
        }

        items.each(function () {
            var m = $(this);
            //m.offset().top为每一个model_box的顶部位置
            if (top > m.offset().top - 150) {
                currentId = "#" + m.attr("id"); // currentId = #receving_info
            } else {
                return false;
            }
        });

        var currentLink = menu.find(".active");//找到类名为active的li标签
        if (currentId && currentLink.attr("href") != currentId) {
            currentLink.removeClass("active");
            menu.find("[href=" + currentId + "]").addClass("active");
        }
    });

    //左侧导航效果
    $('.personal_list li').each(function (index, item) {
        var url = $(item).children('a').prop('href');
        var url_arr = url.split('/');
        var page_flag = window.location.href.split('/')[url_arr.length - 1];
        if (page_flag == undefined || page_flag == ''){
            $('#_menu').find('li:nth-child(1)').children('a').addClass('active');
            return false;
        }else if (url == window.location.href){
            $(item).children('a').addClass('active').siblings('li').children('a').removeClass('active');
            return false;
        }
    });

    //显示剪切头像模态框
    $('#show_upload_avatar').click(function(){
        $('#avatar_modal').modal('show');
        //配置上传头像控件
        var clipArea = new bjj.PhotoClip("#clipArea", {
            size: [260, 260],
            outputSize: [640, 640],
            file: "#file",
            view: "#view",
            ok: "#clipBtn",
            loadStart: function() {
            },
            loadComplete: function() {
            },
            clipFinish: function(dataURL) {
                avatar_url = dataURL;
            }
        });
    });

    $('#upload_avatar').click(function(){
        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL + 'attachment/up_attachment',
            data : {
                str : avatar_url,
                avatar_flag : true
            },
            success: function(response){
                if (response.success){
                    $('#avatar_modal').modal('hide');
                    $('#avatar_img').prop('src', SITE_URL + response.url);
                    $('#top_avatar').prop('src', SITE_URL + response.url);
                    swal('', response.msg, 'success');
                } else {
                    swal('', response.msg, 'info');
                }
            },
            error: function(error){
                console.log(error);
            }
        });
    });

    //编辑
    $('._edit').click(function () {
        $(this).siblings('.save_cancel').css('display', 'inline-block');
        $(this).siblings('input[class=input_text]').select();
        $(this).hide();
    });

    //取消
    $('.cancel').click(function () {
        var input = $(this).parent('.save_cancel').siblings('input');
        var old_val = input.attr('data-old');

        input.val(old_val);
        $(this).parent('.save_cancel').hide();
        $(this).parent('.save_cancel').siblings('._edit').css('display', 'inline-block');
        $('#verification_code').hide();
        $('#send_code').css('display', 'inline-block');
        $('#ensure').hide();
    });

    //账号关联绑定，解除绑定
    function bind_cancel() {
        $('.bind_content').each(function () {
            if($(this).val() == ''){
                $(this).siblings('.cancel_bind').hide().parent('.control-group').find('.bind').show()
            }else{
                $(this).siblings('.bind').hide().parent('.control-group').find('.cancel_bind').show()
            }
        })
    }
    bind_cancel();

    //保存
    $('.save').click(function () {
        var data = {};
        var this_input = $(this).parent('.save_cancel').siblings('.input_text');
        var data_val = this_input.val();
        var data_key = this_input.attr('id');
        data[data_key] = data_val;

        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL + 'user/update_user',
            data : data,
            success: function(response){
                if (response.success) {
                    swal('', response.msg, 'success');
                } else {
                    swal('', response.msg, 'info');
                }
            },
            error: function(error){
                console.log(error);
            }
        });

        $(this).parent('.save_cancel').hide();
        $(this).parent('.save_cancel').siblings('._edit').css('display', 'inline-block');
    });

    /**
     * 修改性别
     */
    $('#gender-save').click(function(){
        var gender = $(this).parent('.edit').siblings('input:checked').val();

        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL + 'user/update_user',
            data : {
                gender : gender
            },
            success: function(response){
                if (response.success) {
                    swal('', response.msg, 'success');
                } else {
                    swal('', response.msg, 'info');
                }
            },
            error: function(error){
                console.log(error);
            }
        });
    });

    /**
     * 修改生日
     */
    $('#birthday-save').click(function(){
        var birthY = $(_birthday.SelY).find("option:selected").val();
        var birthM = $(_birthday.SelM).find("option:selected").val();
        var birthD = $(_birthday.SelD).find("option:selected").val();

        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL + 'user/update_user',
            data : {
                birthday : birthY+'-'+birthM+'-'+birthD
            },
            success: function(response){
                if (response.success) {
                    swal('', response.msg, 'success');
                } else {
                    swal('', response.msg, 'info');
                }
            },
            error: function(error){
                console.log(error);
            }
        });
    });

    /**
     * 修改密码
     */
    $('#psd-save').click(function(){
        var old_psd = $('#old_psd').val();
        var new_psd = $('#new_psd').val();
        var confirm_psd = $('#confirm_psd').val();

        if (old_psd == '' || old_psd == undefined){
            swal('', '当前密码不能为空!', 'info');
        }else if (new_psd == '' || new_psd == undefined){
            swal('', '新密码不能为空!', 'info');
        }else if(new_psd == confirm_psd){
            $.ajax({
                type : 'post',
                dataType: "json",
                url : SITE_URL + 'user/modify_psd',
                data : {
                    old_psd : old_psd,
                    new_psd : new_psd
                },
                success: function(response){
                    if (response.success) {
                        $('#old_psd').val('');
                        $('#new_psd').val('');
                        $('#confirm_psd').val('');
                        swal('', response.msg, 'success');
                    } else {
                        swal('', response.msg, 'info');
                    }
                },
                error: function(error){
                    console.log(error);
                }
            });
        }else{
            swal('', '两次输入的新密码不一致!', 'info');
        }
    });

    /**
     * 绑定微信
     */
    $('#bind_wechat').click(function () {
        window.location.href = SITE_URL + 'user/bind_from_weixin';
        // $(this).css();
    });
    /**
     * 解除微信绑定
     */
    $('#cancel_weixin_bind').on('click', function () {
        if(confirm('确认解除微信绑定')){
            $.ajax({
                type:'post',
                dataType:'json',
                url:SITE_URL + 'user/unbind_from_weixin',
                data:{},
                success:function (result) {
                    if(result.success){
                        swal({
                            title:'解除绑定成功',
                            type:'success'
                        }, function () {
                            location.reload();
                        });
                        // location.reload();
                    }else{
                        swal({
                            title:'解除绑定失败',
                            type:'error'
                        });
                    }
                },
                error:function () {
                    
                }
            })
        }
    });
    /**
     * 绑定手机
     */
    $('#bind_phone').click(function(){
        $(this).hide();
        $(this).siblings('.save_cancel').css('display', 'inline-block');
        $(this).siblings('.save_cancel').find('#send_code').css('display', 'inline-block');
        $(this).siblings('.save_cancel').find('.cancel').css('display', 'inline-block');
    });
    bind_input_empty();

    /**
     * 绑定邮箱
     */
    $('#bind_email').click(function(){
        var this_bind = $('#email');
        var email = this_bind.val();

        if (email == ''){
            swal('', '邮箱不能为空', 'info');
            return false;
        }else{
            $.ajax({
                type : 'post',
                dataType: "json",
                url : SITE_URL + 'verification_code/send_verified_email',
                data : {
                    email : email
                },
                success: function(response){
                    if (response.success){
                        this_bind.siblings('#bind_msg').css('display', 'inline-block');
                        bind_input_empty();
                    }else{
                        swal('', response.msg, 'info');
                    }
                },
                error: function(error){
                    console.log(error);
                }
            });
        }
    });

    /**
     * 发送验证码
     */
    $('#send_code').click(function(){
        var phone = $('#tel').val();
        var this_code = $(this);

        if (phone == ''){
            swal('', '手机号码不能为空', 'info');
            return false;
        }else{
            //先判断手机号码是否可用
            $.ajax({
                type : 'post',
                dataType: "json",
                url : SITE_URL + 'user/check_phone',
                data : {
                    phone : phone
                },
                success: function (response) {
                    if (response) {
                        $.ajax({
                            type : 'post',
                            dataType: "json",
                            url : SITE_URL+'verification_code/get_verified_phone_code',
                            data : {
                                phone : phone
                            },
                            success: function(response){
                                if (response.success){
                                    $('#verification_code').css('display', 'inline-block');
                                    this_code.hide();
                                    $('#ensure').css('display', 'inline-block');
                                }else{
                                    swal('', response.msg, 'info');
                                }
                            },
                            error: function(error){
                                console.log(error);
                            }
                        });
                    } else {
                        swal('', '该手机号已被绑定，请勿重复绑定相同手机号', 'warning');
                    }
                }
            });
        }
    });

    /**
     * 确认验证码
     */
    $('#ensure').click(function(){
        var code = $('#verification_code').val();
        var phone = $(this).parent('.save_cancel').siblings('#tel').val();
        var this_bind = $(this);
        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL + 'verification_code/check_verified_phone_code',
            data : {
                phone : phone,
                code : code
            },
            success: function(response){
                if (response.success){
                    var start_phone = phone.substr(0, 3);
                    var end_phone = phone.substr(7, 4);
                    $('#verification_code').hide();
                    $('#ensure').hide();
                    this_bind.siblings('.cancel').hide();
                    $('#cancel_bind_phone').css('display', 'inline-block').parent('.cancel_bind').css('display', 'inline-block');
                    $('#tel').attr('disabled', true).val(start_phone + '****' + end_phone);
                    swal('', '绑定手机成功', 'warning');
                }else{
                    if (response.msg){
                        swal('', response.msg, 'info');
                    } else {
                        swal('', response.err, 'warning');
                    }
                }
            },
            error: function(error){
                console.log(error);
            }
        });
    });

    /**
     * 解除手机绑定
     */
    var bindphone = '';
    $('#cancel_bind_phone').click(function(){
        var this_cancel_bind = $(this);
        if(confirm('确定取消绑定手机?')){
            $.ajax({
                type : 'get',
                url : SITE_URL + 'user/get_personal_info',
                success: function(response){
                    response = JSON.parse(response);
                    if (response.success){
                        bindphone = response.data.phone;
                        $.ajax({
                            type : 'post',
                            dataType: "json",
                            url : SITE_URL + 'verification_code/get_verified_phone_code',
                            data : {
                                phone : bindphone
                            },
                            success: function(response){
                                if (response.success){
                                    this_cancel_bind.parent().siblings('.save_cancel').css('display', 'inline-block');
                                    this_cancel_bind.hide();
                                    $('#verification_code').css('display', 'inline-block');
                                    $('#ensure_cancel_bind').css('display', 'inline-block');
                                    $('#send_code').hide();
                                    this_cancel_bind.parent().siblings('.save_cancel').find('.cancel').hide();
                                }else{
                                    swal('', response.msg, 'info');
                                }
                            },
                            error: function(error){
                                console.log(error);
                            }
                        });
                    }
                },
                error: function(error){
                    console.log(error);
                }
            });
        }
    });
    $('#ensure_cancel_bind').click(function () {
        var _this  = $(this);
        var code = $('#verification_code').val();
        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL + 'verification_code/check_verified_phone_code',
            data : {
                phone :bindphone,
                code : code
            },
            success: function(response){
                if (response.success){
                    $.ajax({
                        type : 'post',
                        dataType: "json",
                        url : SITE_URL+'user/update_user',
                        data : {
                            phone : null
                        },
                        success: function(response){
                            if (response.success){
                                _this.hide();
                                $('.save_cancel').css('display', 'none');
                                $('#verification_code').hide();
                                $('#bind_phone').css('display', 'inline-block');
                                $('#tel').val('').removeAttr('disabled').attr('placeholder', '未绑定');
                                swal('', '成功解除手机绑定', 'success');
                            }else{
                                swal('', response.msg, 'info');
                            }
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });
                }else{
                    swal('', response.msg, 'info');
                }
            },
            error: function(error){
                console.log(error);
            }
        });
    });
    /**
     * 解除邮箱绑定
     */
    $('#cancel_bind_email').click(function(){
        var this_cancel_bind = $(this);
        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL + 'user/update_user',
            data : {
                email : null
            },
            success: function(response){
                if (response.success){
                    this_cancel_bind.hide();
                    this_cancel_bind.parent('.cancel_bind').siblings('.bind').css('display', 'inline-block');
                    $('#email').val('');
                    swal('', '成功解除邮箱绑定', 'success');
                }else{
                    swal('', response.msg, 'info');
                }
            },
            error: function(error){
                console.log(error);
            }
        });
    });

    //我的报告
    if ($('#my_report').html() != undefined) {
        $.ajax({
            url:SITE_URL + 'user/get_my_report',
            type: 'get',
            dataType: 'json',
            success: function (response) {
                if (response.success){
                    $.each(response.data, function(index, row){
                        row.update_time = row.update_time.substring(0, 16);
                    });

                    var tpl = document.getElementById('report_tpl').innerHTML;
                    $("#report_list").html(template(tpl, {list: response.data}));
                }else{
                    $('#report_list').html('没有报告');
                }
            },
            error: function () {
            }
        })
    }

    //添加报告信息
    /*$('#add-report-info').click(function (e) {
        e.preventDefault();
        $('#report-list').show();
    });*/

    //我的夺宝
    if ($('#my_indiana').html() != undefined) {
        $.ajax({
            url:SITE_URL + 'integral_indiana/my_indiana',
            type: 'get',
            dataType: 'json',
            success: function (response) {
                if (response.success){
                    var tpl = document.getElementById('my_indiana_tpl').innerHTML;
                    $("#my_indiana_container").html(template(tpl, {list: response.data}));
                }
            },
            error: function () {
            }
        })
    }
    //我的抽奖
    if ($('#my_sweepstake').html() != undefined) {
        var page = 1,
            page_size = 10;
        $.ajax({
            type: 'get',
            dataType: 'json',
            url: SITE_URL + 'sweepstakes_commodity/get_my_prize/' + page + '/' + page_size,
            success: function (response) {
                if(response.success){
                    var tpl = document.getElementById('my_indiana_tpl').innerHTML;
                    $("#my_indiana_container").html(template(tpl, {list: response.data}));
                }else{

                }
            }
        });
    }
    //我的优惠券
    if ($('#my_discount').html() != undefined) {
        function get_user_discount(){
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: SITE_URL + 'user/get_discount_coupon_by_user_id',
                success: function (response) {
                    if(response.success){
                        $.each(response.data, function(index, row){
                            row.start_time = row.start_time.substr(0, 10);
                            row.end_time = row.end_time.substr(0, 10);
                        });
                        var tpl = document.getElementById('user_discount_tpl').innerHTML;
                        $("#user_discount").html(template(tpl, {list: response.data}));
                    }
                }
            });
        }

        function get_valid_discount(){
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: SITE_URL + 'user/get_pulished_discount_coupon',
                success: function (response) {
                    if(response.success){
                        $.each(response.data, function(index, row){
                            row.start_time = row.start_time.substr(0, 10);
                            row.end_time = row.end_time.substr(0, 10);
                        });
                        var tpl = document.getElementById('discount_tpl').innerHTML;
                        $("#discount").html(template(tpl, {list: response.data}));
                    }
                }
            });
        }

        get_user_discount();
        get_valid_discount();

        $(document).on('click', '#receive_discount', function(){
            var discount_id = $(this).data('discount-id');
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: SITE_URL + 'user/add_discount_coupon_to_user',
                data: {
                    id : discount_id
                },
                success: function (response) {
                    if(response.success){
                        get_user_discount();
                        get_valid_discount();
                    }
                }
            });
        });
    }

    /**
     * 添加报告信息
     */
    var report_info = {};

    $('#submit-report-info').click(function (e) {
        var verification = true;
        e.preventDefault();
        report_info.number = $('#order_number').val();
        report_info.name = $('#name').val();
        report_info.birth = $('#J-xl').val();
        report_info.phone = $('#phone').val();
        report_info.gender = $('input[name="sex"]:checked').val();
        report_info.identity_card = $('#identity_card').val();
        report_info.province = $('#province').find('option:selected').text();
        report_info.province_code = $('#province').find('option:selected').val();
        report_info.city = $('#city').find('option:selected').text();
        report_info.city_code = $('#city').find('option:selected').val();
        if (report_info.city_code == "120200" || report_info.city_code == "310200" || report_info.city_code == "429004" || report_info.city_code == "500200") {
            // 没有县级行政区信息
            report_info.district = "--区--";
            report_info.district_code = "0";
            $('#district').prop('disabled',true);
        }else{
            $('#district').prop('disabled',false);
            report_info.district = $('#district').find('option:selected').text();
            report_info.district_code = $('#district').find('option:selected').val();
        }

        report_info.address = $('#address').val();
        report_info.smoking = $('input[name="smoking"]:checked').val();
        report_info.height = $('#height').val();
        report_info.weight = $('#weight').val();
        // report_info.blood_relationship = $('#relationship').find('option:selected').val();
        console.log(report_info);
        $.each(report_info, function (index, data) {
            if(data == undefined || data == '--请选择--' || data == ''){
                verification = false;
            }
        });
        report_info.personal_history = $('#personal_history').val();
        report_info.family_history = $('#family_history').val();

        if(verification == false){
            swal('', '信息填写不完整，请完善基本信息!', 'info');
        }else{
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: SITE_URL + 'user/add_report',
                data: report_info,
                success: function (response) {
                    if(response.success){
                        swal('', '添加成功', 'success');
                        empty_report_info();
                    }else {
                        swal('', response.msg, 'info');
                        report_info = {};
                    }
                }
            });
        }
    });

    //清空报告信息列表
    function empty_report_info() {
        $('#order_number').val('');
        $('#name').val('');
        $('#J-xl').val('');
        $('#phone').val('');
        initAddress();
        getAddressList();
        $('#identity_card').val('');
        $('#district').empty();
        $('#address').val('');
        $('#height').val('');
        $('#weight').val('');
        $("select[id='relationship'] option[value='0']").prop("selected","selected");

        report_info = {};
    }


    /**
     * 编辑地址
     * 收获信息页面
     */
    if ($('#receiving_info').html() != undefined){
        district = new AMap.DistrictSearch({
            level: 'country',
            showbiz: false,
            subdistrict: 1
        });
        initAddress();
        getAddressList();
    }

    // 保存地址
    $("#save_address").click(function () {
        var verification = true;

        // 地址ID
        var id = $("#add_or_update_address_title").data("id");
        // 详细地址
        var address = $("#address").val();
        if (typeof(address) == 'undefined' || address == "") {
            verification = false;
            $("#address_error").text("请填写详细地址");
        }else {
            $("#address_error").text("");
        }

        // 收货人姓名
        var name = $("#name").val();
        if (typeof(name) == 'undefined' || name == "") {
            verification = false;
            $("#name_error").text("请填写收货人姓名");
        }else {
            $("#name_error").text("");
        }

        // 手机号码
        var phone = $("#phone").val();
        var pattern_phone = /^1(3|4|5|7|8)\d{9}$/;
        if (typeof(phone) == 'undefined' || phone == "") {
            verification = false;
            $("#phone_error").text("请填写收货人手机号码");
        }else if (!pattern_phone.exec(phone)) {
            verification = false;
            $("#phone_error").text("请填写正确的手机号码");
        }else {
            $("#phone_error").text("");
        }

        // 是否默认地址
        var default_addr = false;
        if ($("#default_addr")[0].checked) {
            default_addr = true;
        }else {
            default_addr = false;
        }

        // 省市区信息
        var province = document.getElementById('province');
        var city = document.getElementById('city');
        var district = document.getElementById('district');
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
                            swal('', '地址修改成功', 'success');
                            getAddressList();
                            reset_form();
                        }else {
                            swal('', result.msg, 'info');
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
                            swal('', '添加地址成功', 'success');
                            getAddressList();
                            reset_form();
                        }else {
                            swal('', result.msg, 'info');
                        }
                    },
                    error : function () {
                        console.log('服务器繁忙，请稍后重试');
                    }
                });
            }
        }
    });
    // 删除地址
    $(document).on('click', ".delete_address", function () {
        var id = $(this).data('id');
        $.ajax({
            url: SITE_URL + 'user/delete_address',
            type: 'post',
            dataType: 'json',
            data: {
               id:  id
            },
            success: function (result) {
                if (result.success) {
                    swal('', '删除地址成功', 'success');
                    getAddressList();
                }else {
                    swal('', result.msg, 'info');
                }
            },
            error: function () {
                console.log("服务器繁忙，删除地址失败");
            }
        });
    });
    // 编辑地址
    $(document).on('click', ".edit_address", function () {
        var id = $(this).data("id");
        var tr = $(this).parent().parent();
        var name = tr.children(".name").data('name');
        var province = tr.children(".city").data('province');
        var province_code = tr.children(".city").data('province-code');
        var city = tr.children(".city").data('city');
        var city_code = tr.children(".city").data('city-code');
        var district = tr.children(".city").data('district');
        var district_code = tr.children(".city").data('district-code');
        var address = tr.children(".address").data('address');
        var phone = tr.children(".phone").data('phone');
        var is_default = tr.children(".default").data('default');

        $("#add_or_update_address_title").html("编辑");
        $("#add_or_update_address_title").data("id", id);
        $("#address").val(address);
        $("#name").val(name);
        $("#phone").val(phone);
        if (is_default == 1) {
            $("#default_addr")[0].checked = true;
        }else {
            $("#default_addr")[0].checked = false;
        }

        // 填充省市区选择控件
        $("#province").val(province_code);
        searchNextLevel($("#province")[0], city_code, district_code);
    });

    if ($('#member_center').html() != undefined){
        //获取全部等级信息
        getAllLevel();
    }

    /**
     * 重置亲属表单信息
     */
    function init(){
        $('#member_name').val('');
        $('#J-xl').val('');
        $('#member_tel').val('');
        $('#member_id').val('');
        $('#medication_history').val('');
        $("input[type='checkbox']").prop("checked", false);
        $("select[id='member_relation'] option[value='0']").prop("selected","selected");
        $("select[id='health_status'] option[value='0']").prop("selected","selected");
    }
    $('#save_family_menber').css('display', 'inline-block');
    $('#update_family_menber').css('display', 'none');

    init();
    /**
     * 保存添加的亲属信息
     */
    $('#save_family_menber').click(function () {
        var member_name = $('#member_name').val();
        var sex = $('input[name="member_sex"]:checked').val();
        var birth = $('#J-xl').val();
        var member_tel = $('#member_tel').val();
        var member_id = $('#member_id').val();
        var medication_history = $('#medication_history').val();
        var member_treatment = [];
        var clinical_history = '';
        var member_relation = $('#member_relation').val();
        var health_status = $('#health_status').val();

        $('input[type="checkbox"]').each(function () {
            if($(this).prop("checked") == true){
                member_treatment.push($(this).val());
            }
        });
        if(member_treatment.length == 1){
            clinical_history = member_treatment[0];
        }else{
            clinical_history = member_treatment.join('-');
        };

        $.ajax({
            url: SITE_URL + 'user/add_family_relation',
            type: 'post',
            dataType: 'json',
            data: {
                name: member_name,
                gender: sex,
                phone: member_tel,
                birth: birth,
                identity_card: member_id,
                medication_history: medication_history,
                clinical_history: clinical_history,
                relation: member_relation,
                health_status: health_status

            },
            success: function (result) {
                if (result.success) {
                    get_family_info();
                    swal('', '添加亲属成功', 'success');
                }else {
                    swal('', result.msg, 'info');
                }
            },
            error: function () {
                console.log("服务器繁忙，添加亲属失败");
            }
        });
        init();
    });



    /**
     * 获取亲属信息
     */
    function get_family_info() {
        if ($('#my_family').html() != undefined){
            $.ajax({
                url: SITE_URL + 'user/get_all_family_info',
                type: 'post',
                dataType: 'json',
                success: function (result) {
                    if (result.success) {
                        $('#family_member_count').text(result.data.length);
                        for(var i = 0; i<result.data.length; i++){
                            if(result.data[i].gender == '0'){
                                result.data[i].gender = '女'
                            }else if(result.data[i].gender == '1'){
                                result.data[i].gender = '男'
                            }
                            if(result.data[i].relation == '10'){
                                result.data[i].relation = '父亲'
                            }else if(result.data[i].relation == '20'){
                                result.data[i].relation = '母亲'
                            }
                            var clinical_history_arr = result.data[i].clinical_history.split('-');

                            for(var j = 0; j<clinical_history_arr.length; j++){
                                if(clinical_history_arr[j] == '10'){
                                    result.data[i].identity_treat = '手术';
                                }else if(clinical_history_arr[j] == '20'){
                                    result.data[i].identity_radiotherapy = '放疗';
                                }else if(clinical_history_arr[j] == '30'){
                                    result.data[i].identity_chemotherapy = '化疗';
                                }else if(clinical_history_arr[j] == '40'){
                                    result.data[i].identity_targeted_therapies = '靶向药物治疗';
                                }
                            }
                        }
                        var tpl = document.getElementById('family_item_tpl').innerHTML;
                        $("#family_list_container").html(template(tpl, {list: result.data}));
                    }else {
                        swal('', '您还没有添加家族信息', 'info');
                    }
                },
                error: function () {
                    console.log("服务器繁忙，添加亲属失败");
                }
            });
        };
    };
    get_family_info();

    /**
     * 编辑亲属信息
     */
    var family_id = '';
    $(document).on('click', '#edit_family_menber',function () {
        $('#add_edit').html('编辑亲属信息');
        $('#save_family_menber').css('display', 'none');
        $('#update_family_menber').css('display', 'inline-block');
        $('.cancel-update').css('display', 'inline-block');
        family_id = $(this).data('id');
        $.ajax({
            url: SITE_URL + 'user/get_family_info_by_id/' + family_id,
            type: 'get',
            dataType: 'json',
            success: function (result) {
                if (result.success) {
                    $('#member_name').val(result.data.name);
                    $("input[name='member_sex'][value=" + result.data.gender + "]").prop("checked", "checked");
                    $('#member_tel').val(result.data.phone);
                    $('#J-xl').val(result.data.birth);
                    $('#member_id').val(result.data.identity_card);
                    $('#medication_history').val(result.data.medication_history);

                    var member_clinical_history_arr = result.data.clinical_history.split('-');
                    for(var i = 0; i<member_clinical_history_arr.length;i++){
                        $("input[type='checkbox'][value=" + member_clinical_history_arr[i] + "]").prop("checked", "checked");
                    }
                    $('#member_').val(result.data.clinical_history);
                    $("select[id='member_relation'] option[value='"+ result.data.relation + "']").prop("selected","selected");
                    $("select[id='health_status'] option[value='"+ result.data.health_status + "']").prop("selected","selected");
                }else {
                    swal('', result.msg, 'info');
                }
            }
        });
        init();
    });

    /**
     * 更新亲属信息
     */
    $(document).on('click', '#update_family_menber', function () {
        $('#save_family_menber').css('display', 'inline-block');
        $('#update_family_menber').css('display', 'none');
        $('.cancel-update').css('display', 'none');
        var member_name = $('#member_name').val();
        var sex = $('input[name="member_sex"]:checked').val();
        var birth = $('#J-xl').val();
        var member_tel = $('#member_tel').val();
        var member_id = $('#member_id').val();
        var medication_history = $('#medication_history').val();
        var member_treatment = [];
        var clinical_history = '';
        var member_relation = $('#member_relation').val();
        var health_status = $('#health_status').val();

        $('input[type="checkbox"]').each(function () {
            if($(this).prop("checked") == true){
                member_treatment.push($(this).val());
            }
        });
        if(member_treatment.length == 1){
            clinical_history = member_treatment[0];
        }else{
            clinical_history = member_treatment.join('-');
        }
        $.ajax({
            url: SITE_URL + 'user/update_family_relation_by_id/' + family_id,
            type: 'post',
            dataType: 'json',
            data: {
                name: member_name,
                gender: sex,
                phone: member_tel,
                birth: birth,
                identity_card: member_id,
                medication_history: medication_history,
                clinical_history: clinical_history,
                relation: member_relation,
                health_status: health_status
            },
            success: function (result) {
                if (result.success) {
                    $('#add_edit').html('新增亲属信息');
                    get_family_info();
                    swal('', '更新亲属信息成功', 'success');
                }else {
                    swal('', result.msg, 'info');
                }
            },
            error: function () {
                console.log("服务器繁忙，添加亲属失败");
            }
        });
        init();
    });

    /**
     *取消更新
     */
    $('.cancel-update').click(function () {
        $('#save_family_menber').css('display', 'inline-block');
        $('#update_family_menber').css('display', 'none');
        $('.cancel-update').css('display', 'none');
        $('#add_edit').html('新增亲属信息');
        init();
    });

    /**
     * 删除亲属信息
     */
    $(document).on('click', '#delt_family_menber', function () {
        var family_id = $(this).data('id');
        if(confirm('确定删除该信息吗')){
            $.ajax({
                url:SITE_URL + 'user/delete_family_relation_by_id/' + family_id,
                type: 'get',
                dataType: 'json',
                success: function (result) {
                    if(result.success){
                        get_family_info();
                        swal('', '删除成功!', 'success');
                    }else{
                        swal('', '删除失败!', 'info');
                    }
                },
                error: function () {
                }
            })
        }
    });

    var page = 1;
    /**
     * 我的收藏
     */
    $('.no-follow-person').show();
    $('.pagination ').hide();
    function get_commodity_collection(page) {
        $.ajax({
            url: SITE_URL + 'favorite/get_favorite_by_page',
            type: 'post',
            dataType: 'json',
            data:{
                page:page,
                page_size:10
            },
            success: function (result) {
                if (result.success) {
                    var tpl = document.getElementById('collection_list').innerHTML;
                    $("#thumbnails").html(template(tpl, {list: result.data}));
                    $('#total_pages').text(result.total_pages);
                    if(result.data.length < 0 || result.data == null){
                        $('.no-follow-person').show();
                        $('.pagination ').hide();
                    }else{
                        $('.no-follow-person').hide();
                        $('.pagination ').show();
                    }
                }else {
                    $("#thumbnails").html('');
                    $('.no-follow-person').show();
                    $('.pagination ').hide();
                }
            },
            error: function () {
                console.log("服务器繁忙，获取地址失败");
            }
        });
    }
    get_commodity_collection(1);


    //取消收藏
    $(document).on('click', '.cancel-collection', function (e) {
        e.stopPropagation();
        var commodity_id = $(this).data('commodity-id');
        var commodity_specification_id = $(this).data('specification-id');
        if(confirm('确认取消收藏吗')){
            $.ajax({
                type : 'post',
                dataType: "json",
                url : SITE_URL+'favorite/delete_by_commodity_id',
                data : {
                    commodity_id : commodity_id,
                    commodity_specification_id : commodity_specification_id
                },
                success : function(response){
                    if (response.success){
                        swal('', '取消成功!', 'success');
                        get_commodity_collection(page);
                    }else {
                        swal('', '取消失败!', 'info');
                    }
                },
                error: function(error){

                }
            });
        }
    });
    
    //查看详情
    $(document).on('click', '.thumbnail', function (e) {
        e.stopPropagation();
        var commodity_id = $(this).find('.cancel-collection').data('commodity-id');
        var commodity_specification_id = $(this).find('.cancel-collection').data('specification-id');
        window.location.href=SITE_URL + 'commodity/index/' + commodity_id + '/' + commodity_specification_id;
    });

    //上一页
    $('#Prev_page').click(function () {
        page--;
        if(page < 1){
            page = 1;
        }else{
            get_commodity_collection(page);
        }
    });
    //下一页
    $('#Next_page').click(function () {
        page++;
        if(page >= total_pages){
            swal('', '最后一页', 'info');
            return;
        }else{
            get_commodity_collection(page);
        }
    });
    //跳转页面
    $('#jump_to_page').click(function () {
        page = parseInt($(this).siblings('#page_num').val());
        get_commodity_collection(page);
    });



});

/**
 * 收获信息页面
 */
var district;
/**
 * 从服务器获取当前用户地址列表
 */
function getAddressList() {
    $.ajax({
        url: SITE_URL + 'user/show_address',
        type: 'post',
        dataType: 'json',
        success: function (result) {
            if (result.success) {
                var tpl = document.getElementById('address_item_tpl').innerHTML;
                $("#address_list_container").html(template(tpl, {list: result.data}));
                $("#address_count").html(result.data.length);
            }else {
                swal('', '您还没有添加地址信息', 'info');
            }
        },
        error: function () {
            console.log("服务器繁忙，获取地址失败");
        }
    });
}
/**
 * 初始化省市区选择控件
 */
function initAddress() {
    $("#city").innerHTML = '';
    $("#city").empty();
    $("#city").val("");
    $("#city").removeAttr("disabled");

    $('#district').innerHTML = '';
    $('#district').empty();
    $('#district').val("");
    $('#district').removeAttr("disabled");

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
    console.log(data);
    var subList = data.districtList;
    var level = data.level;
    //清空下一级别的下拉列表
    if (level === 'province') {
        nextLevel = 'city';
        $("#city").innerHTML = '';
        $('#district').innerHTML = '';
        $("#city").empty();
        $("#city").val("");
        $('#district').empty();
        $('#district').val("");
    } else if (level === 'city') {
        nextLevel = 'district';
        $('#district').innerHTML = '';
        $('#district').empty();
        $('#district').val("");
    }
    if (subList) {
        if (subList.length > 0) {
            $('#' + subList[0].level).empty();
        }

        var contentSub = new Option('--请选择--');
        contentSub.setAttribute("value", "");
        for (var i = 0, l = subList.length; i < l; i++) {
            var name = subList[i].name;
            var value = subList[i].adcode;
            var levelSub = subList[i].level;
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
        if (typeof(city_code) != 'undefined' && city_code != "" && levelSub == "city") {
            $('#' + levelSub).val(city_code);
            searchNextLevel($('#' + levelSub)[0], city_code, district_code);
        }else if (typeof(district_code) != 'undefined' && district_code != "" && levelSub == "district") {
            $('#' + levelSub).val(district_code);
        }
    }else {
        if (level == "province") {
            // 将市级、县级下拉列表置为不可用
            $("#city").attr('disabled', 'disabled');
            $("#district").attr('disabled', 'disabled');
        }else if (level == "city") {
            // 将县级下拉列表置为不可用
            $("#district").attr('disabled', 'disabled');
        }
    }

}
/**
 * 根据当前所选省市搜索下级行政区域列表
 * @param obj
 * @param city_code 城市代码，编辑地址时初始化控件使用
 * @param district_code 区县代码，编辑地址时初始化控件使用
 */
function searchNextLevel(obj, city_code, district_code) {
    city_code = city_code || '';
    district_code = district_code || '';
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
    initAddress();
    $("#address").val("");
    $("#name").val("");
    $("#phone").val("");
    $("#default_addr")[0].checked = false;
    $("#district_error").text("");
    $("#address_error").text("");
    $("#name_error").text("");
    $("#phone_error").text("");
}

//判断输入框的值是否变化
(function($) {
    $.fn.watch = function(callback) {
        return this.each(function() {
            //缓存以前的值
            $.data(this, 'originVal', $(this).val());

            //event
            $(this).on('keyup paste', function() {
                var originVal = $(this, 'originVal');
                var currentVal = $(this).val();

                if (originVal !== currentVal) {
                    $(this).siblings('.save_cancel').css('display', 'inline-block');
                    $(this).siblings('._edit').css('display', 'none');
                }else{
                    $(this).siblings('.save_cancel').css('display', 'none');
                    $(this).siblings('._edit').css('display', 'inline-block');
                }
            });
        });
    }

})(jQuery);

function isChange() {
    $('#personal_info .input_text').each(function () {
        $('#data_content').data('data_ori', $(this).val());
        $(this).blur(function () {
            if($(this).val() == $('.data_ori').data('data_ori')){
                $(this).siblings('.save_cancel').css('display', 'none');
                $(this).siblings('._edit').css('display', 'inline-block');
            }else {
                $(this).siblings('.save_cancel').css('display', 'none');
                $(this).siblings('._edit').css('display', 'inline-block');
            }
        })
    })
}

//输入框获取/失去焦点事件
function cancel_edit() {
    $('#personal_info .input_text').focus(function () {
        $(this).siblings('.save_cancel').css('display', 'inline-block');
        $(this).siblings('._edit').css('display', 'none');
    });

    $('#personal_info .input_text').blur(function () {

    })

}
//根据账户关联输入框内容判断绑定输入框是否可编辑状态
function bind_input_empty() {
    if($('#account_contact .unbind').attr('display') == 'inline-block'){
        $(this).parent('cancel_bind').siblings('.bind_content').attr('disabled', true);
    }
}

//获取全部等级信息
function getAllLevel(){
    $.ajax({
        type : 'get',
        dataType: "json",
        url : SITE_URL+'user/get_all_level',
        success: function(response){
            if (response.success){
                var tpl = document.getElementById('level_tpl').innerHTML;
                $("#level_list_container").html(template(tpl, {list: response.data}));
            }else{
                swal('', response.msg, 'info');
            }
        },
        error: function(error){

        }
    });
}



