var notifyCounter = 0;

function addStyleNotify(){
  const style = document.createElement('style');
  style.setAttribute('id','notiStyle');
  style.innerHTML = `
    .noti{
      z-index: 99;
      position: fixed;
      border-radius: 5px;
      padding: 10px;
      color: white;
      right: 5px;
      animation: fadeOut ease 5s;
      display: block;
      opacity: 0;
    }

    @keyframes fadeOut {
      0% {
        opacity:1;
      }
      20% {
        opacity:1;
      }
      100% {
        opacity:0;
      }
    }
  `;
  document.head.appendChild(style);
}

function sendNotification(text, color = "green"){
  var notiStyle = document.getElementById("notiStyle");
  if(notiStyle == null){
    addStyleNotify();
  }
  var top = 5 + 50 * notifyCounter;
  var div = document.createElement("div");
  div.className = "noti";
  div.style.top = top + "px";
  div.style.backgroundColor = color;
  div.innerHTML = text;

  document.body.appendChild(div);
  notifyCounter++;
  setTimeout(delNotification, 5000, div)
}

function delNotification(div){
  div.remove();
  notifyCounter--;
}