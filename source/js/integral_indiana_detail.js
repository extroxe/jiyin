$(function(){
    $('#indiana-now').click(function(){
        var id = $(this).data('id');
        var point = $(this).data('point');
        if(confirm('本次活动需要' + point + '积分，确认参加？')){
            $.ajax({
                type : 'post',
                dataType: "json",
                url : SITE_URL + 'integral_indiana/join_integral_indiana',
                data : {
                    id: id,
                    bet_num: 1
                },
                success : function(response){
                    if (response.success){
                        swal("", "参与成功，请耐心等待夺宝结果!", "success")
                        window.location.reload();
                    }else{
                        swal("", response.msg, "info")
                    }
                },
                error: function(error){
                    console.log(error);
                }
            });
        }
    });
});