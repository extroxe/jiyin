app.controller("reportCtrl",["$scope","_jiyin","dataToURL","$state","$stateParams",function(e,t,i,n,o){e.reportList={},e.inputPage=1,e.list={},e.open=!1,e.back=function(){window.history.go(-1)},e.download=function(e){e.id?window.open(SITE_URL+"attachment/report_download/"+e.id):t.msg("e","请选择要下载的报告")},e.$on("attachment_id",function(t,i){e.list.attachment_id=i}),e.add=function(){e.list={},e.flag=!0,e.open=!0,$("#reportModal").modal("show"),$("#focus_number")[0].focus(),e.$broadcast("open",{open:e.open})},e.edit=function(t){e.list=t,e.province=t.province,e.flag=!1,e.open=!0;var i=$("#province");i.val(e.list.province_code),e.searchNextLevel(i[0],e.list.city_code,e.list.district_code),$("#province").val(t.province_code),$("#reportModal").modal("show"),e.$broadcast("open",{open:e.open})},e.getCommos=function(){t.dataPost("admin/detection_template_admin/get_detection_template").then(function(t){e.commoLists=t.data})},e.getCommos(),e.project_nums=0,e.$watch("list.template_id",function(t){t&&angular.forEach(e.commoLists,function(i){t==i.id&&(e.project_nums=i.projects.length)})}),e.is_more=!1,e.checkProjectNum=function(){e.is_more=e.list.project_num>e.project_nums?!0:!1},e.delete=function(n){confirm("确定删除此报告吗?")&&t.dataPost("admin/report_admin/delete",i({id:n.id})).then(function(i){1==i.success?(t.msg("s","删除成功"),e.getData()):t.msg("e","删除失败")})},e.ok=function(){var n;return e.list.number?e.list.template_id?e.list.project_num>e.project_nums?void t.msg("e","项目数超出范围"):(n=1==e.flag?"admin/report_admin/add":"admin/report_admin/update",e.list.order_id=o.order_id,e.list.order_commodity_id=o.id,void t.dataPost(n,i(e.list)).then(function(i){1==i.success?(t.msg("s",i.msg),e.getData(),$("#reportModal").modal("hide"),e.open=!1):t.msg("e",i.msg)})):void t.msg("e","模板必须填写"):void t.msg("e","报告编号不能为空")},e.getData=function(){t.dataPost("admin/report_admin/get_report_list_by_order_commodity_id",i({order_commodity_id:o.id})).then(function(t){e.reportList=t.data,e.totalPage=t.total_page})},e.getData();var a=new AMap.DistrictSearch({level:"country",showbiz:!1,subdistrict:1});e.initAddress=function(){a.search("中国",function(t,i){"complete"==t&&(i.districtList.length>0?e.getAdministrativeRegion(i.districtList[0]):console.log("获取省级行政区失败"))})},e.getAdministrativeRegion=function(t,i,n){var o=t.districtList,a=t.level;if("province"===a?(nextLevel="city",$("#city").innerHTML="",$("#district").innerHTML="",$("#city").empty(),$("#city").val(""),$("#district").empty(),$("#district").val("")):"city"===a&&(nextLevel="district",$("#district").innerHTML="",$("#district").empty(),$("#district").val("")),o){o.length>0&&$("#"+o[0].level).empty();var d;d=new Option("province"==a?"--市--":"city"==a?"-- 区 --":"--省--"),d.setAttribute("value","");for(var c=0,s=o.length;s>c;c++){{var r=o[c].name,l=o[c].adcode,u=o[c].level;o[c].citycode}0==c&&(document.querySelector("#"+u).add(d),document.querySelector("#"+u).removeAttribute("disabled")),d=new Option(r),d.setAttribute("value",l),d.center=o[c].center,d.adcode=o[c].adcode,document.querySelector("#"+u).add(d)}"undefined"!=typeof i&&""!=i&&"city"==u?($("#"+u).val(i),e.searchNextLevel($("#"+u)[0],i,n)):"undefined"!=typeof n&&""!=n&&"district"==u&&$("#"+u).val(n)}else"province"==a?($("#city").attr("disabled","disabled"),$("#district").attr("disabled","disabled")):"city"==a&&$("#district").attr("disabled","disabled")},e.searchNextLevel=function(t,i,n){var o=t[t.options.selectedIndex],d=(o.text,o.adcode);i=i||"",n=n||"",a.setLevel(o.value),a.search(d,function(t,o){"complete"===t&&e.getAdministrativeRegion(o.districtList[0],i,n)})},e.initAddress(),$("#province")[0].addEventListener("change",function(){var t=this;e.searchNextLevel(t),e.list.province=t[t.options.selectedIndex].text,e.list.province_code=t[t.options.selectedIndex].value},!1),$("#city")[0].addEventListener("change",function(){var t=this;e.searchNextLevel(t),e.list.city=t[t.options.selectedIndex].text,e.list.city_code=t[t.options.selectedIndex].value},!1),$("#district")[0].addEventListener("change",function(){var t=this;e.searchNextLevel(t),e.list.district=t[t.options.selectedIndex].text,e.list.district_code=t[t.options.selectedIndex].value},!1),e.nextPage=function(){e.inputPage<e.totalPage?(e.inputPage++,e.getData()):t.msg("e","当前是最后一页")},e.previousPage=function(){e.inputPage>1?(e.inputPage--,e.getData()):t.msg("e","当前是第一页")},e.firstPage=function(){e.inputPage=1,e.getData()},e.lastPage=function(){e.inputPage=e.totalPage,e.getData()},e.selectPage=function(t){e.inputPage=t,e.getData()}}]),app.controller("reportFileUploadCtrl",["$scope","FileUploader","_jiyin","dataToURL",function(e,t,i,n){var o=e.uploader=new t({url:SITE_URL+"attachment/upload_report"});e.$on("open",function(e,t){1==t.open&&o.clearQueue()}),o.filters.push({name:"customFilter",fn:function(){return this.queue.length<2}}),e.upload=function(t){return o.queue.length>1?void i.msg("e","只能上传一个文件"):void i.fileMd5(t._file).then(function(a){i.dataPost("attachment/check_md5",n({md5_code:a.md5Code})).then(function(i){1==i.exist?(e.$emit("attachment_id",i.attachment_id),t.file.size=t._file.size,t.progress=100,t.isSuccess=!0,t.isUploaded=!0,t.uploader.progress+=100/o.queue.length):t.upload()})})},e.uploadAll=function(){return o.queue.length>1?void i.msg("e","只能上传一个文件"):void angular.forEach(o.queue,function(t){i.fileMd5(t._file).then(function(a){i.dataPost("attachment/check_md5",n({md5_code:a.md5Code})).then(function(i){1==i.exist?(e.$emit("attachment_id",i.attachment_id),t.file.size=t._file.size,t.progress=100,t.isSuccess=!0,t.isUploaded=!0,o.progress+=100/o.queue.length):t.upload()})})})},o.onSuccessItem=function(t,i){e.$emit("attachment_id",i.attachment_id)},e.$on("clearQueue",function(){o.clearQueue()})}]);