/**
 * Created by sailwish009 on 2017/3/9.
 */
$(function () {
    var page = 1,
        page_size = 5;

    //获取我参与的活动信息
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: SITE_URL + 'sweepstakes_commodity/get_my_prize/' + page + '/' + page_size,
        success: function (response) {
            if(response.success){
                var tpl = document.getElementById('activity-record-tpl').innerHTML;
                $('#activity-record').html(template(tpl,{data:response.data}));
            }else{

            }
        }
    });
    //去领奖
    $(document).on('click', '.accept-prize', function () {
        var id = $(this).data('id');
        var result_id = $(this).data('result-id');
        window.location.href = SITE_URL + 'weixin/integral/receive_prize/' + id + '/' + result_id + '/0';
    });
});