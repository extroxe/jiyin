app.controller("logCtrl",["$scope","_jiyin","dataToURL",function(t,e,a){t.articleList={},t.inputPage=1,t.infoList={},t.pageSize=10,t.keyword="",t.state=0,t.checkState=["全部"],t.register_start_time="",t.register_end_time="",t.getData=function(){e.dataPost("admin/article_admin/paginate_log/",a({keyword:t.keyword,interface_name:t.state,start_create_time:t.register_start_time,end_create_time:t.register_end_time,page:t.inputPage,page_size:t.pageSize})).then(function(e){t.totalPage=e.total_page===!1?1:e.total_page,t.articleList=e.data})},t.getData(),t.reset=function(){t.keyword="",t.state=0,t.checkState=["全部"],t.register_start_time="",t.register_end_time="",t.getData()},e.dataGet("admin/agent_admin/get_all_agent_code").then(function(e){e.success&&(t.status=e.data)}),t.stateFlagSub=function(e){angular.forEach(t.status,function(a,i){i==e&&(t.state=a.value,t.checkState[0]=a.name)})},t.search=function(){t.inputPage=1,t.getData()},$("#search").keydown(function(e){13==e.keyCode&&t.search()}),t.watch=function(e){t.title="查询",$("#article").modal("show"),t.infoList=e},t.cancel=function(){$("#article").modal("hide")},t.nextPage=function(){t.inputPage<t.totalPage?(t.inputPage++,t.getData()):e.msg("e","当前是最后一页")},t.previousPage=function(){t.inputPage>1?(t.inputPage--,t.getData()):e.msg("e","当前是第一页")},t.firstPage=function(){t.inputPage=1,t.getData()},t.lastPage=function(){t.inputPage=t.totalPage,t.getData()},t.selectPage=function(e){t.inputPage=e,t.getData()}}]);