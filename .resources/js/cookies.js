function createCookie(name, value){
  document.cookie = name + "= " + value + "; expires=Thu, 18 Dec 2213 12:00:00 UTC; SameSite=Lax";
}

function getCookie(id) {
  var name = id + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function delCookie(name){
  document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; SameSite=Lax";
}
