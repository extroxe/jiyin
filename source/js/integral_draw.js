var turnplate={
    restaraunts:[],				//大转盘奖品名称
    colors:[],					//大转盘奖品区块对应背景颜色
    outsideRadius:185,			//大转盘外圆的半径
    textRadius:155,				//大转盘奖品位置距离圆心的距离
    insideRadius:68,			//大转盘内圆的半径
    startAngle:0,				//开始角度

    bRotate:false				//false:停止;ture:旋转
};

    var rotateTimeOut = function (){
        $('#wheelcanvas').rotate({
            angle:0,
            animateTo:2160,
            duration:8000,
            callback:function (){
                swal('', '网络超时，请检查您的网络设置!', 'warning');
            }
        });
    };

    //旋转转盘 item:奖品位置; txt：提示语;
    var rotateFn = function (item, txt){
        var angles = item * (360 / turnplate.restaraunts.length) - (360 / (turnplate.restaraunts.length*2));
        if(angles < 270){
            angles = 270 - angles;
        }else{
            angles = 360 - angles + 270;
        }
        $('#wheelcanvas').stopRotate();
        $('#wheelcanvas').rotate({
            angle:0,
            animateTo:angles+1800,
            duration:8000,
            callback:function (){
                swal('', '恭喜获得' + txt, 'success');
                turnplate.bRotate = !turnplate.bRotate;
            }
        });
    };

var sweepstakes_id;
var default_item;
var sweepstakes_commoditys;

//页面所有元素加载完毕后执行drawRouletteWheel()方法对转盘进行渲染
window.onload=function(){
    var get_sweepstakes_commodity_flag = true;
    $('.no-activity').hide();
    $('.on-activity').hide();
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: SITE_URL + 'sweepstakes_commodity/get_sweepstakes_commodity' ,
        success:function (data) {
            if (data.success){
                $('.on-activity').text(data.data[0].start_time.substr(0,16) + ' 至 ' + data.data[0].end_time.substr(0,16));
                $('.on-activity').show();
                get_sweepstakes_commodity_flag = true;
                sweepstakes_id = data.data[0].sweepstakes_id;
                get_scroll_info(sweepstakes_id);
                sweepstakes_commoditys = data.data;
                var arr = [];
                for(var i = 0; i < data.data.length; i++){
                    if (data.data[i].commodity_id == null && data.data[i].point != null){
                        arr[i] = data.data[i].point + "积分";
                    }else if (data.data[i].commodity_id != null && data.data[i].point == null){
                        arr[i] = data.data[i].commodity_name.substr(0, 10);
                    }else if (data.data[i].commodity_id == null && data.data[i].point == null){
                        arr[i] = "谢谢参与";
                        default_item = i;
                    }
                }

                //动态添加大转盘的奖品与奖品区域背景颜色
                turnplate.restaraunts = arr;
                for (var j = 0; j < arr.length; j++){
                    if (j % 2 == 0){
                        turnplate.colors[j] = "#FFF4D6";
                    }else{
                        turnplate.colors[j] = "#FFFFFF";
                    }
                }
                drawRouletteWheel();
            } else {
                $('.no-activity').show();
                $('.banner .pointer').css('cursor', 'default');
            }
        }
    });

    get_rules();
    //获取滚动奖品
    var page = 1, page_size = 30;
    var tpl = document.getElementById('get_scroll_prizes_tpl').innerHTML;
    function get_scroll_info(sweepstakes_id) {
        $.ajax({
            type:'post',
            dataType: 'json',
            url: SITE_URL + 'sweepstakes_commodity/get_scroll_prize/'+ sweepstakes_id + '/' + page + '/' + page_size,
            success:function (response) {
                if(response.success) {
                    $.each(response.data, function (index, data) {
                        if(data['prize_name'].length > 6){
                            data['prize_name'] = data['prize_name'].substr(0,6) + '...';
                        }
                        if(data['phone'] == false){
                            data['phone'] = '无';
                        }
                    });
                    $('table tbody').html(template(tpl, {data: response.data}));
                    setInterval(function () {
                        $('table tbody tr:first').appendTo('table tbody').animate("slow");
                    },1000)
                }else{
                }
            }
        });
    }

    if(get_sweepstakes_commodity_flag == true){
        $('.pointer').click(function (){
            $.ajax({
                type: 'get',
                dataType: 'json',
                url: SITE_URL + 'sweepstakes_commodity/find_one/' + sweepstakes_id,
                success: function (data) {
                    if (data.success){
                        user_prize = data.data;
                        if (data.data.commodity_id != null){
                            sweepstakes_commodity_id = data.data.id;
                            sweepstakes_insert_id = data.insert_id;
                        }else{
                            receive(data.insert_id);
                        }
                        //找到转盘坐标
                        for(var i = 0; i < sweepstakes_commoditys.length; i++){
                            if (sweepstakes_commoditys[i].id === data.data.id){
                                item = i + 1;
                            }
                        }
                    }else{
                        //没有符合条件奖品，置为谢谢参与
                        item = default_item + 1;
                    }
                    if(turnplate.bRotate)return;
                    turnplate.bRotate = !turnplate.bRotate;
                    //获取随机数(奖品个数范围内)
                    //奖品数量等于10,指针落在对应奖品区域的中心角度[252, 216, 180, 144, 108, 72, 36, 360, 324, 288]
                    rotateFn(item, turnplate.restaraunts[item - 1]);
                }
            });
        });
    }
};

