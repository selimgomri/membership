
function getSwimmers() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById('output').innerHTML = this.responseText;
    }
  }
  xhttp.open('POST', document.getElementById('addSwimmer').dataset.ajaxUrl, true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('response=getSwimmers');
}

function getSwimmersForSquad() {
  var squad = (document.getElementById('squadSelect')).value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById('swimmerSelect').innerHTML = this.responseText;
    }
  }
  xhttp.open('POST', document.getElementById('addSwimmer').dataset.ajaxUrl, true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('response=squadSelect&squadSelect=' + squad);
}

function addSwimmerToList() {
  var swimmer = (document.getElementById('swimmerSelect')).value;
  if (swimmer !== 'Select a swimmer') {
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        getSwimmers();
        getSwimmersForSquad();
        document.getElementById("status").innerHTML =
        '<div class="mt-3 mb-0 alert alert-success alert-dismissible fade show" role="alert">' +
        '<strong>Successfully Added Swimmer</strong>'  +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        '</button>' +
        '</div>';
      } else {
        document.getElementById("status").innerHTML =
        '<div class="mt-3 mb-0 alert alert-warning alert-dismissible fade show" role="alert">' +
        '<strong>Unable to add swimmer</strong><br>They may already be on the list' +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        '</button>' +
        '</div>';
      }
    }
    xhttp.open('POST', document.getElementById('addSwimmer').dataset.ajaxUrl, true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send('response=insert&swimmerInsert=' + swimmer);
  } else {
    document.getElementById('status').innerHTML =
    '<div class="mt-3 mb-0 alert alert-warning alert-dismissible fade show" role="alert">' +
    '<strong>No swimmer selected</strong><br>Please select a swimmer to add to this list' +
    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
    '<span aria-hidden="true">&times;</span>' +
    '</button>' +
    '</div>';
  }
}

function dropSwimmerFromList(relation) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      getSwimmers();
    }
  }
  xhttp.open('POST', document.getElementById('addSwimmer').dataset.ajaxUrl, true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send('response=dropRelation&relation=' + relation);
  console.log(relation);
}

var entryTable = document.querySelector('#output');
entryTable.addEventListener('click', clickPropogation, false);

function clickPropogation(e) {
  if (e.target !== e.currentTarget) {
    var clickedItem = e.target.id;
    var clickedItemValue;
    if (clickedItem != '') {
      var clickedItemValue = document.getElementById(clickedItem).value;
      dropSwimmerFromList(clickedItemValue);
    }
  }
  e.stopPropagation();
}

// Call getResult immediately
getSwimmers();
getSwimmersForSquad();
document.getElementById('squadSelect').onchange=getSwimmersForSquad;
document.getElementById('addSwimmer').onclick=addSwimmerToList;