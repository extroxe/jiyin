/**
 * Created by sailwish004 on 2017/2/7.
 */
$(function () {
    $('.pay-success').css('background-image', 'linear-gradient(to bottom, #c2c2c2, #ccc)');
    var time = 5;
    var timer = setInterval(function () {
        $('#timer').html('(' +time+')');
        time--;
        $('.pay-success').css('cursor', 'default');
        $('.pay-success').css('background-image', 'linear-gradient(to bottom, #c2c2c2, #ccc)');
        if(time == 0){
            clearInterval(timer);
            $('#timer').hide();
            $('.pay-success').css('cursor', 'pointer');
            $('.pay-success').css('background-image', '-webkit-gradient(linear, 0 0, 0 100%, from(#62c462), to(#51a351))');
        }
    }, 1000);
})