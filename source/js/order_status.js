/**
 * Created by sailwish009 on 2016/11/24.
 */
$(function () {
    var i = 3;
    var intervalid;
    intervalid = setInterval(function(){
        if (i == 0) {
            window.location.href = SITE_URL + 'order/order_list';
            clearInterval(intervalid);
        }
        $('#timer').html(i);
        i--;
    }, 1000);
});
