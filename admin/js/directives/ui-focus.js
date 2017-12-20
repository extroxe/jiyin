angular.module('app')
  .directive('uiFocus', function($timeout, $parse) {
    return {
      link: function(scope, element, attr) {
        var model = $parse(attr.uiFocus);
        scope.$watch(model, function(value) {
          if(value === true) {
            $timeout(function() {
              element[0].focus();
            });
          }
        });
        element.bind('blur', function() {
           scope.$apply(model.assign(scope, false));
        });
      }
    };
  });
angular.module('app')
  .directive('uiFootable', function () {
      return {
        restrict: 'A',
        link: function ($scope, element, attrs) {
          element.footable();
        }
      };
});
angular.module('app')
    .directive('uiDatetime', function () {
        return {
            restrict: 'A',
            link: function ($scope, element, attrs) {
                element.datetimepicker({
                    format: 'yyyy-mm-dd 00:00:00',
                    autoclose: true,
                    todayBtn: true,
                    minView: "month",
                });
            }
        };
});
angular.module('app')
    .directive('uiDate', function () {
        return {
            restrict: 'A',
            link: function ($scope, element, attrs) {
                element.datetimepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayBtn: true
                });
            }
        };
    });
angular.module('app')
    .directive('chkBind',function(){
    return {
        restrict: 'A',
        require: '?^ngBind',
        link: function(scope,element,attr,ctrl){
            element = element[0];
            scope.$watch(attr.ngBind, function ngBindWatchAction(value) {
                var flag = false;
                if(1 === value || true === value){
                    flag = true;
                }
                element.checked = flag;
            });

            element.onclick = function(event,o){
                if(true === event.target.checked) {
                    scope.$apply(function(){
                        eval("scope."+attr.ngBind +"=1;");
                    });
                }else{
                    scope.$apply(function(){
                        eval("scope."+attr.ngBind +"=0;");
                    });
                }
            };
        }
    };
});