app.controller("modalCouponCtrl",["$scope","$modalInstance","_jiyin","params","FileUploader","dataToURL","$filter",function(i,t,e,n,s,o,f){i.infoList=n.infoList,i.title=n.title,i.ael=n.ael,i.infoList.privilege=parseInt(i.infoList.privilege),i.getStatus=function(){e.dataPost("admin/system_code_admin/get_by_type/discount_coupon_status").then(function(t){i.statusList=t})},i.getStatus(),i.$watch("infoList.end_time",function(t){if(t){if(!i.infoList.start_time)return i.infoList.end_time="",void e.msg("e","请先选择开始时间");var n=new Date(t),s=new Date(i.infoList.start_time);n.getTime()>s.getTime()?i.infoList.useful_life=Math.ceil((n.getTime()-s.getTime())/864e5):e.msg("e","结束时间不能小于开始时间")}}),i.$watch("infoList.useful_life",function(t){if(t){if(!i.infoList.start_time)return i.infoList.useful_life="",void e.msg("e","请先选择开始时间");var n=864e5*i.infoList.useful_life;if(0>=n)return void e.msg("e","结束时间不能小于开始时间");var s=new Date(i.infoList.start_time),o=new Date(s.getTime()+n);i.infoList.end_time=f("formatDate")(o,"yyyy-MM-dd h:m:s")}}),i.cancel=function(){t.dismiss("cancel")},i.ok=function(){return i.infoList.name?i.infoList.condition?i.infoList.privilege?i.infoList.useful_life||i.infoList.start_time&&i.infoList.end_time?i.infoList.status_id?void t.close(i.infoList):void e.msg("e","状态不能为空"):void e.msg("e","生效起止时间有效期不能同时为空"):void e.msg("e","减免金额不能为空"):void e.msg("e","满足条件不能为空"):void e.msg("e","名称不能为空")}}]);