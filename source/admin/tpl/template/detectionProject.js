app.controller("detectionProjectCtrl",["$scope","_jiyin","dataToURL",function(t,e,a){t.detectionProjectList={},t.inputPage=1,t.res="",t.getData=function(){e.dataPost("admin/detection_project_admin/get_project_by_page",a({page:t.inputPage,page_size:10,template_id:t.res})).then(function(e){t.detectionProjectList=e.data,t.totalPage=e.total_page})},t.getData(),t.addList=function(){t.title="增加数据",e.modal({tempUrl:"/source/admin/tpl/modal/modal-detectionProject.html",tempCtrl:"modalDeprojectCtrl",ok:t.add,size:"lg",params:{title:t.title,infoList:{},ael:"add"}})},t.add=function(n){e.dataPost("admin/detection_project_admin/add_project",a(n)).then(function(a){1==a.success?(e.msg("s","添加成功"),t.getData()):e.msg("e","添加失败")})},t.getCommos=function(){e.dataPost("admin/detection_template_admin/get_detection_template").then(function(e){t.commoLists=e.data})},t.getCommos(),t.searchByTemplate=function(e){t.inputPage=1,t.res=e,t.getData()},t.editList=function(a){t.title="编辑数据",e.modal({tempUrl:"/source/admin/tpl/modal/modal-detectionProject.html",tempCtrl:"modalDeprojectCtrl",ok:t.edit,size:"lg",params:{title:t.title,infoList:a,ael:"edit"}})},t.edit=function(n){console.log(n),e.dataPost("admin/detection_project_admin/update_project",a(n)).then(function(a){1==a.success?(e.msg("s","修改成功"),t.getData()):e.msg("e","修改失败")})},t.deleteData=function(n){confirm("确认删除这条数据吗?")&&e.dataPost("admin/detection_project_admin/delete_project",a({id:n.id})).then(function(a){1==a.success?(e.msg("s","删除成功"),t.getData()):e.msg("e",a.msg)})},t.nextPage=function(){t.inputPage<t.totalPage?(t.inputPage++,t.getData()):e.msg("e","当前是最后一页")},t.previousPage=function(){t.inputPage>1?(t.inputPage--,t.getData()):e.msg("e","当前是第一页")},t.firstPage=function(){t.inputPage=1,t.getData()},t.lastPage=function(){t.inputPage=t.totalPage,t.getData()},t.selectPage=function(e){t.inputPage=e,t.getData()}}]),app.filter("hidden",function(){return function(t){var e=t.length;return e>15&&(t=t.substring(0,15)+"..."),t}});