app.directive("autoFocus",function(){return function(t,a){a[0].focus()}}),app.controller("sampleMagnet2Ctrl",["$rootScope","$scope","_jiyin","dataToURL",function(t,a,e,n){function o(t,o){e.dataPost("admin/report_admin/get_report_by_number",n({number:t})).then(function(t){t.success?(a.dataList.push(t.data),0==t.data.report_status?a.not_fill_count++:a.fill_count++):e.msg("e",t.msg),o.val(""),$(".not-fill").text(a.not_fill_count),$(".fill").text(a.fill_count);var n=$(document).height()-$(window).height();$(document).scrollTop(n)})}a.is_admin=t.is_admin,a.inputPage=1,a.pageSize=10,a.keyword="",a.check=!1,a.totalPage=1,a.checkedArray=[],a.inputNumber="",a.back=function(){window.history.go(-1)},a.info={},a.submit=[],a.downloadSamples=function(t){return a.submit=[],angular.forEach(a.dataList,function(t){a.checkedArray.push(t.number),a.info.number=t.number,a.submit.push(angular.copy(a.info))}),!a.submit||a.submit.length<1?void e.msg("e","请输入要导出的报告编号"):($(".remark_input").each(function(t,e){angular.forEach(a.submit,function(t){t.number==$(e).data("number")&&""!=$(e).val()?t.remark=$(e).val():t.number==$(e).data("number")&&""==$(e).val()&&(t.remark="")})}),$("#real_number").val(JSON.stringify(a.submit)),$("#is_excel").val(t),void document.getElementById("reportInputForm").submit())},a.downloadSamplesForCSV=function(){a.submit=[],angular.forEach(a.dataList,function(t){a.checkedArray.push(t.number),a.info.number=t.number,a.submit.push(angular.copy(a.info))}),$(".remark_input").each(function(t,e){angular.forEach(a.submit,function(t){t.number==$(e).data("number")&&""!=$(e).val()?t.remark=$(e).val():t.number==$(e).data("number")&&""==$(e).val()&&(t.remark="")})}),$("#real_number").val(JSON.stringify(a.submit)),$("#is_excel").val(0),window.open(SITE_URL+"admin/report_admin/download_report_for_csv"+params)},a.inputToExport=function(){a.inputNumber=null,$("#reportExportModal").modal("show")},a.downloadSamplesFromInput=function(t){var e=a.inputNumber.replace(/\t/g,"");e=$.trim(e),$("#real_number").val(e.replace(/\n/g,"_")),$("#is_excel").val(t),document.getElementById("reportInputForm").submit()},a.dataList=[],$(document).on("keyup",".sample-number",function(t){a.hasReport=!1;var n=$(this),u=$(this).val();angular.forEach(a.dataList,function(o){o.number==u&&13==t.keyCode&&(a.$apply(function(){e.msg("e","报告信息已存在！")}),n.val(""),a.hasReport=!0)}),13!=t.keyCode||a.hasReport||o(u,n)}),a.not_fill_count=0,a.fill_count=0,a.delete=function(t){for(var n=0;n<a.dataList.length;n++)a.dataList[n].id==t.id&&(0==a.dataList[n].report_status?a.not_fill_count--:a.fill_count--,a.dataList.splice(n,1),n--,e.msg("s","删除成功！"));$(".not-fill").text(a.not_fill_count),$(".fill").text(a.fill_count)},a.nextPage=function(){a.inputPage<a.totalPage?(a.check=!1,a.inputPage++,a.getData()):e.msg("e","当前是最后一页")},a.previousPage=function(){a.inputPage>1?(a.check=!1,a.inputPage--,a.getData()):e.msg("e","当前是第一页")},a.firstPage=function(){1==a.totalPage?e.msg("e","当前是第一页"):(a.check=!1,a.inputPage=1,a.getData())},a.lastPage=function(){1==a.totalPage?e.msg("e","当前是最后一页"):(a.check=!1,a.inputPage=a.totalPage,a.getData())},a.selectPage=function(t){1==a.totalPage?e.msg("e","当前是最后一页"):(a.check=!1,a.inputPage=t,a.getData())}}]);