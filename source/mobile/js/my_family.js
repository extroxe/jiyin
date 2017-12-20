angular.module('app')
    .controller('myFamilyCtrl', ['$scope', '$timeout', '$location', 'ajax', function ($scope,$location, $timeout, ajax){
        $scope.my_family_list = [];
        $scope.clinical_history = [];
        $scope.clinical_history_arr = [];

        /**
         * 初始化家族信息
         */
        ajax.req('POST', 'user/get_all_family_info')
            .then(function (data) {
                if(data.success){
                    angular.forEach(data.data, function (family_list, index, family_array) {
                        if(family_list.relation == '10'){
                            family_list.relation = '父亲'
                        }else if(family_list.relation == '20'){
                            family_list.relation = '母亲'
                        };
                        /*if(family_list.health_status == '10'){
                            family_list.health_status = '健康';
                        }else if(family_list.health_status == '20'){
                            family_list.health_status = '亚健康';
                        }else if(family_list.health_status == '30'){
                            family_list.health_status = '疾病';
                        }*/
                        $scope.clinical_history = family_list.clinical_history.split('-');
                        $scope.clinical_history_arr = [];
                        data.data[index].clinical_history_arr = [];
                        angular.forEach($scope.clinical_history, function (clinical_history_list, index, clinical_history_array) {

                            if(clinical_history_list == '10'){
                                clinical_history_array = '手术'
                            }else if(clinical_history_list == '20'){
                                clinical_history_array = '化疗'
                            }else if(clinical_history_list == '30'){
                                clinical_history_array = '放疗'
                            }else if(clinical_history_list == '40'){
                                clinical_history_array = '靶向药物治疗'
                            }
                            $scope.clinical_history_arr.push(clinical_history_array);
                        });
                        family_array[index].clinical_history_arr = $scope.clinical_history_arr;
                        $scope.my_family_list = data.data;
                    });
                }
            });

        /**
         * 查看家族个人信息
         */
        $scope.family_info = function (family_id) {
            window.location.href = SITE_URL + 'weixin/user/family_info/'+ family_id;
        };

        /**
         * 添加家属
         */

        $scope.add_family_member = function () {
            window.location.href = SITE_URL + 'weixin/user/family_info';
        }
    }])
