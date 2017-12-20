angular.module('app')
    .controller('familyInfoCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        $scope.family_info = [];
        $scope.treat = false;
        $scope.radiotherapy = false;
        $scope.chemotherapy = false;
        $scope.targeted_therapies = false;

        var prompt=new Prompt();
        var singlePage = new Page({
            "onLoad":function(e){
                var targetPageId;
            }
        });

        /**
         * 获取亲属id
         */
        $scope.url= window.location.href;
        $scope.family_id = $scope.url.substring($scope.url.lastIndexOf('/') + 1);

        /**
         * 初始化亲属信息
         */
        $scope.initData = function () {
            ajax.req('POST', 'user/get_family_info_by_id/' + $scope.family_id)
                .then(function (data) {
                    if(data.success){
                        $scope.family_info = data.data;
                        $scope.family_info.clinical_history_arr = data.data.clinical_history.split('-');
                        angular.forEach($scope.family_info.clinical_history_arr, function (clinical_history) {
                            if(clinical_history == '10'){
                                $scope.treat = true;
                            }else if(clinical_history == '20'){
                                $scope.radiotherapy = true;
                            }else if(clinical_history == '30'){
                                $scope.chemotherapy = true;
                            }else if(clinical_history == '40'){
                                $scope.targeted_therapies = true;
                            }
                        });
                        if($scope.family_info.relation == '10'){
                            $scope.family_info.relation = '父亲';
                        }else if($scope.family_info.relation == '20'){
                            $scope.family_info.relation = '母亲';
                        };
                        if($scope.family_info.health_status == '10'){
                            $scope.family_info.health_status = '健康';
                        }else if($scope.family_info.health_status == '20'){
                            $scope.family_info.health_status = '亚健康';
                        }else if($scope.family_info.health_status == '30'){
                            $scope.family_info.health_status = '疾病';
                        }
                    }
                });
        };
        $scope.initData();
        /**
         * 添加亲属
         */

        //保存出生日期
        $scope.saveBirthday = function(data){
            $scope.family_info.birth = data;
        };

        //修改具体用药史和疗效
        $scope.saveMedicationHistory = function (id) {
            // $scope.initData();
            if ($scope.medication_history_info == undefined || $scope.medication_history_info == ''){
                prompt.setText("请输入用药史和疗效");
                prompt.show();
            }else{
                $scope.family_info.medication_history = angular.copy($scope.medication_history_info);
                singlePage.close(id);
            }
        };
        //退出弹框
        $scope.backTab = function(id){ singlePage.close(id)};
        //修改血缘关系
        $scope.uprelation = function (relation_data) {
            $scope.family_info.relation = relation_data;
        };

        //修改健康状态
        $scope.HealthStatus = function (health_data) {
            $scope.family_info.health_status = health_data;
        };

        /**
         * 修改亲属信息
         */
        $scope.update_family_member = function () {

            if($scope.family_info.relation == '父亲'){
                $scope.relation = '10'
            }else if($scope.family_info.relation == '母亲'){
                $scope.relation = '20'
            }
            if($scope.family_info.health_status == '健康'){
                $scope.health_status = '10'
            }else if($scope.family_info.health_status == '亚健康'){
                $scope.health_status = '20'
            }else if($scope.family_info.health_status == '疾病'){
                $scope.health_status = '30'
            }

            $scope.clinical_history = '';
            $scope.member_treatment = [];
            $('input[type="checkbox"]').each(function () {
                if($(this).prop("checked") == true){
                    $scope.member_treatment.push($(this).val());
                }
            });
            $scope.clinical_history = $scope.member_treatment.join('-');
            ajax.req('POST', 'user/update_family_relation_by_id/' + $scope.family_id, {
                name: $scope.family_info.name,
                gender: $scope.family_info.gender,
                phone: $scope.family_info.phone,
                birth: $scope.family_info.birth,
                identity_card: $scope.family_info.identity_card,
                medication_history: $scope.family_info.medication_history,
                clinical_history: $scope.clinical_history,
                relation: $scope.relation,
                health_status:$scope.health_status
            }).then(function(response){
                if (response.success){
                    prompt.setText(response.msg);
                    prompt.show();
                    history.go(-1);
                }else{
                    prompt.setText(response.msg);
                    prompt.show();
                }
            })

        };

        /**
         * 新添加亲属信息
         */

        //添加出生日期
        $scope.addFamilyBirth = function (data) {
            prompt.setText("修改出生日期成功");
            prompt.show();
            $scope.family_info.birth = data;
        };

        //添加用工药史和疗效
        $scope.addMedicationHistory = function (id) {
            console.log($scope.family_info.gender);
            $scope.gender = $scope.family_info.gender;
            if ($scope.medication_history_info == undefined || $scope.medication_history_info == ''){
                prompt.setText("请输入相关信息");
                prompt.show();
            }else{
                prompt.setText("添加信息成功");
                prompt.show();
                $scope.family_info.medication_history = angular.copy($scope.medication_history_info);
                singlePage.close(id);
            }
        };
        //添加血缘关系
        $scope.addrelation = function (data) {
            if(data == '父亲'){
                data = '10';
            }else if(data == '母亲'){
                data = '20'
            }
            prompt.setText("添加信息成功");
            prompt.show();
            $scope.family_info.relation = data;
        };

        //添加健康状态
        $scope.addHealthStatus = function (data) {
            if(data == '健康'){
                data = '10';
            }else if(data == '亚健康'){
                data = '20'
            }else if(data == '疾病'){
                data = '30'
            }
            prompt.setText("添加信息成功");
            prompt.show();
            $scope.family_info.health_status = data;
        };


        // 添加治疗史
        /**
         * 添加亲属
         */
        $scope.add_family_member = function () {
            $scope.clinical_history = '';
            $scope.member_treatment = [];
            $('input[type="checkbox"]').each(function () {
                if($(this).prop("checked") == true){
                    $scope.member_treatment.push($(this).val());
                }
            });
            $scope.clinical_history = $scope.member_treatment.join('-');
            if($scope.family_info.name != undefined && $scope.family_info.gender != undefined && $scope.family_info.birth !=undefined && $scope.family_info.identity_card !=undefined && $scope.family_info.medication_history != undefined && $scope.clinical_history != undefined && $scope.family_info.relation != undefined && $scope.family_info.health_status != undefined){
                ajax.req('POST', 'user/add_family_relation', {
                    name: $scope.family_info.name,
                    gender: $scope.family_info.gender,
                    phone: $scope.family_info.phone,
                    birth: $scope.family_info.birth,
                    identity_card: $scope.family_info.identity_card,
                    medication_history: $scope.family_info.medication_history,
                    clinical_history: $scope.clinical_history,
                    relation: $scope.family_info.relation,
                    health_status:$scope.family_info.health_status
                }).then(function (data) {
                    if (data.success){
                        prompt.setText(data.msg);
                        prompt.show();
                        window.location.href = SITE_URL + 'weixin/user/my_family';
                    }else{
                        prompt.setText(data.error);
                        prompt.show();
                    }

                })
            }else{
                prompt.setText('请填写完整信息');
                prompt.show();
            }

        };

        /**
         * 删除亲属信息
         */
        var popConfirm=new Alert("确定要删除该亲属信息吗",{
            "onClickOk":function(e){
                ajax.req('POST', 'user/delete_family_relation_by_id/' + $scope.family_id)
                    .then(function (data) {
                        if(data.success){
                            prompt.setText("删除亲属信息成功");
                            prompt.show();
                            e.hide();
                            history.go(-1);

                        }else{
                            prompt.setText("服务器繁忙");
                            prompt.show();
                        }
                    })
            },"onClickCancel":function(e){
                console.log(e.target);
                e.hide();
            }
        });
        $scope.del_family_member = function () {
            popConfirm.show();
        };

        //显示弹出框
        $scope.show_mid_frame = function(id, target){
            var openType=target||"";
            singlePage.open(id,openType);
            // $scope.gender = $scope.family_info.gender;
            $scope.medication_history_info = angular.copy($scope.family_info.medication_history);
            document.getElementsByClassName('clinical_history_detail')[0].focus();
        };

        //查看报告
        $scope.goToUrl = function (id, target) {
            $('.personal-title').hide();
            $('#page_modify .titlebar').show();
            var openType=target||"";
            singlePage.open(id,openType);
        };

        var view={
            /*=========================
             Model
             ===========================*/
            initialize:function(){
                /*Data*/

                /*DOM*/
                this.textDate=document.querySelector(".SID-Date");

                /*Plugin*/
                this.spDate;
                //定义5分钟间隔的分钟数据
                this.minutesData=[];
                this.minuteInterval=5;
                for(var minute=0;minute<60;minute=minute+this.minuteInterval){
                    //if(minute<10)minute="0"+minute;
                    var tempMinute=minute<10?"0"+minute:minute;
                    this.minutesData.push({"key":tempMinute,"value":tempMinute+"分","flag":"time"});
                }

                /*Render*/
                this.render();

                /*Events*/
                this._attach();
            },
            render:function(){
                this._initPlugin();
            },
            /*=========================
             Plugin
             ===========================*/
            _newSpDate:function(defaultYMD){
                var self=this;
                this.spDate=new SpDate({
                    viewType:"date",
                    yearClass:"text-center",
                    monthClass:"text-center",
                    dayClass:"text-center",
                    onClickDone:function(e){
                        self.textDate.value=e.activeText;
                        if($scope.family_id != 'family_info'){
                            $scope.saveBirthday(e.activeText);
                        }else{
                            $scope.addFamilyBirth(e.activeText);
                        }

                        e.hide();
                    },
                    onShowed:function(e){
                        //e.setIsClickMaskHide(true);
                    },
                    onHid:function(e){
                        e.destroy();
                    }
                });
                if(defaultYMD && defaultYMD.length>0){
                    this.spDate.setDefaultYear(defaultYMD[0]);
                    this.spDate.setDefaultMonth(defaultYMD[1]);
                    this.spDate.setDefaultDay(defaultYMD[2]);
                    this.spDate.update();
                }
            },
            _initPlugin:function(){

            },
            /*=========================
             Events
             ===========================*/
            _attach:function(e){
                var self=this;
                this.textDate.onclick=function(e){
                    self._onClickTextDate(e);
                };
            },
            /*=========================
             Event Handler
             ===========================*/
            _onClickTextDate:function(e){
                var self=this;
                var defaultYMD=[];
                if(this.textDate.value!=""){
                    defaultYMD=this.textDate.value.split("-");
                }
                this._newSpDate(defaultYMD);
                setTimeout(function(){
                    self.spDate.show();
                },10);
                //self.spDate.show();
            }
        };
        var view_relation={
            /*=========================
             Model
             ===========================*/
            initializegender:function(){
                /*DOM*/
                this.textSp=document.getElementById("ID-Sp");

                /*Plugin*/
                this.scrollpicker={};
                this.scrollpicker.hasEvent=false;
                this.nums2=[
                    {'key':'I','value':'父亲'},
                    {'key':'II','value':'母亲'},
                ];

                /*Data*/

                /*Render*/
                this.render();

                /*Events*/
                this._attach();
            },
            render:function(){
                this._initPlugin();
            },
            /*=========================
             Plugin
             ===========================*/
            _initScrollPicker:function(){
                var self=this;
                this.scrollpicker=new Scrollpicker({
                    "parent":"article",
                    "onClickDone":function(e){
                        //获得全部选中项
                        //console.log(e.activeOptions);
                        //打印值
                        var activeText="";
                        e.activeOptions.forEach(function(n,i,a){
                            if(i==e.activeOptions.length-1){
                                activeText+=n["value"];
                            }else{
                                activeText+=n["value"]+"-";
                            }
                        });
                        self.textSp.value=activeText;
                        if($scope.family_id != 'family_info'){
                            $scope.uprelation(activeText);
                        }else{
                            $scope.addrelation(activeText);
                        }

                        e.hide();
                    },
                    "onClickCancel":function(e){
                        e.updateSlots();
                        e.hide();
                    },
                    
                });
            },
            _addScrollpickerData:function(){
                // this.scrollpicker.addSlot(this.nums1,'','','d');//数据,样式,默认value，默认key
                this.scrollpicker.addSlot(this.nums2);
            },
            _initPlugin:function(){
                this._initScrollPicker();
                this._addScrollpickerData();
            },
            /*=========================
             Events
             ===========================*/
            _attach:function(e){
                var self=this;
                if(!self.textSp.hasEvent){
                    this.textSp.addEventListener("click",function(e){
                        self._onClickTextSp(e);
                    },false);
                    self.textSp.hasEvent=true;
                }
            },
            /*=========================
             Event Handler
             ===========================*/
            _onClickTextSp:function(e){
                this.scrollpicker.show();
            }
        };
        var view_health_status={
            /*=========================
             Model
             ===========================*/
            initializehealth:function(){
                /*DOM*/
                this.textSp=document.getElementById("ID-Health");

                /*Plugin*/
                this.scrollpicker={};
                this.scrollpicker.hasEvent=false;
                this.nums3=[
                    {'key':'I','value':'健康'},
                    {'key':'II','value':'亚健康'},
                    {'key':'II','value':'疾病'}
                ];

                /*Data*/

                /*Render*/
                this.render();

                /*Events*/
                this._attach();
            },
            render:function(){
                this._initPlugin();
            },
            /*=========================
             Plugin
             ===========================*/
            _initScrollPicker:function(){
                var self=this;
                this.scrollpicker=new Scrollpicker({
                    "parent":"article",
                    "onClickDone":function(e){
                        //获得全部选中项
                        //console.log(e.activeOptions);
                        //打印值
                        var activeText="";
                        e.activeOptions.forEach(function(n,i,a){
                            if(i==e.activeOptions.length-1){
                                activeText+=n["value"];
                            }else{
                                activeText+=n["value"]+"-";
                            }
                        });
                        self.textSp.value=activeText;
                        if($scope.family_id != 'family_info'){
                            $scope.HealthStatus(activeText);
                        }else{
                            $scope.addHealthStatus(activeText);
                        }

                        e.hide();
                    },
                    "onClickCancel":function(e){
                        e.updateSlots();
                        e.hide();
                    },

                });
            },
            _addScrollpickerData:function(){
                this.scrollpicker.addSlot(this.nums3);
            },
            _initPlugin:function(){
                this._initScrollPicker();
                this._addScrollpickerData();
            },
            /*=========================
             Events
             ===========================*/
            _attach:function(e){
                var self=this;
                if(!self.textSp.hasEvent){
                    this.textSp.addEventListener("click",function(e){
                        self._onClickTextSp(e);
                    },false);
                    self.textSp.hasEvent=true;
                }
            },
            /*=========================
             Event Handler
             ===========================*/
            _onClickTextSp:function(e){
                this.scrollpicker.show();
            }
        }

        view.initialize();
        view_relation.initializegender();
        view_health_status.initializehealth();

        $scope.back = function () {
            $('#page_modify .titlebar').hide();
            $('.personal-title').show();
            singlePage.close('#page_modify');
        }
    }]);
