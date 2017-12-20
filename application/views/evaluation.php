<div id="page-content" class="home-page">
    <div class="container">
        <div class="row-fluid">
            <div class="span12">
                <ul class="title">
                    <li>评价订单</li>
                </ul>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <div id="order_number">
                    <span>订单编号：</span>
                    <a href="javascript:void(0)" class="order-number"></a>
                    <span class="current_time"></span>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <div id="all_product_box">

                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <div id="submit_btn">
                    <button class="btn btn-2 btn-link">提交评价</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/html" id="evaluation_list_tpl">
        <div class="product_box" style="border-bottom: 1px solid #ddd;margin-bottom: 10px;background-color: #f2f7ff;">
            <div class="row-fluid">
                <div class="span4">
                    <div class="product_info">
                        <img src="<?=site_url('<%:= data.thumbnail_path %>'); ?>">
                        <p><%:= data.commodity_name %></p>
                        <span><span style="color: #444">子订单编号：</span><%:= data.sub_number %></span>
                    </div>
                </div>
                <div class="span8">
                    <div class="review_info">
                        <ul>
                            <li>
                                <img src="<?=site_url('source/img/sprite-tip_03.png'); ?>">
                                <p>请至少填写一件商品的评价</p>
                            </li>
                            <li class="star_li">
                                <label>商品满意度</label>
                                <span class="star fa fa-star"></span>
                                <span class="star fa fa-star"></span>
                                <span class="star fa fa-star"></span>
                                <span class="star fa fa-star"></span>
                                <span class="star fa fa-star"></span>
                            </li>
                            <li class="last_li">
                                <label>评价晒单</label>
                                <div class="text_area">
                                    <textarea class="content" placeholder="请给出您的评价"></textarea>
                                    <div class="img_upload">
                                        <!-- 上传图片 -->
                                        <p id="imghead" class="upload_img_btn" onclick="$('#previewImg').click()"></p>
                                        <input type="file" style="display: none;" id="previewImg">

                                        <span class="describle">共
                                                <span class="upload_img_num" style="color:#eb3c3f"> 0 </span>张，还可以上传
                                                <span class="all_img_num" style="color:#eb3c3f"> 5 </span> 张
                                            </span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </script>
</div>

