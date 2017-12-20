angular.module('app')
    .controller('shoppingMallCtrl', ['$scope', 'ajax', function($scope, ajax){
        //获取推荐商品
        $scope.recommends = [];
        ajax.req('POST', 'commodity/get_recommend')
            .then(function (data) {
               if (data.success){
                   $scope.recommends = data.data;
               }
            });
        //获取父级和子级分类
        $scope.parent_categorys = [];
        $scope.child_categorys = [];
        ajax.req('POST', 'category/get_father_category')
            .then(function (data) {
                if (data.success){
                    $scope.parent_categorys = data.data;
                }
            });
        // 初始化轮播图
        var s1=new Slider("#carousel1",{
            "pagination":".slider-pagination",
            "autoplay":5000,
            "loop":true
        });

        // var img_height = ;
        $('.slider-container, .slider-container div, .slider-container img').css('height', '212px');

        $('#empty_content').css('display', 'none');
        $(document).on('keyup', '#search_box', function () {
            if($(this).val() != ''){
                $('#empty_content').css('display', 'block');
            }else{
                $('#empty_content').css('display', 'none');
            }
        });
        $('#empty_content').click(function () {
            $("#search_box").val("");
            $(this).css('display', 'none');
        });
        
        //手机端显示软键盘隐藏底部导航
        $('input[type=search]').focusin(function(){
            $('ul.tabbar.tabbar-footer').hide();
        });

        $('input[type=search]').focusout(function(){
            $('ul.tabbar.tabbar-footer').show();
        });

        $scope.open = true;
        $('#icon_down').click(function () {
            $('details').attr("open", $scope.open);
            if($(this).find("i").hasClass("icon-arrowdown")){
                $(this).find("i").removeClass("icon-arrowdown").addClass("icon-arrowup")
            }else{
                $(this).find("i").removeClass("icon-arrowup").addClass("icon-arrowdown")
            }

            $scope.open = !$scope.open;
        })
    }]);

