"use strict";app.controller("modalAgentCtrl",["$rootScope","$scope","$modalInstance","_jiyin","params",function(o,i,t,n,c){i.is_admin=o.is_admin,i.infoList=c.infoList,i.title=c.title,i.ael=c.ael,null==i.infoList.color&&(i.infoList.color="#fff"),setTimeout(function(){$("#color").val(i.infoList.color)},200),i.infoList.is_show&&(i.showFlag=i.infoList.is_show=!0),i.$on("attachment_id",function(o,t){i.infoList.attachment_id=t}),i.$watch("ael",function(o){o&&setTimeout(function(){$("#colorpicker").farbtastic("#color"),$("#color").focus(function(){$("#colorpicker").show()}),$("#color").blur(function(){$("#colorpicker").hide()})},1e3)}),i.cancel=function(){t.dismiss("cancel")},i.ok=function(){i.infoList.color=$("#color").val(),i.infoList.is_show=1;var o=/^\w+@\w+\..+$/;return i.infoList.email&&0==o.test(i.infoList.email)?void n.msg("e","邮箱不符合规则"):void t.close(i.infoList)}}]);