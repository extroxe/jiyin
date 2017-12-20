angular.module('app')
    .controller('myReportCtrl', ['$scope', 'ajax', function ($scope, ajax) {
        $scope.reports = [];

        var prompt = new Prompt();
        $scope.init_report_info = function () {
            ajax.req('GET', 'my_city/get_report_by_page', {page: 1, page_size: 10})
                .then(function (response) {
                    if (response.success) {
                        $scope.reports = response.data;
                    }
                });
        };
        $scope.init_report_info();
        //查看pdf
        $scope.watch_pdf = function (report) {
            window.location.href = SITE_URL + report.path;
        };
    }]);
