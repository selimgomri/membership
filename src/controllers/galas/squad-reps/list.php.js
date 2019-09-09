document.getElementById('squad-select').addEventListener('change', function(event) {
  var squad = document.getElementById('squad-select').value;
  if (squad !== null) {
    // Redirect to new page
    window.location.href = <?=json_encode(autoUrl("galas/3/squad-rep-view?squad="))?> + squad;
  }
});