app.controller("MailCtrl",["$scope",function(a){a.folds=[{name:"Inbox",filter:""},{name:"Starred",filter:"starred"},{name:"Sent",filter:"sent"},{name:"Important",filter:"important"},{name:"Draft",filter:"draft"},{name:"Trash",filter:"trash"}],a.labels=[{name:"Angular",filter:"angular",color:"#23b7e5"},{name:"Bootstrap",filter:"bootstrap",color:"#7266ba"},{name:"Client",filter:"client",color:"#fad733"},{name:"Work",filter:"work",color:"#27c24c"}],a.addLabel=function(){a.labels.push({name:a.newLabel.name,filter:angular.lowercase(a.newLabel.name),color:"#ccc"}),a.newLabel.name=""},a.labelClass=function(a){return{"b-l-info":"angular"===angular.lowercase(a),"b-l-primary":"bootstrap"===angular.lowercase(a),"b-l-warning":"client"===angular.lowercase(a),"b-l-success":"work"===angular.lowercase(a)}}}]),app.controller("MailListCtrl",["$scope","mails","$stateParams",function(a,l,e){a.fold=e.fold,l.all().then(function(l){a.mails=l})}]),app.controller("MailDetailCtrl",["$scope","mails","$stateParams",function(a,l,e){l.get(e.mailId).then(function(l){a.mail=l})}]),app.controller("MailNewCtrl",["$scope",function(a){a.mail={to:"",subject:"",content:""},a.tolist=[{name:"James",email:"james@gmail.com"},{name:"Luoris Kiso",email:"luoris.kiso@hotmail.com"},{name:"Lucy Yokes",email:"lucy.yokes@gmail.com"}]}]),angular.module("app").directive("labelColor",function(){return function(a,l,e){l.css({color:e.color})}});