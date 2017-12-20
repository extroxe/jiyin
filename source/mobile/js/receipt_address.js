angular.module('app')
    .controller('receiptAddressCtrl', ['$scope', 'ajax', function($scope, ajax){
        var prompt=new Prompt();
        $scope.show_promt = function (msg) {
            prompt.setText(msg);
            prompt.show();
        };
        //初始化滑动页
        var singlePage = new Page({
            "onLoad":function(e){
                //var targetPageId;
                if(e.isRoot){

                }else{

                }
            }
        });

        //初始化switch
        var switch_view = {
            _initFormControls:function(){
                this.formControls=new Formcontrols();
            },
            _initPlugin:function(){
                this._initFormControls();
            },
            _attach:function(e){
                var self=this;
            },
            _onLoad:function(){
                var self=this;
                this._initPlugin();
                this._attach();
            }
        };

        $scope.closePage = function (id) {
            singlePage.close(id);
            $('.receipt-info').show();
           /* if (id == '#page_address') {
                $('.item-footer').hide();
                $('#order_header').show();
            }
            if(id == '#page_modify'){
                $('.item-footer').show();
            }*/

        };

        switch_view._onLoad();
        $scope.address = [];
        $scope.address_row = [];

        //获取地址数据
        $scope.get_address = function(){
            ajax.req('GET', 'user/show_address')
                .then(function(response){
                    if (response.success){
                        $scope.address = response.data;
                    }
                });
        };
        $scope.get_address();

        //设为默认地址
        $scope.set_default = function(address, index){
            address.default = 1;
            ajax.req('POST', 'user/update_address', address)
                .then(function(response){
                    if (response.success){
                        angular.forEach($scope.address, function(data, i){
                            if (i != index){
                                $scope.address[i].default = 0;
                            }
                        });
                    }
                })
        };

        //选择设置为默认地址
        $scope.select_default = function($event){
            if ($($event.target).siblings('input[name=switch_default]').val() == 'ok'){
                $scope.address_row.default = 1;
            }else{
                $scope.address_row.default = 0;
            }
        };

        //编辑地址
        $scope.edit = function(id, row){
            $('.receipt-info').hide();
            var province = $('#province');
            singlePage.open(id);

            $scope.type = 'edit';
            $scope.address_row = row;
            province.val($scope.address_row.province_code);
            $scope.searchNextLevel(province[0], $scope.address_row.city_code, $scope.address_row.district_code);
        };

        //添加地址
        $scope.add = function(id, target){
            $('.receipt-info').hide();
            var openType = target || "";
            var province = $('#province');
            var city = $('#city').find("option[class='city']");
            var district = $('#district').find("option[class='district']");
            singlePage.open(id, openType);

            $scope.type = 'add';
            $scope.address_row = [];
            province.val('');

            if(city.length == 0){
                $('#city').find("option[value='']").prop("selected",true)
            }else{
                $('#city').find("option[class='city']").prop("selected",true)
            }
            if(district.length == 0){
                $('#district').find("option:first-child").prop("selected",true)
            }else{
                $('#district').find("option[class='district']").prop("selected",true)
            }
            /*city.innerHTML = '';
            city.empty();
            city.val("");
            city.removeAttr("disabled");

            district.innerHTML = '';
            district.empty();
            district.val("");
            district.removeAttr("disabled");*/
        };

        

        //完成添加地址
        $scope.complete_add = function(){
            if ($scope.address_row.name == undefined || $scope.address_row.name == ''){
                $scope.show_promt("请填写姓名");
            }else if ($scope.address_row.phone == undefined || $scope.address_row.phone == ''){
                $scope.show_promt("请填写联系电话");
            }else if($scope.address_row.province == undefined){
                $scope.show_promt("请选择省份");
            }else if($scope.address_row.city == undefined){
                $scope.show_promt("请选择城市");
            }else if (!($scope.address_row.city_code == "120200" || $scope.address_row.city_code == "310200" || $scope.address_row.city_code == "500200" || $scope.address_row.city_code == "441900") && $scope.address_row.district == undefined){
                $scope.show_promt("请选择地区");
            }else if ($scope.address_row.address == undefined || $scope.address_row.address == ''){
                $scope.show_promt("请填写详细地址");
            }else {
                ajax.req('POST', 'user/add_address', $scope.address_row)
                    .then(function(response){
                        if (response.success){
                            $('.receipt-info').show();
                            singlePage.close('.page_modify');
                            $scope.get_address();
                        }else{
                            $scope.show_promt(response.msg);
                        }
                    })
            }
        };

        //完成编辑地址
        $scope.complete_edit = function(){
            if ($scope.address_row.name == undefined || $scope.address_row.name == ''){
                $scope.show_promt("请填写姓名");
            }else if ($scope.address_row.phone == undefined || $scope.address_row.phone == ''){
                $scope.show_promt("请填写联系电话");
            }else if($scope.address_row.province == undefined){
                $scope.show_promt("请选择省份");
            }else if($scope.address_row.city == undefined){
                $scope.show_promt("请选择城市");
            }else if (!($scope.address_row.city_code == "120200" || $scope.address_row.city_code == "310200" || $scope.address_row.city_code == "500200" || $scope.address_row.city_code == "441900") && $scope.address_row.district == undefined){
                $scope.show_promt("请选择地区");
            }else if ($scope.address_row.address == undefined || $scope.address_row.address == ''){
                $scope.show_promt("请填写详细地址");
            }else {
                ajax.req('POST', 'user/update_address', $scope.address_row)
                    .then(function (response) {
                        if (response.success) {
                            history.go(-1);
                            $('.receipt-info').show();
                            $scope.get_address();
                        } else {
                            $scope.show_promt(response.msg);
                        }
                    })
            }
        };

        //删除地址
        $scope.delete = function(index){
            var popConfirm=new Alert("确认删除该地址?",{
                onClickOk:function(e){
                    ajax.req('POST', 'user/delete_address', {id : $scope.address[index].id})
                        .then(function(response){
                            if (response.success){
                                $scope.address.splice(index, 1);
                            }else{
                                $scope.show_promt(response.msg);
                            }
                            e.hide();
                        })
                },onClickCancel:function(e){
                    e.hide();
                }
            });
            popConfirm.show();

        };

        //选择地址信息
        var district = new AMap.DistrictSearch({
            level: 'country',
            showbiz: false,
            subdistrict: 1
        });
        /**
         * 初始化省市区选择控件
         */
        $scope.initAddress = function() {
            /*$("#city").innerHTML = '';
            $("#city").empty();
            $("#city").val("");
            $("#city").removeAttr("disabled");

            $('#district').innerHTML = '';
            $('#district').empty();
            $('#district').val("");
            $('#district').removeAttr("disabled");*/

            district.search('中国', function(status, result) {
                if(status=='complete'){
                    if (result.districtList.length > 0) {
                        $scope.getAdministrativeRegion(result.districtList[0]);
                    }else {
                        console.log('获取省级行政区失败');
                    }
                }
            });
        };
        /**
         * 解析省市区信息
         * @param data
         */
        $scope.getAdministrativeRegion = function(data, city_code, district_code) {
            var subList = data.districtList;
            var level = data.level;
            //清空下一级别的下拉列表
            if (level === 'province') {
                nextLevel = 'city';
                $("#city").innerHTML = '';
                $('#district').innerHTML = '';
                $("#city").empty();
                $("#city").val("");
                $('#district').empty();
                $('#district').val("");
            } else if (level === 'city') {
                nextLevel = 'district';
                $('#district').innerHTML = '';
                $('#district').empty();
                $('#district').val("");
            }
            if (subList) {
                if (subList.length > 0) {
                    $('#' + subList[0].level).empty();
                }

                var contentSub;

                if(level == 'province'){
                    contentSub = new Option('-- 市 --');
                }else if(level == 'city'){
                    contentSub = new Option('-- 区 --');
                }else{
                    contentSub = new Option('-- 省 --');
                }

                contentSub.setAttribute("value", "");
                for (var i = 0, l = subList.length; i < l; i++) {
                    var name = subList[i].name;
                    var value = subList[i].adcode;
                    var levelSub = subList[i].level;
                    var cityCode = subList[i].citycode;

                    if (i == 0) {
                        document.querySelector('#' + levelSub).add(contentSub);
                        document.querySelector('#' + levelSub).removeAttribute('disabled');
                    }
                    contentSub=new Option(name);
                    contentSub.setAttribute("value", value);
                    contentSub.center = subList[i].center;
                    contentSub.adcode = subList[i].adcode;

                    document.querySelector('#' + levelSub).add(contentSub);
                }
                if (typeof(city_code) != 'undefined' && city_code != "" && levelSub == "city") {
                    $('#' + levelSub).val(city_code);
                    $scope.searchNextLevel($('#' + levelSub)[0], city_code, district_code);
                }else if (typeof(district_code) != 'undefined' && district_code != "" && levelSub == "district") {
                    $('#' + levelSub).val(district_code);
                }
            }else {
                if (level == "province") {
                    // 将市级、县级下拉列表置为不可用
                    $("#city").attr('disabled', 'disabled');
                    $("#district").attr('disabled', 'disabled');
                }else if (level == "city") {
                    // 将县级下拉列表置为不可用
                    $("#district").attr('disabled', 'disabled');
                }
            }

        };
        /**
         * 根据当前所选省市搜索下级行政区域列表
         * @param obj
         * @param city_code 城市代码，编辑地址时初始化控件使用
         * @param district_code 区县代码，编辑地址时初始化控件使用
         */
        $scope.searchNextLevel = function(obj, city_code, district_code) {
            var option = obj[obj.options.selectedIndex];
            var keyword = option.text; //关键字
            var adcode = option.adcode;
            city_code = city_code || '';
            district_code = district_code || '';
            district.setLevel(option.value); //行政区级别
            //行政区查询
            //按照adcode进行查询可以保证数据返回的唯一性
            district.search(adcode, function(status, result) {
                if(status === 'complete'){
                    $scope.getAdministrativeRegion(result.districtList[0], city_code, district_code);
                }
            });
        };

        $scope.initAddress();

        //监听地址选择事件
        $('#province')[0].addEventListener('change', function(){
            var obj = this;
            $scope.searchNextLevel(obj);
            $scope.address_row.province = obj[obj.options.selectedIndex].text;
            $scope.address_row.province_code = obj[obj.options.selectedIndex].value;
        }, false);

        $('#city')[0].addEventListener('change', function(){
            var obj = this;
            $scope.searchNextLevel(obj);
            $scope.address_row.city = obj[obj.options.selectedIndex].text;
            $scope.address_row.city_code = obj[obj.options.selectedIndex].value;
        }, false);

        $('#district')[0].addEventListener('change', function(){
            var obj = this;
            $scope.searchNextLevel(obj);
            $scope.address_row.district = obj[obj.options.selectedIndex].text;
            $scope.address_row.district_code = obj[obj.options.selectedIndex].value;
        }, false);
    }]);