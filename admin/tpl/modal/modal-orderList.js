/**
 * Created by Boss Peng on 2016/11/24.
 */
app.controller('modalOrderCtrl', ['$scope', '$modalInstance', '_jiyin', 'params', 'FileUploader', 'dataToURL',
    function ($scope, $modalInstance, _jiyin, params, FileUploader, dataToURL) {
        $scope.infoList = params.infoList;
        $scope.title = params.title;
        $scope.ael = params.ael;
        $scope.is_point = params.isPoint;

        /**
        * 获取终端类型
        */
        $scope.getTerminal = function () {
            _jiyin.dataPost('admin/system_code_admin/get_by_type/terminal_type')
                .then(function (result) {
                    $scope.terminalList = result;
                })
        };
        $scope.getTerminal();
        /**
        * 获取支付方式
        */
        $scope.getPay = function () {
            _jiyin.dataPost('admin/system_code_admin/get_by_type/payment')
                .then(function (result) {
                    $scope.payList = result;
                })
        };
        $scope.getPay();
        /**
         * 获取订单状态
         */
        $scope.getStatus = function () {
            _jiyin.dataPost('admin/system_code_admin/get_by_type/order_status')
                .then(function (result) {
                    $scope.statusList = result;
                })
        };
        $scope.getStatus();
        /**
         * 获取快递公司
         */
        $scope.getExpress = function () {
            _jiyin.dataPost('admin/express_admin/get_all_express_company')
                .then(function (result) {
                    $scope.expressList = result.data;
                })
        };
        $scope.getExpress();
        /**
         * 取消关闭
         */
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        };
        $scope.ok = function () {
            if(!$scope.infoList.terminal_type){
                _jiyin.msg('e','终端类型不能为空');
                return ;
            }
            if(!$scope.infoList.status_id){
                _jiyin.msg('e','订单状态不能为空');
                return ;
            }
            if(!$scope.infoList.predict_complete_time){
                _jiyin.msg('e','预计完成时间不能为空');
                return ;
            }
            if($scope.infoList.status_id == 30){
                if(!$scope.infoList.express_company_id){
                    _jiyin.msg('e','快递公司不能为空');
                    return ;
                }
                if(!$scope.infoList.express_number){
                    _jiyin.msg('e','快递单号不能为空');
                    return ;
                }
            }
            $modalInstance.close($scope.infoList);
        }
    }]);