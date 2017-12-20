/**
 * Created by sailwish009 on 2017/2/10.
 */
$(function () {
    KindEditor.ready(function(K) {
        window.editor = K.create('#myEditor', {
            width: '900px',
            height: '500px',
            allowFileManager: true,
            readonlyMode: false,
            themeType: 'default',
            newlineTag: 'p',
            resizeType: 1,
            allowPreviewEmoticons: true,
            allowImageUpload : true,
            allowUpload: true,
            uploadJson: SITE_URL + 'attachment/up_attachment', //服务端上传图片处理URI
            fileManagerJson: SITE_URL + 'attachment/up_attachment',
            items: ['source', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', '|', 'lineheight', 'indent', 'wordpaste', '|', 'outdent', '|', 'emoticons', 'image', 'multiimage', 'link', 'fullscreen']
        });
    });
    var url = window.location.href;
    var post_bar_id = url.substring(url.lastIndexOf('/') + 1);
    var recommend_post = [];
    var flag = true;
    
    /**
     * 根据post_bar_id 获取 贴吧信息
     */
    /**
     * 根据贴吧id获取贴吧信息
     */
    function get_post_bar_info(post_bar_id){
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'post_bar/get_post_bar_by_id' ,
            data:{
                id:post_bar_id
            },
            success:function (response) {
                if(response.success){
                    $('.post_name').text(response.data.name)
                    $('.breadcrumb .post_bar_name').text(response.data.name);
                    $('.body .focus_num span').text(response.data.focus_count);
                    $('.body .post_num span').text(response.data.post_count);
                    if (response.data.description == null){
                        $('#des_content').parent().hide();
                    }else{
                        $('#des_content').parent().show();
                        $('#des_content').text(response.data.description);
                    }
                    if(response.data.is_focused == true){
                        $('#focus_post_bar').hide();
                        $('#cancel_focus_post_bar').show();
                    }else{
                        $('#focus_post_bar').show();
                        $('#cancel_focus_post_bar').hide();
                    }

                }else{
                    alert(response.msg);
                }
            }
        });
    };
    get_post_bar_info(post_bar_id);

    /**
     * 关注贴吧
     */
    $('#cancel_focus_post_bar').hide();
    $(document).on('click', '#focus_post_bar', function () {
        var self = $(this);
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'post_bar/focus_post_bar',
            data:{
                post_bar_id: post_bar_id
            },
            success:function (response) {
                get_post_bar_info(post_bar_id);
                if(response.success){
                    self.hide();
                    $('#cancel_focus_post_bar').show();
                    alert(response.msg)
                }else{
                    alert(response.msg);
                }
            },
            error:function () {

            }
        })
    });
    /**
     * 取消关注
     */
    $(document).on('click', '#cancel_focus_post_bar', function () {
        var self = $(this);
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'post_bar/cancel_focus_post_bar',
            data:{
                post_bar_id:post_bar_id
            },
            success:function (response) {
                get_post_bar_info(post_bar_id);
                if(response.success){
                    self.hide();
                    $('#focus_post_bar').show();
                    alert(response.msg);
                }else{
                    alert(response.msg);
                }
            },
            error:function () {

            }
        })
    });

    /**
     * 获取推荐的吧
     */
    function init_recommend(){
        recommend_post = [];
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: SITE_URL + 'post_bar/get_recommend_post_bar',
            data:{
                num: 3
            },
            success:function (response) {
                if(response.success){
                    recommend_post = response.data;
                    for(var i = 0; i<recommend_post.length;i++){
                        $('.hot_post ul').append('<li>\
                    <span class="background">' + parseInt(i+1) + '</span>\
                    <a href="'+ SITE_URL + 'my_city/post/' + recommend_post[i].id +'">' + recommend_post[i].name +'</a>\
                </li>');
                    }
                    console.log(response.msg);
                }else{
                    alert(response.msg)
                }
            }
        });
    };
    init_recommend();
    /**
     * 换一批
     */
    $('.exchange').click(function () {
        $('.hot_post ul').html('');
        init_recommend();
    });
    
    $('.post_bar_name').click(function () {
        window.location.href = SITE_URL + 'my_city/post/' + post_bar_id;
    });


    /**
     * 发帖
     */

    $('.publish_post_btn').click(function () {

        var title = $('.post_title').val();
        // var content = editor.getText();
        var content = window.editor.text();
        console.log(content);
        var status_id = 2;
        if(title == ''){
            alert('标题不能为空')
        }else if(content == ''){
            alert('内容不能为空')
        }else{
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: SITE_URL + 'post/add',
                data:{
                    post_bar_id: post_bar_id,
                    title: title,
                    content: content,
                    status_id: status_id
                },
                success:function (response) {
                    if(response.success){
                        alert('发帖成功');
                         window.location.href = SITE_URL + 'my_city/post/' + post_bar_id;
                    }else{
                        alert(response.msg)
                    }
                }
            })
        }

    })

});
