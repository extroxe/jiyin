app.controller("alipayCtrl",["$scope","_jiyin","dataToURL",function(t,a,e){t.alipayList={},t.inputPage=1,t.getData=function(){a.dataPost("admin/refund_admin/paginate",e({payment_id:2,page:t.inputPage,page_size:10})).then(function(a){t.alipayList=a.data,t.totalPage=a.total_page})},t.getData(),t.look=function(e){t.title="查看数据",a.modal({tempUrl:"/source/admin/tpl/modal/modal-refund.html",tempCtrl:"modalRefundCtrl",ok:t.edit,size:"lg",params:{title:t.title,infoList:e,ael:"edit"}})},t.agree=function(n){confirm("确认同意这条退款吗?")&&a.dataPost("admin/refund_admin/audit_refund",e({id:n.id,audit_result:!0})).then(function(){a.msg("s","操作成功"),t.getData()})},t.refuse=function(n){confirm("确认拒绝这条退款吗?")&&a.dataPost("admin/refund_admin/audit_refund",e({id:n.id,audit_result:!1})).then(function(){a.msg("s","操作成功"),t.getData()})},t.nextPage=function(){t.inputPage<t.totalPage?(t.inputPage++,t.getData()):a.msg("e","当前是最后一页")},t.previousPage=function(){t.inputPage>1?(t.inputPage--,t.getData()):a.msg("e","当前是第一页")},t.firstPage=function(){t.inputPage=1,t.getData()},t.lastPage=function(){t.inputPage=t.totalPage,t.getData()},t.selectPage=function(a){t.inputPage=a,t.getData()}}]);