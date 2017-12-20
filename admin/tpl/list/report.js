/**
 * Created by sailwish001 on 2017/1/12.
 */
app.controller('reportCtrl', ['$scope', '_jiyin', 'dataToURL', '$state', '$stateParams',  function ($scope, _jiyin, dataToURL, $state, $stateParams) {
    $scope.reportList = {};
    $scope.inputPage = 1;
    $scope.list = {};
    $scope.open = false;

    $scope.back = function () {
        window.history.go(-1);
    };

    $scope.download = function (data) {
        if (data.id) {
            window.open(SITE_URL + 'attachment/report_download/' + data.id);
        }else {
            _jiyin.msg('e', '请选择要下载的报告');
        }

    };

    $scope.$on('attachment_id', function(event, attachment_id) {
        $scope.list.attachment_id = attachment_id;
    });

  /*  $scope.add = function () {
        $scope.list = {};
        $scope.flag = true;
        $scope.open = true;
        $("#reportModal").modal('show');
        $scope.$broadcast('open', {
            open: $scope.open
        });
    };*/
    $scope.add = function () {
        $scope.list = {};
        $scope.flag = true;
        $scope.open = true;
        $("#reportModal").modal('show');
        /*document.getElementById("focus_number").focus();*/
        $("#focus_number")[0].focus();
        $scope.$broadcast('open', {
            open: $scope.open
        });
    };

    $scope.edit = function (data) {
        $scope.list = data;
        $scope.province = data.province;
        $scope.flag = false;
        $scope.open = true;
        var province = $('#province');

        province.val($scope.list.province_code);
        $scope.searchNextLevel(province[0], $scope.list.city_code, $scope.list.district_code);
        $('#province').val(data.province_code);
        $("#reportModal").modal('show');
        $scope.$broadcast('open', {
            open: $scope.open
        });
    };

    /*$scope.edit = function (data) {
        $scope.list = data;
        $scope.flag = false;
        $scope.open = true;
        $("#reportModal").modal('show');
        $scope.$broadcast('open', {
            open: $scope.open
        });
    };*/

    /*获取模板*/
    $scope.getCommos = function () {
        _jiyin.dataPost('admin/detection_template_admin/get_detection_template')
            .then(function (result) {
                $scope.commoLists = result.data;
            })
    };
    $scope.getCommos();
    $scope.project_nums = 0;
    $scope.$watch('list.template_id', function (nv) {
        if(nv){
            angular.forEach($scope.commoLists, function (data, index) {
                if(nv == data.id){
                    $scope.project_nums = data.projects.length;
                }
            })
        }
    });
    $scope.is_more = false;
    $scope.checkProjectNum = function () {
        if($scope.list.project_num > $scope.project_nums){
            // _jiyin.msg('e', '项目数超出最大数量');
            $scope.is_more = true;
        }else{
            $scope.is_more = false;
        }
    };

    $scope.delete = function (data) {
        if(confirm('确定删除此报告吗?')){
            _jiyin.dataPost('admin/report_admin/delete', dataToURL({id: data.id}))
                .then(function (result) {
                    if(result.success == true){
                        _jiyin.msg('s', '删除成功');
                        $scope.getData();
                    }else {
                        _jiyin.msg('e', '删除失败');
                    }
                });
        }
    };

    $scope.ok = function () {
        var url;
        if(!$scope.list.number){
            _jiyin.msg('e','报告编号不能为空');
            return ;
        }
        if(!$scope.list.template_id){
            _jiyin.msg('e', '模板必须填写');
            return;
        }
        if($scope.list.project_num > $scope.project_nums){
            _jiyin.msg('e', '项目数超出范围');
            return;
        }
        if($scope.flag == true){
            url = 'admin/report_admin/add';
        }else{
            url = 'admin/report_admin/update'
        }
        $scope.list.order_id = $stateParams.order_id;
        $scope.list.order_commodity_id = $stateParams.id;
        _jiyin.dataPost(url, dataToURL($scope.list))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s',result.msg);
                    $scope.getData();
                    $("#reportModal").modal('hide');
                    $scope.open = false;
                }else {
                    _jiyin.msg('e', result.msg);
                }
            });
    };

   /* $scope.ok = function () {
        var url;

        if($scope.flag == true){
            url = 'admin/report_admin/add';
        }else{
            url = 'admin/report_admin/update'
        }
        $scope.list.order_commodity_id = $stateParams.id;
        _jiyin.dataPost(url, dataToURL($scope.list))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s',result.msg);
                    $scope.getData();
                    $("#reportModal").modal('hide');
                    $scope.open = false;
                }else {
                    _jiyin.msg('e', result.msg);
                }
            });
    };*/
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/report_admin/get_report_list_by_order_commodity_id', dataToURL({
            order_commodity_id: $stateParams.id
        }))
        .then(function(result){
            $scope.reportList = result.data;
            $scope.totalPage = result.total_page;
        });
    };
    $scope.getData();

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
                contentSub = new Option('--市--');
            }else if(level == 'city'){
                contentSub = new Option('-- 区 --');
            }else{
                contentSub = new Option('--省--');
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
        $scope.list.province = obj[obj.options.selectedIndex].text;
        $scope.list.province_code = obj[obj.options.selectedIndex].value;
    }, false);

    $('#city')[0].addEventListener('change', function(){
        var obj = this;
        $scope.searchNextLevel(obj);
        $scope.list.city = obj[obj.options.selectedIndex].text;
        $scope.list.city_code = obj[obj.options.selectedIndex].value;
    }, false);

    $('#district')[0].addEventListener('change', function(){
        var obj = this;
        $scope.searchNextLevel(obj);
        $scope.list.district = obj[obj.options.selectedIndex].text;
        $scope.list.district_code = obj[obj.options.selectedIndex].value;
    }, false);



    /**
     * 下一页
     */
    $scope.nextPage = function(){
        if($scope.inputPage < $scope.totalPage){
            $scope.inputPage ++;
            $scope.getData();
        }else{
            _jiyin.msg('e', '当前是最后一页');
        }
    };
    /**
     * 上一页
     */
    $scope.previousPage = function(){
        if($scope.inputPage > 1){
            $scope.inputPage --;
            $scope.getData();
        }else{
            _jiyin.msg('e', '当前是第一页');
        }
    };
    /**
     * 第一页
     */
    $scope.firstPage = function () {
        $scope.inputPage = 1;
        $scope.getData();
    };
    /**
     * 最后一页
     */
    $scope.lastPage = function () {
        $scope.inputPage = $scope.totalPage;
        $scope.getData();
    };
    $scope.selectPage = function (page) {
        $scope.inputPage = page;
        $scope.getData();
    }
}]);

