app.controller("commentCtrl",["$scope","_jiyin","dataToURL","$stateParams","$state",function(t,a,n,e){t.commentList={},t.inputPage=1,t.infoList={},t.keyword="",t.look=function(a){$("#replyAll").modal("show"),t.replyList=a.replies},t.search=function(){t.inputPage=1,a.dataPost("admin/post_admin/paginate_comment/"+t.inputPage+"/10",n({post_bar_id:e.id,keyword:t.keyword})).then(function(a){1==a.success&&(t.totalPage=a.total_page,t.commentList=a.data)})},$("#search").keydown(function(a){13==a.keyCode&&t.search()}),t.show=function(){t.title="增加",t.add=!0,t.infoList={},t.infoList.post_id=e.id,$("#comment").modal("show")},t.edit=function(a){t.title="编辑",t.infoList=a,t.add=!1,$("#comment").modal("show")},t.reply=function(a){t.infoList={},t.infoList.post_id=e.id,t.infoList.root_comment_id=a.id,t.infoList.comment_id=a.id,t.infoList.to_user_id=a.publisher_id,$("#reply").modal("show")},t.replyOk=function(){a.dataPost("admin/post_admin/add_comment",n(t.infoList)).then(function(n){1==n.success?(a.msg("s","添加成功"),t.getData(),$("#reply").modal("hide")):a.msg("e",n.msg)})},t.getBar=function(){a.dataPost("admin/post_admin/paginate/1/999").then(function(a){t.postList=a.data})},t.getBar(),t.getStatus=function(){a.dataPost("admin/system_code_admin/get_by_type/comment_status").then(function(a){t.statusList=a})},t.getStatus(),t.ok=function(){1==t.add?a.dataPost("admin/post_admin/add_comment",n(t.infoList)).then(function(n){1==n.success?(a.msg("s","添加成功"),t.getData(),$("#comment").modal("hide")):a.msg("e",n.msg)}):a.dataPost("admin/post_admin/update_comment",n({id:t.infoList.id,status_id:t.infoList.status_id})).then(function(n){1==n.success?(a.msg("s","修改成功"),t.getData(),$("#comment").modal("hide")):a.msg("e",n.msg)})},t.cancel=function(){$("#comment").modal("hide")},t.getData=function(){a.dataPost("admin/post_admin/paginate_comment/"+t.inputPage+"/10/",n({post_bar_id:e.id,keyword:t.keyword})).then(function(a){1==a.success&&(t.totalPage=a.total_page,t.commentList=a.data)})},t.getData(),t.delete=function(e){confirm("确认删除这条数据吗?")&&a.dataPost("admin/post_admin/delete_comment",n({id:e.id})).then(function(n){1==n.success?(a.msg("s","删除成功"),t.getData()):a.msg("e",n.msg)})},t.remove=function(e){confirm("确认删除这条数据吗?")&&a.dataPost("admin/post_admin/delete_comment",n({id:e.id})).then(function(n){1==n.success?(a.msg("s","删除成功"),t.getData(),e.status_id=2,e.status_name="已删除（管理员）"):a.msg("e",n.msg)})},t.nextPage=function(){t.inputPage<t.totalPage?(t.inputPage++,t.getData()):a.msg("e","当前是最后一页")},t.previousPage=function(){t.inputPage>1?(t.inputPage--,t.getData()):a.msg("e","当前是第一页")},t.firstPage=function(){t.inputPage=1,t.getData()},t.lastPage=function(){t.inputPage=t.totalPage,t.getData()},t.selectPage=function(a){t.inputPage=a,t.getData()}}]);