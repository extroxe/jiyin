app.factory("mails",["$http",function(n){var t="js/app/mail/mails.json",r=n.get(t).then(function(n){return n.data.mails}),a={};return a.all=function(){return r},a.get=function(n){return r.then(function(t){for(var r=0;r<t.length;r++)if(t[r].id==n)return t[r];return null})},a}]);