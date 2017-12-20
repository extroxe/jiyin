/**
 * Created by sailwish001 on 2017/5/19.
 */

app.controller('detectionProjectCtrl', ['$scope', '_jiyin', 'dataToURL', function ($scope, _jiyin, dataToURL) {
    $scope.detectionProjectList = {};
    $scope.inputPage = 1;
    $scope.res = '';
    /**
     * 获取数据
     */
    $scope.getData = function(){
        _jiyin.dataPost('admin/detection_project_admin/get_project_by_page',dataToURL({
            page :$scope.inputPage,
            page_size : 10,
            template_id:$scope.res
        }))
            .then(function(result){
                $scope.detectionProjectList = result.data;
                $scope.totalPage = result.total_page;
            });
    };
    $scope.getData();
    /**
     * 增加
     */
    $scope.addList = function () {
        $scope.title = '增加数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-detectionProject.html',
            tempCtrl : 'modalDeprojectCtrl',
            ok : $scope.add,
            size : 'lg',
            params : {
                title: $scope.title,
                infoList: {},
                ael: 'add'
            }
        });
    };
    $scope.add = function (list) {
        _jiyin.dataPost('admin/detection_project_admin/add_project',dataToURL(list))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s','添加成功');
                    $scope.getData();
                }else{
                    _jiyin.msg('e','添加失败');
                }
            });
    };
    /*获取模板*/
    $scope.getCommos = function () {
        _jiyin.dataPost('admin/detection_template_admin/get_detection_template')
            .then(function (result) {
                $scope.commoLists = result.data;
            })
    };
    $scope.getCommos();
    $scope.searchByTemplate = function (res) {
        $scope.inputPage = 1;
        $scope.res = res;
        $scope.getData();
        // _jiyin.dataPost('admin/detection_project_admin/get_project_by_page',dataToURL({template_id:res}))
        //     .then(function (result) {
        //         $scope.detectionProjectList = result.data;
        //     })
    };
    /**
     * 编辑
     */
    $scope.editList = function (data) {
        $scope.title = '编辑数据';
        _jiyin.modal({
            tempUrl : '/source/admin/tpl/modal/modal-detectionProject.html',
            tempCtrl : 'modalDeprojectCtrl',
            ok : $scope.edit,
            size : 'lg',
            params : {
                title: $scope.title,
                infoList: data,
                ael: 'edit'
            }
        });
    };
    $scope.edit = function (list) {
        console.log(list)
        _jiyin.dataPost('admin/detection_project_admin/update_project',dataToURL(list))
            .then(function (result) {
                if(result.success == true){
                    _jiyin.msg('s','修改成功');
                    $scope.getData();
                }else{
                    _jiyin.msg('e','修改失败');
                }
            });
    };
    /**
     * 删除信息
     * @param data
     */
    $scope.deleteData = function(data){
        if(confirm('确认删除这条数据吗?')){
            _jiyin.dataPost('admin/detection_project_admin/delete_project',dataToURL({ id: data.id}))
                .then(function(result){
                    if(result.success == true){
                        _jiyin.msg('s', '删除成功');
                        $scope.getData();
                    }else {
                        _jiyin.msg('e', result.msg);
                    }
                });
        }
    };
    /**
     * 下一页
     */
    $scope.nextPage = function(){
        if($scope.inputPage < $scope.totalPage){
            $scope.inputPage ++;
            $scope.getData();
        }else{
            _jiyin.msg('e', '当前是最后一页');
        }
    };
    /**
     * 上一页
     */
    $scope.previousPage = function(){
        if($scope.inputPage > 1){
            $scope.inputPage --;
            $scope.getData();
        }else{
            _jiyin.msg('e', '当前是第一页');
        }
    };
    /**
     * 第一页
     */
    $scope.firstPage = function () {
        $scope.inputPage = 1;
        $scope.getData();
    };
    /**
     * 最后一页
     */
    $scope.lastPage = function () {
        $scope.inputPage = $scope.totalPage;
        $scope.getData();
    };
    $scope.selectPage = function (page) {
        $scope.inputPage = page;
        $scope.getData();
    }
}]);
app.filter('hidden',function () {
    return function (data) {
        var lenth= data.length;
        if (lenth >15){
            data = data.substring(0,15)+"...";
        }
        return data;
    }
});