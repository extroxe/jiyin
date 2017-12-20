angular.module('app')
    .controller('evaluation', ['$rootScope','$scope', 'ajax','FileUploader', 'fileMd5', function ($rootScope,$scope, ajax,FileUploader,fileMd5) {
        $scope.evaluation = []; //评价信息
        $scope.score = [];//评分
        $scope.commodity = []; //商品信息
        $scope.flag = false; //按钮可点击
        $scope.flag_review = false; //是否可评论
        $scope.img_src = []; //图片路径
        $scope.star = [{num:1, star_src:SITE_URL + 'source/mobile/img/icon/collect.png'},
            {num:2, star_src:SITE_URL + 'source/mobile/img/icon/collect.png'},
            {num:3, star_src:SITE_URL + 'source/mobile/img/icon/collect.png'},
            {num:4, star_src:SITE_URL + 'source/mobile/img/icon/collect.png'},
            {num:5, star_src:SITE_URL + 'source/mobile/img/icon/collect.png'}
           ];

        $scope.url = window.location.href;
        $scope.status_id = $scope.url.substring($scope.url.lastIndexOf('/') + 1);

        $scope.attachment_ids = [];
        var prompt = new Prompt();
        /**
         * 获取订单id
         */
        $scope.$watch('order_id', function (nv, ov) {
            $scope.order_id = nv;
            if (nv) {
                $scope.order_id = nv;
                ajax.req('POST', 'order/get_evaluation_by_order_id', {id:nv}, true)
                    .then(function (data) {
                        if (data.success) {
                            $scope.commodity = data.data;
                            angular.forEach($scope.commodity, function (commodity) {
                                if(commodity.id != null){
                                    $scope.flag_review = true;
                                    $scope.flag = true;
                                    $scope.evaluation = commodity.content;
                                    $scope.score = parseInt(commodity.score);
                                    $scope.img_src = commodity.pic;
                                    for(var i = 0; i<$scope.score; i++){
                                        $scope.star[i].star_src = SITE_URL + 'source/mobile/img/icon/collected.png'
                                    }
                                    $rootScope.$on("$destroy",function(){
                                        $scope.mark();
                                    });
                                    
                                }else{
                                    $scope.flag_review = false;
                                    $scope.flag = false;
                                    $scope.evaluation = [];
                                    $scope.score = [];
                                    $scope.img_src = [];
                                }
                            })
                        }
                    });
            }
        });

        //评价打分
        $scope.mark = function (index, $event) {
            $($event.target).siblings().attr('src', SITE_URL + 'source/mobile/img/icon/collect.png');
            $($event.target).attr('src', SITE_URL + 'source/mobile/img/icon/collected.png');
            $($event.target).prevAll().attr('src', SITE_URL + 'source/mobile/img/icon/collected.png');
            $scope.score = index+1;
        }

        $scope.img_flag = true;
        $scope.star_change = function () {
            $scope.img_flag = false;
        };

        var uploader = $scope.uploader = new FileUploader({
            url: SITE_URL + 'attachment/up_attachment',
            removeAfterUpload : true
        });

        // $scope.uploading = false;
        /**
         * 上传图片
         */
        $scope.thumb = [];
        $scope.newvalue = false;
            $scope.$watch('uploader.queue[0]', function(nv, ov){
            // $scope.thumb.push(SITE_URL + 'source/mobile/img/upload.png');
            $scope.uploading = uploader.uploading;
            if (nv){
                $scope.newvalue = true;
                if($scope.attachment_ids.length > 5){
                    prompt.setText('最多只能上传五张图片');
                    prompt.show();
                }else{
                    fileMd5(nv._file).then(function (result) {
                        ajax.req('POST', 'attachment/check_md5', {md5_code: result.md5Code})
                            .then(function (result) {
                                if(result.exist == true){
                                    $scope.thumb.push(SITE_URL + result.path);
                                    $scope.attachment_ids.push(result.attachment_id);
                                    uploader.queue[0].remove();
                                    if($scope.attachment_ids.length > 4){
                                        $('.camera').css('display','none');
                                    }
                                }else{
                                    $scope.thumb.push(SITE_URL + 'source/mobile/img/upload.png');
                                    nv.upload();
                                }
                            });
                    });
                }
            }
        });


        uploader.onSuccessItem = function(fileItem, response, status, headers) {
            if (response.success){
                $scope.thumb.splice(($scope.thumb.length-1), 1);
                $scope.thumb.push(SITE_URL + response.url);
                $scope.attachment_ids.push(response.attachment_id);
                // uploader.queue[0].remove();
                if($scope.attachment_ids.length > 5){
                    $scope.uploadFlag = true;
                }
            }else{
                prompt.setText(response.msg);
                prompt.show();
            }
        };

        $scope.img_del = function(key) {    //删除，删除的时候thumb和form里面的图片数据都要删除，避免提交不必要的
            var guidArr = [];
            for(var p in $scope.thumb){
                guidArr.push(p);
            }
            $scope.attachment_ids.splice(key, 1);
            $scope.thumb.splice(key,1);
            $('.camera').css('display','block');
        };

        //查看大图
        $scope.enlarge = function (src) {
            $scope.img_src = src;
            $('.enlarge-mask').css('display', 'table');
        };
        //取消查看
        $scope.closelarge = function () {
            $('.enlarge-mask').css('display', 'none');
            $scope.img_src = '';
        };
        /**
         * 提交评价
         */

        $scope.submit_evaluation = function (commodity_list,evaluation) {
            $scope.attachment_ids = $scope.attachment_ids.slice(',').join('-');
            ajax.req('POST', 'order/evaluate_order', {
                commodity_id: commodity_list.commodity_id,
                order_id: $scope.order_id,
                order_commodity_id: commodity_list.order_commodity_id,
                score: $scope.score,
                content: evaluation,
                attachment_ids: $scope.attachment_ids
            }, true)
                .then(function (data) {
                    if (data.success) {
                        prompt.setText('评价成功');
                        prompt.show();
                        $scope.flag = true;
                        window.location.href = SITE_URL + 'weixin/user/evaluation_list/' + commodity_list.commodity_id
                    }else{
                        prompt.setText(data.error);
                        prompt.show();
                    }
                })
        };

        $scope.back = function () {
            var last_url = document.referrer.substring(document.referrer.lastIndexOf('/')).replace('/','');
            if(last_url == '/order_list' || angular.isNumber(parseInt(last_url))){
                window.location.href = SITE_URL + 'weixin/user/order_list/'+ $scope.status_id;
            }else{
                history.go(-1);
            }
        };
    }]);