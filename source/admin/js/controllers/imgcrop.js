app.controller("ImgCropCtrl",["$scope",function(e){e.myImage="",e.myCroppedImage="",e.cropType="circle";var r=function(r){var n=r.currentTarget.files[0],a=new FileReader;a.onload=function(r){e.$apply(function(e){e.myImage=r.target.result})},a.readAsDataURL(n)};angular.element(document.querySelector("#fileInput")).on("change",r)}]);