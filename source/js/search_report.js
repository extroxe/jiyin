$(function(){
    var send_code_flag = false;
    var set_time_flag = true;

    //发送验证码
    $('#send_code_btn').click(function(){
        var temp = $(this);
        var phone = $('#phone').val();

        if (phone == ''){
            swal('', '请填写手机号!', 'info');
        }else{
            set_time_flag = false;
            $.ajax({
                type : 'post',
                dataType: "json",
                url : SITE_URL+'verification_code/get_verified_phone_code',
                data : {
                    phone : phone,
                    purpose_id : 4
                },
                success : function(response){
                    if(response.success){
                        send_code_flag = true;
                        get_verification_code_timer(60);
                        temp.attr('disabled', true);
                    }else{
                        set_time_flag = true;
                        swal('', '验证码发送失败，请重新发送!', 'info');
                    }
                },
                error : function(error){
                    swal('', '网络错误，请检查网络是否已连接...', 'info');
                }
            });
        }
    });

    //查询
    $('#search_btn').click(function(){
        var identity_card = $('#identity_card').val();
        var phone = $('#phone').val();
        var verification_code = $('#verification_code').val();

        // if (identity_card == ''){
        //     alert('请填写身份证号后6位！');
        //     return false;
        // }else if (phone == ''){
        //     alert('请填写手机号！');
        //     return false;
        // }else if (verification_code == ''){
        //     alert('请填写验证码！');
        //     return false;
        // }
        
        if (phone == ''){
            swal('', '请填写手机号!', 'info');
            return false;
        }else if (verification_code == ''){
            swal('', '请填写验证码!', 'info');
            return false;
        }

        if (send_code_flag){
            $.ajax({
                type : 'post',
                dataType: "json",
                url : SITE_URL+'index/get_report_by_condition',
                data : {
                    identity_card : identity_card,
                    phone : phone,
                    verification_code : verification_code
                },
                success : function(response){
                    if (response.success){
                        $.each(response.data, function(index, row){
                            row.update_time = row.update_time.substring(0, 16);
                        });

                        var tpl = document.getElementById('report_tpl').innerHTML;
                        $("#report_list").html(template(tpl, {list: response.data}));
                    } else {
                        swal('', response.msg, 'info');
                    }
                },
                error : function(error){

                }
            });
        }else{
            swal('', '请先发送验证码再查询！', 'info');
        }
    });
});


//设置时间倒计时
function get_verification_code_timer(second) {
    if (parseInt(second) < 1) {
        return;
    }
    second = parseInt(second);
    second--;
    $('#send_code_btn').text('('+second+'s) 重新获取');
    $('#send_code_btn').attr('disabled',true);
    if (second >0){
        setTimeout(function(){
            get_verification_code_timer(second);
        }, 1000);
    }else {
        // set_time_flag = true;
        $('#send_code_btn').text('发送验证码').attr('disabled',false);
    }
}