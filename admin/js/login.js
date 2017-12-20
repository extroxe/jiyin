/**
 * Created by sailwish001 on 2016/11/2.
 */
angular.module('login', ['ngAnimate','toaster'])
    .factory('dataToURL', function () {
        return function (data) {
            var str = '';
            for( var key in data){
                str += '&' + key + '='+ data[key];
            }
            return str.slice(1,str.length);
        };
    })
    .controller('LoginCtrl', ['$scope', '$http', '$q', '$timeout', 'dataToURL', 'toaster',
        function($scope, $http, $q, $timeout, dataToURL, toaster) {
        $scope.user = {};
        $scope.focusMe = function() {
            document.getElementById('userAccount').focus();
        };
            $scope.data = function(url, params) {
                var deferred = $q.defer();
                $http({
                    method : 'POST',
                    contentType : 'application/json',
                    headers : {'Content-Type':'application/x-www-form-urlencoded;charset=utf-8;'},
                    dataType : 'json',
                    data : params,
                    url : url
                }).success(function(result) {
                    deferred.resolve(result);
                }).error(function(result) {
                    console.log(result.data);
                    deferred.reject("Error!");
                });
                return deferred.promise;
            };
            $scope.login = function() {
                var url = SITE_URL + '/admin/admin/login';
                $scope.data(url, dataToURL({
                    "username" : $scope.user.account,
                    "password" : $scope.user.password
                })).then(function(result) {
                    if (result.success) {
                        window.location.href = SITE_URL + 'admin/admin/index#/app';
                        location.reload();
                    }else {
                        toaster.pop('error', '信息提示', result.msg);
                    }

                });
            };
            $("body").keydown(function() {
                if (event.keyCode == "13") {//keyCode=13是回车键
                    $scope.login();
                }
            });
            /*$scope.keydown = function($event) {
                var from = $event.srcElement;
                var fromId = from.id;
                var key = $event.which;
                if (key == 13) {
                    if (!from.value) {
                        return;
                    }

                    if ('userAccount' === fromId) {
                        var pwd = document.getElementById('userPassword');
                        if (!pwd.value || '' == pwd.value) {
                            $timeout(function() {
                                pwd.focus();
                            });
                        } else {
                            $scope.login();
                        }
                    } else if ('userPassword' === fromId) {
                        var account = document.getElementById('userAccount');
                        if (!account.value || '' == account.value) {
                            $timeout(function() {
                                account.focus();
                            });
                        } else {
                            $scope.login();
                        }
                    }
                }
            };*/
    }]);