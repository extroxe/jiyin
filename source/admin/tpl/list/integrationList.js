app.directive("autoFocus",function(){return function(e,t){t[0].focus()}}),app.controller("integraCtrl",["$scope","_jiyin","dataToURL","$stateParams","$state",function(e,t,i,a,d){e.integraList={},e.inputPage=1,e.keyword="",e.status=[],e.state="0",e.is_agents=["全部","是","否"],e.is_agent="全部",e.select_all=!1,e.select_one=!1,e.checkedArray=[],e.pageSize={},e.pageSize.page="10",e.pageSizes=["10","50","100","200"],e.search=function(){t.dataPost("admin/order_admin/paginate/"+e.inputPage+"/"+e.pageSize.page,i({is_point:1,keyword:e.keyword,start_create_time:e.register_start_time,end_create_time:e.register_end_time,order_status:e.state,is_agent:e.is_agent_id})).then(function(i){if(i.success){if(e.integraList=i.data,e.tempReportList=[],e.integraList)for(var a=0;a<e.integraList.length;a++)e.integraList[a].sub_order.length>2?(e.tempReportList[a]=e.integraList[a].sub_order.slice(2,e.integraList[a].sub_order.length),e.integraList[a].sub_order=e.integraList[a].sub_order.slice(0,2)):e.tempReportList[a]=[];e.totalPage=i.total_page;var d=0;angular.forEach(e.orderList,function(t){return-1!=e.checkedArray.indexOf(t.id)?(t.checked=!0,void d++):void(t.checked=!1)}),e.select_all=d===e.orderList.length}else e.select_all=!1,e.integraList=[],t.msg("e","未查询到相关订单！")})},e.select_all=!1,e.selectAll=function(){if(e.select_all=!e.select_all,e.select_all)angular.forEach(e.orderList,function(t){e.checkedArray.indexOf(t.id)<0&&e.checkedArray.push(t.id),t.checked=!0});else{for(var t=0;t<e.orderList.length;t++)e.checkedArray.indexOf(e.orderList[t].id)>=0&&(e.checkedArray.splice(e.checkedArray.indexOf(e.orderList[t].id),1),t--);angular.forEach(e.orderList,function(e){e.checked=!1})}console.log(e.select_all),console.log(e.checkedArray)},e.selectOne=function(t){angular.forEach(e.integraList,function(i){var a=e.checkedArray.indexOf(t);-1===a&&i.checked&&t==i.id?e.checkedArray.push(i.id):-1===a||i.checked||t!=i.id||e.checkedArray.splice(a,1)}),e.select_all=e.integraList.length===e.checkedArray.length,console.log(e.checkedArray),console.log(e.select_all)},e.enterEvent=function(t){var i=window.event?t.keyCode:t.which;13==i&&e.search()},e.getOrderDetailModal=function(t){e.notPaidValue=0,e.paidValue=0,e.deliveredValue=0,e.sentbackValue=0,e.assayingValue=0,$("#orderDetail").modal("show"),e.orderDetail=angular.copy(t),"10"==t.status_id?($("div.not_paid").find(".node").addClass("active").siblings("div").find(".node").removeClass("active"),$("div.not_paid").siblings("div").find(".node").removeClass("active"),e.notPaidValue=50):"20"==t.status_id?($("div.paid").find(".node").addClass("active").siblings("div").find(".node").removeClass("active").prevAll(".node").addClass("active"),$("div.paid").siblings("div").find(".node").removeClass("active"),$("div.paid").prevAll("div").find(".node").addClass("active"),e.notPaidValue=100,e.paidValue=50):"30"==t.status_id?($("div.delivered").find(".node").addClass("active").siblings("div").find(".node").removeClass("active").prevAll(".node").addClass("active"),$("div.delivered").siblings("div").find(".node").removeClass("active"),$("div.delivered").prevAll("div").find(".node").addClass("active"),e.notPaidValue=100,e.paidValue=100,e.deliveredValue=50):"40"==t.status_id?($("div.sentback").find(".node").addClass("active").siblings("div").find(".node").removeClass("active").prevAll(".node").addClass("active"),$("div.sentback").siblings("div").find(".node").removeClass("active"),$("div.sentback").prevAll("div").find(".node").addClass("active"),e.notPaidValue=100,e.paidValue=100,e.deliveredValue=100,e.sentbackValue=50):"50"==t.status_id?($("div.assaying").find(".node").eq(0).addClass("active").siblings("div").find(".node").removeClass("active").prevAll(".node").addClass("active"),$("div.assaying").siblings("div").find(".node").removeClass("active"),$("div.assaying").prevAll("div").find(".node").addClass("active"),e.notPaidValue=100,e.paidValue=100,e.deliveredValue=100,e.sentbackValue=100,e.assayingValue=50):($("div.order-progress").find(".node").addClass("active"),e.notPaidValue=100,e.paidValue=100,e.deliveredValue=100,e.sentbackValue=100,e.assayingValue=100)},e.modifyAmountModal=function(){$("#modifyAmount").modal("show")},e.cancelOrderModal=function(t){e.cancelOrderDetail.id=t,$("#cancelOrder").modal("show")},e.cancel=function(e){$(e).modal("hide")},e.Jprintf=function(){$("#closeOrderDetail").hide(),$("#printBtn").hide(),$("#order_footer").hide(),$("#orderDetail").jqprint({importCSS:!0}),setTimeout(function(){$("#closeOrderDetail").show(),$("#printBtn").show(),$("#order_footer").show()},1e3)},e.cancelOrderDetail={},e.cancelOrder=function(){return e.cancelOrderDetail.status_id="100",e.cancelOrderDetail.id?e.cancelOrderDetail.reason?void t.dataPost("admin/order_admin/update",i(e.cancelOrderDetail)).then(function(e){e.success?($("#cancelOrder").modal("hide"),t.msg("s",e.msg)):t.msg("e",e.msg)}):void t.msg("e","请填写取消订单原因"):void t.msg("e","请选择要取消的订单")},t.dataGet("admin/order_admin/get_all_order_status").then(function(t){t.success&&(e.status=t.data)}),e.stateIsAgent=function(t){angular.forEach(e.is_agents,function(i,a){t==a&&(e.is_agent=i,e.is_agent_id=t)})},e.download=function(){if(e.checkedArray.length<=0)return void t.msg("e","请选择需要导出订单");var i="?";0!=e.checkedArray.length&&(i+="&order_id="+e.checkedArray.join("_")),i+="&is_online=1",window.open(SITE_URL+"admin/order_admin/download_order"+i)},e.getData=function(){t.dataPost("admin/order_admin/paginate/"+e.inputPage+"/10",i({is_point:1,keyword:e.keyword,start_create_time:e.register_start_time,end_create_time:e.register_end_time,order_status:e.state,is_agent:e.is_agent_id})).then(function(t){if(e.integraList=t.data,e.tempReportList=[],e.integraList)for(var i=0;i<e.integraList.length;i++)e.integraList[i].sub_order.length>2?(e.tempReportList[i]=e.integraList[i].sub_order.slice(2,e.integraList[i].sub_order.length),e.integraList[i].sub_order=e.integraList[i].sub_order.slice(0,2)):e.tempReportList[i]=[];e.totalPage=t.total_page;var a=0;angular.forEach(e.orderList,function(t){return-1!=e.checkedArray.indexOf(t.id)?(t.checked=!0,void a++):void(t.checked=!1)}),e.select_all=a===e.orderList.length})},e.getData(),e.push_report=function(t){for(var i=0;i<e.tempReportList[t].length;i++)e.orderList[t].sub_order.push(e.tempReportList[t][i])},e.shift_report=function(t){for(var i=0;i<e.tempReportList[t].length;i++)e.orderList[t].sub_order=e.orderList[t].sub_order.slice(0,2)},e.searchOrderByStatus=function(t){e.state=t,e.search()},$(document).on("click","#operation li",function(){$(this).addClass("active").siblings().removeClass("active")}),e.editList=function(i){e.title="编辑数据",t.modal({tempUrl:"/source/admin/tpl/modal/modal-orderList.html",tempCtrl:"modalOrderCtrl",ok:e.edit,size:"lg",params:{title:e.title,infoList:i,ael:"edit",isPoint:!0}})},e.edit=function(a){t.dataPost("admin/order_admin/update",i(a)).then(function(i){1==i.success?(t.msg("s",i.msg),e.getData()):t.msg("e",i.msg)})},e.lookSub=function(e){d.go("app.subOrderList",{id:e.id,type:"int"})},e.exinfo=function(i){t.dataGet("admin/order_admin/show_express_info_by_order_id/"+i.id).then(function(i){1==i.success?($("#list").modal("show"),e.exinfo=i.data.Traces):t.msg("e",i.msg)})},e.deleteData=function(a){confirm("确认删除这条数据吗?")&&t.data("",i({id:a.id})).then(function(){t.msg("s","删除成功"),e.getData()})},e.nextPage=function(){e.inputPage<e.totalPage?(e.inputPage++,e.getData()):t.msg("e","当前是最后一页")},e.previousPage=function(){e.inputPage>1?(e.inputPage--,e.getData()):t.msg("e","当前是第一页")},e.firstPage=function(){e.inputPage=1,e.getData()},e.lastPage=function(){e.inputPage=e.totalPage,e.getData()},e.selectPage=function(t){e.inputPage=t,e.getData()}}]);