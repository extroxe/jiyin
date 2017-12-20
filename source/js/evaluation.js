/**
 * Created by sailwish009 on 2017/2/7.
 */
$(function () {

    var url = window.location.href;
    var id = url.substring(url.lastIndexOf('/') + 1);
    var score = 0;
    var order_data = [];
    var attachment_ids = [];
    var commodity_id = '';
    var order_id = '';
    var star_click_flag = false;
    var current_score = 0;

    //评价打分
    $(document).on('mouseover', '.star', function () {
        star_click_flag = false;
        score = $(this).index();
        $('.star').removeClass('active');
        $(this).addClass('active');
        $(this).prevAll('.star').addClass('active');
    });
    $(document).on('mouseout', '.star', function () {
        if (star_click_flag){
            return false;
        }
        $(this).removeClass('active');
        $(this).siblings('.star').removeClass('active');
        if (current_score > 0){
            $($('.star')[current_score -1]).addClass('active');
            $($('.star')[current_score- 1]).prevAll('.star').addClass('active');
        }
    });
    $(document).on('click', '.star', function () {
        star_click_flag= true;
        current_score = score = $(this).index();
        $(this).addClass('active');
        $(this).prevAll('.star').addClass('active');
    });

    //获取子订单信息
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: SITE_URL + 'order/get_sub_order_by_id',
        data:{
            order_commodity_id: id
        },
        success:function (response) {
            if(response.success){
                order_data = response.data;
                commodity_id = response.data.commodity_id;
                order_id = response.data.order_id;

                $('#order_number a').text(response.data.number);

                var tpl = document.getElementById('evaluation_list_tpl').innerHTML;
                $('#all_product_box').html(template(tpl, {data: response.data}));

                if($("#all_product_box").html().trim() != ""){
                    //    获取评价信息
                        get_evaluation(order_id);
                }
            }else{
                console.log(response.msg);
            }
        },
        error:function () {

        }
    });

    function get_evaluation(order_id) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'order/get_evaluation_by_order_id',
            data:{
                id: order_id
            },
            success: function (response) {
                if(response.success){

                    $('.img_detail').remove();
                    $.each(response.data, function (index, sub_order) {
                        if(sub_order.order_commodity_id == id && sub_order.id != null){
                            $(document).unbind();
                            $(document).off('mouseover', '.star');
                            $(document).off('mouseout', '.star');
                            $(".star").eq(parseInt(sub_order.score - 1)).addClass('active');
                            $(".star").eq(parseInt(sub_order.score - 1)).prevAll().addClass('active');
                            $(".content").val(sub_order.content).attr("disabled", true);
                            var img = $('#imghead');
                            $.each(sub_order.pic, function (index, sub_pic) {
                                img.before('<div class="img_detail">\
                               <img src=' + SITE_URL + sub_pic.path+ '>\
                                </div>');
                            });
                            $(".star").unbind("click");
                            img.remove();
                            $(".describle").remove();
                            $(".img_detail").css("top", '0');
                            $("#submit_btn button").attr('disabled',true).text("已评价");
                        }
                    })
                }else{
                    console.log('获取失败')
                }
            }
        });
    }

    //删除图片
    $(document).on('click', '.delete', function () {
        var index_del = $(this).data('index');
        $('.delete').each(function (index) {
            if(index == index_del){
                attachment_ids.splice(index,1);
            }
        });
        $(this).parent().remove();
        up_num();
        console.log(attachment_ids);
    });

    up_num();


    //图片上传预览
    var del_index = 0;
    $(document).on('change', '#previewImg', function () {
        var file = this;
        var img = $('#imghead');
        var image_url;
        var file_reader = new FileReader();
        if(file.files[0].size/1024/1024 > 5){
            $('.describle').html('<span style="color: red; font-size: 15px">上传图片不能大于5M!</span>')
        }else{
            $('.describle').html('共\
            <span class="upload_img_num" style="color:#eb3c3f"> 0 </span>张，还可以上传\
            <span class="all_img_num" style="color:#eb3c3f"> 5 </span> 张');
            file_reader.onload = (function(e){
                image_url = e.target.result;
                img.before('<div class="img_detail">\
                   <img src=' + image_url+ '>\
                   <img class="img_loading" src="' + SITE_URL + 'source/img/load.gif">\
                    <span class="delete fa fa-times" data-index = '+ del_index +'></span>\
                    </div>');
                up_num();
                check_md5(file, image_url);
                del_index++;
            });
            file_reader.readAsDataURL(file.files[0]);
        }

    });

//提交评价
    var attach_ids = [];
    var attachment_id_str = '';
    $('#submit_btn').click(function (e) {
        attach_ids = [].concat(attachment_ids);
        var content = $('.content').val();
        attachment_id_str = attach_ids.splice(',').join('-');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: SITE_URL + 'order/evaluate_order',

            data:{
                commodity_id: commodity_id,
                order_id: order_id,
                order_commodity_id: id,
                score: score,
                content: content,
                attachment_ids: attachment_id_str
            },
            success:function (response) {
                if(response.success){
                    alert(response.msg);
                    get_evaluation(order_id);
                }else{
                    alert(response.msg);
                }
            },
            error:function () {
            }
        });
    });
    
    //查看订单详情
    $(".order-number").click(function () {
        window.location.href = SITE_URL + 'order/detail/' + order_id;
    })

    function check_md5(file, image_url) {
        var fileReader = new FileReader(),
            blobSlice = File.prototype.mozSlice || File.prototype.webkitSlice || File.prototype.slice,
            file = file.files[0],
            chunkSize = 2097152,
            chunks = Math.ceil(file.size / chunkSize),
            currentChunk = 0,
            spark = new SparkMD5();

        fileReader.onload = function(e) {
            spark.appendBinary(e.target.result); // append binary string
            currentChunk++;

            if (currentChunk < chunks) {
                loadNext();
            } else {
                upload_img(spark.end(), image_url);
            }
        };
        function loadNext() {
            var start = currentChunk * chunkSize,
                end = start + chunkSize >= file.size ? file.size : start + chunkSize;
            fileReader.readAsBinaryString(blobSlice.call(file, start, end));
        }
        loadNext();
    }

    function upload_img(md5, image_url){
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: SITE_URL + 'attachment/check_md5',
            data:{
                md5_code: md5
            },
            success:function (response) {
                if(response.exist == false){
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: SITE_URL + 'attachment/up_attachment',
                        data:{
                            str:image_url
                        },
                        success:function (response) {
                            if(response){
                                $('.img_loading').hide();
                                attachment_ids.push(response.attachment_id);
                            }else {
                            }
                        }
                    });
                }else{
                    $('.img_loading').hide();
                    attachment_ids.push(response.attachment_id);
                }
                console.log(attachment_ids);
            }
        });


    }

});

//上传图片数量显示
var lost;
function up_num() {
    var num = 0;

    $('.img_detail').each(function () {
        num ++;
    });
    lost = 5 - num;
    $('.upload_img_num').html(num);
    $('.all_img_num').html(lost);
    if(lost <= 0){
        $('#imghead').css('display', 'none');
        $('.img_detail').css('top', '0');
        $('.describle').css('display', 'none');
    }else{
        $('#imghead').css('display', 'inline-block');
        $('.describle').css('display', 'inline-block');

    }
}


