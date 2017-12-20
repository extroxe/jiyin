/**
 * Created by sailwish009 on 2016/12/14.
 */
$(function () {
    var now = (new Date()).toLocaleString();

    /**
     * 显示寄回模态框
     */
    $('#send_back_btn').click(function(){
        var checked_box = $('.cart_table').find('input[type=checkbox]:checked');
        var type_ids = [];
        $('.cart_table input[type=checkbox]').each(function () {
            if($(this).prop('checked')){
                type_ids.push($(this).data('sub-order-type-id'));
            }
        });

        if($.inArray(2, type_ids) != '-1'){
            alert('实物商品不需寄回！');
            return false;
        }else if (checked_box.length > 1){
            alert('只能选择单个订单');
            return false;
        }else if (checked_box.length <= 0){
            alert('请选择订单');
            return false;
        }else{
            $('#send_back_modal').modal('show');
        }

        var sub_order_id = checked_box.data('sub-order-id');

        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL+'order/get_sent_back_info',
            data : {
                id : sub_order_id
            },
            success : function(response){
                if (response.success){
                    $('#express_company option').each(function(){
                        if ($(this).val() == 2){
                            $(this).attr('selected', 'selected');
                        }
                    });
                    $('#express_number').val(response.data.express_number);
                }
            },
            error : function(error){

            }
        });
    });

    /**
     * 保存寄回信息
     */
    $('#save_send_back_info').click(function(){
        var checked_box = $('.cart_table').find('input[type=checkbox]:checked');
        var sub_order_id = checked_box.data('sub-order-id');
        var express_company_id = $('#express_company option:selected').val();
        var express_number = $('#express_number').val();

        $.ajax({
            type : 'post',
            dataType: "json",
            url : SITE_URL+'order/update_sub_order',
            data : {
                id : sub_order_id,
                express_company_id : express_company_id,
                express_number : express_number
            },
            success : function(response){
                if (response.success){
                    $('#send_back_modal').modal('hide');
                    alert('保存成功');
                    $('#express_number').val('');
                }
            },
            error : function(error){

            }
        });
    });

    /**
     * 退款
     */
    $('.delect_click').click(function(){
        var order_id = $(this).data('id');
        if (confirm('确定申请退款？')) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    order_id: order_id
                },
                url: SITE_URL + 'refund/application_for_refund/',
                success: function (data) {
                    if (data.success) {
                        alert('退款申请成功！');
                    } else {
                        alert(data.msg);
                    }
                },
                error: function () {
                }
            });
        }
    });

    //跳转评价页
    $('.evaluate_click').click(function(){
        var sub_order_id = $(this).data('id');
        window.location.href = SITE_URL+'order/evaluation/' + sub_order_id;
    })
});