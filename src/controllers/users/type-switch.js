function apply() {
  var type = document.getElementById('accountType');
  var typeValue = type.value;
  var user = type.dataset.userId;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById('accountTypeOutput').innerHTML = this.responseText;
    }
  }
  xhttp.open("POST", <?=json_encode(autoUrl('users/ajax/userSettings/'))?> + user, true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('accountType=' + typeValue);
}

document.getElementById('accountType').onchange=apply;