app.directive("uiEditor",["$rootScope",function(){return{restrict:"EA",require:"?ngModel",scope:{ngDisabled:"=?"},link:function(e,t,i,n){var o,a;(void 0==e.ngDisabled||""==e.ngDisabled)&&(e.ngDisabled=!1),e.$watch("ngDisabled",function(e){if(null!=e){if(!a.edit||!a.edit.doc)return;a.readonly(1==e?!0:!1)}});var l={initEditor:function(){a=KindEditor.create(t[0],{width:"100%",height:"200px",allowFileManager:!0,readonlyMode:e.ngDisabled,themeType:"default",newlineTag:"p",resizeType:1,allowPreviewEmoticons:!0,allowImageUpload:!0,allowUpload:!0,uploadJson:SITE_URL+"attachment/up_attachment",fileManagerJson:SITE_URL+"attachment/up_attachment",items:["source","fontname","fontsize","|","forecolor","hilitecolor","bold","italic","underline","removeformat","|","justifyleft","justifycenter","justifyright","insertorderedlist","insertunorderedlist","|","table","lineheight","indent","wordpaste","|","outdent","|","emoticons","image","multiimage","link","fullscreen"],afterChange:function(){n.$setViewValue(this.html())}})},setContent:function(e){a&&a.html(e)}};n&&(o=n.$viewValue,n.$render=function(){o=n.$isEmpty(n.$viewValue)?"":n.$viewValue,l.setContent(o)},l.initEditor())}}}]);