function autoGrow(element) {
  element.style.height = "5rem";
  element.style.height = (element.scrollHeight+20)+"px";
}

autoGrow(document.getElementById('content'));