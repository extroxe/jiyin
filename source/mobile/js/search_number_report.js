/**
 * Created by sailwish001 on 2017/6/5.
 */
angular.module('app')
    .controller('searchReportCtrl',['$scope', 'ajax', '$interval', function ($scope, ajax, $interval) {
        $scope.verification_code = '';
        $scope.number = '';
        $scope.report_info = {};
        $scope.userInfo = {};
        $scope.projects = [];
        $scope.project_ids = [];
        $scope.url = window.location.href;
        var lastStr = $scope.url.substring($scope.url.lastIndexOf('/') + 1);

        $scope.reports = JSON.parse(localStorage.getItem("reports"));
        if($scope.url.indexOf('#search_result') != -1){
            $('.search-report').hide();
            $('.my-report').show();
        }

        if(!$scope.number || $scope.number == ''){
            if(($scope.url.indexOf('#') != -1 && $scope.url.indexOf('/1/') == -1) || ($scope.url.indexOf('#') == -1 && isNaN(parseInt(lastStr)))){
                window.location.href = SITE_URL + 'weixin/user/search_report_by_number/' + new Date().getTime();
            }else if(($scope.url.indexOf('#') != -1 && $scope.url.indexOf('/1/') != -1) || ($scope.url.indexOf('#') == -1 && lastStr == 1)){
                window.location.href = SITE_URL + 'weixin/user/search_report_by_number/1/' + new Date().getTime();
            }
        }

        //获取当前页面高度
        var winHeight = $(window).height();
        $(window).resize(function(){
            var thisHeight=$(this).height();
            if(winHeight - thisHeight >50){
                //窗口发生改变(大),故此时键盘弹出
                //当软键盘弹出，在这里面操作
                $('.bottom-btn').addClass('activity').removeClass('footer');
            }else{
                //窗口发生改变(小),故此时键盘收起
                //当软键盘收起，在此处操作
                $('.bottom-btn').addClass('footer').removeClass('activity');
            }
        });

        var prompt = new Prompt();
        var result = '';
        //初始化滑动页
        var singlePage = new Page({
            "onLoad": function (e) {
                //var targetPageId;
                if (e.isRoot) {

                } else {

                }
            }
        });

        /*查询*/
        $scope.search_report_info = function (element,target) {
            $('.alert').remove();
            $('.mask').remove();
            if(!$scope.number || $scope.number == ''){
                var popAlert=new Alert("报告编号有误",{"提示":true});
                popAlert.show();
                return;
            }
            if($scope.verification_code.toLowerCase() != result.toLowerCase()){
                var popAlert=new Alert("验证码有误",{"提示":true});
                popAlert.show();

            }else{
                ajax.req('POST', 'user/check_report', {number: $scope.number})
                    .then(function(response){
                        if (response.success){
                            if((response.data.report.name == '' || !response.data.report.name
                                || response.data.report.name == null || response.data.report.phone == '' || !response.data.report.phone
                                || response.data.report.phone == null) && response.data.report.path == null){
                                var popAlert=new Alert("因您未录入检测人信息</br>报告延迟，暂时还未出具体报告",{"提示":true});
                                popAlert.show();
                            }else {
                                if((response.data.report.name == '' || !response.data.report.name
                                    || response.data.report.name == null || response.data.report.phone == '' || !response.data.report.phone
                                    || response.data.report.phone == null) && response.data.report.path != null){
                                    $scope.report_info = {};

                                    $scope.pdf_path = SITE_URL + response.data.report.path;
                                    var popConfirm=new Alert("因您寄回样本未录入检测人信息</br>您的报告信息栏是空白信息",{
                                        onClickOk:function(e){

                                        },onClickCancel:function(e){
                                            window.location.href = SITE_URL + response.data.report.path;
                                            $('.alert').remove();
                                            $('.mask').remove();
                                        }
                                    });
                                    popConfirm.show();
                                    $('.alert-handler').find('a').eq(0).prop('href','javascript:void(0)');
                                    $('.alert-handler').find('a').eq(0).text('已知 前往查询');
                                    $('.alert-handler').find('a').eq(1).remove();
                                }else{
                                    $scope.report_info = response.data.report;
                                    localStorage.setItem("reports", JSON.stringify($scope.report_info));
                                    $('.search-report').hide();
                                    var openType = target || "";
                                    singlePage.open(element, openType);
                                    $(element).find('.titlebar').show();
                                }
                            }
                        }else{
                            var popConfirm=new Alert("查询不到您输入的样本码</br>若样本码输入无误</br>可拨打400-100-3908联系客服处理",{
                                onClickOk:function(e){
                                    $('.alert').remove();
                                    $('.mask').remove();
                                },onClickCancel:function(e){

                                }
                            });
                            popConfirm.show();
                            $('.alert-handler').find('a').eq(0).prop('href','tel:400-100-3908');
                            $('.alert-handler').find('a').eq(0).text('拨打');
                            $('.alert-handler').find('a').eq(1).text('取消');
                        }
                    });
            }
            drawPic();
        };
        //查看pdf
        $scope.watch_pdf = function (report) {
            window.location.href = SITE_URL + report.path;
        };

        $scope.backPdfList = function () {
            history.go(-1);
            $('.my-report').show();
            $('#myPDF').remove();
        };

        $scope.url = window.location.href;
        if($scope.url.indexOf('#') != -1 && $scope.reports == ''){
            $scope.back();
        }

        $scope.check_history = function (id) {
            $(id).find('.titlebar').show();
            singlePage.open(id);
        };
        $scope.backPage = function (id, className) {
            $(id).find('.titlebar').hide();
            if(className){
                $(className).show();
            }
            history.go(-1);
        };

        /**生成一个随机数**/
        function randomNum(min,max){
            return Math.floor( Math.random()*(max-min)+min);
        }
        /**生成一个随机色**/
        function randomColor(min,max){
            var r = randomNum(min,max);
            var g = randomNum(min,max);
            var b = randomNum(min,max);
            return "rgb("+r+","+g+","+b+")";
        }
        drawPic();
        document.getElementById("changeImg").onclick = function(e){
            e.preventDefault();
            drawPic();
        };

        /**绘制验证码图片**/
        function drawPic(){
            result='';
            var canvas=document.getElementById("canvas");
            var width=canvas.width;
            var height=canvas.height;
            var ctx = canvas.getContext('2d');
            ctx.textBaseline = 'bottom';

            /**绘制背景色**/
            ctx.fillStyle = randomColor(180,240); //颜色若太深可能导致看不清
            ctx.fillRect(0,0,width,height);
            /**绘制文字**/
            var str = 'ABCEFGHJKLMNPQRSTWXY123456789';

            for(var i=0; i<4; i++){
                var txt = str[randomNum(0,str.length)];
                ctx.fillStyle = randomColor(50,160);  //随机生成字体颜色
                ctx.font = randomNum(25,40)+'px SimHei'; //随机生成字体大小
                var x = 10+i*25;
                var y = randomNum(30,35);
                var deg = randomNum(-45, 45);
                //修改坐标原点和旋转角度
                ctx.translate(x,y);
                ctx.rotate(deg*Math.PI/180);
                ctx.fillText(txt, 0,0);
                //恢复坐标原点和旋转角度
                ctx.rotate(-deg*Math.PI/180);
                ctx.translate(-x,-y);
                result += txt;
            }
            /**绘制干扰线**/
            // for(var i=0; i<8; i++){
            //     ctx.strokeStyle = randomColor(40,180);
            //     ctx.beginPath();
            //     ctx.moveTo( randomNum(0,width), randomNum(0,height) );
            //     ctx.lineTo( randomNum(0,width), randomNum(0,height) );
            //     ctx.stroke();
            // }
            /**绘制干扰点**/
            for(var i=0; i<100; i++){
                ctx.fillStyle = randomColor(0,255);
                ctx.beginPath();
                ctx.arc(randomNum(0,width),randomNum(0,height), 1, 0, 2*Math.PI);
                ctx.fill();
            }

        }


    }]);