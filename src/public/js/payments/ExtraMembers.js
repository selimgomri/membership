
function getSwimmers() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      console.log("We got here");
      document.getElementById("output").innerHTML = this.responseText;
      console.log(this.responseText);
    }
  }
  xhttp.open("POST", document.getElementById('addSwimmer').dataset.ajaxUrl, true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("response=getSwimmers");
  console.log("Sent");
}

function getSwimmersForSquad() {
var squad = (document.getElementById("squadSelect")).value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      console.log("We got here");
      document.getElementById("swimmerSelect").innerHTML = this.responseText;
      console.log(this.responseText);
    }
  }
  xhttp.open("POST", document.getElementById('addSwimmer').dataset.ajaxUrl, true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("response=squadSelect&squadSelect=" + squad);
  console.log("Sent");
}

function addSwimmerToExtra() {
  var button = document.getElementById('addSwimmer');
  var ajaxUrl = button.dataset.ajaxUrl;
  button.disabled = true;
  var swimmer = (document.getElementById('swimmerSelect')).value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    var status = document.getElementById('status');
    if (this.readyState == 4 && this.status == 200) {
      getSwimmers();
      button.disabled = false;
      var response = JSON.parse(this.response);
      status.innerHTML =
      '<div class="mt-3 mb-0 alert alert-dismissible fade show" id="status-alert-box" role="alert">' + response.alertContent +
      '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
      '<span aria-hidden="true">&times;</span>' +
      '</button>' +
      '</div>';
      document.getElementById('status-alert-box').classList.add(response.alertClass);
    } else if (this.readyState == 4) {
      button.disabled = false;
      status.innerHTML =
      '<div class="mt-3 mb-0 alert alert-warning alert-dismissible fade show" role="alert">' +
      '<strong>An unknown error occurred</strong><br>Please try again later' +
      '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
      '<span aria-hidden="true">&times;</span>' +
      '</button>' +
      '</div>';
    }
  }
  xhttp.open('POST', ajaxUrl, true);
  xhttp.setRequestHeader("content-type", "application/x-www-form-urlencoded");
  xhttp.send("response=insert&swimmerInsert=" + swimmer);
  console.log("response=insert&swimmerInsert=" + swimmer);
  console.log("Sent");
}

function dropSwimmerFromExtra(relation) {
var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
  if (this.readyState == 4 && this.status == 200) {
    getSwimmers();
  }
}
xhttp.open("POST", document.getElementById('addSwimmer').dataset.ajaxUrl, true);
xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xhttp.send("response=dropRelation&relation=" + relation);
}

var entryTable = document.querySelector("#output");
entryTable.addEventListener("click", clickPropogation, false);

function clickPropogation(e) {
  if (e.target !== e.currentTarget) {
      var clickedItem = e.target.id;
      var clickedItemValue;
      if (clickedItem != "") {
        var clickedItemValue = document.getElementById(clickedItem).value;
        dropSwimmerFromExtra(clickedItemValue);
      }
  }
  e.stopPropagation();
}

// Call getResult immediately
getSwimmers();
document.getElementById("squadSelect").onchange=getSwimmersForSquad;
document.getElementById("addSwimmer").onclick=addSwimmerToExtra;