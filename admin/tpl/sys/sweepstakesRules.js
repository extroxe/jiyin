/**
 * Created by sailwish001 on 2017/3/7.
 */
app.controller('sweepCtrl', ['$scope', '_jiyin', 'dataToURL', function ($scope, _jiyin, dataToURL) {
    $scope.content='';
    $scope.getSetting = function () {
        _jiyin.dataPost('admin/system_setting_admin/get_sweepstakes_rules')
            .then(function (result) {
                $scope.content = result.data.value;
            });
    };
    $scope.getSetting();
    $scope.save = function () {
        if(!$scope.content){
            _jiyin.msg('e','请先设置积分抽奖规则');
            return ;
        }
        _jiyin.dataPost('admin/system_setting_admin/sweepstakes_rules', dataToURL({content: $scope.content}))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s','设置成功');
                    $scope.getSetting();
                }
            });
    };
}]);