app.controller("commonclassCtrl",["$scope","_jiyin","dataToURL","$stateParams","$state","$filter",function(t,e,i,a,n){t.commonclassList={},t.list={},t.reList={},t.inputPage=1,t.dataShow=!0,t.isPoint=!1,t.picList=[],t.keyword="",t.urlPC="",t.urlWC="",t.register_start_time="",t.register_end_time="",t.open=!1;var s="",o="";t.synchronize=function(){return s=t.register_start_time,o=t.register_end_time,""==s||""==o?void e.msg("e","请输入开始时间和结束时间"):s>o?void e.msg("e","开始时间不能大于结束时间"):(t.inputPage=1,void e.dataPost("admin/erp_admin/synchronize_commodity_from_erp/",i({start_time:s,end_time:o})).then(function(t){t.success?e.msg("s",t.msg):e.msg("e",t.msg)}))},t.getData=function(){e.dataPost("admin/commodity_admin/paginate/"+t.inputPage+"/10",i({keyword:t.keyword,agent_id:"admin",start_time:t.register_start_time,end_time:t.register_end_time})).then(function(i){i.success?(t.commonclassList=i.data,t.totalPage=i.total_page):(t.inputPage=1,t.dataShow=!1,t.totalPage=1,e.msg("e",i.msg))})},t.getData(),$("#search").keydown(function(e){13==e.keyCode&&t.getData()}),t.search=function(){t.getData()},t.specification=function(t){n.go("app.specification",{commodity_id:t.id,type:"com",commodity_type:t.type_id})},t.show=function(){t.title="添加商品",t.ael="add",t.list={},t.list.category_id="0",t.url=!1,t.getType(),t.get_agent_type(),t.getCate(),t.getLevel(),t.open=!0,t.$broadcast("open",{open:t.open}),$("#add").modal("show")},t.edit=function(e){t.title="编辑商品",t.ael="edit",t.url=!0,t.urlPC=SITE_URL+"commodity/index/"+e.id,t.urlWC=SITE_URL+"weixin/index/commodity_detail/"+e.id,t.list=e,t.getThumbnail(),t.getType(),t.getCate(),t.getLevel(),t.open=!0,t.$broadcast("open",{open:t.open}),$("#add").modal("show")},t.getThumbnail=function(){t.list.id&&e.dataPost("admin/commodity_admin/show_thumbnail",i({commodity_id:t.list.id})).then(function(e){t.picList=e.success?e.data:[]})},t.getType=function(){t.typeList=[],e.dataPost("admin/system_code_admin/get_by_type/commodity_type").then(function(e){e&&angular.forEach(e,function(e){"1"!=e.value&&0==t.url&&t.typeList.push(e),1==t.url&&t.typeList.push(e)})})},t.getLevel=function(){e.dataGet("admin/level_admin/get_level").then(function(e){t.levelList=e.success?e.data:[]})},t.get_agent_type=function(){e.dataGet("admin/user_admin/get_agents").then(function(e){t.agentList=e.success?e.data:[]})},t.getCate=function(){e.dataPost("admin/category_admin/get_categories").then(function(e){t.cateList=e.success?e.data:[]})},t.$on("attachment_ids",function(e,i){t.list.attachment_ids||(t.list.attachment_ids=[]),t.list.attachment_ids.push(i)}),t.$on("path",function(e,i){null==t.picList&&(t.picList=[]);var a=[];a.path=i,t.picList.push(a)}),t.removePic=function(a,n){confirm("确定删除该图片吗?")&&(a?e.dataPost("admin/commodity_admin/delete_thumbnail",i({id:a})).then(function(i){i.success?(t.picList.splice(n,1),t.getThumbnail()):e.msg("e",i.msg)}):t.picList.splice(n,1))},t.ok=function(){return t.list.name?t.list.number?t.list.category_id?t.list.type_id?t.list.detail?(t.list.attachment_ids&&(t.list.attachment_ids=t.list.attachment_ids.toString()),void("add"==t.ael?(t.list.is_point=0,e.dataPost("admin/commodity_admin/add",i(t.list)).then(function(i){1==i.success?(e.msg("s","添加成功"),t.getData(),$("#add").modal("hide")):e.msg("e",i.msg)})):e.dataPost("admin/commodity_admin/update",i(t.list)).then(function(i){1==i.success?(e.msg("s","修改成功"),t.getData(),$("#add").modal("hide")):e.msg("e",i.msg)}))):void e.msg("e","商品详情不能为空"):void e.msg("e","商品类型不能为空"):void e.msg("e","商品分类不能为空"):void e.msg("e","商品编号不能为空"):void e.msg("e","商品名称不能为空")},t.lookEva=function(t){n.go("app.evaluate",{commodity_id:t.id,type:"com"})},t.setPostage=function(t){n.go("app.setpostage",{category:0,category_id:0,commodity:t.name,commodity_id:t.id})},t.nextPage=function(){t.inputPage<t.totalPage?(t.inputPage++,t.getData()):e.msg("e","当前是最后一页")},t.previousPage=function(){t.inputPage>1?(t.inputPage--,t.getData()):e.msg("e","当前是第一页")},t.firstPage=function(){t.inputPage=1,t.getData()},t.lastPage=function(){t.inputPage=t.totalPage,t.getData()},t.selectPage=function(e){t.inputPage=e,t.getData()}}]);