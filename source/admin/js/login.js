angular.module("login",["ngAnimate","toaster"]).factory("dataToURL",function(){return function(n){var o="";for(var e in n)o+="&"+e+"="+n[e];return o.slice(1,o.length)}}).controller("LoginCtrl",["$scope","$http","$q","$timeout","dataToURL","toaster",function(n,o,e,t,r,a){n.user={},n.focusMe=function(){document.getElementById("userAccount").focus()},n.data=function(n,t){var r=e.defer();return o({method:"POST",contentType:"application/json",headers:{"Content-Type":"application/x-www-form-urlencoded;charset=utf-8;"},dataType:"json",data:t,url:n}).success(function(n){r.resolve(n)}).error(function(n){console.log(n.data),r.reject("Error!")}),r.promise},n.login=function(){var o=SITE_URL+"/admin/admin/login";n.data(o,r({username:n.user.account,password:n.user.password})).then(function(n){n.success?(window.location.href=SITE_URL+"admin/admin/index#/app",location.reload()):a.pop("error","信息提示",n.msg)})},$("body").keydown(function(){"13"==event.keyCode&&n.login()})}]);