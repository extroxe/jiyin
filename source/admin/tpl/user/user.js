app.controller("userCtrl",["$scope","$rootScope","_jiyin","dataToURL",function(s,n,t,i){s.infoList={},s.passList={},s.getUserInfo=function(){t.dataPost("admin/admin/get_userinfo").then(function(t){1==t.success&&(s.infoList=t.data,n.currentUser=t.data)})},s.getUserInfo(),s.saveSetting=function(){s.infoList.birthday=$(".user-birthday").val(),t.dataPost("admin/admin/update",i({name:s.infoList.name,gender:s.infoList.gender,phone:s.infoList.phone,email:s.infoList.email,birthday:s.infoList.birthday})).then(function(n){1==n.success?(t.msg("s","个人设置保存成功"),s.getUserInfo()):t.msg("e",n.error)})},s.savePassword=function(){t.dataPost("admin/admin/change_password",i(s.passList)).then(function(n){1==n.success?(t.msg("s","密码设置保存成功"),s.passList={}):t.msg("e",n.error)})}}]);