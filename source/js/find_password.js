$(function () {
    var time = 120;
    var phone_num = '';
    var reset_flag = false;

    //获取验证码
    $(document).on('click', '#get_verification_code', function () {
        phone_num = $("#phone").val();
        var verify_phone = /^1[3|4|5|7|8]\d{9}$/;
        if (verify_phone.test(phone_num)) {
            $(this).attr('disabled', true);
            $.ajax({
                url: SITE_URL+ '/verification_code/get_verified_phone_code',
                type: 'POST',
                data: {
                    phone : phone_num,
                    purpose_id : 5
                },
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        // 验证码发送成功
                        reset_flag = true;
                        var timer = setInterval(function () {
                            time--;
                            if(time == 0){
                                clearInterval(timer);
                                $('#get_verification_code').attr('disabled',false).text('获取验证码');
                                time = 120;
                                return;
                            }
                            $('#get_verification_code').text(time + '后重新获取');
                        },1000);
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

    //提交
    $(document).on('click', '#submit', function(){
        var phone = $("#phone").val();
        var code = $('#verification_code').val();
        var new_password = $('#new_password').val();
        var confirm_password = $('#confirm_password').val();
        var verify_phone = /^1[3|4|5|7|8]\d{9}$/;
        if (phone == ''){
            alert('手机号不能为空');
        }else if (!verify_phone.test(phone)){
            alert('手机号不正确');
        }else if(code == ''){
            alert('验证码不能为空');
        }else if (new_password !== confirm_password){
            alert('两次输入的密码不一致');
        }else{
            $.ajax({
                type : 'post',
                dataType: "json",
                url : SITE_URL+'index/update_password_by_verified_code',
                data : {
                    phone : phone,
                    code : code,
                    password: new_password
                },
                success: function(response){
                    if (response.success){
                        alert(response.msg)
                    }else{
                        alert(response.msg);
                    }
                },
                error: function(error){

                }
            });
        }
    });

    fill(phone_num);
    //确认账号
    $(document).on('click', '#verify_phone',function(){
        fill(phone_num);
    });

    //重置密码
    $(document).on('click', '#reset_password',function(){
        if(reset_flag == true){
            reset();
        }
    });

    //下一步
    $(document).on('click', '#next',function(){
        reset();
    });

});

//确认账号
function fill(phone_num){
    var tpl = document.getElementById('find_password_tpl').innerHTML;
    $('#find_password').html(template(tpl,{phone_num:phone_num}));
}

//重置密码
function reset(){
    var tpl = document.getElementById('reset_password_tpl').innerHTML;
    $('#find_password').html(template(tpl));
}