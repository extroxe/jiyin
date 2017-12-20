/**
 * Created by sailwish001 on 2016/12/6.
 */
'use strict';

app.controller('modalAgentCtrl', ['$rootScope', '$scope', '$modalInstance', '_jiyin', 'params',
    function ($rootScope, $scope, $modalInstance, _jiyin, params) {
        $scope.is_admin = $rootScope.is_admin;
        $scope.infoList = params.infoList;
        $scope.title = params.title;
        $scope.ael = params.ael;
        if ($scope.infoList.color == null) {
            $scope.infoList.color = '#fff';
        }

        setTimeout(function () {
            $('#color').val($scope.infoList.color);
        }, 200);

        if($scope.infoList.is_show){
            $scope.showFlag = $scope.infoList.is_show = 1 ? true : false;
        }

        $scope.$on('attachment_id', function(event, attachment_id) {
            $scope.infoList.attachment_id = attachment_id;
        });

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

        /**
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
        $scope.ok = function () {
            $scope.infoList.color = $('#color').val();
            $scope.infoList.is_show = 1;
            var regEmail = /^\w+@\w+\..+$/;
            if($scope.infoList.email && regEmail.test($scope.infoList.email) == false){
                _jiyin.msg('e','邮箱不符合规则');
                return ;
            }
            $modalInstance.close($scope.infoList);
        }
    }]);
// app.controller('FileUploadCtrl', ['$scope', 'FileUploader', '_jiyin', 'dataToURL', function($scope, FileUploader, _jiyin, dataToURL) {
//     var uploader = $scope.uploader = new FileUploader({
//         url: SITE_URL + 'attachment/up_attachment'
//     });
//     // FILTERS
//     uploader.filters.push({
//         name: 'customFilter',
//         fn: function(item /*{File|FileLikeObject}*/ , options) {
//             return this.queue.length < 2;
//         }
//     });
//     $scope.upload = function(item){
//         _jiyin.fileMd5(item._file).then(function (result) {
//             _jiyin.dataPost('attachment/check_md5', dataToURL({md5_code: result.md5Code}))
//                 .then(function (result) {
//                     if(result.exist == true){
//                         $scope.$emit('attachment_id', result.attachment_id);
//                         item.file.size = item._file.size;
//                         item.progress = 100;
//                         item.isSuccess = true;
//                         item.isUploaded = true;
//                         item.uploader.progress += 100/uploader.queue.length;
//                     }else{
//                         item.upload();
//                     }
//                 });
//         });
//     };
//     $scope.uploadAll = function () {
//         angular.forEach(uploader.queue, function (data, index) {
//             _jiyin.fileMd5(data._file).then(function (result) {
//                 _jiyin.dataPost('attachment/check_md5', dataToURL({md5_code: result.md5Code}))
//                     .then(function (result) {
//                         if(result.exist == true){
//                             $scope.$emit('attachment_id', result.attachment_id);
//                             data.file.size = data._file.size;
//                             data.progress = 100;
//                             data.isSuccess = true;
//                             data.isUploaded = true;
//                             uploader.progress += 100/uploader.queue.length;
//                         }else{
//                             data.upload();
//                         }
//                     });
//             });
//         });
//     };
//     uploader.onSuccessItem = function(fileItem, response, status, headers) {
//         $scope.$emit('attachment_id', response.attachment_id);
//     };
//     $scope.$on('clearQueue', function() {
//         uploader.clearQueue();
//     });
// }]);