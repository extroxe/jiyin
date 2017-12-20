/**
 * Created by sailwish009 on 2016/12/29.
 */

angular.module('app')
    .controller("signUp", ['$scope','$timeout','$interval', 'ajax', function ($scope, $timeout,$interval, ajax){

        $scope.userInfo = [];
        $scope.code = '';
        $scope.flag = false;
        $scope.send_code_ope = '发送验证码';
        var popToast;

        $scope.send_checkcode = function (userInfo) {
            userInfo.verification_code = '';
            ajax.req('POST', 'verification_code/get_register_code',{phone:userInfo.phone})
                .then(function (data) {

                    userInfo.verification_code = '';
                    if(data.success){
                        $scope.code = data;
                        get_verification_code_timer(120);
                    }else{
                        popToast=new Toast(data.msg);
                        // alert(data.msg)
                    }
                });
        };

        $scope.submitForm = function (userInfo) {
            if($scope.code.success && userInfo.username != 'undefined' && userInfo.password != 'undefined' && userInfo.phone != 'undefined'){
                ajax.req('POST', 'user/register', {
                    username : userInfo.username,
                    password : userInfo.password,
                    password_confirm : userInfo.password_confirm,
                    phone : userInfo.phone,
                    verification_code : userInfo.verification_code
                })
                    .then(function (data) {
                        if(data.success){
                            if(!popToast || popToast&&!popToast.toastBox){
                                popToast=new Toast("注册成功",{
                                    "onHid":function(e){
                                        e.destroy();
                                    }
                                });
                            }
                            popToast.show();
                            window.location.href = SITE_URL + 'weixin/index/sign_in';
                        }else{
                            if(data.error){
                                popToast=new Toast(data.error);
                                popToast.show();
                            }else{
                                popToast=new Toast(data.msg);
                                popToast.show();
                            }
                        }
                    })
            }else{
                popToast=new Toast('请填写完整信息');
                popToast.show();
            }
        };

        function get_verification_code_timer(second) {

            if (parseInt(second) < 1) {
                return;
            }

            var temp = parseInt(second);
            $scope.flag = true;
            
            $scope.send_code_ope = temp + 's后发送验证';
            
            var timer = $interval(function () {
                temp--;
                if (temp < 1) {
                    $scope.send_code_ope = "获取验证码";
                    $scope.flag = false;
                    $interval.cancel(timer);
                }else {
                    $scope.flag = true;
                    $scope.send_code_ope = temp + 's后发送验证';
                }
            }, 1000);

        };

        $scope.back = function (){
            history.go(-1);
        }

    }]);

