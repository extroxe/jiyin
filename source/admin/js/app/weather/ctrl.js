function JSON_CALLBACK(){}app.controller("WeatherCtrl",["$scope","yahooApi","geoApi",function(e,c,a){e.userSearchText="",e.search={},e.forcast={},e.place={},e.data={},a.then(function(c){e.userSearchText=c.data.city+", "+c.data.country_code,e.getLocations()}),e.getLocations=function(){var a='select * from geo.places where text="'+e.userSearchText+'"';c.query({q:a,format:"json"},{},function(c){e.search=c,1!==c.query.count||c.query.results.channel||e.getWeather(c.query.results.place.woeid,c.query.results.place.name,c.query.results.place.country.content)})},e.getWeather=function(a,t,s){e.place.city=t,e.place.country=s;var r="select item from weather.forecast where woeid="+a;c.query({q:r,format:"json"},{},function(c){c.query.results.channel.item.forecast.forEach(function(c){c.icon=e.getCustomIcon(c.code)}),c.query.results.channel.item.condition.icon=e.getCustomIcon(c.query.results.channel.item.condition.code),e.data=c})},e.getCustomIcon=function(e){switch(e){case"0":case"1":case"2":case"24":case"25":return"wind";case"5":case"6":case"7":case"18":return"sleet";case"3":case"4":case"8":case"9":case"10":case"11":case"12":case"35":case"37":case"38":case"39":case"40":case"45":case"47":return"rain";case"13":case"14":case"15":case"16":case"17":case"41":case"42":case"43":case"46":return"snow";case"19":case"20":case"21":case"22":case"23":return"fog";case"26":case"27":case"28":case"44":return"cloudy";case"29":return"partly-cloudy-night";case"30":return"partly-cloudy-day";case"31":case"33":return"clear-night";case"32":case"34":case"36":return"clear-day";default:return""}}}]),app.factory("yahooApi",["$resource",function(e){return e("http://query.yahooapis.com/v1/public/yql",{},{query:{method:"GET",isArray:!1}})}]),app.factory("geoApi",["$http",function(e){return e.jsonp("http://muslimsalat.com/daily.json?callback=JSON_CALLBACK")}]);