app.controller("postageCtrl",["$scope","_jiyin","dataToURL","$stateParams","$state",function(t,a,e,n,s){t.couponList={},t.inputPage=1,t.getData=function(){a.dataGet("admin/postage_admin/paginate/"+t.inputPage+"/10").then(function(a){t.totalPage=0==a.total_page?1:a.total_page,t.postageList=a.data})},t.getData(),t.addList=function(){t.title="增加邮费规则",a.modal({tempUrl:"/source/admin/tpl/modal/modal-postage.html",tempCtrl:"modalPostageCtrl",ok:t.add,size:"lg",params:{title:t.title,infoList:{},ael:"add"}})},t.add=function(n){a.dataPost("admin/postage_admin/add",e(n)).then(function(e){1==e.success?(a.msg("s",e.msg),t.getData()):a.msg("e",e.msg)})},t.editList=function(e){t.title="编辑邮费规则",a.modal({tempUrl:"/source/admin/tpl/modal/modal-postage.html",tempCtrl:"modalPostageCtrl",ok:t.edit,size:"lg",params:{title:t.title,infoList:e,ael:"edit"}})},t.edit=function(n){a.dataPost("admin/postage_admin/update",e(n)).then(function(e){1==e.success?(a.msg("s",e.msg),t.getData()):a.msg("e",e.msg)})},t.deleteData=function(n){confirm("确认删除这条邮费规则吗?")&&a.dataPost("admin/postage_admin/delete",e({id:n.id})).then(function(e){1==e.success?(a.msg("s","删除成功"),t.getData()):a.msg("e",e.msg)})},t.enableUse=function(n){confirm("确定要启用该邮费规则吗?")&&(n.status_id=1,a.dataPost("admin/postage_admin/update",e(n)).then(function(e){1==e.success?(a.msg("s",e.msg),t.getData()):a.msg("e",e.msg)}))},t.lookData=function(t){console.log(t),s.go("app.postageset",{id:t.id})},t.nextPage=function(){t.inputPage<t.totalPage?(t.inputPage++,t.getData()):a.msg("e","当前是最后一页")},t.previousPage=function(){t.inputPage>1?(t.inputPage--,t.getData()):a.msg("e","当前是第一页")},t.firstPage=function(){t.inputPage=1,t.getData()},t.lastPage=function(){t.inputPage=t.totalPage,t.getData()},t.selectPage=function(a){t.inputPage=a,t.getData()}}]);