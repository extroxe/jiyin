/**
 * Created by sailwish001 on 2016/12/13.
 */
app.controller('userCtrl', ['$scope', '$rootScope', '_jiyin', 'dataToURL', function ($scope, $rootScope, _jiyin, dataToURL) {
    $scope.infoList = {};
    $scope.passList = {};

    $scope.getUserInfo = function(){
        _jiyin.dataPost('admin/admin/get_userinfo')
            .then(function (result) {
                if(result.success == true){
                    $scope.infoList = result.data;
                    $rootScope.currentUser = result.data;
                }
            })
    };
    $scope.getUserInfo();
    $scope.saveSetting = function () {
        $scope.infoList.birthday = $('.user-birthday').val();
      _jiyin.dataPost('admin/admin/update', dataToURL({
          name: $scope.infoList.name,
          gender: $scope.infoList.gender,
          phone: $scope.infoList.phone,
          email: $scope.infoList.email,
          birthday: $scope.infoList.birthday
      }))
        .then(function (result) {
            if(result.success == true){
                _jiyin.msg('s', '个人设置保存成功');
                $scope.getUserInfo();
            }else{
                _jiyin.msg('e', result.error);
            }
        })
    };
    $scope.savePassword = function () {
        _jiyin.dataPost('admin/admin/change_password', dataToURL($scope.passList))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s', '密码设置保存成功');
                    $scope.passList = {};
                }else{
                    _jiyin.msg('e', result.error);
                }
            })
    };
}]);