document.getElementById('squad-select').addEventListener('change', function(event) {
  var squad = document.getElementById('squad-select').value;
  if (squad !== null) {
    // Redirect to new page
    window.location.href = <?=json_encode(autoUrl("galas/3/squad-rep-view?squad="))?> + squad;
  }
});

function ajaxUpdate(event, entryId, state) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      console.log(this.responseText);
    }
  };
  xhttp.open('POST', <?=json_encode(autoUrl('galas/squad-reps/entry-states'))?>, true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send(encodeURI('entry=' + entryId + '&state=' + state + '&event=' + event));
}

document.getElementById('entries-list').addEventListener("click", clickPropogation, false);

function clickPropogation(e) {
  if (e.target !== e.currentTarget) {
    var clickedItem = e.target.id;
    var clickedItemChecked;
    if (clickedItem != "") {
      var item = document.getElementById(clickedItem);
      var clickedItemChecked = item.checked;
      var entryId = item.dataset.entryId;
      if (item.dataset.ajaxAction == 'mark-paid') {
        ajaxUpdate('mark-paid', entryId, clickedItemChecked);
      } else if (item.dataset.ajaxAction == 'approve-entry') {
        ajaxUpdate('approve-entry', entryId, clickedItemChecked);
      }
    }
  }
  e.stopPropagation();
}