angular.module('app', ['angularFileUpload'])
    .run(function($rootScope, $http){
        $rootScope.http = $http.get;
        $rootScope.SITE_URL = SITE_URL;
        $rootScope.back = function(){
            window.location.href = document.referrer;
        };
    })
    .factory('dataToURL', function () {
        return function (data) {
            var str = '';
            for( var key in data){
                str += '&' + key + '='+ data[key];
            }
            return str.slice(1,str.length);
        }
    })
    .factory('ajax', ['$http', '$q', 'dataToURL', function($http, $q, dataToURL){
    return {
        req: function (method, url, data, trans_url) {
            var trans = trans_url || true;
            var deferred = $q.defer();
            var request = {
                method:method,
                url: SITE_URL + url,
                headers: {
                    'Content-Type':'application/x-www-form-urlencoded;charset=utf-8;',
                    'X-Requested-With':'XMLHttpRequest'
                } ,
                data: trans ? dataToURL(data) : data
            };
            $http(request)
                .success(function(data, status, headers, config){
                    deferred.resolve(data)
                })
                .error(function(data, status, headers, config){
                    var popToast;
                    if(!popToast || popToast&&!popToast.toastBox){
                        popToast=new Toast("系统繁忙，请稍后重试",{
                            "onHid":function(e){
                                e.destroy();
                            }
                        });
                    }
                    popToast.show();
                    deferred.reject(data);
                });
            return deferred.promise;
            }
        }
    }])
    .factory('fileMd5', ['$q', function($q){
        return function(file){
            var deferred = $q.defer();
            var fileReader = new FileReader(),

                blobSlice = File.prototype.mozSlice || File.prototype.webkitSlice || File.prototype.slice,
                chunkSize = 2097152,
                chunks = Math.ceil(file.size / chunkSize),
                currentChunk = 0,
                spark = new SparkMD5();

            fileReader.onload = function(e) {
                spark.appendBinary(e.target.result);
                currentChunk++;
                if (currentChunk < chunks) {
                    loadNext();
                } else {
                    var md5 = spark.end();
                    deferred.resolve({md5Code: md5, file: file});
                }
            };
            var loadNext = function() {
                var start = currentChunk * chunkSize;
                var end = start + chunkSize >= file.size ? file.size : start + chunkSize;
                fileReader.readAsBinaryString(blobSlice.call(file, start, end));
            };
            loadNext();
            return deferred.promise;
        };
    }])
    .filter('to_trusted', ['$sce', function ($sce) {
        return function (text) {
            return $sce.trustAsHtml(text);
        }
    }])
    .filter('sum_price', function(){
        return function(arr, discount_privilege){
            var total_price = 0;
            angular.forEach(arr, function(data, index){
                if (data.is_point == 1){
                    total_price += parseFloat(data.price);
                }else{
                    total_price += data.amount * (data.flash_sale_price ? data.flash_sale_price : data.price);
                }
            });

            if (discount_privilege != undefined && discount_privilege > 0){
                total_price -= discount_privilege;
            }

            return total_price;
        };
    })
    .filter('total_price', function () {
        return function (total_price, postage) {
            if(postage && postage > 0){
                total_price += postage;
            }
            return total_price;
        }
    })
    .filter('total_num', function () {
        return function(arr){
            var total_num = 0;
            angular.forEach(arr, function(data, index){
                total_num += parseInt(data.amount);
            });
            return total_num;
        };
    })
    .filter('substring', function () {
        return function (date, start, end) {
            return date.substring(start, end);
        }
    })
    .filter('trans_int', function () {
        return function (string) {
            return parseInt(string);
        }
    });