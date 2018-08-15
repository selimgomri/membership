<?php

$pagetitle = "SMS Lists";

$sql = "SELECT * FROM `squads` ORDER BY `squads`.`SquadFee` DESC;";
$stmt = $db->query($sql);

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

?>

<div class="container">
  <div class="my-3 p-3 bg-white rounded box-shadow">
    <h1 class="border-bottom border-gray pb-2 mb-2">
      SMS Contact Lists
    </h1>
    <p class="lead">
      Select a squad to retrive a phone number list. Copy the list into the "To"
      field of your SMS App.
    </p>

		<div class="form-group">
		  <label class="sr-only" for="squad">Select a Squad</label>
		  <select class="custom-select" placeholder="Select a Squad" id="squad" name="squad">
			  <option value="allSquads">Show All Squads</option>;
			  <? foreach ($stmt as $row) { ?>
				<option value="<?php echo $row['SquadID']; ?>"><?php echo $row['SquadName']; ?></option>
				<? } ?>
	    </select>
		</div>

		<div class="form-group">
			<input id="output" class="form-control">
		  </input>
		</div>

		<p class="mb-0">
			<button class="btn btn-secondary" id="copyButton">
				Copy to Clipboard
			</button>
		</p>

  </div>
</div>

<script>
function getResult() {
  var squad = document.getElementById("squad");
  var squadValue = squad.options[squad.selectedIndex].value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("output").value = this.responseText;
        // window.history.replaceState("string", "Title", "<?php echo autoUrl("swimmers"); ?>?squadID=" + squadValue + "&search=" + searchValue);
      }
    }
    xhttp.open("POST", "<?php echo autoURL("notify/sms/ajax"); ?>", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("squadID=" + squadValue);
    console.log("Sent");
}
// Call getResult immediately
getResult();

function copyToClipboard(elem) {
  // create hidden text element, if it doesn't already exist
  var targetId = "output";
  var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
  var origSelectionStart, origSelectionEnd;
  if (isInput) {
      // can just use the original source element for the selection and copy
      target = elem;
      origSelectionStart = elem.selectionStart;
      origSelectionEnd = elem.selectionEnd;
  } else {
    // must use a temporary form element for the selection and copy
    target = document.getElementById(targetId);
    if (!target) {
      var target = document.createElement("textarea");
      target.style.position = "absolute";
      target.style.left = "-9999px";
      target.style.top = "0";
      target.id = targetId;
      document.body.appendChild(target);
    }
    target.textContent = elem.textContent;
  }
  // select the content
  var currentFocus = document.activeElement;
  target.focus();
  target.setSelectionRange(0, target.value.length);

  // copy the selection
  var succeed;
  try {
	  succeed = document.execCommand("copy");
  } catch(e) {
    succeed = false;
  }
  // restore original focus
  if (currentFocus && typeof currentFocus.focus === "function") {
    currentFocus.focus();
  }

  if (isInput) {
    // restore prior selection
    elem.setSelectionRange(origSelectionStart, origSelectionEnd);
  } else {
    // clear temporary content
    target.textContent = "";
  }
  return succeed;
}

document.getElementById("squad").onchange=getResult;
document.getElementById("copyButton").addEventListener("click", function() {
  copyToClipboard(document.getElementById("output"));
});
</script>


<?php

include BASE_PATH . "views/footer.php";

?>
