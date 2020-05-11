<?php

// function getUserNameByID($db, $id) {
// 	$sql = "SELECT `Forename`, `Surname` FROM `users` WHERE UserID = '$id';";
// 	$result = mysqli_query($db, $sql);
// 	if ($result) {
// 		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
// 		return $row['Forename'] . " " . $row['Surname'];
// 	}
// }

// function getUserInfoByID($db, $id) {
// 	$sql = "SELECT * FROM users WHERE UserID = '$id';";
// 	$outputResult = mysqli_query($db, $sql);
// 	$row = mysqli_fetch_array($outputResult, MYSQLI_ASSOC);
// 	$grav_url = 'https://www.gravatar.com/avatar/' . md5( mb_strtolower( trim( $row['EmailAddress'] ) ) ) . "?d=" . urlencode("https://www.chesterlestreetasc.co.uk/apple-touch-icon-ipad-retina.png") . "&s=80";
// 	$output = '
// 	<div class="d-flex align-items-center p-3 my-3 text-white bg-primary rounded shadow" id="dash">
// 		<img class="mr-3" src="' . $grav_url . '" alt="" width="48" height="48">
// 		<div class="lh-100">
// 			<h6 class="mb-0 text-white lh-100">' . $row['Forename'] . ' ' . $row['Surname'] . '</h6>
// 			<small>' . $row['AccessLevel'] . '</small>
// 		</div>
// 	</div>
// 	<div class="">
// 		<h2 class="border-bottom border-gray pb-2 mb-0">Basic Information</h2>
// 		<div class="media pt-3">
// 			<p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
// 				<strong class="d-block text-gray-dark">Name</strong>
// 				' . $row['Forename'] . ' ' . $row['Surname'] . '
// 			</p>
// 		</div>
// 		<div class="media pt-3">
// 			<p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
// 				<strong class="d-block text-gray-dark">User Identification Number</strong>
// 				<span class="mono">CLSU' . $row['UserID'] . '</span>
// 			</p>
// 		</div>
// 		<div class="media pt-3">
// 			<p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
// 				<strong class="d-block text-gray-dark">Username</strong>
// 				<span class="mono">' . $row['Username'] . '</span>
// 			</p>
// 		</div>';
// 		if ($row['AccessLevel'] == "Parent") {
// 			$output .= '
// 			<div class="media pt-3">
// 				<p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
// 					<strong class="d-block text-gray-dark">Fees to Pay</strong>
// 					Squads: ' . monthlyFeeCost($db, $id, "string") . ' <br>
// 					Extras (eg CF): ' . monthlyExtraCost($db, $id, "string") . '
// 				</p>
// 			</div>';
// 		}
// 		$output .= '
// 		<div class="media py-3">
// 			<p class="media-body mb-0 lh-125">
// 				<strong class="d-block text-gray-dark">Account Type</strong>
// 			</p>';
// 				$par = $coa = $com = $gal = $adm = "";
// 				if ($row['AccessLevel'] == "Coach") {
// 					$coa = "selected";
// 				} else if ($row['AccessLevel'] == "Committee") {
// 					$com = "selected";
// 				} else if ($row['AccessLevel'] == "Galas") {
// 					$gal = "selected";
// 				} else if ($row['AccessLevel'] == "Admin") {
// 					$adm = "selected";
// 				} else {
// 					$par = "selected";
// 				}
// 				$output .= '<div class="input-group mt-2 mb-0">
// 			  <div class="input-group-prepend">
// 			    <label class="input-group-text" for="accountType">Type</label>
// 			  </div>
// 			  <select class="custom-select" id="accountType" name="accountType">
// 			    <option ' . $par . ' value="Parent">Parent (Default)</option>
// 			    <option ' . $coa . ' value="Coach">Coach</option>
// 			    <option ' . $com . ' value="Committee">General Committee Member</option>
// 					<option ' . $gal . ' value="Galas">Galas</option>
// 					<option ' . $adm . ' value="Admin">Admin</option>
// 			  </select>
// 			</div>
// 			<div id="accountTypeOutput">
// 			</div>
// 		</div>
// 	</div>
// 	<div class="">
// 		<h2 class="border-bottom border-gray pb-2 mb-0">Contact Details</h2>
// 		<div class="media pt-3">
// 			<p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
// 				<strong class="d-block text-gray-dark">Email Address</strong>
// 				<a href="mailto:' . $row['EmailAddress'] . '">' . $row['EmailAddress'] . '</a>
// 			</p>
// 		</div>
// 		<div class="media pt-3">
// 			<p class="media-body mb-0 lh-125">
// 				<strong class="d-block text-gray-dark">Mobile Number</strong>
// 				' . $row['Mobile'] . '
// 			</p>
// 		</div>
// 	</div>
// 	<script>
// 	function apply() {
// 	  var type = document.getElementById("accountType");
// 	  var typeValue = type.value;
// 	  console.log(typeValue);
// 	    var xhttp = new XMLHttpRequest();
// 	    xhttp.onreadystatechange = function() {
// 	      if (this.readyState == 4 && this.status == 200) {
// 	        console.log("We got here");
// 	        document.getElementById("accountTypeOutput").innerHTML = this.responseText;
// 	        console.log(this.responseText);
// 	      }
// 	    }
// 	    xhttp.open("POST", "' . autoURL("users/ajax/userSettings/" . $id) . '", true);
// 	    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
// 	    xhttp.send("accountType=" + typeValue);
// 	    console.log("Sent");
// 	}

// 	document.getElementById("accountType").onchange=apply;
// 	</script>
// 	';
// 	return $output;
// }
