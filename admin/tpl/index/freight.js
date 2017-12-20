app.controller('freightCtrl', ['$scope', '_jiyin', 'dataToURL', '$state', function ($scope, _jiyin, dataToURL, $state) {

    $scope.postage = [];
    $scope.set = {};
    $scope.provinceList = [];
    $scope.setPostage = [];
    $scope.allowPush = [];
    $scope.adcodes = [];
    //选择地址信息
    var district = new AMap.DistrictSearch({
        level: 'country',
        showbiz: false,
        subdistrict: 1
    });

    /**
     * 初始化省市区选择控件
     */
    $scope.initAddress = function(ad) {
        district.search(ad, function(status, result) {
            if(status=='complete'){
                if (result.districtList.length > 0) {
                    angular.forEach(result.districtList[0].districtList, function (data,index) {
                        $scope.provinceList.push(data);
                    });
                    var str = $scope.provinceList.splice(22,1);
                    $scope.provinceList.unshift(str[0]);
                }else {
                    _jiyin.msg('e', '获取省级行政区失败');
                }
            }
        });
    };

    $scope.initAddress('中国');

    /**
     * 根据省份获取城市信息
     */
    $scope.provincfe_name = '四川省';
    $scope.provincfe_code = '12323';
    $scope.getCity = function (pName, pCode, e) {
        $scope.provincfe_name = pName;
        $scope.provincfe_code = pCode;
        district.search(pCode, function(status, result) {
            if(status=='complete'){
                $scope.cityList = [];
                if (result.districtList.length > 0) {
                    var tpl = document.getElementById('city_tpl').innerHTML;
                    $("#city").html(template(tpl, {data: result.districtList[0].districtList}));
                }else {
                    _jiyin.msg('e', '获取省级行政区失败');
                }
            }
        });
    };

    $scope.getCity('四川省', '510000');

    /**
     * 根据城市获取区县信息
     */
    $scope.getDistrict = function (cityName, cityCode) {
        district.search(cityName, function(status, result) {
            if($scope.adcodes.indexOf(cityName) == -1 ){
                $scope.adcodes.push(cityName);
                $scope.allowPush = true;
            }else{
                $scope.allowPush = false;
            }
            
            if(status=='complete'){
                if (result.districtList.length > 0) {
                    console.log(result.districtList[0]);
                    console.log(result.districtList[0].districtList);
                   if(!result.districtList[0].districtList){
                       $scope.newDistrictObj = {};
                       result.districtList[0].districtList = [];
                       $scope.newDistrictObj.adcode = result.districtList[0].adcode;
                       $scope.newDistrictObj.citycode = result.districtList[0].citycode;
                       $scope.newDistrictObj.level = 'district';
                       $scope.newDistrictObj.name = result.districtList[0].name;
                       $scope.newDistrictObj.price = result.districtList[0].price;

                       result.districtList[0].districtList.push($scope.newDistrictObj);
                   }
                    $scope.getFreightRule(result.districtList[0].districtList, cityName, cityCode, $scope.allowPush);

                }else {
                    _jiyin.msg('e', '获取省级行政区失败');
                }
            }
        });
    };
    $scope.getDistrict('成都市', '510100');

    /**
     * 点击城市获取区现邮费
     */
    $(document).on('click', '.cityTr', function () {
        var cityName = $(this).data('city-name');
        var cityCode = $(this).data('city-code');
        $scope.getDistrict(cityName, cityCode);
    });

    /**
     * 获取运费规则
     */
    $scope.getFreightRule = function (districtList, cityName, cityCode, allowed) {
        _jiyin.dataGet('admin/postage_admin/get_all')
            .then(function(result){
                if(result.success){
                    angular.forEach(districtList, function (disData, disIndex) {

                        $scope.set.province = $scope.provincfe_name;
                        $scope.set.province_code = $scope.provincfe_code;
                        $scope.set.city = cityName;
                        $scope.set.city_code = cityCode;
                        $scope.set.district = disData.name;
                        $scope.set.district_code = disData.adcode;
                        angular.forEach(result.data, function (freightData, fIndex) {
                            if(!freightData.district_code){
                                freightData.district_code = freightData.city_code;
                                freightData.disrict = freightData.city;
                            }
                        });
                        if(allowed){
                            angular.forEach(result.data, function (freightData, fIndex) {

                                if(freightData.district_code == disData.adcode && disData.price != ''){
                                    // disData.condition = angular.copy(freightData.condition);
                                    disData.price = angular.copy(freightData.price);
                                    // $scope.set.condition = angular.copy(freightData.condition);
                                    $scope.set.price = angular.copy(freightData.price);
                                    $scope.set.setPostage = true;
                                }
                            });
                            $scope.setPostage.push(angular.copy($scope.set));
                            $scope.set = {};
                        }else{
                            $scope.set = {};
                            angular.forEach($scope.setPostage, function(postData, key){
                                if(!postData.district_code || postData.district_code == null){
                                    postData.district_code = postData.city_code;
                                    postData.district = postData.city;
                                }
                               if(postData.district_code == disData.adcode && postData.city_code == cityCode && postData.price){
                                   // disData.condition = postData.condition;
                                   disData.price = postData.price;
                                   return;
                               }
                            });
                        }
                    });
                    $scope.set = {};
                    var tpl = document.getElementById('district_tpl').innerHTML;
                    $(".district").html(template(tpl, {data: districtList}));
                }else{
                    if(allowed){
                        angular.forEach(districtList, function (districtData, index) {
                            $scope.set.city = angular.copy(cityName);
                            $scope.set.city_code = angular.copy(cityCode);
                            $scope.set.district = angular.copy(districtData.name);
                            $scope.set.district_code = angular.copy(districtData.adcode);
                            $scope.setPostage.push(angular.copy($scope.set));
                        });
                        var tpl = document.getElementById('district_tpl').innerHTML;
                        $(".district").html(template(tpl, {data: districtList}));
                    }else{
                        $scope.district_list = [];
                        angular.forEach($scope.setPostage, function(postData, key){
                            if(postData.city_code == cityCode){
                                $scope.district_list.push(postData);
                            }
                        });
                        var tpl = document.getElementById('district_tpl').innerHTML;
                        $(".district").html(template(tpl, {data: $scope.district_list}));
                    }
                }
            });
    };

    //
    // $(document).on('change', '.postage-condition', function () {
    //     var ele = $(this);
    //     var provinceName = '';
    //     var provinceCode = '';
    //     angular.forEach($scope.provinceList, function (province, index) {
    //         if(province.adcode.substr(0,2) == ele.data('district-code').toString().substr(0,2)){
    //             provinceName = province.name;
    //             provinceCode = province.adcode;
    //             return;
    //         }
    //     });
    //     angular.forEach($scope.setPostage, function (data, index) {
    //         if (data.district_code == ele.data('district-code')) {
    //             data.province_code = provinceCode;
    //             data.province = provinceName;
    //             // data.condition = ele.val();
    //             $(".postage-price").each(function (eleIndex, elem) {
    //                 if ($(elem).data('district-code') == ele.data('district-code')) {
    //                     data.price = $(elem).val();
    //                     return;
    //                 }
    //             });
    //             data.setPostage = true;
    //         }
    //     });
    // });

    $(document).on('change', '.postage-price', function () {
        var ele = $(this);
        var provinceName = '';
        var provinceCode = '';
        angular.forEach($scope.provinceList, function (province, index) {
            if(province.adcode.substr(0,2) == ele.data('district-code').toString().substr(0,2)){
                provinceName = province.name;
                provinceCode = province.adcode;
            }
        });
        angular.forEach( $scope.setPostage, function (data, index) {
            if (data.district_code == ele.data('district-code')) {
                data.province_code = provinceCode;
                data.province = provinceName;
                data.price = ele.val();
                $(".postage-condition").each(function (eleIndex, elem) {
                    if ($(elem).data('district-code') == ele.data('district-code')) {
                        // data.condition = $(elem).val();
                        return;
                    }
                });
                data.setPostage = true;
            }else if(!data.district_code || data.district_code == null){
                
            }
        });
        console.log($scope.setPostage);
    });
    
    //提交邮费设置规则
    $scope.submitPostageRule = function () {
        $scope.submitResult = [];
        angular.forEach($scope.setPostage, function (data, index) {
            $scope.submitResult.push(angular.copy(data));
        });

        console.log( $scope.submitResult);
        for (var i = 0; i < $scope.submitResult.length; i++) {
            if ($scope.submitResult[i].hasOwnProperty('setPostage')) {
                delete $scope.submitResult[i].setPostage;
            }

            if($scope.submitResult[i].city_code == $scope.submitResult[i].district_code){
                $scope.submitResult[i].district_code = null;
                $scope.submitResult[i].district = null;
            }

            if ($scope.submitResult[i].price == '' || $scope.submitResult[i].price == undefined) {
                $scope.submitResult.splice(i, 1);
                i--;
            }
        }

        _jiyin.dataPost('admin/postage_admin/set_postage', dataToURL({postage:JSON.stringify($scope.submitResult)}))
            .then(function(result){
                if(result.success){
                    _jiyin.msg('s', '邮费设置成功');
                    // $scope.initAddress('中国');
                }else{
                    _jiyin.msg('e', '邮费设置失败');
                }
            });
    }


}]);