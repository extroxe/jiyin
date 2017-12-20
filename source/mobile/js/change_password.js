/**
 * Created by sailwish009 on 2017/1/3.
 */
angular.module('app')
    .controller('changePassword', ['$scope', 'ajax', '$interval', function ($scope, ajax, $interval) {
        $scope.userInfo = [];
        $scope.code = '';
        $scope.flag = false;
        $scope.send_code_ope = '发送验证码';
        $scope.bind_Phone = function () {
            ajax.req('GET', 'user/get_phone_by_user_id')
                .then(function(response){
                    if (response.success){
                        $scope.bindPhone = response.data;
                        }
                    })

        };
        $scope.bind_Phone();
        var prompt = new Prompt();
        /**
         * 发送验证码
         */
        $scope.send_checkcode = function (userInfo) {
            userInfo.verification_code = '';
            ajax.req('POST', 'user/check_user_phone_valid', {phone : userInfo.phone})
                .then(function(response){
                    if (response.success){
                        ajax.req('POST', 'verification_code/get_verified_phone_code',{
                            phone:userInfo.phone
                        }).then(function (data) {
                            if(data.success){
                                $scope.get_verification_code_timer(60);
                            }else{
                                prompt.setText(data.msg);
                                prompt.show();
                            }
                        });
                    }else{
                        prompt.setText(response.msg);
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

        /**
         * 确认修改
         */
        $scope.save = function(userInfo){
            if(userInfo.phone == undefined || userInfo.phone == ''){
                prompt.setText("请填写手机号");
                prompt.show();
            }else if (userInfo.verification_code == undefined || userInfo.verification_code == ''){
                prompt.setText("请填写验证码");
                prompt.show();
            }else if ($scope.new_password == undefined || $scope.new_password == ''){
                prompt.setText("请填写新密码");
                prompt.show();
            }else if ($scope.confirm_password == undefined || $scope.confirm_password == ''){
                prompt.setText("请确认新密码");
                prompt.show();
            }else if ($scope.new_password != $scope.confirm_password){
                prompt.setText("两次密码不一致");
                prompt.show();
            }else{
                ajax.req('POST', 'user/modify_psd_by_verification', {
                    phone : userInfo.phone,
                    code : userInfo.verification_code,
                    new_password : $scope.new_password,
                    confirm_password : $scope.confirm_password
                }).then(function(response){
                    if (response.success){
                        prompt.setText("密码修改成功");
                        prompt.show();
                        $scope.back();
                    }else{
                        prompt.setText(response.msg);
                        prompt.show();
                    }
                });
            }
        }
        
    }]);