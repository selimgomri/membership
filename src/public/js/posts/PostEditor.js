function autoGrow(element) {
  element.style.height = "10000px";
  element.style.height = (element.scrollHeight+20)+"px";
}

autoGrow(document.getElementById('content'));