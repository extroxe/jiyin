app.controller("recommendCtrl",["$scope","_jiyin","dataToURL",function(t,e,a){function n(){var t=new Date,e="-",a=":",n=t.getMonth()+1,o=t.getDate();n>=1&&9>=n&&(n="0"+n),o>=0&&9>=o&&(o="0"+o);var i=t.getFullYear()+e+n+e+o+" "+t.getHours()+a+t.getMinutes()+a+t.getSeconds();return i}t.recommendList={},t.inputPage=1,t.keywords="",t.getData=function(){e.dataPost("admin/commodity_admin/recommend_paginate/"+t.inputPage+"/10",a({is_point:0,type_id:1,agent_id:"admin",keywords:t.keywords})).then(function(a){a.success?(t.recommendList=a.data,t.totalPage=a.total_page):(t.recommendList=[],t.totalPage=1,e.msg("e",a.msg))})},t.getData(),$("#search").keydown(function(e){13==e.keyCode&&t.getData()}),t.search=function(){t.getData()},t.date=n(),t.addList=function(){t.title="添加热卖商品",e.modal({tempUrl:"/source/admin/tpl/modal/modal-recommond.html",tempCtrl:"modalRecomCtrl",ok:t.add,size:"lg",params:{title:t.title,infoList:{},ael:"add",isPoint:0}})},t.add=function(n){e.dataPost("admin/commodity_admin/add_recommend",a(n)).then(function(a){1==a.success?(e.msg("s","添加成功"),t.getData()):e.msg("e",a.msg)})},t.editList=function(a){t.title="编辑热卖商品",e.modal({tempUrl:"/source/admin/tpl/modal/modal-recommond.html",tempCtrl:"modalRecomCtrl",ok:t.edit,size:"lg",params:{title:t.title,infoList:a,ael:"edit"}})},t.edit=function(n){e.dataPost("admin/commodity_admin/update_recommend",a(n)).then(function(a){1==a.success?(e.msg("s","修改成功"),t.getData()):e.msg("e",a.msg)})},t.deleteData=function(n){confirm("确认删除这条数据吗?")&&e.dataPost("admin/commodity_admin/delete_recommend",a({id:n.id})).then(function(a){1==a.success?(e.msg("s","删除成功"),t.getData()):e.msg("e",a.msg)})},t.nextPage=function(){t.inputPage<t.totalPage?(t.inputPage++,t.getData()):e.msg("e","当前是最后一页")},t.previousPage=function(){t.inputPage>1?(t.inputPage--,t.getData()):e.msg("e","当前是第一页")},t.firstPage=function(){t.inputPage=1,t.getData()},t.lastPage=function(){t.inputPage=t.totalPage,t.getData()},t.selectPage=function(e){t.inputPage=e,t.getData()}}]);