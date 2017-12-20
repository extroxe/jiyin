$(document).ready(function(){
    //轮播
    $('.carousel').carousel({
        interval: 3000
    });

    var is_weixin = $('#page-content').data('is-weixin');
    if(is_weixin){
        $('#bing_phone').show();
    }
    // $('#bing_phone').show();

    $('#page-content .box').hover(function(){
        $('.box .img_detail').animate({left:'-150px'},500);
    },function(){
        $('.box .img_detail').animate({left:'0px'},500);
    });

    $('#flash_sale .mask').click(function(){
        window.location.href = SITE_URL + 'index/search?flash_sale=true&page_size=9';
    });

    $('#hot_sale .mask').click(function(){
        window.location.href = SITE_URL + 'index/search?hot_sale=true&page_size=9';
    });

    $('#hot_change .mask').click(function(){
        window.location.href = SITE_URL + 'index/search?hot_exchange=true&page_size=9';
    });

    // $(document).on('click','.check-directly', function () {
    //     $('.content .bind-phone').hide();
    //     $('.content .sign-up-username').show();
    //     $('.tabbar .bind-phone').removeClass('active');
    //     $('.tabbar .sign-up-username').addClass('active');
    // });

//    获取限时折扣
    var flash_sale = [];
    var end_times = [];
    var count_down_times = [];

    $.ajax({
        type:'get',
        url: SITE_URL + 'index/get_flash_sale',
        dataType: 'json',
        success:function (result) {
            if(result){
                flash_sale = result;
                for(var i = 0; i<flash_sale.length; i++){
                    var unlinx_time = Date.parse(new Date(flash_sale[i].end_time));
                    end_times.push(unlinx_time);
                }

                setInterval(function () {
                    var now_time = Math.round(new Date().getTime());
                    for(var i = 0; i<end_times.length; i++){

                        var count_time = end_times[i] - now_time;
                        var leftsecond = parseInt(count_time/1000);
                        var d=Math.floor(leftsecond/(60*60*24));
                        var hour=Math.floor((leftsecond-d*24*60*60)/3600);
                        var min=Math.floor((leftsecond-d*24*60*60-hour*3600)/60);
                        var sec=Math.floor(leftsecond-d*24*60*60-hour*3600-min*60);

                        d = checkTime(d);
                        hour = checkTime(hour);
                        min = checkTime(min);
                        sec = checkTime(sec);
                        var text= d + '天 ' + hour + ':' + min + ':' + sec;
                        $('.count-time').eq(i).text(text);
                    }
                    // console.log(text);
                }, 1000);
                console.log(end_times);
            }
        }
    });

    function checkTime(i){ //将0-9的数字前面加上0，例1变为01
        if(i<10)
        {
            i = "0" + i;
        }
        return i;
    }
    // $('.hinter').css('visibility', 'hidden');
    $(document).on('click', ".get-code",function () {
        var phone = $("#phone").val();
        var verify_phone = /^1[3|4|5|7|8]\d{9}$/;
        if (verify_phone.test(phone)) {
            $.ajax({
                url: SITE_URL+ '/verification_code/get_verified_phone_code',
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
        }else{
            $('.hinter').css('visibility', 'visible');
        }
    });
    var phone = '';
    var code = '';
    $(document).on('click', '.check-directly', function () {
        phone = $('#phone').val();
        code = $('#code').val();
        if(!phone || phone == ''){
            swal('数据格式有误');
            return;
        }
        $.ajax({
            type:'post',
            url:SITE_URL + 'index/bind_phone_for_login',
            dataType:'json',
            data:{
                phone: phone,
                code: code
            },
            success:function (result) {
                if(result.success){
                    if(result.data == phone){
                        $('.content .bind-phone').hide();
                        $('.content .sign-up-username').show();
                        $('.tabbar .bind-phone').removeClass('active');
                        $('.tabbar .sign-up-username').addClass('active');
                    }else{
                        swal({
                            title: '绑定成功',
                            type: 'success'
                        },function (isConfirm) {
                            if(isConfirm){
                                window.location.href = SITE_URL + 'index';
                            }
                        });
                    }

                }else{
                    if(result.msg == '当前手机号已绑定了微信，请登录系统后解绑重新绑定'){
                        swal({title:result.msg},function (isConfirm) {
                            if(isConfirm){
                                window.location.href = SITE_URL + 'index';
                            }
                        });
                    }else{
                        swal(result.msg)
                    }
                }
            }
        })
    });
    $('.hinter').css('visibility', 'hidden');
    $(document).on('focus', '.content .sign-up-username input', function () {
        $('.hinter').css('visibility', 'hidden');
    });
    //确认用户名
    $(document).on('click', '.sign-confirm', function () {
        var username = $('#username').val();
        var password = $('#password').val();
        var conform_password = $('#conform_password').val();
        
        if(!username || username == ''){
            $('.username-hinter').css('visibility', 'visible');
            return;
        }
        if(!password || password == ''){
            $('.password-hinter').css('visibility', 'visible');
            return;
        }
        if(!conform_password || conform_password == ''){
            $('.confirm-hinter1').css('visibility', 'visible');
            return;
        }
        if(password != conform_password){
            $('.confirm-hinter2').css('visibility', 'visible');
            return;
        }
        $.ajax({
            url: SITE_URL+ '/user/fill_in_userinfo',
            type: 'POST',
            data: {
                username : username,
                password : password,
                password_confirm : conform_password,
                phone : phone,
            },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    swal({
                        title: '绑定成功',
                        type:'success'
                    },function (isConfirm) {
                        if(isConfirm){
                            window.location.href = SITE_URL + 'index';
                        }
                    });
                }else {
                    swal(data.msg);
                }
            },
            error: function () {
                // 服务器错误
                alert('服务器繁忙，请稍后重试！');
            }

        })

    });

});

/**
 * 设置获取验证码按钮上的定时器
 * @param $second 秒数
 */
function get_verification_code_timer(second) {
    if (parseInt(second) < 1) {
        return;
    }

    var temp = parseInt(second);
    $(".get-code").attr('disabled', 'disabled');
    $(".get-code").html(temp+"s后获取");
    var timer = setInterval(function () {
        temp--;
        if (temp < 1) {
            $(".get-code").html("获取验证码");
            $(".get-code").removeAttr('disabled');
            clearInterval(timer);
        }else {
            $(".get-code").attr('disabled', 'disabled');
            $(".get-code").html(temp+"s后获取");
        }
    }, 1000);

}