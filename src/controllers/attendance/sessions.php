<?php
/*$epoch = date(DATE_ATOM, mktime(0, 0, 0, 1, 1, 1970));
$displayUntil = date(strtotime());
echo $epoch . "<br>";
if ($displayUntil < $epoch) {
  $displayUntil = null;
  echo "TRUE";
}*/
$content .= '

<div class="my-3 p-3 bg-white rounded box-shadow">
  <h2 class="border-bottom border-gray pb-2">Select a Squad to Manage its Sessions</h2>
  <form>
  <div class="form-group">
  <label for="squad">Select Squad</label>
  <select class="custom-select" name="squad" id="squad">
    <option value="0">Choose your squad from the menu</option>';
    $sql = "SELECT SquadID, SquadName FROM squads ORDER BY SquadFee DESC, SquadName ASC";
    $result = mysqli_query($link, $sql);
    $count = mysqli_num_rows($result);
    if ($count > 0) {
      for ($i = 0; $i < $count; $i++) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $content .= "<option value=\"" . $row['SquadID'] . "\"";
        $content .= ">" . $row['SquadName'] . "</option>";
      }
    }
  $content .= '</select>
  </div>
  </form>
  <p class="mb-0">Then select from the options below to either View Sessions or Add a New Session for the squad</p>
  </div>

  <div id="modalArea"><div id="output"><div class="ajaxPlaceholder"><strong>Session Manager will appear here</strong> <br>Select a squad first</div></div></div>

  <script>
  function resetRegisterArea() {
    var register = document.getElementById("output");
    register.innerHTML = \'<div class="ajaxPlaceholder mb-0">Fill in the details above and we can load the register</div>\';
  }
  function getSessions() {
    var squad = document.getElementById("squad");
    var squadValue = squad.options[squad.selectedIndex].value;
    console.log(squadValue);
      if (squadValue == 0) {
        document.getElementById("output").innerHTML = \'<div class="ajaxPlaceholder"><strong>Session Manager will appear here</strong> <br>Select a squad first</div>\';
        return;
      }
      else {
        var sessAjax = new XMLHttpRequest();
        sessAjax.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            document.getElementById("output").innerHTML = this.responseText;
            console.log(this.responseText);
          }
        }
        sessAjax.open("POST", "' . autoURL("ajax/sessions.php") . '", true);
        sessAjax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        sessAjax.send("action=getSessions&squadID=" + squadValue);
        console.log("Sent");
      }
    }
    function addSession() {
    	var squad = document.getElementById("squad");
    	var squadValue = squad.options[squad.selectedIndex].value;
      console.log(squadValue);

    	var sessionName = document.getElementById("newSessionName");
    	var sessionNameValue = sessionName.value;
      console.log(sessionNameValue);

    	var sessionDay = document.getElementById("newSessionDay");
    	var sessionDayValue = sessionDay.options[sessionDay.selectedIndex].value;
      console.log(sessionDayValue);

    	var sessionVenue = document.getElementById("newSessionVenue");
    	var sessionVenueValue = sessionVenue.options[sessionVenue.selectedIndex].value;
      console.log(sessionVenueValue);

    	var sessionStart = document.getElementById("newSessionStartTime");
    	var sessionStartValue = sessionStart.value;
      console.log(sessionStartValue);

    	var sessionEnd = document.getElementById("newSessionEndTime");
    	var sessionEndValue = sessionEnd.value;
      console.log(sessionEndValue);

      var startDate = document.getElementById("newSessionStartDate");
    	var startDateValue = startDate.value;
      console.log(startDateValue);

    	var endDate = document.getElementById("newSessionEndDate");
    	var endDateValue = endDate.value;
      console.log(endDateValue);

      var mainSequenceValue = null;
      var radios = document.getElementsByName("newSessionMS");
      for (var i = 0, length = radios.length; i < length; i++)
      {
       if (radios[i].checked)
       {
        // do whatever you want with the checked radio
        mainSequenceValue = radios[i].value;

        // only one radio can be logically checked, don\'t check the rest
        break;
       }
      }

      console.log("HELLO");

    	var sessAjax = new XMLHttpRequest();
    	sessAjax.onreadystatechange = function() {
    		if (this.readyState == 4 && this.status == 200) {
    			document.getElementById("output").innerHTML = this.responseText;
    			console.log(this.responseText);
    		}
      }
  		sessAjax.open("POST", "' . autoURL("ajax/sessions.php") . '", true);
  		sessAjax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  		sessAjax.send("action=addSession&squadID=" + squadValue + "&sessionName=" + sessionNameValue + "&venueID=" + sessionVenueValue + "&sessionDay=" + sessionDayValue + "&startTime=" + sessionStartValue + "&endTime=" + sessionEndValue + "&newSessionMS=" + mainSequenceValue + "&newSessionStartDate=" + startDateValue + "&newSessionEndDate=" + endDateValue);
  		console.log("Sent");
  		console.log("action=addSession&squadID=" + squadValue + "&sessionName=" + sessionNameValue + "&venueID=" + sessionVenueValue + "&sessionDay=" + sessionDayValue + "&startTime=" + sessionStartValue + "&endTime=" + sessionEndValue + "&newSessionMS=" + mainSequenceValue + "&newSessionStartDate=" + startDateValue + "&newSessionEndDate=" + endDateValue);
    }
  document.getElementById("squad").onchange=getSessions;
</script>';
?>
