"use strict";app.controller("modalReplyContentCtrl",["$scope","$modalInstance","_jiyin","params",function(i,n,t,o){i.infoList={},i.infoList.id=o.id,i.title=o.title,i.ael=o.ael,i.cancel=function(){n.dismiss("cancel")},i.ok=function(){return i.infoList.replyContent?void n.close(i.infoList):void t.msg("e","评论不能为空")}}]);