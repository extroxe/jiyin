"use strict";app.controller("modalAgentChangePasswdCtrl",["$scope","$modalInstance","_jiyin","params",function(i,s,n,t){i.infoList=t.infoList,i.title=t.title,i.ael=t.ael,i.cancel=function(){s.dismiss("cancel")},i.ok=function(){return i.infoList.newPasswd?i.infoList.newPasswd!=i.infoList.rePasswd?void n.msg("e","密码不一致"):void s.close(i.infoList):void n.msg("e","密码不能为空")}}]);