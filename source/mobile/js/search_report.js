/**
 * Created by sailwish001 on 2017/6/5.
 */
angular.module('app')
    .controller('searchReportCtrl',['$scope', 'ajax', '$interval', function ($scope, ajax, $interval) {
        $scope.flag = false;
        $scope.userInfo = [];
        $scope.send_code_ope = '发送验证码';
        $scope.reports = [];
        $scope.userInfo.card = '';

        $scope.url = window.location.href;

        var lastStr = $scope.url.substring($scope.url.lastIndexOf('/') + 1);
        $scope.reports = JSON.parse(localStorage.getItem("reports"));
        if($scope.url.indexOf('#search_result') != -1){
            $('.search-report').hide();
            $('.my-report').show();
        }

        // if($scope.userInfo.phone == '' || !$scope.userInfo.phone){
        //     if(($scope.url.indexOf('#') != -1 && $scope.url.indexOf('/1/') == -1) || ($scope.url.indexOf('#') == -1 && isNaN(parseInt(lastStr)))){
        //         window.location.href = SITE_URL + 'weixin/user/search_report/' + new Date().getTime();
        //     }else if(($scope.url.indexOf('#') != -1 && $scope.url.indexOf('/1/') != -1) || ($scope.url.indexOf('#') == -1 && lastStr == 1)){
        //         window.location.href = SITE_URL + 'weixin/user/search_report/1/' + new Date().getTime();
        //     }
        // }

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
        //初始化滑动页
        var singlePage = new Page({
            "onLoad": function (e) {
                //var targetPageId;
                if (e.isRoot) {

                } else {

                }
            }
        });
        /**
         * 发送验证码
         */
        $scope.send_checkcode = function (userInfo) {
            userInfo.verification_code = '';
            ajax.req('POST', 'verification_code/get_verified_phone_code',{
                phone:userInfo.phone,
                purpose_id : 4
            }).then(function (data) {
                if(data.success){
                    $scope.get_verification_code_timer(60);
                }else{
                    prompt.setText(data.msg);
                    prompt.show();
                }
            });
        };
        /**
         * 获取重新发送验证码所需时间
         */
        $scope.get_verification_code_timer = function(second) {

            if (parseInt(second) < 1) {
                return;
            }

            var temp = parseInt(second);
            $scope.flag = true;

            $scope.send_code_ope = temp + 's后重新发送';

            var timer = $interval(function () {
                temp--;
                if (temp < 1) {
                    $scope.send_code_ope = "获取验证码";
                    $scope.flag = false;
                    $interval.cancel(timer);
                }else {
                    $scope.flag = true;
                    $scope.send_code_ope = temp + 's后重新发送';
                }
            }, 1000);

        };
        /*查询*/
        var popConfirm=new Alert("您未录入检测人信息</br>无法手机号查询",{
            onClickOk:function(e){
                var url = window.location.href;
                if(url.indexOf('/1') != -1 ){
                    window.location.href = SITE_URL + 'weixin/user/search_report_by_number/1';
                }else{
                    window.location.href = SITE_URL + 'weixin/user/search_report_by_number';
                }
            },onClickCancel:function(e){
                var url = window.location.href;
                if(url.indexOf('/1') != -1 ){
                    window.location.href = SITE_URL + 'weixin/user/add_report/1';
                }else{
                    window.location.href = SITE_URL + 'weixin/user/add_report';
                }
            }
        });

        $scope.search_report_info = function (element,target) {
            $scope.localtionStr = JSON.parse(localStorage.getItem("reports"));
            console.log($scope.localtionStr);
            if(!$scope.userInfo.name || $scope.userInfo.name == ''){
                var popAlert=new Alert("请输入检测人姓名",{"title":'提示'});
                popAlert.show();
                return;
            }
            if(!$scope.userInfo.phone || $scope.userInfo.phone == ''){
                var popAlert=new Alert("请输入手机号",{"title":'提示'});
                popAlert.show();
                return;
            }
            if(!$scope.userInfo.verification_code || $scope.userInfo.verification_code == ''){
                var popAlert=new Alert("请输入手机验证码",{"title":'提示'});
                popAlert.show();
                return;
            }
            ajax.req('POST', 'index/get_report_by_condition', {
                phone : $scope.userInfo.phone,
                verification_code : $scope.userInfo.verification_code,
                identity_card : $scope.userInfo.card
            }).then(function(response){
                if (response.success){
                    angular.forEach(response.data, function(row, index){
                        row.update_time = row.update_time.substring(0, 16);
                    });
                    $scope.reports = response.data;

                    localStorage.setItem("reports", JSON.stringify($scope.reports));
                    //清空所有的key-value数据。
                    //localStorage.clear();

                    $('.search-report').hide();
                    var openType = target || "";
                    singlePage.open(element, openType);
                }else if(response.msg == '没有数据'){
                    popConfirm.show();
                    $('.alert-handler').find('a').eq(0).text('前往录入');
                    $('.alert-handler').find('a').eq(1).text('条码查询');
                }else{
                    var popAlert=new Alert(response.msg,{"title":'提示'});
                    popAlert.show();
                }

            });
        };
        //查看pdf
        $scope.localtionStr = [];
        $scope.watch_pdf = function (report) {
            // var myPDF = new PDFObject({ url: SITE_URL + 'source/uploads/report/dfee3345d0e94942eaf808cec7a793de.pdf' }).embed();
            window.location.href = SITE_URL + report.path;
            $scope.localtionStr = JSON.parse(localStorage.getItem("reports"));
            console.log($scope.localtionStr);
        };

        $scope.url = window.location.href;
        $scope.search_report_by_number = function () {
            var url = window.location.href;
            if(url.indexOf('/1') != -1 ){
                window.location.href=SITE_URL + 'weixin/user/search_report_by_number/1';
            }else{
                window.location.href=SITE_URL + 'weixin/user/search_report_by_number';
            }

        };
        $scope.back = function () {
            history.go(-1);
            $('.search-report').show();
        };

        $scope.backPdfList = function () {
            history.go(-1);
            $('.my-report').show();
            $('#myPDF').remove();
        }

    }]);