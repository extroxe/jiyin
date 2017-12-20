'use strict';

/* Controllers */

angular.module('app')
    .filter('formatDate', function () {
        return function (date, fmt) {
            if (/(y+)/.test(fmt)) {
                fmt = fmt.replace(RegExp.$1, (date.getFullYear() + '').substr(4 - RegExp.$1.length));
            }
            var o = {
                'M+': date.getMonth() + 1,
                'd+': date.getDate(),
                'h+': date.getHours(),
                'm+': date.getMinutes(),
                's+': date.getSeconds()
            };
            for (var k in o) {
                if (new RegExp('(' + k + ')').test(fmt)) {
                    var str = o[k] + '';
                    fmt = fmt.replace(RegExp.$1, (RegExp.$1.length === 1) ? str : ('00' + str).substr(str.length));
                }
            }
            return fmt;
        };
    })
    .controller('AppCtrl', ['$rootScope', '$scope', '_jiyin', '$translate', '$localStorage', '$window',
        function ($rootScope, $scope, _jiyin, $translate, $localStorage, $window) {
            // add 'ie' classes to html
            var isIE = !!navigator.userAgent.match(/MSIE/i);
            isIE && angular.element($window.document.body).addClass('ie');
            isSmartDevice($window) && angular.element($window.document.body).addClass('smart');

            // config
            $scope.app = {
                name: 'jys-web',
                version: '1.3.3',
                // for chart colors
                color: {
                    primary: '#7266ba',
                    info: '#23b7e5',
                    success: '#27c24c',
                    warning: '#fad733',
                    danger: '#f05050',
                    light: '#e8eff0',
                    dark: '#3a3f51',
                    black: '#1c2b36'
                },
                settings: {
                    themeID: 1,
                    navbarHeaderColor: 'bg-black',
                    navbarCollapseColor: 'bg-white-only',
                    asideColor: 'bg-black',
                    headerFixed: true,
                    asideFixed: false,
                    asideFolded: false,
                    asideDock: false,
                    container: false
                }
            }

            // save settings to local storage
            if (angular.isDefined($localStorage.settings)) {
                $scope.app.settings = $localStorage.settings;
            } else {
                $localStorage.settings = $scope.app.settings;
            }
            $scope.$watch('app.settings', function () {
                if ($scope.app.settings.asideDock && $scope.app.settings.asideFixed) {
                    // aside dock and fixed must set the header fixed.
                    $scope.app.settings.headerFixed = true;
                }
                // save to local storage
                $localStorage.settings = $scope.app.settings;
            }, true);

            // angular translate
            $scope.lang = {isopen: false};
            $scope.langs = {en: 'English', de_DE: 'German', it_IT: 'Italian'};
            $scope.selectLang = $scope.langs[$translate.proposedLanguage()] || "English";
            $scope.setLang = function (langKey, $event) {
                // set the current lang
                $scope.selectLang = $scope.langs[langKey];
                // You can change the language during runtime
                $translate.use(langKey);
                $scope.lang.isopen = !$scope.lang.isopen;
            };

            function isSmartDevice($window) {
                // Adapted from http://www.detectmobilebrowsers.com
                var ua = $window['navigator']['userAgent'] || $window['navigator']['vendor'] || $window['opera'];
                // Checks for iOs, Android, Blackberry, Opera Mini, and Windows mobile devices
                return (/iPhone|iPod|iPad|Silk|Android|BlackBerry|Opera Mini|IEMobile/).test(ua);
            }

            $scope.getUserInfo = function () {
                _jiyin.dataPost('admin/admin/get_userinfo')
                    .then(function (result) {
                        if (result.success == true) {
                            $scope.infoList = result.data;
                            $rootScope.currentUser = result.data;

                            if ($rootScope.currentUser.role_id == 20) {
                                $rootScope.is_admin = true;
                            } else {
                                $rootScope.is_admin = false;
                            }
                        }
                    })
            };
            $scope.getUserInfo();

        }]);