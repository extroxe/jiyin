app.controller("indianaCtrl",["$scope","_jiyin","dataToURL",function(t,n,e){t.content="",t.getSetting=function(){n.dataPost("admin/system_setting_admin/get_indiana_rules").then(function(n){t.content=n.data.value})},t.getSetting(),t.save=function(){return t.content?void n.dataPost("admin/system_setting_admin/indiana_rules",e({content:t.content})).then(function(e){1==e.success&&(n.msg("s","设置成功"),t.getSetting())}):void n.msg("e","请先设置积分夺宝规则")}}]);