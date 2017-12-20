/**
 * Created by sailwish001 on 2016/11/18.
 */
'use strict';

app.controller('modalBannerCtrl', ['$rootScope', '$scope', '$modalInstance', '_jiyin', 'params', 'FileUploader', 'dataToURL',
    function ($rootScope, $scope, $modalInstance, _jiyin, params, FileUploader, dataToURL) {
        $scope.is_admin = $rootScope.is_admin;
        $scope.infoList = angular.copy(params.infoList);
        $scope.title = params.title;
        $scope.ael = params.ael;

        $scope.$watch('currentUser.role_id', function (nv) {
            if(nv == '30'){
                $scope.infoList.position_id = '6';
                $scope.infoList.agent_name = $rootScope.currentUser.username;
                $scope.infoList.agent_id = $rootScope.currentUser.id;
            }
        });
        
        /**
         * 获取位置
         */
        $scope.getPosition = function () {
            _jiyin.dataPost('admin/system_code_admin/get_by_type/banner_position')
                .then(function (result) {
                    $scope.positionList = result;
            })
        };
        $scope.getPosition();

        //添加代理商
        $scope.getAgent = function () {
            $scope.title = '添加代理商';
            _jiyin.modal({
                tempUrl : '/source/admin/tpl/modal/modal-agentAddCommodity.html',
                tempCtrl : 'agentAddCommodityCtrl',
                ok : $scope.addAgent,
                size : 'lg',
                params : {
                    title: $scope.title,
                    ael: 'add'
                }
            });
        };
        $scope.addAgent = function (data) {
            $scope.infoList.agent_name = data.name;
            $scope.infoList.agent_id = data.id;
        };
        /**
         * 删除图片
         */
        $scope.removePic = function (id) {
            if(comfirm('确定删除该图片吗?')){
                _jiyin.dataPost('',dataToURL({id : id}))
                    .then(function (result) {
                        if (result.success) {
                            var data = $scope.rowData.pic;
                            for (var i = 0; i < data.length; i++) {
                                if (picId === data[i].id) {
                                    $scope.rowData.pic.splice(i, 1);
                                }
                            }
                        }
                    });
            }
        };
        $scope.$on('attachment_id', function(event, attachment_id) {
            $scope.infoList.attachment_id = attachment_id;
        });
        $scope.$on('attachment_path', function(event, attachment_path) {
            $scope.infoList.path = attachment_path;
        });
        /**
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
        $scope.ok = function () {
            if(!$scope.infoList.position_id){
                _jiyin.msg('e','位置不能为空');
                return ;
            }
            if($scope.infoList.position_id == '6' && (!$scope.infoList.agent_id || $scope.infoList.agent_id == '' || $scope.infoList.agent_id == null)){
                _jiyin.msg('e','请添加代理商');
                return ;
            }
            /*if(!$scope.infoList.url){
                _jiyin.msg('e','对应链接不能为空');
                return ;
            }*/
            if(!$scope.infoList.attachment_id){
                _jiyin.msg('e','还没有上传图片');
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
            return this.queue.length < 1;
        }
    });
    $scope.upload = function(item){
        _jiyin.fileMd5(item._file).then(function (result) {
            _jiyin.dataPost('attachment/check_md5', dataToURL({md5_code: result.md5Code}))
                .then(function (result) {
                    if(result.exist == true){
                        $scope.$emit('attachment_id', result.attachment_id);
                        $scope.$emit('attachment_path', result.path);
                        item.file.size = item._file.size;
                        item.progress = 100;
                        item.isSuccess = true;
                        item.isUploaded = true;
                        item.uploader.progress += 100/uploader.queue.length;
                        // uploader.clearQueue();
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
                            uploader.clearQueue();
                        }else{
                            data.upload();
                        }
                    });
            });
        });
    };
    uploader.onSuccessItem = function(fileItem, response, status, headers) {
        $scope.$emit('attachment_id', response.attachment_id);
        $scope.$emit('attachment_path', response.url);
        // uploader.clearQueue();
    };
    $scope.$on('clearQueue', function() {
        uploader.clearQueue();
    });
}]);