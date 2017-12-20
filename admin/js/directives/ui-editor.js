
app.directive('uiEditor', ['$rootScope', function($rootScope) {
	/*return {
		restrict : 'EA',
		require : '?ngModel',
		scope : {
			ngDisabled : '=?',
			width : '@?',
			height : '@?',
			items: '=?'
		},
		link : function(scope, element, attrs, ctrl) {
			//KindEditor.remove(element.attr('id'));
			var _initContent;
			if (undefined == scope.ngDisabled || '' == scope.ngDisabled) {
				scope.ngDisabled = false;
			}
			if(undefined != scope.swAuth){
				scope.ngDisabled = true;
				var flag = $rootScope.authedFunc(scope.swAuth);
				if (true === flag) {
					scope.ngDisabled = false;
				}
			}
			scope.$watch('ngDisabled', function(nv, ov) {
				if (null != nv) {
					if (!scope.editor.edit || !scope.editor.edit.doc) {
						return;
					}
					if (true == nv) {
						scope.editor.readonly(true);
					} else {
						scope.editor.readonly(false);
					}
				}
			});

			var fexUE = {
				initEditor : function() {
					scope.editor = KindEditor.create(element[0], {
						width : scope.width?scope.width:'100%',
						height : scope.height?scope.height:'200px',
						allowFileManager : true,
						readonlyMode : scope.ngDisabled,
						themeType : 'default',
						newlineTag : 'p',
						resizeType : 1,
						allowPreviewEmoticons : true,
						allowImageUpload : true,
						allowUpload: true,
						imageUploadJson: SITE_URL + 'attachment/up_attachment',
						fileManagerJson: SITE_URL + 'attachment/up_attachment',
						items : scope.items?scope.items:['source', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', '|', 'table', 'lineheight', 'indent', 'wordpaste', '|', 'outdent', '|', 'emoticons', 'image', 'link', 'fullscreen'],
						afterUpload: function(){this.sync();},
						afterChange : function() {
							ctrl.$setViewValue(this.html());
						}
					});
				},
				setContent : function(content) {
					if (scope.editor) {
						scope.editor.html(content);
					}
				}
			};

			if (!ctrl) {
				return;
			}

			_initContent = ctrl.$viewValue;
			ctrl.$render = function() {
				_initContent = ctrl.$isEmpty(ctrl.$viewValue) ? '' : ctrl.$viewValue;
				fexUE.setContent(_initContent);
			};

			fexUE.initEditor();

		}
	}*/
	return {
		restrict: 'EA',
		require: '?ngModel',
		scope : {
			ngDisabled : '=?'
		},
		link: function(scope, element, attrs, ctrl) {
			var _initContent, editor;
			if (undefined == scope.ngDisabled || '' == scope.ngDisabled) {
				scope.ngDisabled = false;
			}
			scope.$watch('ngDisabled', function(nv, ov) {
				if (null != nv) {
					if (!editor.edit || !editor.edit.doc) {
						return;
					}
					if (true == nv) {
						editor.readonly(true);
					} else {
						editor.readonly(false);
					}
				}
			});
			var fexUE = {
				initEditor: function() {
					editor = KindEditor.create(element[0], {
						width: '100%',
						height: '200px',
						allowFileManager: true,
						readonlyMode: scope.ngDisabled,
						themeType: 'default',
						newlineTag: 'p',
						resizeType: 1,
						allowPreviewEmoticons: true,
						allowImageUpload : true,
						allowUpload: true,
						uploadJson: SITE_URL + 'attachment/up_attachment', //服务端上传图片处理URI
						fileManagerJson: SITE_URL + 'attachment/up_attachment',
						items: ['source', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', '|', 'table', 'lineheight', 'indent', 'wordpaste', '|', 'outdent', '|', 'emoticons', 'image', 'multiimage', 'link', 'fullscreen'],
						afterChange: function() {
							ctrl.$setViewValue(this.html());
							//scope.rowData.content = this.html();
						}
					});
				},
				setContent: function(content) {
					if (editor) {
						editor.html(content);
					}
				}
			};
			if (!ctrl) {
				return;
			}
			_initContent = ctrl.$viewValue;
			ctrl.$render = function() {
				_initContent = ctrl.$isEmpty(ctrl.$viewValue) ? '' : ctrl.$viewValue;
				fexUE.setContent(_initContent);
			};
			fexUE.initEditor();
		}
	}
}]);
