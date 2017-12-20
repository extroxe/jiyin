/**
 * Created by sailwish001 on 2016/12/6.
 */
'use strict';

app.controller('modalUserCtrl', ['$scope', '$modalInstance', '_jiyin', 'params', 'FileUploader', 'dataToURL',
    function ($scope, $modalInstance, _jiyin, params, FileUploader, dataToURL) {
        $scope.infoList = params.infoList;
        $scope.title = params.title;
        $scope.role_id = params.role_id;
        $scope.ael = params.ael;
        // $scope.infoList.color = angular.copy(params.color) ? angular.copy(params.color) :'#fff';
        setTimeout( function () {
            $('#color').val($scope.infoList.color);
        }, 200);
        /**
         * 获取角色
         */
        if($scope.infoList.is_show){
            $scope.showFlag = $scope.infoList.is_show = 1 ? true : false;
        }
        $scope.getRole = function () {
            _jiyin.dataPost('admin/system_code_admin/get_by_type/role')
                .then(function (result) {
                    $scope.roleList = result;
                })
        };
        $scope.getRole();

        $scope.$watch('ael', function(nv){
            if(nv){
                setTimeout(function(){
                    $('#colorpicker').farbtastic('#color');
                     $('#color').focus(function () {
                        $('#colorpicker').show();
                    });
                    $('#color').blur(function () {
                        $('#colorpicker').hide();
                    });
                }, 1000);
            }
        })

        setTimeout(function () {
            $('#color').val($scope.infoList.color);
        }, 200);
       
        /**
         * 获取等级
         */
        $scope.getLevel = function () {
            _jiyin.dataPost('admin/level_admin/get_level')
                .then(function (result) {
                    $scope.levelList = result.data;
                })
        };
        $scope.getLevel();
        $scope.$on('attachment_id', function(event, attachment_id) {
            $scope.infoList.attachment_id = attachment_id;
        });
        /**
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
        $scope.ok = function () {
            $scope.infoList.birthday = $('.user-birthday').val();
            if ($scope.infoList.role_id == 30) {
                $scope.infoList.color = $('#color').val();
            }else{
                $scope.infoList.color = '';
            }
            if(!$scope.infoList.password){
                _jiyin.msg('e','密码不能为空');
                return ;
            }
            if(!$scope.infoList.username){
                _jiyin.msg('e','用户名不能为空');
                return ;
            }
            if(!$scope.infoList.phone){
                _jiyin.msg('e','电话号码不能为空');
                return ;
            }
            if(!$scope.infoList.current_point){
                _jiyin.msg('e','当前积分不能为空');
                return ;
            }
            if(!$scope.infoList.total_point){
                _jiyin.msg('e','总积分不能为空');
                return ;
            }
            if(!$scope.infoList.role_id){
                _jiyin.msg('e','角色不能为空');
                return ;
            }
            $scope.infoList.is_show = 1;
            var regPhone = /^1(3|4|5|7|8)\d{9}$/;
            var regEmail = /^\w+@\w+\..+$/;
            if($scope.infoList.phone && regPhone.test($scope.infoList.phone) == false){
                _jiyin.msg('e','手机号不符合规则');
                return ;
            }
            if($scope.infoList.email && regEmail.test($scope.infoList.email) == false){
                _jiyin.msg('e','邮箱不符合规则');
                return ;
            }
            $modalInstance.close($scope.infoList);
        }
    }]);
app.controller('FileUploadCtrl', ['$scope', 'FileUploader', '_jiyin', 'dataToURL', function($scope, FileUploader, _jiyin, dataToURL) {
    var uploader = $scope.uploader = new FileUploader({
        url: SITE_URL + 'attachment/up_attachment'
    });
    // FILTERS
    uploader.filters.push({
        name: 'customFilter',
        fn: function(item /*{File|FileLikeObject}*/ , options) {
            return this.queue.length < 2;
        }
    });
    $scope.upload = function(item){
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