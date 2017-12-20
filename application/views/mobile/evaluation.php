<article ng-controller="evaluation" ng-init="order_id = '<?php echo isset($order_id) && intval($order_id) > 0 ? $order_id : ''; ?>'">
    <div class="titlebar">
        <a class="titlebar-button" ng-click="back()">
            <i style="color: #eee" class="icon size16 icon-arrowleft back_btn"></i>
        </a>
        <h1><?php echo $title; ?></h1>
    </div>
    <div class="group evaluate_box" ng-repeat="commodity_list in commodity">
        <div class="inputbox review_img">
            <img ng-if="sub_order.path !== null" ng-src="{{ SITE_URL + commodity_list.commodity_thumbnail_path }}">
            <textarea ng-class="{'textareabackgroung': flag_review, 'textareanormal': !flag_review}" ng-disabled = "flag_review == true" placeholder="亲！请留下您宝贵的意见和建议，我们会认真听取并采纳您的意见" ng-model="evaluation">
            </textarea>
        </div>
        <div class="inputbox load_img" style="padding: 15px">
            <label class="camera" ng-if="flag != true" for="one-input">
                <img ng-src="{{ SITE_URL + 'source/mobile/img/icon/camera_icon.png' }}">
            </label>
            <input type="file" name="file" id="one-input" style="display: none" nv-file-select="" ng-disabled="uploadFlag == true" uploader="uploader">
            <div class="thumb" ng-repeat="item in thumb" style="position:relative;">
                <!-- 采用angular循环的方式，对存入thumb的图片进行展示 -->

                    <img class="imgloaded" ng-src="{{item}}" ng-click="enlarge(item)"/>
                    <span ng-if="item" class="icon-clear-fill" ng-click="img_del($index)"></span>
            </div>

            <div ng-if="commodity_list.id != null" ng-repeat="img in img_src">
                <img class="imgloaded" ng-src="{{ SITE_URL + img.path }}">
            </div>
        </div>
        <div class="inputbox underline topline score">
            <label class="inputbox-left">商品评分</label>
            <div class="inputbox-right inputbox star_box">
                <div class="star">
                    <img ng-repeat="stars in star" ng-click="mark($index, $event)" class="star1" ng-src="{{stars.star_src}}">
                </div>
            </div>
        </div>

        <div class="inputbox submit_review">
            <button class="button" ng-disabled="flag == true" ng-click="submit_evaluation(commodity_list, evaluation)">提交评价</button>
        </div>
    </div>
    <div class="enlarge-mask" ng-click="closelarge()">

        <span><img src="{{img_src}}"></span>
    </div>
</article>

