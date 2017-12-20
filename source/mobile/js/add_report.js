angular.module('app')
    .controller('addReportCtrl', ['$scope', 'ajax','$timeout',function ($scope, ajax,$timeout) {
        $scope.reports = [];
        $scope.address_row = [];
        $scope.report_info = {};
        $scope.report_info.project = [];
        $scope.project_detail = [];
        $scope.has_report_info = false;
        $scope.isLocation = false;
        $scope.report_info.gender = 1;
        $scope.new_report_info = {};
        
        $scope.url = window.location.href;
        $scope.is_agent ='/' + $scope.url.substring($scope.url.lastIndexOf('/') + 1);
        if($scope.is_agent != '/1'){
            $scope.is_agent = '';
        }

        // window.location.href = $scope.url + '/' + new Date().getTime();
        var lastStr = $scope.url.substring($scope.url.lastIndexOf('/') + 1);
        $scope.url_agent = $scope.url.indexOf('/1/');
        if(isNaN(parseInt(lastStr))){
            window.location.href = $scope.url + '/' + new Date().getTime();
        }else if(lastStr == 1){
            window.location.href = $scope.url + '/' + new Date().getTime();
        }
        //获取当前页面高度
        var winHeight = $(window).height();
        $(window).resize(function(){
            var thisHeight=$(this).height();
            if(winHeight - thisHeight >50){
                //窗口发生改变(大),故此时键盘弹出
                //当软键盘弹出，在这里面操作
                $('.bottom-btn').addClass('activity').removeClass('footer');
            }else{
                //窗口发生改变(小),故此时键盘收起
                //当软键盘收起，在此处操作
                $('.bottom-btn').addClass('footer').removeClass('activity');
            }
        });
        
        var date = new Date();
        $scope.current_date = date.getFullYear() + '/' + parseInt(date.getMonth() + 1) + '/' + date.getDate();

        //获取报告编号
        $scope.fill_report_info = function () {
            $('.location-img').show();
            // $scope.isLocation = false;
            var report_number = $('.code-result').val();
            $scope.report_info.number = report_number;

            ajax.req('POST', 'user/check_report', {number: report_number})
                .then(function (response) {
                    if (response.success) {
                        $scope.has_report_number = true;
                        $scope.order_commodity_id = response.data.report.order_commodity_id;

                        $scope.project_num = response.data.project_num;
                        $scope.project_lists = response.data.project;
                        if (response.data.report.report_status == '1'){
                            $scope.isLocation = true;
                            $('.address-parent').css('display','inline-block');
                            $('.cancel-addr').hide();
                            $scope.has_report_info = true;
                            $scope.project_num = response.data.project_num;
                            response.data.report.phone = parseInt(response.data.report.phone);
                            $scope.report_info = response.data.report;
                            $('#Weight-Height').val($scope.report_info.height + 'cm/' + $scope.report_info.weight + 'kg');

                            $('.address-detail').text($scope.report_info.province + ' ' + $scope.report_info.city + ' ' + $scope.report_info.district).css('display', 'inline-block');
                            if ($scope.report_info.gender == 1) {
                                $('#SEX').val('男')
                            } else {
                                $('#SEX').val('女')
                            }
                            if ($scope.report_info.smoking == 0) {
                                $('#Smoking').val('否')
                            } else {
                                $('#Smoking').val('是')
                            }
                           /* if ($scope.report_info.blood_relationship == 10) {
                                $('#Relationship').val('父亲')
                            } else if ($scope.report_info.blood_relationship == 20) {
                                $('#Relationship').val('母亲')
                            } else if ($scope.report_info.blood_relationship == 30) {
                                $('#Relationship').val('哥哥')
                            } else if ($scope.report_info.blood_relationship == 40) {
                                $('#Relationship').val('弟弟')
                            } else if ($scope.report_info.blood_relationship == 50) {
                                $('#Relationship').val('姐姐')
                            } else if ($scope.report_info.blood_relationship == 60) {
                                $('#Relationship').val('妹妹')
                            } else if ($scope.report_info.blood_relationship == 70) {
                                $('#Relationship').val('爷爷')
                            } else if ($scope.report_info.blood_relationship == 80) {
                                $('#Relationship').val('奶奶')
                            } else if ($scope.report_info.blood_relationship == 90) {
                                $('#Relationship').val('舅舅')
                            } else if ($scope.report_info.blood_relationship == 100) {
                                $('#Relationship').val('叔叔')
                            } else if ($scope.report_info.blood_relationship == 110) {
                                $('#Relationship').val('阿姨')
                            } else if ($scope.report_info.blood_relationship == 120) {
                                $('#Relationship').val('姑姑')
                            } else if ($scope.report_info.blood_relationship == 130) {
                                $('#Relationship').val('本人')
                            } else if ($scope.report_info.blood_relationship == 140) {
                                $('#Relationship').val('爱人')
                            } else if ($scope.report_info.blood_relationship == 150) {
                                $('#Relationship').val('孩子')
                            } else {
                                $('#Relationship').val('其他')
                            }*/
                            $('.address-parent').show();
                            $('#province').hide();
                            $('#city').hide();
                            $('#district').hide();
                            $('.location-img').hide();
                            $('.save-userinfo button').prop('disabled', true);

                        }  else{
                            $('.address-parent').hide();
                            $('#province').show();
                            $('#city').show();
                            $('#district').show();
                            $('.location-img').show();
                            $('.save-userinfo button').prop('disabled', false);
                            }
                        if($scope.isLocation){
                            $('.address-parent').show();
                            $('#province').hide();
                            $('#city').hide();
                            $('#district').hide();
                            $('.location-img').hide();
                            $('.location-img').css('visibility', 'hidden');
                        }else if(!$scope.isLocation){
                            $('.address-parent').hide();
                            $('#province').show();
                            $('#city').show();
                            $('#district').show();
                            $('.location-img').show();
                            $('.location-img').css('visibility', 'visible');
                        }
                        singlePage.open('#page_modify');
                    } else {
                        var popConfirm=new Alert("查询不到您输入的样本码</br>若样本码输入无误</br>可拨打400-100-3908联系客服处理",{
                            onClickOk:function(e){
                                $('.alert').remove();
                                $('.mask').remove();
                            },onClickCancel:function(e){

                            }
                        });
                        popConfirm.show();
                        $('.alert-handler').find('a').eq(0).prop('href','tel:400-100-3908');
                        $('.alert-handler').find('a').eq(0).text('拨打');
                        $('.alert-handler').find('a').eq(1).text('取消');
                    }
                });
        };
        //添加报告信息初始化
        $scope.init_report_form = function () {
            $scope.has_report_info = false;
            $scope.has_report_number = false;
            $scope.isLocation = false;
            $scope.templateName = '';
            $scope.report_info.name = '';
            $('button.submit-btn').prop('disabled', true);
            $scope.report_info = {};
            $('#Weight-Height').val('');
            $('.address-detail').text('');
            $('.address-parent').show();
            $('.cancel-addr').hide();
            $('#SEX').val('');
            $('#Smoking').val('');
            $('#Relationship').val('');
            $scope.initAddress();
            $('#province').val("");
            $("#city").val("");
            $('#district').val("");

        };

        $scope.$watch('report_info.identity_card', function (nv) {
            if(nv && nv.length == 18 && $scope.report_info.report_status != 1){

                $scope.report_info.birth = nv.substr(6,4) + '-' + nv.substr(10,2) + '-' + nv.substr(12, 2);
                // var _reTimeReg = /^(?:19|20)[0-9][0-9]-(?:(?:0[1-9])|(?:1[0-2]))-(?:(?:[0-2][1-9])|(?:[1-3][0-1]))$/;
                /*if(!_reTimeReg.test(birth)){
                    prompt.setText("请填写正确身份证号");
                    prompt.show();
                    return false;
                }else{*/
                    // $scope.report_info.birth = birth;
                // }
                if(nv[16]%2 == 0){
                    $scope.report_info.gender = 0;
                }else{
                    $scope.report_info.gender = 1;
                }
            }else if ((nv == '' || nv == undefined) && $scope.report_info.report_status != 1 ){
                $scope.report_info.birth = "";
                $scope.report_info.gender = 3;
            }
        });

        //保存报告信息
        $scope.check_report_info = function (id) {
            /*if($('#SEX').val() == '男'){
                $scope.report_info.gender = 1;
            }else{
                $scope.report_info.gender = 0;
            }*/

            $scope.new_report_info = {};
            $scope.report_info.number = $('.code-result').val();
            if ($scope.report_info.number === undefined || $scope.report_info.number === "" || $scope.report_info.number === null) {
                var popAlert=new Alert("报告编号不能为空",{"提示":true});
                popAlert.show();
                return;
            }
            if ($scope.report_info.name === undefined || $scope.report_info.name === "" || $scope.report_info.name === null) {
                var popAlert=new Alert("姓名不能为空",{"提示":true});
                popAlert.show();
                return;
            }
            if ($scope.report_info.phone === undefined || $scope.report_info.phone === "" || $scope.report_info.phone === null) {
                var popAlert=new Alert("电话不能为空",{"提示":true});
                popAlert.show();
                return;
            }else {
                var regu = /^1(3|4|5|7|8)\d{9}$/;
                var re = new RegExp(regu);
                if (!re.test($scope.report_info.phone)) {
                    var popAlert=new Alert("请填写正确手机号码",{"提示":true});
                    popAlert.show();
                    return;
                }
            }
            if ($scope.report_info.gender === undefined || $scope.report_info.gender === "" || $scope.report_info.gender === null) {
                var popAlert=new Alert("性别不能为空",{"提示":true});
                popAlert.show();

                return;
            }
            if ($scope.report_info.birth === undefined || $scope.report_info.birth === "" || $scope.report_info.birth === null) {
                var popAlert=new Alert("出生年月不能为空",{"提示":true});
                popAlert.show();
                /*prompt.setText("出生年月不能为空");
                prompt.show();*/
                return;
            }
            // if ($scope.report_info.smoking == undefined || $scope.report_info.smoking == "") {
            //     var popAlert=new Alert("是否吸烟选项不能为空",{"提示":true});
            //     popAlert.show();
            //     /*prompt.setText("是否吸烟选项不能为空");
            //     prompt.show();*/
            // }


            var idCard = /^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X|x)$/;
            var idcaedTest = new RegExp(idCard);
            if($scope.report_info.identity_card && !idcaedTest.test($scope.report_info.identity_card)){
                var popAlert=new Alert("请填写正确身份证号码",{"提示":true});
                popAlert.show();
                return;
            }
            /*if ($scope.report_info.identity_card == undefined || $scope.report_info.identity_card == "") {
                prompt.setText("身份证号不能为空");
                prompt.show();
                return;
            }*/
            // if ($scope.report_info.height === undefined || $scope.report_info.height === "") {
            //     var popAlert=new Alert("身高不能为空",{"提示":true});
            //     popAlert.show();
            //     return;
            // }
            // if ($scope.report_info.weight === undefined || $scope.report_info.weight === "") {
            //     var popAlert=new Alert("体重不能为空",{"提示":true});
            //     popAlert.show();
            //     return;
            // }
            if ($scope.report_info.address === undefined || $scope.report_info.address === "" || $scope.report_info.address === null) {
                var popAlert=new Alert("详细地址不能为空不能为空",{"提示":true});
                popAlert.show();
                return;
            }
            /*if ($scope.report_info.blood_relationship == undefined || $scope.report_info.blood_relationship == "") {
             prompt.setText("血缘关系不能为空不能为空");
             prompt.show();
             return;
             }*/
            if ($scope.report_info.province == undefined || $scope.report_info.province == "" || $scope.report_info.province_code == undefined || $scope.report_info.province_code == "" || $scope.report_info.province === null || $scope.report_info.province_code === null) {
                var popAlert=new Alert("请选择省份",{"提示":true});
                popAlert.show();
                return;
            }
            if ($scope.report_info.city == undefined || $scope.report_info.city == "" || $scope.report_info.city_code == undefined || $scope.report_info.city_code == "" || $scope.report_info.city === null || $scope.report_info.city_code === null) {
                var popAlert=new Alert("请选择城市",{"提示":true});
                popAlert.show();
                return;
            }
            $scope.new_report_info = $scope.report_info;
            return true;
        };

        $scope.save_report_info = function () {
            if($scope.check_report_info()){
                var popConfirm=new Alert("确认保存检测人信息？",{
                    "onClickOk":function(e){
                        $('.add-report').show();
                        // e.hide();
                        $('.mask').remove();
                        $('.alert').remove();
                        singlePage.close('#page_modify');
                    },"onClickCancel":function(e){
                        // e.hide();
                        $('.mask').remove();
                        $('.alert').remove();
                    }
                });
                popConfirm.show();


            }
        };
        $scope.sampleNumber = '';

        $scope.chech_list = {};
        $scope.project_name = [];
        $scope.check_project_num = function (e, id, template_id, project_num) {
            if(!$scope.chech_list[template_id] || $scope.chech_list[template_id] == ''){
                $scope.chech_list[template_id] = [];
            }
            angular.forEach($scope.chech_list, function (data, index) {
                var eq = data.indexOf(id);
                if($(e.target).hasClass('active') && eq != -1 && index == template_id){
                    data.splice(eq, 1);
                    $(e.target).removeClass('active active1 active2');
                }else if(!$(e.target).hasClass('active') && eq == -1 && index == template_id){
                    data.push(id);
                    if($scope.url_agent != -1){
                        $(e.target).addClass('active active2');
                    }else{
                        $(e.target).addClass('active active1');
                    }
                }

                if ($scope.chech_list[template_id].length == project_num) {
                    $(e.target).parent('.project-list').find("button").prop('disabled', true);
                    $(e.target).parent('.project-list').find('.active').prop('disabled', false);
                } else {
                    $(e.target).parent('.project-list').find("button").prop('disabled', false);
                    $('button.submit-btn').prop('disabled', false);
                }

            });

            $scope.templateName = $scope.project_lists[0][0].template_name + '(' + $scope.project_lists[0].length + '选' + $scope.project_lists[0][0].project_num + ')···';
            console.log($scope.chech_list);
        };

        //项目提示信息
        $scope.project_hinter = function (e) {
            if ($(e.target).siblings('.project-descript').css('display') == 'none') {
                $('.project-descript').css('display', 'none');
                $(e.target).siblings('.project-descript').css('display', 'inline-block')
            } else {
                $(e.target).siblings('.project-descript').css('display', 'none');
            }
        };
        var prompt = new Prompt();
        //启用性别选择
        var view_gender = {
            /*=========================
             Model
             ===========================*/
            initializegender: function () {
                /*DOM*/
                this.textSp = document.getElementById("SEX");
                $scope.report_info.gender = 1;
                /*Plugin*/
                this.scrollpicker = {};
                this.scrollpicker.hasEvent = false;
                this.nums = [
                    {'key': 'I', 'value': '男'},
                    {'key': 'II', 'value': '女'},
                ];

                /*Data*/

                /*Render*/
                this.render();

                /*Events*/
                this._attach();
            },
            render: function () {
                this._initPlugin();
            },
            /*=========================
             Plugin
             ===========================*/
            _initScrollPicker: function () {
                var self = this;
                this.scrollpicker = new Scrollpicker({
                    "parent": "article",
                    "onClickDone": function (e) {
                        //获得全部选中项
                        //console.log(e.activeOptions);
                        //打印值
                        var activeText = "";
                        e.activeOptions.forEach(function (n, i, a) {
                            if (i == e.activeOptions.length - 1) {
                                activeText += n["value"];
                            } else {
                                activeText += n["value"] + "-";
                            }
                        });
                        self.textSp.value = activeText;
                        if (self.textSp.value == '男') {
                            $scope.report_info.gender = 1;
                        }
                        else {
                            $scope.report_info.gender = 0;
                        }
                        e.hide();
                    },
                    "onClickCancel": function (e) {
                        e.updateSlots();
                        e.hide();
                    }

                });
            },
            _addScrollpickerData: function () {
                this.scrollpicker.addSlot(this.nums);
            },
            _initPlugin: function () {
                this._initScrollPicker();
                this._addScrollpickerData();
            },
            /*=========================
             Events
             ===========================*/
            _attach: function (e) {
                var self = this;
                if (!self.textSp.hasEvent) {
                    this.textSp.addEventListener("click", function (e) {
                        self._onClickTextSp(e);
                    }, false);
                    self.textSp.hasEvent = true;
                }
            },
            /*=========================
             Event Handler
             ===========================*/
            _onClickTextSp: function (e) {
                this.scrollpicker.show();
            }
        };
        // view_gender.initializegender();

        //启用是否吸烟
        // var view_smoking = {
        //     /*=========================
        //      Model
        //      ===========================*/
        //     initializesmoke: function () {
        //         /*DOM*/
        //         this.textSp = document.getElementById("Smoking");
        //         $scope.report_info.smoking = 1;
        //         /*Plugin*/
        //         this.scrollpicker = {};
        //         this.scrollpicker.hasEvent = false;
        //         this.nums = [
        //             {'key': 'I', 'value': '是'},
        //             {'key': 'II', 'value': '否'}
        //         ];
        //
        //         /*Data*/
        //
        //         /*Render*/
        //         this.render();
        //
        //         /*Events*/
        //         this._attach();
        //     },
        //     render: function () {
        //         this._initPlugin();
        //     },
        //     /*=========================
        //      Plugin
        //      ===========================*/
        //     _initScrollPicker: function () {
        //         var self = this;
        //         this.scrollpicker = new Scrollpicker({
        //             "parent": "article",
        //             "onClickDone": function (e) {
        //                 //获得全部选中项
        //                 //console.log(e.activeOptions);
        //                 //打印值
        //                 var activeText = "";
        //                 e.activeOptions.forEach(function (n, i, a) {
        //                     if (i == e.activeOptions.length - 1) {
        //                         activeText += n["value"];
        //                     } else {
        //                         activeText += n["value"] + "-";
        //                     }
        //                 });
        //                 self.textSp.value = activeText;
        //                 if (self.textSp.value == '是') {
        //                     $scope.report_info.smoking = 1;
        //                 }
        //                 else {
        //                     $scope.report_info.smoking = 0;
        //                 }
        //                 /*console.log($scope.report_info.smoking);*/
        //
        //                 e.hide();
        //             },
        //             "onClickCancel": function (e) {
        //                 e.updateSlots();
        //                 e.hide();
        //             }
        //
        //         });
        //     },
        //     _addScrollpickerData: function () {
        //         // this.scrollpicker.addSlot(this.nums1,'','','d');//数据,样式,默认value，默认key
        //         this.scrollpicker.addSlot(this.nums);
        //     },
        //     _initPlugin: function () {
        //         this._initScrollPicker();
        //         this._addScrollpickerData();
        //     },
        //     /*=========================
        //      Events
        //      ===========================*/
        //     _attach: function (e) {
        //         var self = this;
        //         if (!self.textSp.hasEvent) {
        //             this.textSp.addEventListener("click", function (e) {
        //                 self._onClickTextSp(e);
        //             }, false);
        //             self.textSp.hasEvent = true;
        //         }
        //     },
        //     /*=========================
        //      Event Handler
        //      ===========================*/
        //     _onClickTextSp: function (e) {
        //         this.scrollpicker.show();
        //     }
        // };
        // view_smoking.initializesmoke();

        //生日
        var birthView = {
            /*=========================
             Model
             ===========================*/
            initialize: function () {
                /*Data*/

                /*DOM*/
                this.textDate = document.querySelector(".SID-Date");

                /*Plugin*/
                this.spDate;
                //定义5分钟间隔的分钟数据
                this.minutesData = [];
                this.minuteInterval = 5;
                for (var minute = 0; minute < 60; minute = minute + this.minuteInterval) {
                    //if(minute<10)minute="0"+minute;
                    var tempMinute = minute < 10 ? "0" + minute : minute;
                    this.minutesData.push({"key": tempMinute, "value": tempMinute + "分", "flag": "time"});
                }

                /*Render*/
                this.render();

                /*Events*/
                this._attach();
            },
            render: function () {
                this._initPlugin();
            },
            /*=========================
             Plugin
             ===========================*/
            _newSpDate: function (defaultYMD) {
                var self = this;
                this.spDate = new SpDate({
                    viewType: "date",
                    yearClass: "text-center",
                    monthClass: "text-center",
                    dayClass: "text-center",
                    onClickDone: function (e) {
                        $scope.report_info.birth = e.activeText;
                        self.textDate.value = e.activeText;


                        e.hide();
                    },
                    onShowed: function (e) {
                        //e.setIsClickMaskHide(true);
                    },
                    onHid: function (e) {
                        e.destroy();
                    }
                });
                if (defaultYMD && defaultYMD.length > 0) {
                    this.spDate.setDefaultYear(defaultYMD[0]);
                    this.spDate.setDefaultMonth(defaultYMD[1]);
                    this.spDate.setDefaultDay(defaultYMD[2]);
                    this.spDate.update();
                }else{
                    this.spDate.setDefaultYear('1980');
                    this.spDate.setDefaultMonth('01');
                    this.spDate.setDefaultDay('01');
                    this.spDate.update();
                }
            },
            _initPlugin: function () {

            },
            /*=========================
             Events
             ===========================*/
            _attach: function (e) {
                var self = this;
                this.textDate.onclick = function (e) {
                    self._onClickTextDate(e);
                };
            },
            /*=========================
             Event Handler
             ===========================*/
            _onClickTextDate: function (e) {
                var self = this;
                var defaultYMD = [];
                if (this.textDate.value != "") {
                    defaultYMD = this.textDate.value.split("-");
                }
                this._newSpDate(defaultYMD);
                setTimeout(function () {
                    self.spDate.show();
                }, 10);
               // self.spDate.show();
            }
        };
        birthView.initialize();
        //启用血缘关系选择
        /*var view_relation = {
            /!*=========================
             Model
             ===========================*!/
            initializerelation: function () {
                /!*DOM*!/
                this.textSp = document.getElementById("Relationship");

                /!*Plugin*!/
                this.scrollpicker = {};
                this.scrollpicker.hasEvent = false;
                this.nums = [
                    {'key': 130, 'value': '本人'},
                    {'key': 140, 'value': '爱人'},
                    {'key': 150, 'value': '小孩'},
                    {'key': 10, 'value': '父亲'},
                    {'key': 20, 'value': '母亲'},
                    {'key': 40, 'value': '弟弟'},
                    {'key': 50, 'value': '姐姐'},
                    {'key': 30, 'value': '哥哥'},
                    {'key': 60, 'value': '妹妹'},
                    {'key': 70, 'value': '爷爷'},
                    {'key': 80, 'value': '奶奶'},
                    {'key': 90, 'value': '舅舅'},
                    {'key': 100, 'value': '叔叔'},
                    {'key': 110, 'value': '阿姨'},
                    {'key': 120, 'value': '阿姨'},
                    {'key': 160, 'value': '其它'}
                ];

                /!*Data*!/

                /!*Render*!/
                this.render();

                /!*Events*!/
                this._attach();
            },
            render: function () {
                this._initPlugin();
            },
            /!*=========================
             Plugin
             ===========================*!/
            _initScrollPicker: function () {
                var self = this;
                this.scrollpicker = new Scrollpicker({
                    "parent": "article",
                    "onClickDone": function (e) {
                        //获得全部选中项
                        var activeText = "";
                        var activeKey = '';
                        e.activeOptions.forEach(function (n, i, a) {
                            if (i == e.activeOptions.length - 1) {
                                activeText += n["value"];
                                activeKey += n['key'];
                            } else {
                                activeText += n["value"] + "-";
                                activeKey += n['key'] + "-";
                            }
                        });
                        self.textSp.value = activeText;
                        $scope.report_info.blood_relationship = activeKey;
                        e.hide();
                    },
                    "onClickCancel": function (e) {
                        e.updateSlots();
                        e.hide();
                    },

                });
            },
            _addScrollpickerData: function () {
                this.scrollpicker.addSlot(this.nums);
            },
            _initPlugin: function () {
                this._initScrollPicker();
                this._addScrollpickerData();
            },
            /!*=========================
             Events
             ===========================*!/
            _attach: function (e) {
                var self = this;
                if (!self.textSp.hasEvent) {
                    this.textSp.addEventListener("click", function (e) {
                        self._onClickTextSp(e);
                    }, false);
                    self.textSp.hasEvent = true;
                }
            },
            /!*=========================
             Event Handler
             ===========================*!/
            _onClickTextSp: function (e) {
                this.scrollpicker.show();
            }
        };*/
        // view_relation.initializerelation();
        //启用身高体重选择
        var view_weight_height = {
            /*=========================
             Model
             ===========================*/
            initializewheight: function () {
                /*DOM*/
                this.textSp = document.getElementById("Weight-Height");

                /*Plugin*/
                this.scrollpicker = {};
                this.scrollpicker.hasEvent = false;
                this.nums1 = [];
                for (var heightInit = 80; heightInit <= 190; heightInit++) {
                    this.nums1.push({'key': heightInit, 'value': heightInit + 'cm'});
                }
                this.nums2 = [];
                for (var weightInit = 10; weightInit <= 200; weightInit++) {
                    this.nums2.push({'key': weightInit, 'value': weightInit + 'kg'});
                }

                /*Data*/

                /*Render*/
                this.render();

                /*Events*/
                this._attach();
            },
            render: function () {
                this._initPlugin();
            },
            /*=========================
             Plugin
             ===========================*/
            _initScrollPicker: function () {
                var self = this;
                this.scrollpicker = new Scrollpicker({
                    "parent": "article",
                    "onClickDone": function (e) {
                        //获得全部选中项
                        var activeText = "";
                        e.activeOptions.forEach(function (n, i, a) {
                            if (i == e.activeOptions.length - 1) {
                                activeText += n["value"];
                            } else {
                                activeText += n["value"] + "/";
                            }
                        });
                        self.textSp.value = activeText;
                        $scope.height_weight = self.textSp.value;
                        $scope.report_info.height = self.textSp.value.split("/")[0].replace('cm', '');
                        $scope.report_info.weight = self.textSp.value.split("/")[1].replace('kg', '');
                        /*console.log($scope.height_weight,$scope.report_info.height,$scope.report_info.weight);*/
                        e.hide();
                    },
                    "onClickCancel": function (e) {
                        e.updateSlots();
                        e.hide();
                    },

                });
            },
            _addScrollpickerData: function () {
                // this.scrollpicker.addSlot(this.nums1,'','','d');//数据,样式,默认value，默认key
                this.scrollpicker.addSlot(this.nums1, '', '150cm');
                this.scrollpicker.addSlot(this.nums2, '', '50kg');
            },
            _initPlugin: function () {
                this._initScrollPicker();
                this._addScrollpickerData();
            },
            /*=========================
             Events
             ===========================*/
            _attach: function (e) {
                var self = this;
                if (!self.textSp.hasEvent) {
                    this.textSp.addEventListener("click", function (e) {
                        self._onClickTextSp(e);
                    }, false);
                    self.textSp.hasEvent = true;
                }
            },
            /*=========================
             Event Handler
             ===========================*/
            _onClickTextSp: function (e) {
                this.scrollpicker.show();
            }
        };
        view_weight_height.initializewheight();


        //初始化滑动页
        var singlePage = new Page({
            "onLoad": function (e) {
                //var targetPageId;
                if (e.isRoot) {

                } else {

                }
            }
        });

      /*  $(document).on('change', '.code-result', function () {
            $scope.init_report_form();
            var report_number = $(this).val();
            ajax.req('POST', 'user/check_report', {number: report_number})
                .then(function (response) {
                    if (response.success) {
                        $scope.project_num = response.data.project_num;
                    }
                })
        });*/
        $('#confirm_submit').prop('disabled', false);
       $scope.$watch('sampleNumber', function (nv) {
            if(nv){
                $scope.init_report_form();
                // var report_number = nv;
                ajax.req('POST', 'user/check_report', {number: nv})
                    .then(function (response) {
                        if (response.success) {

                            angular.forEach(response.data.project, function (data,index) {
                                if(data[0].project_num == data.length){
                                    $scope.chech_list[data[0].template_id] = [];
                                    angular.forEach(data, function (sub_data, eq) {
                                        $scope.chech_list[data[0].template_id].push(sub_data.id);
                                    })
                                }
                            });

                            if(Object.keys($scope.chech_list).length == response.data.project.length){
                                $scope.projectFullNum = true;
                                $scope.templateName = response.data.project[0][0].template_name + '(' + response.data.project[0].length + '选' + response.data.project[0][0].project_num + ')···';
                            }else{
                                $scope.projectFullNum = false;
                            }

                            console.log($scope.chech_list);

                            $scope.order_commodity_id = response.data.report.order_commodity_id;
                            $('#confirm_submit').prop('disabled', false);
                            $scope.project_num = response.data.project_num;
                            $scope.new_report_info = response.data.report;
                            if(response.data.report.report_status == 1 ){

                                $scope.report_info.name = response.data.report.name;
                                $scope.templateName = response.data.project[0][0].template_name;
                                $('#confirm_submit').prop('disabled', true);
                                $('.submit-btn').prop('disabled', true);
                                //  angular.forEach(response.data.report.project, function (id, index) {
                                //     $('input[value=id]').prop('checked', true);
                                //     $('input[name="project"]').prop('disabled', true);
                                // });
                            }else{
                                // $scope.templateName = '';
                                $scope.report_info.name = '';
                                $('#confirm_submit').prop('disabled', false);
                                $('.submit-btn').prop('disabled', false);
                            }
                        }else{
                            $('#confirm_submit').prop('disabled', true);
                            $('.submit-btn').prop('disabled', true);
                        }
                    })
            }
        });
        //添加新报告查询
        $scope.pre_num = '';
        $scope.add_report_info = function (id, target) {
            var report_number = $scope.sampleNumber;
            if($scope.pre_num != report_number){
                $scope.chech_list = {};
            }
            $scope.pre_num = report_number;
            ajax.req('POST', 'user/check_report', {number: report_number})
                .then(function (response) {
                    if (response.success) {
                        $scope.has_report_number = true;
                        $scope.project_num = response.data.project_num;
                        $scope.project_lists = response.data.project;
                        console.log($scope.chech_list);
                        if(response.data.report.report_status == 1 && response.data.report.project && response.data.report.project != null && response.data.report.project != '' && response.data.report.project.length > 0){
                            $scope.chech_list[-1] = [];
                            angular.forEach(response.data.report.project, function (id, index) {
                                $scope.chech_list[-1].push(id);
                            });
                        }
                        if ($scope.has_report_number && response.data.project.length > 0) {
                            var openType = target || "";
                            singlePage.open(id, openType);
                            $('.project-name').show();

                            angular.forEach($scope.project_lists, function (data, index) {
                                if(data[0].project_num == data.length){
                                    $scope.chech_list[data[0].template_id] = [];
                                    angular.forEach(data, function (sub_data, eq) {
                                        $scope.chech_list[data[0].template_id].push(sub_data.id);
                                    });
                                }
                            });

                             if(Object.keys($scope.chech_list).length > 0){
                                 $scope.chech_list.all_id = [];
                                 angular.forEach($scope.chech_list, function (data) {
                                     angular.forEach(data, function (data_id) {
                                         if($scope.chech_list.all_id.indexOf(data_id) == -1){
                                             $scope.chech_list.all_id.push(data_id)
                                         }
                                     })
                                 });
                                 console.log($scope.chech_list);
                                angular.forEach($scope.project_lists, function (p_data, p_index) {
                                    var template_id = p_data[0].template_id;
                                    var project_num = p_data[0].project_num;
                                    angular.forEach(p_data, function (item, item_index) {
                                        if($scope.chech_list.all_id.indexOf(item.id) != -1){
                                            if($scope.url_agent != -1){
                                                item.checked = 'active active2';
                                            }else{
                                                item.checked = 'active active1';
                                            }
                                            item.disabled = false;
                                            if(response.data.report.report_status == 1){
                                                item.disabled = true;
                                                item.opacity = 0.5;
                                            }else{
                                                if(p_data[0].project_num == p_data.length){
                                                    item.disabled = true;
                                                    item.opacity = 0.5;
                                                }else {
                                                    item.disabled = false;
                                                    item.opacity = 1;
                                                }
                                            }
                                        }else if($scope.chech_list.all_id.length > 1 && $scope.chech_list.all_id.indexOf(item.id) == -1){

                                            item.checked = '';

                                            if(response.data.report.report_status == 1){
                                                item.disabled = true;
                                            }else{
                                                // if(!item.disabled){
                                                //     item.disabled = false;
                                                //     item.opacity = 1;
                                                // }
                                                item.disabled = false;
                                            }
                                            if($scope.chech_list[template_id] && ($scope.chech_list[template_id].length == project_num)){
                                                item.disabled = true;
                                            }else {
                                                if(response.data.report.report_status == 1){
                                                    item.disabled = true;
                                                }else{
                                                    // if(!item.disabled){
                                                    //     item.disabled = false;
                                                        // item.opacity = 1;
                                                    // }
                                                    item.disabled = false;
                                                }
                                            }
                                        }

                                    });
                                });
                                 // console.log($scope.project_lists);
                             }else if(Object.keys($scope.chech_list).length > 0 && response.data.report.report_status == 1 || response.data.report.report_status == 1){
                                 setTimeout(function () {
                                     $('.template-list button').prop('disabled',true);
                                 }, 10);
                             }else{
                                 setTimeout(function () {
                                     $('.template-list button').prop('disabled',false);
                                 }, 10);
                             }
                        }else{
                            var popConfirm=new Alert("无可选套餐",{
                                onClickOk:function(e){
                                    $('.alert').remove();
                                    $('.mask').remove();
                                },onClickCancel:function(e){

                                }
                            });
                            $('.alert-handler').find('a').eq(0).remove();
                            popConfirm.show();
                        }
                    }else{
                        var popConfirm=new Alert("查询不到您输入的样本码</br>若样本码输入无误</br>可拨打400-100-3908联系客服处理",{
                            onClickOk:function(e){
                                $('.alert').remove();
                                $('.mask').remove();
                            },onClickCancel:function(e){

                            }
                        });
                        popConfirm.show();
                        $('.alert-handler').find('a').eq(0).prop('href','tel:400-100-3908');
                        $('.alert-handler').find('a').eq(0).text('拨打');
                        $('.alert-handler').find('a').eq(1).text('取消');
                    }
                })
        };
        $scope.add_personal = function (id, target, child) {
            var openType = target || "";
            singlePage.open(id, openType);
            document.getElementById(child).focus();
            $scope.report_info.family_history_temp = $scope.report_info.family_history;
            $scope.report_info.personal_history_temp = $scope.report_info.personal_history;
        };

        //微信扫码
        $scope.scan = function () {
            $scope.sampleNumber = "";
            wx.scanQRCode({
                needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
                scanType: ["qrCode", "barCode"], // 可以指定扫二维码还是一维码，默认二者都有
                success: function (res) {
                    var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
                    console.log('xsx');
                    result = result.split(",");
                    if (result.length > 1) {
                        $('.code-result').val(result[1]);
                        $scope.sampleNumber = result[1];
                    } else {
                        alert('请扫描正确的条形码');
                    }
                }
            });
        };

        $('#search-report-inputbox').bind('search', function () {
            alert('查询成功');
        });
        $scope.show_form_flag = false;
        $scope.show_form = function () {
            $scope.show_form_flag = !$scope.show_form_flag;
        };

        $scope.saveMedicationHistory_family = function (id) {
            $scope.report_info.family_history = $scope.report_info.family_history_temp;
            singlePage.close(id)
        };
        $scope.saveMedicationHistory_personal = function (id) {
            $scope.report_info.personal_history = $scope.report_info.personal_history_temp;
            singlePage.close(id)
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
        $scope.initAddress = function () {
            district.search('中国', function (status, result) {
                if (status == 'complete') {
                    if (result.districtList.length > 0) {
                        $scope.getAdministrativeRegion(result.districtList[0]);
                    } else {
                        console.log('获取省级行政区失败');
                    }
                }
            });
        };
        /**
         * 解析省市区信息
         * @param data
         */
        $scope.count = 0;
        $scope.getAdministrativeRegion = function (data, city_code, district_code) {

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

                if (level == 'province') {
                    contentSub = new Option('-- 市 --');
                } else if (level == 'city') {
                    contentSub = new Option('-- 区 --');
                } else {
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
                    contentSub = new Option(name);
                    contentSub.setAttribute("value", value);
                    contentSub.center = subList[i].center;
                    contentSub.adcode = subList[i].adcode;

                    document.querySelector('#' + levelSub).add(contentSub);
                }
                if (typeof(city_code) != 'undefined' && city_code != "" && levelSub == "city") {
                    $('#' + levelSub).val(city_code);
                    $scope.searchNextLevel($('#' + levelSub)[0], city_code, district_code);
                } else if (typeof(district_code) != 'undefined' && district_code != "" && levelSub == "district") {
                    $('#' + levelSub).val(district_code);
                }
            } else {
                if (level == "province") {
                    // 将市级、县级下拉列表置为不可用
                    $("#city").attr('disabled', 'disabled');
                    $("#district").attr('disabled', 'disabled');
                } else if (level == "city") {
                    // 将县级下拉列表置为不可用
                    $("#district").attr('disabled', 'disabled');
                }
            }
            $scope.count++;
        };
        /**
         * 根据当前所选省市搜索下级行政区域列表
         * @param obj
         * @param city_code 城市代码，编辑地址时初始化控件使用
         * @param district_code 区县代码，编辑地址时初始化控件使用
         */
        $scope.searchNextLevel = function (obj, city_code, district_code) {
            var option = obj[obj.options.selectedIndex];
            var keyword = option.text; //关键字
            var adcode = option.adcode;
            city_code = city_code || '';
            district_code = district_code || '';
            district.setLevel(option.value); //行政区级别
            //行政区查询
            //按照adcode进行查询可以保证数据返回的唯一性
            district.search(adcode, function (status, result) {
                if (status === 'complete') {
                    $scope.getAdministrativeRegion(result.districtList[0], city_code, district_code);
                }
            });
        };

        $scope.initAddress();

        //监听地址选择事件
        $('#province')[0].addEventListener('change', function () {
            var obj = this;
            $scope.isLocation = false;
            $scope.searchNextLevel(obj);
            $scope.report_info.province = obj[obj.options.selectedIndex].text;
            $scope.report_info.province_code = obj[obj.options.selectedIndex].value;
        }, false);

        $('#city')[0].addEventListener('change', function () {
            var obj = this;
            $scope.searchNextLevel(obj);
            $scope.isLocation = false;
            $scope.report_info.city = obj[obj.options.selectedIndex].text;
            $scope.report_info.city_code = obj[obj.options.selectedIndex].value;
            if ($scope.report_info.city_code == "120200" || $scope.report_info.city_code == "310200" || $scope.report_info.city_code == "500200") {
                $scope.report_info.district = '';
                $scope.report_info.district_code = '';
            }
        }, false);

        $('#district')[0].addEventListener('change', function () {
            var obj = this;
            $scope.searchNextLevel(obj);
            $scope.isLocation = false;
            $scope.report_info.district = obj[obj.options.selectedIndex].text;
            $scope.report_info.district_code = obj[obj.options.selectedIndex].value;
        }, false);

        var map, geolocation;
        //加载地图，调用浏览器定位服务
        map = new AMap.Map('container', {
            resizeEnable: true
        });
        $scope.location = function () {
            $scope.isLocation = true;
            map.plugin('AMap.Geolocation', function () {
                geolocation = new AMap.Geolocation({
                    enableHighAccuracy: true,//是否使用高精度定位，默认:true
                    timeout: 10000,          //超过10秒后停止定位，默认：无穷大
                    buttonOffset: new AMap.Pixel(10, 20),//定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
                    zoomToAccuracy: true,      //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
                    buttonPosition: 'RB'
                });
                map.addControl(geolocation);
                geolocation.getCurrentPosition();
                AMap.event.addListener(geolocation, 'complete', onComplete);//返回定位信息
                AMap.event.addListener(geolocation, 'error', onError);      //返回定位出错信息
            });
        };
        $scope.address_detail = '';
        //解析定位结果
        $scope.province_list = [];
        function onComplete(data) {
            $('#province').hide();
            $('#city').hide();
            $('#district').hide();
            $('.address-parent').show();
            var str = ['定位成功'];
            str.push('经度：' + data.position.getLng());
            str.push('纬度：' + data.position.getLat());
            if (data.accuracy) {
                str.push('精度：' + data.accuracy + ' 米');
            }//如为IP精确定位结果则没有精度信息
            str.push('是否经过偏移：' + (data.isConverted ? '是' : '否'));
            console.log(data.formattedAddress);
            $('.address-detail').text(data.addressComponent.province + ' ' + data.addressComponent.city + ' ' + data.addressComponent.district);
            // 获取省份信息
            $scope.report_info.province = data.addressComponent.province;
            $scope.report_info.province_code = data.addressComponent.adcode.substr(0, 2) + "0000";
            $scope.report_info.city = data.addressComponent.city;
            if ($scope.report_info.city == undefined || $scope.report_info.city == "") {
                // 直辖市市区名称
                $scope.report_info.city = data.addressComponent.province + "辖区";
            }
            $scope.report_info.city_code = data.addressComponent.adcode.substr(0, 4) + "00";
            $scope.report_info.district = data.addressComponent.district;
            $scope.report_info.district_code = data.addressComponent.adcode;
            $scope.isLocation = true;

            if ($scope.report_info.province == undefined || $scope.report_info.province == "") {
                $('.address-detail').text(data.addressComponent.province + ' ' + data.addressComponent.city + ' ' + data.addressComponent.district);
            }

            $scope.province_list = $('#province').find('option');
            $scope.city_list = $('#city').find('option');
            $scope.district_list = $('#district').find('option');
            $('.location-img').css('visibility', 'hidden');
            $('.cancel-addr').show();
        }

        //解析定位错误信息
        function onError(data) {
            console.log('定位失败！')
        }

        $scope.show_address_select = function () {
            $('.address-parent').hide();
            $('#province').show();
            $('#city').show();
            $('#district').show();
            $('.location-img').css('visibility', 'visible');
            $('.location-img').css('display', 'inline-block');
        };

        //保存检测项目
        $scope.save_project = function (id) {
            $scope.save = false;
            if(!$scope.save){
                angular.forEach($scope.project_lists, function (second_list, index) {
                    var template_id = second_list[0].template_id;
                    var template_num = second_list[0].project_num;
                    if(!$scope.chech_list[template_id] || $scope.chech_list[template_id].length < template_num){
                        var popConfirmCommit=new Alert("请选择对应数量的模板！",{
                            "onClickOk":function(e){
                                $('.mask').remove();
                                $('.alert').remove();
                            },"onClickCancel":function(e){
                                $('.mask').remove();
                                $('.alert').remove();
                            }
                        });
                        popConfirmCommit.show();
                        $scope.save = true;
                        return;
                    }
                });
            }
            if(!$scope.save){
                singlePage.close(id);
                $('.add-report').show();
                $('.project-name').hide();
                $scope.projectFullNum = true;
            }else{
                $scope.projectFullNum = false;
            }
            // $scope.project_name = $scope.project_name.join(',');
        };
        //提交报告信息
        $scope.submit_report_info = function () {
            if(!$scope.projectFullNum){
                var popConfirmCommit=new Alert("请选择对应数量的模板！",{
                    "onClickOk":function(e){
                        $('.mask').remove();
                        $('.alert').remove();
                    },"onClickCancel":function(e){
                        $('.mask').remove();
                        $('.alert').remove();
                    }
                });
                popConfirmCommit.show();
                return;
            }
            var popConfirmCommit=new Alert("确认提交检测人信息？",{
                "onClickOk":function(e){
                    $('.mask').remove();
                    $('.alert').remove();
                    $scope.commit_report();
                },"onClickCancel":function(e){
                    $('.mask').remove();
                    $('.alert').remove();
                }
            });
            popConfirmCommit.show();
        };

        $scope.commit_report = function () {

            $scope.result_id = [];
            angular.forEach($scope.chech_list, function (data, index) {
               if(index != '-1' && index != 'all_id'){
                   angular.forEach(data, function (id) {
                       $scope.result_id.push(id);
                   })
               }
            });
            // if($scope.project_num && $scope.result_id.length == 0) {
            //     var popAlert = new Alert("请选择检测项目", {"提示": true});
            //     popAlert.show();
            //     return;
            // }
            // if(!$scope.project_num && $scope.result_id.length == 0){
            //     $scope.new_report_info.project = '';
            // }else{
            //     $scope.new_report_info.project = $scope.chech_list.join(',');
            // }
            if($scope.result_id.length == 0){
                var popAlert = new Alert("请选择检测项目", {"提示": true});
                popAlert.show();
                return;
            }
            $scope.new_report_info.project = $scope.result_id.join(',');
            $scope.new_report_info.order_commodity_id = $scope.order_commodity_id;
            console.log($scope.new_report_info);
            ajax.req('POST', 'user/add_report_userInfo', $scope.new_report_info).then(function (response) {
                if (response.success) {
                    singlePage.open('#submit_success');
                    $scope.init_report_form();
                    $scope.sampleNumber = '';
                }else {
                    if (response.error) {
                        var popAlert=new Alert(response.error,{"提示":true});
                        popAlert.show();
                    }
                    else {
                        var popAlert=new Alert(response.msg,{"提示":true});
                        popAlert.show();
                    }
                }
            })
        };
        // singlePage.open('#submit_success');

        //返回我得报告
        $scope.closePage = function (id) {
            singlePage.close(id);
            $('.mask').remove();
            $('.alert').remove();
           // $('.add-report').show();
        };
        $scope.closeNestStepPage = function (id) {
            singlePage.close(id);
        };

        $scope.close_effect = function (id) {
            singlePage.close(id);
        };
        $scope.open_effect = function (id) {
            singlePage.open(id);
        };
        $scope.select_project = function (id, data) {
            singlePage.open(id);
            //$('.add-report').hide();
        };
        $scope.close_project = function (id, data) {
            // singlePage.close(id);
            history.go(-1);
            //$('.add-report').show();
            //$('.project-header').hide();

        };
        
        // $scope.openAggrement = function (id) {
        //     singlePage.open(id);
        // };

        $scope.open_description = function (id, project) {
            singlePage.open(id);
            //$('.project-header').hide();
            //$('.description-header').show();
            $scope.project_detail = project;
        };
        $scope.close_description = function (id, project) {
            singlePage.close(id);
           // $('.project-header').show();
            //$('.description-header').hide();
            $scope.project_detail = [];
        }
    }]);
