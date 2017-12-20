angular.module('app')
    .controller('userInfoCtrl', ['$scope', 'ajax', 'FileUploader', 'fileMd5', function ($scope, ajax, FileUploader, fileMd5) {
        var prompt=new Prompt();
        $scope.show_promt = function (msg) {
            prompt.setText(msg);
            prompt.show();
        };
        var singlePage = new Page({
            "onLoad":function(e){
                var targetPageId;
            }
        });

        var uploader = $scope.uploader = new FileUploader({
            url : SITE_URL + 'attachment/up_attachment',
            removeAfterUpload : true
        });

        $scope.user_info = [];
        $scope.init = function(){
            ajax.req('POST', 'user/get_personal_info')
                .then(function (data) {
                    if (data.success){
                        $scope.user_info = data.data;
                    }else {
                        window.location.href = SITE_URL + '/weixin';
                    }
                });
        };
        $scope.init();

        //保存出生日期
        $scope.saveBirthday = function(date){
            ajax.req('POST', 'user/save_user_info', {
                nickname : $scope.user_info.nickname,
                gender : $scope.user_info.gender,
                birthday : date
            }).then(function(response){
                    if (response.success){
                        $scope.show_promt("修改出生日期成功");
                    }else{
                        $scope.show_promt(response.msg);
                    }
                })
        };

        //保存性别
        $scope.saveGender = function(){
            ajax.req('POST', 'user/save_user_info', {
                nickname : $scope.user_info.nickname,
                gender : $scope.gender,
                birthday : $scope.user_info.birthday
            }).then(function(response){
                    if (response.success){
                        $scope.show_promt("修改性别成功");
                        $scope.user_info.gender = $scope.gender;
                        $scope.back();
                    }else{
                        $scope.show_promt(response.msg);
                    }
                })
        };

        //保存昵称
        $scope.saveNickname = function(){
            if ($scope.nickname == undefined || $scope.nickname == ''){
                $scope.show_promt("请输入昵称");
            }else{
                ajax.req('POST', 'user/save_user_info', {
                    nickname : $scope.nickname,
                    gender : $scope.user_info.gender,
                    birthday : $scope.user_info.birthday
                }).then(function(response){
                    if (response.success){
                        $scope.show_promt("修改昵称成功");
                        $scope.user_info.nickname = $scope.nickname;
                        history.go(-1)
                    }else{
                        $scope.show_promt(response.msg);
                    }
                })
            }
        };

        //显示弹出框
        $scope.show_mid_frame = function(id, target){
            var openType=target||"";
            singlePage.open(id,openType);
            $scope.gender = $scope.user_info.gender;
        };

        //修改头像
        $scope.$watch('uploader.queue[0]', function(nv, ov){
            if (nv){
                fileMd5(nv._file).then(function (result) {
                    ajax.req('POST', 'attachment/check_md5', {md5_code: result.md5Code})
                        .then(function (result) {
                            if(result.exist == true){
                                $scope.update_avatar(result.attachment_id);
                            }else{
                                nv.upload();
                            }
                        });
                });
            }
        });

        $scope.$watch('uploader.progress', function(nv, ov){
            if (nv != 100 && nv != 0){
                $scope.loadView.show();
            }else{
                setTimeout(function() {
                    $scope.loadView.hide();
                }, 1000);
            }
        });

        //更新头像
        $scope.update_avatar = function(attachment_id){
            ajax.req('POST', 'user/save_user_info', {
                nickname : $scope.user_info.nickname,
                gender : $scope.gender,
                birthday : $scope.user_info.birthday,
                avatar : attachment_id
            }).then(function(response){
                    if (response.success){
                        $scope.init();
                        $scope.show_promt("修改头像成功");
                    }else{
                        $scope.show_promt(response.msg);
                    }
                })
        };

        uploader.onSuccessItem = function(fileItem, response, status, headers) {
            if (response.success){
                $scope.update_avatar(response.attachment_id);
            }else{
                $scope.show_promt(response.msg);
            }
        };

        var view={
            /*=========================
             Model
             ===========================*/
            initialize:function(){
                /*Data*/

                /*Plugin*/
                this.loading;

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
                        $scope.saveBirthday(e.activeText);
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
            _initLoading:function(){
                $scope.loadView = this.loading;
                $scope.loadView = new Loading();
            },
            _initPlugin:function(){
                this._initLoading();
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

        view.initialize();
        $scope.back = function () {
            history.go(-1);
        }
    }]);


