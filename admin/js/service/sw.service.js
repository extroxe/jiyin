app.factory('dataToURL', function () {
	return function (data) {
		if (data instanceof Object) {
			for(var item in data) {
				if (data.hasOwnProperty(item)) {
					if (typeof(data[item]) == 'undefined' || (!data[item] && typeof(data[item])!="undefined" && data[item] != 0)) {
						data[item] = "";
					}
				}
			}
		}
		var str = '';
		for( var key in data){
			str += '&' + key + '='+ encodeURIComponent(data[key]);
		}
		return str.slice(1,str.length);
	}
});
app.factory('_jiyin',['$http','$q','toaster','$modal','$rootScope', function($http, $q, toaster, $modal, $rootScope){
	var  service = {};
	/**
	 * 获取文件MD5 code
	 */
	service.fileMd5 = function(file){
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
	service.msg = function(t,text){
		var type = {'s':'success', 'i':'info', 'wt':'wait', 'w':'warning', 'e':'error'}[t];
		type = type ? type : 'success';
		text = text || '';
		toaster.pop(type, '信息提示', text);
	};
	var get_count = 0;
	var post_count = 0;
	service.dataGet = function(url, params) {
		$rootScope.isLoading = true;
		get_count++;
		var deferred = $q.defer();
		$http({
			method : 'GET',
			contentType : 'application/json',
			headers : {'Content-Type':'application/x-www-form-urlencoded;charset=utf-8;', 'X-Requested-With':'XMLHttpRequest'},
			dataType : 'json',
			data : params,
			url : SITE_URL + url
		}).success(function(result) {
			get_count--;
			if(get_count == 0 && post_count == 0 && $rootScope.isLoading){
				$rootScope.isLoading = false;
			}
			if(result.timeout){
				service.msg('e', result.msg);
				window.location.href = SITE_URL + "admin/admin";
			}
			deferred.resolve(result);
		}).error(function(result) {
			// console.log(result.data);
			post_count--;
			if(get_count == 0 && post_count == 0 && $rootScope.isLoading){
				$rootScope.isLoading = false;
				service.msg('e', '系统繁忙，请稍后重试');
			}
			deferred.reject("Error!");
		});
		return deferred.promise;
	};
	service.dataPost = function(url, params) {
		post_count++;
		$rootScope.isLoading = true;
		var deferred = $q.defer();
		$http({
			method : 'POST',
			contentType : 'application/json',
			headers : {'Content-Type':'application/x-www-form-urlencoded;charset=utf-8;', 'X-Requested-With':'XMLHttpRequest'},
			dataType : 'json',
			data : params,
			url : SITE_URL + url
		}).success(function(result) {
			post_count--;
			if(get_count == 0 && post_count == 0 && $rootScope.isLoading){
				$rootScope.isLoading = false;
			}
			if(result.timeout){
				service.msg('e', result.msg);
				window.location.href = SITE_URL + "admin/admin";
			}
			deferred.resolve(result);
		}).error(function(result) {
			post_count--;
			if(get_count == 0 && post_count == 0 && $rootScope.isLoading){
				$rootScope.isLoading = false;
				service.msg('e', '系统繁忙，请稍后重试');
			}
			// console.log(result.data);
			deferred.reject("Error!");
		});
		return deferred.promise;
	};
	service.modal = function(config) {
		var type = config.type;
		var size = config.size||'';
		var tempUrl = '';
		var tempCtrl = '';
		if(undefined == type && undefined != config.tempUrl){
			tempUrl = config.tempUrl;
			tempCtrl = config.tempCtrl;
		}
		if(''==tempUrl || ''==tempCtrl){
			this.msg('e','未指定模板文件！');
			return;
		}
		var modalInstance = $modal.open({
			templateUrl : tempUrl,
			controller : tempCtrl,
			size : size,
			resolve : {
				params : function() {
					return config.params;
				}
			}
		});
		modalInstance.result.then(config.ok,config.cancle);
	};
	return service;
}]);

