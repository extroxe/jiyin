angular.module("app").directive("uiShift",["$timeout",function(i){return{restrict:"A",link:function(n,t,e){function r(){i(function(){var i=e.uiShift,n=e.target;o.hasClass("in")||o[i](n).addClass("in")})}function u(){a&&a.prepend(t),!a&&o.insertAfter(f),o.removeClass("in")}var a,o=$(t),d=$(window),f=o.prev(),s=d.width();!f.length&&(a=o.parent()),768>s&&r()||u(),d.resize(function(){s!==d.width()&&i(function(){d.width()<768&&r()||u(),s=d.width()})})}}}]);