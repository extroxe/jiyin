app.controller("hotsaleCtrl",["$scope","_jiyin","FileUploader","dataToURL",function(t,e,a,n){var o=t.uploader=new a({url:SITE_URL+"attachment/up_attachment"});o.onAfterAddingFile=function(a){e.fileMd5(a._file).then(function(c){e.dataPost("attachment/check_md5",n({md5_code:c.md5Code})).then(function(e){1==e.exist?(t.path=e.path,o.clearQueue(),t.save(t.path)):a.upload()})})},o.onSuccessItem=function(e,a){t.path=a.url,o.clearQueue(),t.save(t.path)},t.save=function(a){e.dataPost("admin/system_setting_admin/hot_sale_cover",n({path:a})).then(function(a){1==a.success&&(e.msg("s","设置成功"),t.get())})},t.get=function(){e.dataPost("admin/system_setting_admin/get_hot_sale_cover").then(function(e){t.path=1==e.success?e.data.value:""})},t.get()}]);