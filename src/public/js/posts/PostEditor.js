/**
 * Function to automatically resize the textarea
 * @param  string box id of the box to select
 * @return void
 */
function resizeBox(box) {
	var scrollHeight = document.querySelector(box).scrollHeight
  document.querySelector(box).style.height = (scrollHeight) + "px";
}

document.querySelector("#content") = resizeBox("#content");
document.querySelector("#content").oninput = resizeBox("#content");
