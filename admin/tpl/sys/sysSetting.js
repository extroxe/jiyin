/**
 * Created by sailwish001 on 2016/12/6.
 */
app.controller('sysCtrl', ['$scope', '_jiyin', 'dataToURL', function ($scope, _jiyin, dataToURL) {
    $scope.getSetting = function () {
      _jiyin.dataPost('admin/system_setting_admin/get_system_info')
          .then(function (result) {
              var data = JSON.parse(result.data[0].value);
              $scope.days = data.expectedCompletionDays;
          });
    };
    $scope.getSetting();
    $scope.save = function () {
        if(!$scope.days){
            _jiyin.msg('e','请先设置天数');
            return ;
        }
        var data = {
            expectedCompletionDays : $scope.days
        };
        var datas = JSON.stringify(data);
        _jiyin.dataPost('admin/system_setting_admin/set_load_status', dataToURL({value: datas}))
            .then(function (result) {
               if(result.success == true){
                   _jiyin.msg('s','设置成功');
                   $scope.getSetting();
               }
            });
    };
}]);