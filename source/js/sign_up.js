/**
 * Created by sailwish009 on 2016/12/2.
 */
$(function () {
    jQuery.validator.addMethod("phone", function (value, element) {
        var verify_phone = /^1[3|4|5|7|8]\d{9}$/;
        return this.optional(element) || (verify_phone.test(value));
    }, "请填写正确的手机号");

    var validate = $("#register_form").validate({
        focusInvalid: true,
        errorElement: 'span',
        rules: {
            username: {
                required:true,
                minlength: 5,
                remote: {
                    url: SITE_URL+'/user/check_username',
                    type: 'post',
                    data: {
                        username: function () {
                            return $('#username').val();
                        }
                    }
                }
            },
            password: {
                required:true,
                minlength: 6
            },
            password_confirm: {
                required:true,
                equalTo: "#password"
            },
            phone: {
                required:true,
                minlength: 11,
                maxlength: 11,
                phone: true,
                remote: {
                    url: SITE_URL+'/user/check_phone',
                    type: 'post',
                    data: {
                        username: function () {
                            return $('#phone').val();
                        }
                    }
                }
            },
            verification_code: {
                required:true,
                minlength:4
            }
        },

        messages: {
            username: {
                required:"请填写用户名",
                minlength: $.validator.format("用户名至少需要{0}位"),
                remote: "该用户名已被注册"
            },
            password: {
                required:"请填写密码",
                minlength: $.validator.format("密码至少需要{0}位")
            },
            password_confirm: {
                required:"请填写确认密码",
                equalTo: "两次密码输入不一致"
            },
            phone: {
                required: "请填写手机号",
                minlength: "请填写正确的手机号",
                maxlength: "请填写正确的手机号",
                phone: "请填写正确的手机号",
                remote: "该手机号已被使用"
            },
            verification_code: {
                required: "请填写手机验证码",
                minlength: "请填写手机验证码"
            }
        },
        errorPlacement: function ( error, element ) {
            // Add the `help-block` class to the error element
            // element.parent().parent().addClass('error').removeClass('success');
            error.addClass( "help-inline" );

            if ( element.prop( "type" ) === "checkbox" ) {
                error.insertAfter( element.parent( "label" ) );
            } else {
                if ($(element).attr("id") == 'verification_code') {
                    error.insertAfter( element.next() );
                }else {
                    error.insertAfter( element );
                }

            }
        },
        success: function ( label, element ) {
            $(element).parent().parent().addClass('success').removeClass('error');
        },
        highlight: function ( element, errorClass, validClass ) {
            if ($(element).attr("id") == 'phone') {
                $("#get_verification_code").attr('disabled', 'disabled');
            }
            $(element).parent().parent().addClass('error').removeClass('success');
        },
        unhighlight: function (element, errorClass, validClass) {
            if ($(element).attr("id") == 'verification_code') {
                $(element).next().next().remove();
            }else {
                $(element).next().remove();
            }
            if ($(element).attr("id") == 'phone') {
                $("#get_verification_code").removeAttr('disabled');
            }
            $(element).parent().parent().addClass('success').removeClass('error');
        },
        submitHandler: function (form) {
            $.ajax({
                url: SITE_URL+ '/user/register',
                type: 'POST',
                data: {
                    username : $('#username').val(),
                    password : $('#password').val(),
                    password_confirm : $('#password_confirm').val(),
                    phone : $('#phone').val(),
                    verification_code : $('#verification_code').val()
                },
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        alert('注册成功，请登录');
                        window.location.href = SITE_URL + 'index/sign_in';
                    }else {
                        alert(data.msg);
                    }
                },
                error: function () {
                    // 服务器错误
                    alert('服务器繁忙，请稍后重试！');
                }
            });
        }
    });
    
    $("#get_verification_code").click(function () {
        var phone = $("#phone").val();
        var verify_phone = /^1[3|4|5|7|8]\d{9}$/;
        if (verify_phone.test(phone)) {
            $.ajax({
                url: SITE_URL+ '/verification_code/get_register_code',
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
})

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
    $("#get_verification_code").html(temp+"s后重新获取");
    var timer = setInterval(function () {
        temp--;
        if (temp < 1) {
            $("#get_verification_code").html("获取验证码");
            $("#get_verification_code").removeAttr('disabled');
            clearInterval(timer);
        }else {
            $("#get_verification_code").attr('disabled', 'disabled');
            $("#get_verification_code").html(temp+"s后重新获取");
        }
    }, 1000);

}