"use strict";angular.module("ui.validate",[]).directive("uiValidate",function(){return{restrict:"A",require:"ngModel",link:function(a,n,i,t){function u(n){return angular.isString(n)?void a.$watch(n,function(){angular.forEach(l,function(a){a(t.$modelValue)})}):angular.isArray(n)?void angular.forEach(n,function(n){a.$watch(n,function(){angular.forEach(l,function(a){a(t.$modelValue)})})}):void(angular.isObject(n)&&angular.forEach(n,function(n,i){angular.isString(n)&&a.$watch(n,function(){l[i](t.$modelValue)}),angular.isArray(n)&&angular.forEach(n,function(n){a.$watch(n,function(){l[i](t.$modelValue)})})}))}var r,l={},e=a.$eval(i.uiValidate);e&&(angular.isString(e)&&(e={validator:e}),angular.forEach(e,function(n,i){r=function(u){var r=a.$eval(n,{$value:u});return angular.isObject(r)&&angular.isFunction(r.then)?(r.then(function(){t.$setValidity(i,!0)},function(){t.$setValidity(i,!1)}),u):r?(t.$setValidity(i,!0),u):(t.$setValidity(i,!1),u)},l[i]=r,t.$formatters.push(r),t.$parsers.push(r)}),i.uiValidateWatch&&u(a.$eval(i.uiValidateWatch)))}}});