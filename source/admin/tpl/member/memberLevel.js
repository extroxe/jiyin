app.controller("levelCtrl",["$scope","_jiyin","dataToURL",function(t,e,a){t.levelList={},t.inputPage=1,t.showEdit=!0,t.getData=function(){e.dataGet("admin/level_admin/get_level").then(function(e){t.levelList=e.data,t.totalPage=e.total_page})},t.getData(),t.changeSort=function(n,i,l){var s;s=n[i],n[i]=n[i+l],n[i+l]=s;var g=[],o=[];return angular.forEach(t.levelList,function(t){g[g.length]=t.id,o[o.length]=t.rank}),g=g.toString(),e.dataPost("admin/level_admin/adjust_rank",a({id:g})).then(function(a){1==a.success?(e.msg("s","调整成功"),t.getData()):e.msg("e",a.msg)}),!1},t.addList=function(){t.title="增加数据",e.modal({tempUrl:"/source/admin/tpl/modal/modal-level.html",tempCtrl:"modalLevelCtrl",ok:t.add,size:"lg",params:{title:t.title,infoList:{},ael:"add"}})},t.add=function(n){e.dataPost("admin/level_admin/add",a(n)).then(function(a){1==a.success?(e.msg("s",a.msg),t.getData()):e.msg("e",a.msg)})},t.editList=function(a){t.title="编辑数据",e.modal({tempUrl:"/source/admin/tpl/modal/modal-level.html",tempCtrl:"modalLevelCtrl",ok:t.edit,size:"lg",params:{title:t.title,infoList:a,ael:"edit"}})},t.edit=function(n){e.dataPost("admin/level_admin/update",a(n)).then(function(a){1==a.success?(e.msg("s",a.msg),t.getData()):e.msg("e",a.msg)})},t.deleteData=function(n){confirm("确认删除这条数据吗?")&&e.dataPost("admin/level_admin/del_level",a({id:n.id})).then(function(a){1==a.success?(e.msg("s","删除成功"),t.getData()):e.msg("e",a.msg)})},t.nextPage=function(){t.inputPage<t.totalPage?(t.inputPage++,t.getData()):e.msg("e","当前是最后一页")},t.previousPage=function(){t.inputPage>1?(t.inputPage--,t.getData()):e.msg("e","当前是第一页")},t.firstPage=function(){t.inputPage=1,t.getData()},t.lastPage=function(){t.inputPage=t.totalPage,t.getData()},t.selectPage=function(e){t.inputPage=e,t.getData()}}]);