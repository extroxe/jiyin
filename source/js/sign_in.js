/**
 * Created by sailwish009 on 2016/12/2.
 */
$(document).ready(function () {
    $('.info_input').each(function () {

    });

    /**
     * 登录验证
     */
    $('#username').bind('keypress',function(event){
        if(event.keyCode == "13") {
            $(this).parents('.form-horizontal').find('#password').focus();
        }
    });
    $('#password').bind('keypress',function(event){
        if(event.keyCode == "13") {
            sign_in();
        }
    });

    $('#login').click(function(){
        sign_in();
        // Save();
    });

    $('#weixin_login').click(function(){
        // 微信登录验证
        window.location.href = SITE_URL + '/user/login_from_weixin';
    });

    if ($.cookie("username") != undefined && $.cookie("password") != undefined) {
        $("#remenberUser").attr("checked", true);
        $("#username").val($.cookie("username"));
        $("#password").val('ertyughj');
    }

    $("#get_verification_code").click(function (e) {
        e.preventDefault();
        var phone = $("#phone").val();
        var verify_phone = /^1[3|4|5|7|8]\d{9}$/;
        if (verify_phone.test(phone)) {
            $.ajax({
                url: SITE_URL+ '/verification_code/get_register_code_for_login',
                type: 'POST',
                data: {
                    phone : phone
                },
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        // 验证码发送成功
                        get_verification_code_timer(120);
                    }else {
                        // 验证码发送失败
                    }
                },
                error: function () {
                    // 服务器错误
                }
            });
        }
    });

//    验证码登录
    $(document).on('click', '#login_by_code', function () {
        var phone = $('#phone').val();
        var code = $('#code').val();

        if(!phone || phone == ''){
            swal('请输入手机号');
            return;
        }

        if(!code || code == ''){
            swal('验证码有误');
            return;
        }

        $.ajax({
            url: SITE_URL+ 'index/do_login_by_phone',
            type: 'POST',
            data: {
                phone : phone,
                code: code
            },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    // 验证码发送成功
                    back_history();
                }else {
                    // 验证码发送失败
                    swal(data.msg)
                }
            },
            error: function () {
                // 服务器错误
            }
        });
    })

});

/**
 * 登录验证
 */
function sign_in() {
    var username = $('#username').val();
    var new_password = $('#password').val();
    var password = '';
    var auto_login = 0;
    if ($('#remenberUser').is(':checked')){
        auto_login = 1;
    }

    if (auto_login == 1 && $.cookie("password") != undefined){
        if (new_password == 'ertyughj'){
            password = $.cookie("password");
        }else{
            password = new_password;
        }
    }else{
        password = new_password;
    }

    $.ajax({
        type : 'post',
        dataType: "json",
        url : SITE_URL+'index/do_login',
        data : {
            username : username,
            password : password,
            auto_login : auto_login
        },
        success : function(response){
            if (response.success){
                back_history();
            }else{
                alert(response.msg);
            }
        },
        error : function(error){

        }
    });
}
var prev_url = document.referrer;
var url_arr = prev_url.substr(7, prev_url.length).split('/');
function back_history() {
    if (prev_url == '' || (url_arr[1] == 'index' && (url_arr[2] == 'sign_up' || url_arr[2] == 'sign_in'))){
        window.location.href = SITE_URL + 'index';
    }else{
        history.go(-1);
    }
}
/**
 * 设置获取验证码按钮上的定时器
 * @param $second 秒数
 */
function get_verification_code_timer(second) {
    if (parseInt(second) < 1) {
        return;
    }

    var temp = parseInt(second);
    $("#get_verification_code").attr('disabled', 'disabled');
    $("#get_verification_code").html(temp+"s后获取");
    var timer = setInterval(function () {
        temp--;
        if (temp < 1) {
            $("#get_verification_code").html("获取验证码");
            $("#get_verification_code").removeAttr('disabled');
            clearInterval(timer);
        }else {
            $("#get_verification_code").attr('disabled', 'disabled');
            $("#get_verification_code").html(temp+"s后获取");
        }
    }, 1000);

}



