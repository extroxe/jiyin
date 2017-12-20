/**
 * Created by sailwish001 on 2016/11/23.
 */
app.controller('modalCouponCtrl', ['$scope', '$modalInstance', '_jiyin', 'params', 'FileUploader', 'dataToURL', '$filter',
    function ($scope, $modalInstance, _jiyin, params, FileUploader, dataToURL, $filter) {
        $scope.infoList = params.infoList;
        $scope.title = params.title;
        $scope.ael = params.ael;
        $scope.infoList.privilege = parseInt($scope.infoList.privilege);

        /**
         * 获取状态
         */
        $scope.getStatus = function () {
            _jiyin.dataPost('admin/system_code_admin/get_by_type/discount_coupon_status')
                .then(function (result) {
                    $scope.statusList = result;
                })
        };
        $scope.getStatus();

        $scope.$watch('infoList.end_time', function(nv, ov){
            if (nv){
                if (!$scope.infoList.start_time){
                    $scope.infoList.end_time = '';
                    _jiyin.msg('e','请先选择开始时间');
                    return;
                }

                var endTime = new Date(nv);
                var starTime = new Date($scope.infoList.start_time);
                if (endTime.getTime() > starTime.getTime()){
                    $scope.infoList.useful_life = Math.ceil((endTime.getTime() - starTime.getTime()) / (24 * 60 * 60 * 1000));
                }else{
                    _jiyin.msg('e','结束时间不能小于开始时间');
                }
            }
        });

        $scope.$watch('infoList.useful_life', function(nv, ov){
            if (nv){
                if (!$scope.infoList.start_time){
                    $scope.infoList.useful_life = '';
                    _jiyin.msg('e','请先选择开始时间');
                    return;
                }

                var time = $scope.infoList.useful_life * (24 * 60 * 60 * 1000);

                if (time <= 0){
                    _jiyin.msg('e','结束时间不能小于开始时间');
                    return;
                }

                var starTime = new Date($scope.infoList.start_time);
                var endTime = new Date(starTime.getTime() + time);
                $scope.infoList.end_time = $filter('formatDate')(endTime, 'yyyy-MM-dd h:m:s');
            }
        });

        /**
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
        $scope.ok = function () {
            if(!$scope.infoList.name){
                _jiyin.msg('e','名称不能为空');
                return ;
            }
            if(!$scope.infoList.condition){
                _jiyin.msg('e','满足条件不能为空');
                return ;
            }
            if(!$scope.infoList.privilege){
                _jiyin.msg('e','减免金额不能为空');
                return ;
            }
            if(!$scope.infoList.useful_life){
                if(!$scope.infoList.start_time || !$scope.infoList.end_time){
                    _jiyin.msg('e','生效起止时间有效期不能同时为空');
                    return ;
                }
            }
            if(!$scope.infoList.status_id){
                _jiyin.msg('e','状态不能为空');
                return ;
            }
            $modalInstance.close($scope.infoList);

        }
    }]);