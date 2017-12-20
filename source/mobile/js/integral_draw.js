angular.module('app')
    .controller('integralDrawCtrl', ['$scope', 'ajax', function ($scope, ajax) {

        var turnplate={
            restaraunts:[],				//大转盘奖品名称
            colors:[],					//大转盘奖品区块对应背景颜色
            outsideRadius:192,			//大转盘外圆的半径
            textRadius:155,				//大转盘奖品位置距离圆心的距离
            insideRadius:68,			//大转盘内圆的半径
            startAngle:0,				//开始角度

            bRotate:false				//false:停止;ture:旋转
        };

        $scope.my_prizes = [];
        $scope.page = 1;
        $scope.page_size = 5;


        window.onload=function(){
            ajax.req('POST', 'sweepstakes_commodity/get_sweepstakes_commodity')
                .then(function (data) {
                    if (data.success){
                        $scope.sweepstakes_commoditys = data.data;
                        $scope.get_scroll_prize($scope.sweepstakes_commoditys[0].sweepstakes_id);
                        var arr = new Array();
                        // $scope.commodity_img_path = [];
                        var j = 0;
                        for(var i = 0; i < $scope.sweepstakes_commoditys.length; i++){
                            if ($scope.sweepstakes_commoditys[i].commodity_id == null && $scope.sweepstakes_commoditys[i].point != null){
                                arr[i] = $scope.sweepstakes_commoditys[i].point + "积分";
                            }else if ($scope.sweepstakes_commoditys[i].point == null && $scope.sweepstakes_commoditys[i].commodity_id != null){
                                arr[i] = $scope.sweepstakes_commoditys[i].commodity_name.substr(0, 10);
                                // $('#sorry-img').after('<img class="img-path" src="' + SITE_URL + $scope.sweepstakes_commoditys[i].commodity_path+ '" style="width: 33px; height: 33px" id="' + i +'">');
                            }else if ($scope.sweepstakes_commoditys[i].commodity_id == null && $scope.sweepstakes_commoditys[i].point == null){
                                arr[i] = "谢谢参与";
                                $scope.default_item = i;
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
                        var s1=new Slider("#carousel1",{
                            "pagination":".slider-pagination",
                            "loop":true,
                            "autoplay":2000
                        });
                    }else{
                        alert('暂无相关抽奖活动');
                    }
                });
            //获取抽奖规则
            ajax.req('POST', 'system_setting/get_sweepstakes_rules')
                .then(function (response) {
                    if (response.success){
                        $scope.sweepstakes_rules = response.data.value;
                    }
                });
        };

        var rotateTimeOut = function (){
            $('#wheelcanvas').rotate({
                angle:0,
                animateTo:2160,
                duration:8000,
                callback:function (){
                    alert('网络超时，请检查您的网络设置！');
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
                    if(txt == '谢谢参与'){
                        txt = '很遗憾，下次再来吧';
                        $scope.hinter_info(txt);
                    }else if(txt.indexOf('积分')>0){
                        txt = '恭喜您，获得' + txt;
                        $scope.hinter_info(txt);
                    } else{
                        txt = '恭喜您，获得' + txt;
                        var popAlert=new Alert(txt,{
                            onClickOk:function(e){
                                //领取实物奖品
                                if ($scope.sweepstakes_commodity_id){
                                    window.location.href = SITE_URL + 'weixin/integral/receive_prize/' + $scope.sweepstakes_commodity_id + '/' + $scope.sweepstakes_insert_id + '/0';
                                }
                                e.hide();
                                e.alert.remove();
                                e.mask.remove();
                            }
                        });
                        $('.alert-handler a')[0].innerHTML = '去领奖';
                        popAlert.show();
                    }

                    turnplate.bRotate = !turnplate.bRotate;
                }
            });
        };

        //显示抽奖提示信息
        $scope.hinter_info = function (txt) {
            var popAlert=new Alert(txt, {
                onClickOk:function (e) {
                    e.hide();
                    e.alert.remove();
                    e.mask.remove();
                }
            });
            $(".alert h1").css({
                "background":"url("+SITE_URL+'source/mobile/img/alert_money.png'+")",
                "background-repeat": "no-repeat",
                "background-size": "100%"
            });
            $(".alert h1").text("");
            $('.alert-handler a')[0].innerHTML = '我知道了';
            $(".alert").css({
                "background":"url("+SITE_URL+'source/mobile/img/alert_bg.png'+")",
                "background-repeat": "no-repeat",
                "background-size": "100%"
            });
            $('.alert-handler').css({
                "background":"url("+SITE_URL+'source/mobile/img/alert_btn.png'+")",
                "background-repeat": "no-repeat",
                "background-size": "100%"
            });
            popAlert.show();
        };

        //领取积分
        $scope.receive = function (insert_id) {
            ajax.req('GET', 'sweepstakes_commodity/receive/' + insert_id)
                .then(function (data) {
                    if (data.success){
                        if (data.point != null){
                            $scope.user_info.current_point = $scope.user_info.current_point + parseInt(data.point);
                        }
                    }
                });
        };
        //去领奖
        $scope.accept_prize = function (id, result_id) {
            window.location.href = SITE_URL + 'weixin/integral/receive_prize/' + id + '/' + result_id + '/0';
        };
        $('.pointer').click(function (){
            $scope.sweepstakes_commodity_id = 0;
            if(parseInt($scope.user_info.current_point) >= parseInt($scope.sweepstakes_commoditys[0].consume_points)) {
                ajax.req('POST', 'sweepstakes_commodity/find_one/' + $scope.sweepstakes_commoditys[0].sweepstakes_id)
                    .then(function (data) {
                        if (data.success){
                            $scope.user_prize = data.data;
                            if (data.data.commodity_id != null){
                                $scope.sweepstakes_commodity_id = data.data.id;
                                $scope.sweepstakes_insert_id = data.insert_id;
                            }else{
                                $scope.receive(data.insert_id);
                            }
                            //找到转盘坐标
                            for(var i = 0; i < $scope.sweepstakes_commoditys.length; i++){
                                if ($scope.sweepstakes_commoditys[i].id === data.data.id){
                                    $scope.item = i + 1;
                                }
                            }
                        }else{
                            //没有符合条件奖品，置为谢谢参与
                            $scope.item = $scope.default_item + 1;
                        }
                        $scope.user_info.current_point -= $scope.sweepstakes_commoditys[0].consume_points;
                        if(turnplate.bRotate)return;
                        turnplate.bRotate = !turnplate.bRotate;
                        //获取随机数(奖品个数范围内)
                        //奖品数量等于10,指针落在对应奖品区域的中心角度[252, 216, 180, 144, 108, 72, 36, 360, 324, 288]
                        rotateFn($scope.item, turnplate.restaraunts[$scope.item - 1]);
                    });
            }else{
                var popAlert = new Alert("当前积分少于活动所需积分，无法抽奖");
                popAlert.show();
            }
        });

        $scope.$watch('user_id', function (nv) {
            if (nv){
                ajax.req('POST', 'user/get_personal_info')
                    .then(function (data) {
                        if (data.success){
                            $scope.user_info = data.data;
                        }else{
                            $scope.scroll_prizes = [];
                        }
                    });
            }
        });
        //获取滚动奖品列表
        $scope.get_scroll_prize = function (sweepstakes_id) {
            ajax.req('POST', 'sweepstakes_commodity/get_scroll_prize/'+ sweepstakes_id +'/1/10')
                .then(function (data) {
                    if (data.success){
                        $scope.scroll_prizes = data.data;
                    }else{
                        $scope.scroll_prizes = [];
                    }
                });
        };

//页面所有元素加载完毕后执行drawRouletteWheel()方法对转盘进行渲染
        function drawRouletteWheel() {
            var canvas = document.getElementById("wheelcanvas");
            if (canvas.getContext) {
                //根据奖品个数计算圆周角度
                var arc = Math.PI / (turnplate.restaraunts.length/2);
                var ctx = canvas.getContext("2d");
                //在给定矩形内清空一个矩形
                ctx.clearRect(0,0,422,422);
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
                    if(text.indexOf("积分")>0){
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
                    }/*else{
                        $.each($('.img-path'), function (index, ele) {
                            var img = document.getElementById(index);
                            img.onload = function () {
                                ctx.drawImage(img, -15, 10, 33, 33);
                            }
                        });
                    }*/

                    //把当前画布返回（调整）到上一个save()状态之前
                    ctx.restore();
                    //----绘制奖品结束----
                }
            }
        }
        //回顶部
        $(".back-top").css("display","none");
        $(window).scroll(function(){
            var sc=$("#rules").offset().top - $(window).scrollTop();
            if(sc < 0){
                $(".back-top").css("display","block");
            }else{
                $(".back-top").css("display","none");
            }
        });

        $(".activity-rules-btn").click(function () {
            $("html, body").animate({scrollTop: $($(this).attr("href")).offset().top - 20 + "px"}, 500);
            return false;
        });

        $scope.back_top = function(){
            $('body,html').animate({scrollTop:0},500);
        };

        setInterval(function(){
            $("#oDiv").find("#oUl").animate({
                marginTop : "-25px"
            },500,function(){
                $(this).css({marginTop : "0px"}).find("li:first").appendTo(this);
            });
        },3000);

        var singlePage=new Page({
            "onLoad": function (e) {
                var targetPageId;
            }
        });

        //获取我的奖品
        $scope.flag = true;
        $scope.get_my_prizes = function (me) {
            ajax.req('GET', 'sweepstakes_commodity/get_my_prize/' + $scope.page + '/' + $scope.page_size)
                .then(function (data) {
                    if (data.success){
                        $scope.flag = true;
                        angular.forEach(data.data, function (prizes, index) {
                            $scope.my_prizes.push(prizes);
                        });
                        setTimeout(function(){
                            // 每次数据加载完，必须重置
                            me.resetload();
                        },1000);
                    }else{
                        // 锁定
                        $scope.flag = false;
                        me.lock();
                        // 无数据
                        me.noData();
                        // 即使加载出错，也得重置
                        me.resetload();
                    }
                });
            $scope.page++;
        };

        $scope.openPage = function(id){
            $('.home-page').hide();
            if($scope.flag){
                $('.content').dropload({
                    scrollArea : window,
                    loadUpFn: function (me) {
                        $scope.get_my_prizes(me);
                    },
                    loadDownFn : function(me){
                        $scope.get_my_prizes(me);
                    }
                });
            }
            singlePage.open(id);
        };

        $scope.back = function () {
            history.go(-1);
            $('.home-page').show();
        };
    }])
    .filter('interception', function () {
        return function (str) {
            return str.substr(0, 15);
        }
    });

