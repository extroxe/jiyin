app.controller("modalDisCtrl",["$scope","$modalInstance","_jiyin","params",function(i,o,t,m){if(i.infoList=m.infoList,i.title=m.title,i.ael=m.ael,"edit"==i.ael){if(null!=i.infoList.commodity_center_name)var e=i.infoList.commodity_center_name;else var e=i.infoList.commodity_specification_name;i.infoList.commodity_name=i.infoList.commodity_name+" "+e+" "+i.infoList.package_type_name}i.getCommo=function(){t.dataPost("admin/commodity_admin/get_all_commodity_by_is_point").then(function(o){i.commoList=o.data})},i.getCommo(),i.add_commodity=function(){t.modal({tempUrl:"/source/admin/tpl/modal/modal-agentAddCommodity.html",tempCtrl:"agentAddCommodityCtrl",ok:i.add,size:"lg",params:{title:i.title,infoList:i.discount,roleList:i.roleList,ael:"add",select:"s"}})},i.commodity_ids=[],i.commodity_list=[],i.selecteCommodities=[],i.selecteCommodityList=[],i.deleteList=[],i.ids=[],i.add=function(o){i.infoList.commodity_name=null!=o[0].commodity_center_id?o[0].commodity_name+" "+o[0].commodity_center_name+" "+o[0].package_type_name:o[0].commodity_name+" "+o[0].commodity_specification_name+" "+o[0].package_type_name,i.infoList.commodity_specification_id=o[0].id,i.infoList.commodity_id=o[0].commodity_id,i.infoList.selling_price=o[0].price,i.infoList.market_price=o[0].market_price},i.cancel=function(){o.dismiss("cancel")},i.ok=function(){return i.infoList.commodity_id||"add"!=i.ael?i.infoList.commodity_specification_id||"add"!=i.ael?i.infoList.price?i.infoList.start_time?i.infoList.end_time?void o.close(i.infoList):void t.msg("e","生效结束时间不能为空"):void t.msg("e","生效起始时间不能为空"):void t.msg("e","折扣价格不能为空"):void t.msg("e","商品规格不能为空"):void t.msg("e","商品名称不能为空")}}]);