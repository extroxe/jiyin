app.controller("indianaCtrl",["$scope","_jiyin","dataToURL","$stateParams","$state",function(t,a,i,e,n){t.indianaList={},t.inputPage=1,t.getData=function(){a.dataGet("admin/integral_indiana_admin/get_all_indiana_info/"+t.inputPage+"/10").then(function(i){if(i.success){for(var e=0;e<i.data.length;e++)i.data[e].commodity_name=null!=i.data[e].commodity_center_name?i.data[e].commodity_name+" "+i.data[e].commodity_center_name:i.data[e].commodity_name+" "+i.data[e].commodity_specification_name;t.indianaList=i.data}else t.indianaList=[],a.msg("e",i.msg);t.totalPage=i.total_page})},t.getData(),t.selectCommodity=function(){t.title="添加积分夺宝商品",a.modal({tempUrl:"/source/admin/tpl/modal/modal-agentAddCommodity.html",tempCtrl:"agentAddCommodityCtrl",ok:t.selected,size:"lg",params:{title:t.title,ael:"add",select:"s"}})},t.selected=function(a){if(null==a[0].commodity_center_name)var i="";else var i=a[0].commodity_center_name;t.list.price=a[0].price,t.list.commodity_name=a[0].commodity_name+" "+i+" "+a[0].package_type_name,t.list.commodity_id=a[0].commodity_id,t.list.commodity_specification_id=a[0].id},t.addInfo=function(){t.title="添加积分夺宝商品",t.add=!0,t.list={},$("#partyModal").modal("show")},t.editList=function(a){t.title="编辑积分夺宝商品",t.add=!1,t.list=a,$("#partyModal").modal("show")},t.ok=function(){return t.list.commodity_id&&t.list.total_points&&t.list.amount_bet?void(1==t.add?a.dataPost("admin/integral_indiana_admin/add",i(t.list)).then(function(i){1==i.success?(a.msg("s","添加成功"),t.getData(),$("#partyModal").modal("hide")):a.msg("e",i.msg)}):0==t.add&&a.dataPost("admin/integral_indiana_admin/update",i(t.list)).then(function(i){1==i.success?(a.msg("s","修改成功"),t.getData(),$("#partyModal").modal("hide")):a.msg("e",i.msg)})):void a.msg("e","带*号为必填，请先填写必填项")},t.deleteData=function(e){confirm("确认删除这条数据吗?")&&a.dataPost("admin/integral_indiana_admin/delete",i({id:e.id})).then(function(i){1==i.success?(a.msg("s","删除成功"),t.getData()):a.msg("e",i.msg)})},t.look=function(t){n.go("app.indianaUser",{id:t.id})},t.nextPage=function(){t.inputPage<t.totalPage?(t.inputPage++,t.getData()):a.msg("e","当前是最后一页")},t.previousPage=function(){t.inputPage>1?(t.inputPage--,t.getData()):a.msg("e","当前是第一页")},t.firstPage=function(){t.inputPage=1,t.getData()},t.lastPage=function(){t.inputPage=t.totalPage,t.getData()},t.selectPage=function(a){t.inputPage=a,t.getData()}}]);