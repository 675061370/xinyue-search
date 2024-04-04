var CODE_SUCCESS = 200;
var access_token = getCookie("access_token");
var PostBase = {
    access_token: access_token,
    plat: "admin",
    version: "10000"
};
//写cookies
function setCookie(name, value) {
    var Days = 30;
    var exp = new Date();
    exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
    document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();
}
//读取cookies
function getCookie(name) {
    var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");

    if (arr = document.cookie.match(reg))

        return unescape(arr[2]);
    else
        return null;
}
//删除cookies
function delCookie(name) {
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval = getCookie(name);
    if (cval != null)
        document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
}
function IsPC()  
{  
  var userAgentInfo = navigator.userAgent;  
  var Agents = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod");  
  var flag = true;  
  for (var v = 0; v < Agents.length; v++) {  
  if (userAgentInfo.indexOf(Agents[v]) > 0) { flag = false; break; }  
  }  
  return flag; 
}
