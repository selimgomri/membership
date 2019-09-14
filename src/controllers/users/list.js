function getResult() {
  var search = document.getElementById("search");
  var searchValue = search.value;

  // Change query string
  window.history.replaceState('string', 'Title', <?=json_encode(autoUrl('users'))?> + '?search=' + searchValue);

  // Make ajax request
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("output").innerHTML = this.responseText;
    }
  }
  xhttp.open('POST', <?=json_encode(autoUrl('users/ajax/userList'))?>, true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('search=' + searchValue);
}
// Call getResult immediately
getResult();

document.getElementById('search').oninput=getResult;