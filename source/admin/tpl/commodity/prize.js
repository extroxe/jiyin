app.controller("prizeCtrl",["$scope","_jiyin","dataToURL",function(t,e,i){t.prizeList={},t.inputPage=1,t.totalPage=1,t.getData=function(){e.dataGet("admin/sweepstakes_commodity_admin/get_sweepstakes_commodity/"+t.inputPage+"/10").then(function(e){t.prizeList=e.success?e.data:[],t.totalPage=e.total_page})},t.getData(),t.getParty=function(){e.dataGet("admin/sweepstakes_admin/get_sweepstakes_info").then(function(e){t.partyList=e.success?e.data:[]})},t.getParty(),t.selectCommodity=function(){t.title="添加抽奖奖品",e.modal({tempUrl:"/source/admin/tpl/modal/modal-agentAddCommodity.html",tempCtrl:"agentAddCommodityCtrl",ok:t.selected,size:"lg",params:{title:t.title,ael:"add",select:"s"}})},t.selected=function(e){if(null==e[0].specification_center_name)var i="";else var i=e[0].specification_center_name;t.list.price=e[0].price,t.list.commodity_name=e[0].commodity_name+" "+i+" "+e[0].specification_name,t.list.commodity_id=e[0].commodity_id,t.list.commodity_specification_id=e[0].id},t.addInfo=function(){t.title="增加数据",t.add=!0,t.list={},$("#partyModal").modal("show")},t.editList=function(e){t.title="编辑数据",t.add=!1,t.list=e,$("#partyModal").modal("show")},t.ok=function(){return t.list.sweepstakes_id&&t.list.total_number?t.list.commodity_id||t.list.commodity_specification_id||t.list.point?t.list.commodity_id&&t.list.commodity_specification_id&&t.list.point?void e.msg("e","商品和积分不能同时设置"):void(1==t.add?e.dataPost("admin/sweepstakes_commodity_admin/add",i(t.list)).then(function(i){1==i.success?(e.msg("s","添加成功"),t.getData(),$("#partyModal").modal("hide")):e.msg("e",i.msg)}):0==t.add&&e.dataPost("admin/sweepstakes_commodity_admin/update",i(t.list)).then(function(i){1==i.success?(e.msg("s","修改成功"),t.getData(),$("#partyModal").modal("hide")):e.msg("e",i.msg)})):void e.msg("e","请设置商品或积分二选一"):void e.msg("e","带*号为必填，请先填写必填项")},t.deleteData=function(a){confirm("确认删除这条数据吗?")&&e.dataPost("admin/sweepstakes_commodity_admin/delete",i({id:a.id})).then(function(i){1==i.success?(e.msg("s","删除成功"),t.getData()):e.msg("e",i.msg)})},t.nextPage=function(){t.inputPage<t.totalPage?(t.inputPage++,t.getData()):e.msg("e","当前是最后一页")},t.previousPage=function(){t.inputPage>1?(t.inputPage--,t.getData()):e.msg("e","当前是第一页")},t.firstPage=function(){t.inputPage=1,t.getData()},t.lastPage=function(){t.inputPage=t.totalPage,t.getData()},t.selectPage=function(e){t.inputPage=e,t.getData()}}]);