//获取抽奖规则
function get_rules() {
    $.ajax({
        type:'post',
        dataType: 'json',
        url: SITE_URL + 'system_setting/get_sweepstakes_rules',
        success:function (response) {
            if(response.success) {
                $('.rules p').html(response.data.value);
            }else{
                console.log("wrong");
            }
        }
    });
}

var sweepstakes_insert_id;
var user_prize;
var item;
var sweepstakes_commodity_id = 0;

function receive(insert_id) {
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: SITE_URL + 'sweepstakes_commodity/receive/' + insert_id,
        success: function (data) {
            if (data.success) {
                if (data.point != null) {
                    user_info.current_point = user_info.current_point + parseInt(data.point);
                }
            }
        }
    });
}
function drawRouletteWheel() {
    var canvas = document.getElementById("wheelcanvas");
    if (canvas.getContext) {
        //根据奖品个数计算圆周角度
        var arc = Math.PI / (turnplate.restaraunts.length/2);
        var ctx = canvas.getContext("2d");
        //在给定矩形内清空一个矩形
        ctx.clearRect(0,0,300,300);
        //strokeStyle 属性设置或返回用于笔触的颜色、渐变或模式
        ctx.strokeStyle = "#FFBE04";
        //font 属性设置或返回画布上文本内容的当前字体属性
        ctx.font = '16px Microsoft YaHei';

        for(var i = 0; i < turnplate.restaraunts.length; i++) {
            var angle = turnplate.startAngle + i * arc;
            ctx.fillStyle = turnplate.colors[i];
            ctx.beginPath();
            //arc(x,y,r,起始角,结束角,绘制方向) 方法创建弧/曲线（用于创建圆或部分圆）
            ctx.arc(211, 211, turnplate.outsideRadius, angle, angle + arc, false);
            ctx.arc(211, 211, turnplate.insideRadius, angle + arc, angle, true);
            ctx.stroke();
            ctx.fill();
            //锁画布(为了保存之前的画布状态)
            ctx.save();

            //----绘制奖品开始----
            ctx.fillStyle = "#E5302F";
            var text = turnplate.restaraunts[i];
            var line_height = 17;
            //translate方法重新映射画布上的 (0,0) 位置
            ctx.translate(211 + Math.cos(angle + arc / 2) * turnplate.textRadius, 211 + Math.sin(angle + arc / 2) * turnplate.textRadius);

            //rotate方法旋转当前的绘图
            ctx.rotate(angle + arc / 2 + Math.PI / 2);

            /** 下面代码根据奖品类型、奖品名称长度渲染不同效果，如字体、颜色、图片效果。(具体根据实际情况改变) **/
            if(text.indexOf("M")>0){//流量包
                var texts = text.split("M");
                for(var j = 0; j < texts.length; j++){
                    ctx.font = j == 0?'bold 20px Microsoft YaHei':'16px Microsoft YaHei';
                    if(j == 0){
                        ctx.fillText(texts[j]+"M", -ctx.measureText(texts[j]+"M").width / 2, j * line_height);
                    }else{
                        ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
                    }
                }
            }else if(text.indexOf("M") == -1 && text.length>6){//奖品名称长度超过一定范围
                text = text.substring(0,6)+"||"+text.substring(6);
                var texts = text.split("||");
                for(var j = 0; j<texts.length; j++){
                    ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
                }
            }else{
                //在画布上绘制填色的文本。文本的默认颜色是黑色
                //measureText()方法返回包含一个对象，该对象包含以像素计的指定字体宽度
                ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
            }

            //添加对应图标
            if(text.indexOf("闪币")>0){
                var img= document.getElementById("shan-img");
                img.onload=function(){
                    ctx.drawImage(img,-15,10);
                };
                ctx.drawImage(img,-15,10);
            }else if(text.indexOf("谢谢参与")>=0){
                var img= document.getElementById("sorry-img");
                img.onload=function(){
                    ctx.drawImage(img,-15,10);
                };
                ctx.drawImage(img,-15,10);
            }


            //把当前画布返回（调整）到上一个save()状态之前
            ctx.restore();
            //----绘制奖品结束----
        }
    }
}