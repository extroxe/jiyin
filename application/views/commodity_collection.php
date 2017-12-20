<!-- Result Page -->

<div id="commodity_collection"  class="model_box">
    <div class="control-group" style=" border: none; margin-top: -42px">
        <h5>我的收藏</h5>
    </div>
    <div class="no-follow-person" style=" padding: 70px 0;text-align: center;">
        <img src="<?php echo site_url('source/img/warning.png'); ?>" alt="warning" width="50" height="50">
        没有找到相关的商品...
    </div>
    <ul class="thumbnails" id="thumbnails">
       <!-- <li style="float: left">
            <div class="thumbnail" style="margin-left: 0">
                <a href="" style="display: block;">
                    <img src="<?/*=site_url('source/img/05.jpg'); */?>" alt="">
                </a>
                <div class="contain">
                    <h6><span style="font-size: 15px;">¥</span>243</h6>
                    <p class="commodity_name">基因检测套餐</p>
                    <p class="cancel-collection">取消收藏</p>
                </div>
            </div>
        </li>-->

    </ul>
    <div class="row-fluid">
        <div class="span12">
            <div class="pagination pull-right">
                <ul>
                    <li><a href="#" id="Prev_page">上一页</a></li>
                    <li>共<span id="total_pages" style="color: #117d94"></span>页 &nbsp;
                        第<input id="page_num" value="1" type="text">页
                        <a href="#" style="cursor: pointer" id="jump_to_page">跳转</a>
                    </li>
                    <li><a href="#" id="Next_page">下一页</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script id="collection_list" type="text/html">
    <% for(var i = 0; i < list.length; i++){ %>
    <li style="float: left">
        <div class="thumbnail" style="margin-left: 0">
            <a href="javascript:void(0)" style="display: block;">
                <img src="<?=site_url('<%:=list[i].path%>'); ?>" alt="">
            </a>
            <div class="contain">
                <h6><span style="font-size: 15px;">¥</span><%:=list[i].price%></h6>
                <% if(list[i].commodity_center_name && list[i].package_type_name) {%>
                <p class="commodity_name" title="<%:=list[i].name%>"><%:=list[i].name%> <%:=list[i].commodity_center_name%> <%:=list[i].package_type_name%></p>
                <% }else { %>
                <p class="commodity_name" title="<%:=list[i].name%>"><%:=list[i].name%></p>
                <% } %>
               <p class="cancel-collection" style="cursor: pointer" data-specification-id="<%:=list[i].commodity_specification_id%>" data-commodity-id="<%:=list[i].id%>">取消收藏</p>
            </div>
        </div>
    </li>
    <% } %>
</script>
