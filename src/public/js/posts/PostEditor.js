document.querySelector("#content").oninput = function() {
	var scrollHeight = document.querySelector("#content").scrollHeight
  document.querySelector("#content").style.height = (scrollHeight + 20) + "px";
};
