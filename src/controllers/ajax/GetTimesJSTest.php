<?php

include BASE_PATH . 'views/head.php';

?>

<div> <!-- To be caught at end -->

<div class="container py-5">
  <h1>Get Times JS Test</h1>
</div>

<script>
var xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
  if (this.readyState == 4 && this.status == 200) {
    // Typical action to be performed when the document is ready
    console.log(xhttp.responseText);
    //document.getElementById("demo").innerHTML = xhttp.responseText;
  }
};
xhttp.open('GET', 'https://cors-anywhere.herokuapp.com/https://www.swimmingresults.org/biogs/biogs_details.php?tiref=731872', true);
xhttp.send();
</script>

<?php

$footer = new \SDCS\Footer();
$footer->render();