app.controller('reportFileUploadCtrl', ['$scope', 'FileUploader', '_jiyin', 'dataToURL', function($scope, FileUploader, _jiyin, dataToURL) {
    var uploader = $scope.uploader = new FileUploader({
        url: SITE_URL + 'attachment/upload_report'
    });
    $scope.$on('open', function (event, args) {
       if(args.open == true){
           uploader.clearQueue();
       }
    });
    // FILTERS
    uploader.filters.push({
        name: 'customFilter',
        fn: function(item /*{File|FileLikeObject}*/ , options) {
            return this.queue.length < 2;
        }
    });
    $scope.upload = function(item){
        if(uploader.queue.length > 1){
            _jiyin.msg('e', '只能上传一个文件');
            return;
        }
        _jiyin.fileMd5(item._file).then(function (result) {
            _jiyin.dataPost('attachment/check_md5', dataToURL({md5_code: result.md5Code}))
                .then(function (result) {
                    if(result.exist == true){
                        $scope.$emit('attachment_id', result.attachment_id);
                        item.file.size = item._file.size;
                        item.progress = 100;
                        item.isSuccess = true;
                        item.isUploaded = true;
                        item.uploader.progress += 100/uploader.queue.length;
                    }else{
                        item.upload();
                    }
                });
        });
    };
    $scope.uploadAll = function () {
        if(uploader.queue.length > 1){
            _jiyin.msg('e', '只能上传一个文件');
            return;
        }
        angular.forEach(uploader.queue, function (data, index) {
            _jiyin.fileMd5(data._file).then(function (result) {
                _jiyin.dataPost('attachment/check_md5', dataToURL({md5_code: result.md5Code}))
                    .then(function (result) {
                        if(result.exist == true){
                            $scope.$emit('attachment_id', result.attachment_id);
                            data.file.size = data._file.size;
                            data.progress = 100;
                            data.isSuccess = true;
                            data.isUploaded = true;
                            uploader.progress += 100/uploader.queue.length;
                        }else{
                            data.upload();
                        }
                    });
            });
        });
    };
    uploader.onSuccessItem = function(fileItem, response, status, headers) {
        $scope.$emit('attachment_id', response.attachment_id);
    };
    $scope.$on('clearQueue', function() {
        uploader.clearQueue();
    });
}]);