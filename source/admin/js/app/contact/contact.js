app.controller("ContactCtrl",["$scope","$http","$filter",function(e,t,n){t.get("js/app/contact/contacts.json").then(function(t){e.items=t.data.items,e.item=n("orderBy")(e.items,"first")[0],e.item.selected=!0}),e.filter="",e.groups=[{name:"Coworkers"},{name:"Family"},{name:"Friends"},{name:"Partners"},{name:"Group"}],e.createGroup=function(){var t={name:"New Group"};t.name=e.checkItem(t,e.groups,"name"),e.groups.push(t)},e.checkItem=function(e,t,n){var i=0;return angular.forEach(t,function(t){if(0==t[n].indexOf(e[n])){var r=t[n].replace(e[n],"").trim();i=r?Math.max(i,parseInt(r)+1):1}}),e[n]+(i?" "+i:"")},e.deleteGroup=function(t){e.groups.splice(e.groups.indexOf(t),1)},e.selectGroup=function(t){angular.forEach(e.groups,function(e){e.selected=!1}),e.group=t,e.group.selected=!0,e.filter=t.name},e.selectItem=function(t){angular.forEach(e.items,function(e){e.selected=!1,e.editing=!1}),e.item=t,e.item.selected=!0},e.deleteItem=function(t){e.items.splice(e.items.indexOf(t),1),e.item=n("orderBy")(e.items,"first")[0],e.item&&(e.item.selected=!0)},e.createItem=function(){var t={group:"Friends",avatar:"img/a0.jpg"};e.items.push(t),e.selectItem(t),e.item.editing=!0},e.editItem=function(e){e&&e.selected&&(e.editing=!0)},e.doneEditing=function(e){e.editing=!1}}]);