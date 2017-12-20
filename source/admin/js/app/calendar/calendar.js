app.controller("FullcalendarCtrl",["$scope",function(e){var a=new Date,t=a.getDate(),l=a.getMonth(),n=a.getFullYear();e.eventSource={url:"http://www.google.com/calendar/feeds/usa__en%40holiday.calendar.google.com/public/basic",className:"gcal-event",currentTimezone:"America/Chicago"},e.events=[{title:"All Day Event",start:new Date(n,l,1),className:["b-l b-2x b-info"],location:"New York",info:"This a all day event that will start from 9:00 am to 9:00 pm, have fun!"},{title:"Dance class",start:new Date(n,l,3),end:new Date(n,l,4,9,30),allDay:!1,className:["b-l b-2x b-danger"],location:"London",info:"Two days dance training class."},{title:"Game racing",start:new Date(n,l,6,16,0),className:["b-l b-2x b-info"],location:"Hongkong",info:"The most big racing of this year."},{title:"Soccer",start:new Date(n,l,8,15,0),className:["b-l b-2x b-info"],location:"Rio",info:"Do not forget to watch."},{title:"Family",start:new Date(n,l,9,19,30),end:new Date(n,l,9,20,30),className:["b-l b-2x b-success"],info:"Family party"},{title:"Long Event",start:new Date(n,l,t-5),end:new Date(n,l,t-2),className:["bg-success bg"],location:"HD City",info:"It is a long long event"},{title:"Play game",start:new Date(n,l,t-1,16,0),className:["b-l b-2x b-info"],location:"Tokyo",info:"Tokyo Game Racing"},{title:"Birthday Party",start:new Date(n,l,t+1,19,0),end:new Date(n,l,t+1,22,30),allDay:!1,className:["b-l b-2x b-primary"],location:"New York",info:"Party all day"},{title:"Repeating Event",start:new Date(n,l,t+4,16,0),alDay:!1,className:["b-l b-2x b-warning"],location:"Home Town",info:"Repeat every day"},{title:"Click for Google",start:new Date(n,l,28),end:new Date(n,l,29),url:"http://google.com/",className:["b-l b-2x b-primary"]},{title:"Feed cat",start:new Date(n,l+1,6,18,0),className:["b-l b-2x b-info"]}],e.precision=400,e.lastClickTime=0,e.alertOnEventClick=function(a){var t=(new Date).getTime();t-e.lastClickTime<=e.precision&&e.events.push({title:"New Event",start:a,className:["b-l b-2x b-info"]}),e.lastClickTime=t},e.alertOnDrop=function(a,t){e.alertMessage="Event Droped to make dayDelta "+t},e.alertOnResize=function(a,t){e.alertMessage="Event Resized to make dayDelta "+t},e.overlay=$(".fc-overlay"),e.alertOnMouseOver=function(a,t){e.event=a,e.overlay.removeClass("left right").find(".arrow").removeClass("left right top pull-up");var l=$(t.target).closest(".fc-event"),n=l.closest(".calendar"),o=l.offset().left-n.offset().left,i=n.width()-(l.offset().left-n.offset().left+l.width());i>e.overlay.width()?e.overlay.addClass("left").find(".arrow").addClass("left pull-up"):o>e.overlay.width()?e.overlay.addClass("right").find(".arrow").addClass("right pull-up"):e.overlay.find(".arrow").addClass("top"),0==l.find(".fc-overlay").length&&l.append(e.overlay)},e.uiConfig={calendar:{height:450,editable:!0,header:{left:"prev",center:"title",right:"next"},dayClick:e.alertOnEventClick,eventDrop:e.alertOnDrop,eventResize:e.alertOnResize,eventMouseover:e.alertOnMouseOver}},e.addEvent=function(){e.events.push({title:"New Event",start:new Date(n,l,t),className:["b-l b-2x b-info"]})},e.remove=function(a){e.events.splice(a,1)},e.changeView=function(e){$(".calendar").fullCalendar("changeView",e)},e.today=function(){$(".calendar").fullCalendar("today")},e.eventSources=[e.events]}]);