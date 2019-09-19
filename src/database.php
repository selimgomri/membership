<?php

include BASE_PATH . 'includes/regions/countries-iso3166.php';

function bool($var) {
  return filter_var($var, FILTER_VALIDATE_BOOLEAN);
}

function verifyUser($user, $password) {
  global $db;

  $username = trim($user);
  $password = trim($password);

  try {
    $query = $db->prepare("SELECT Password, UserID, AccessLevel FROM users WHERE EmailAddress = :user LIMIT 1");
    $query->execute(['user' => $username]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    $count = sizeof($result);

    if ($count == 1) {
      $hash = $result[0]['Password'];

      if (password_verify($password, $hash)) {
        if ($result[0]['AccessLevel'] != 'Parent') {
          return true;
        } else {
          // Verify parent has connected child
          $sql = "SELECT COUNT(*) FROM `members` WHERE `UserID` = ?";
        	try {
        		$query = $db->prepare($sql);
        		$query->execute([$result[0]['UserID']]);
            if ($query->fetchColumn() == 0) {
              halt(404);
            }
        	} catch (PDOException $e) {
        		halt(500);
        	}
        }
      }
    }
  } catch (PDOException $e) {
    halt(500);
  }
  return false;
}

function notifySend($to, $subject, $emailMessage, $name = null, $emailaddress = null, $from = null) {

  $email = new \SendGrid\Mail\Mail();
  $mailObject = new \CLSASC\SuperMailer\CreateMail();
  //echo $mailObject->getFormattedHtml();
  //echo $mailObject->getFormattedPlain();

  if (!isset($from['Email'])) {
    $from['Email'] = "noreply@" . env('EMAIL_DOMAIN');
  }
  if (!isset($from['Name'])) {
    $from['Name'] = env('CLUB_NAME');
  }

  $cellClass = 'style="display:table;background:#eee;padding:10px;margin 0 auto 10px auto;width:100%;"';
  $htmlMessage = str_replace('class="cell"', $cellClass, $emailMessage);

  $mailObject->setHtmlContent($htmlMessage);

  if (isset($from['PlainText']) && $from['PlainText']) {
    $message = $emailMessage;
    $mailObject->setHtmlContent($from['PlainText']);
  }

  if (isset($from['Unsub']['Allowed']) && $from['Unsub']['Allowed']) {
    $mailObject->setUnsubscribable();
  }

  if ($from['Email'] == "notify@" . env('EMAIL_DOMAIN') || $from['Email'] == "payments@" . env('EMAIL_DOMAIN')) {
    $email->addHeader("List-Archive", autoUrl("myaccount/notify/history"));
  }

  $email->addHeader("List-Help", autoUrl("notify"));

  if (bool(env('IS_CLS'))) {
    if ($from['Email'] == "notify@" . env('EMAIL_DOMAIN')) {
      $email->addHeader("List-ID", "CLS ASC Targeted Lists <targeted-lists@account." . env('EMAIL_DOMAIN') . ">");
    } else if ($from['Email'] == "payments@" . env('EMAIL_DOMAIN')) {
      $email->addHeader("List-ID", "Direct Debit Payment Information <payment-news@account." . env('EMAIL_DOMAIN') . ">");
    } else if ($from['Name'] == env('CLUB_NAME') . " Security") {
      $email->addHeader("List-ID", "Account Security Updates <account-updates@account." . env('EMAIL_DOMAIN') . ">");
    }

    if ($from['Email'] == "payments@" . env('EMAIL_DOMAIN')) {
      $email->setReplyTo("payments+replytoautoemail@chesterlestreetasc.co.uk", "Payments Team");
    } else if ($from['Email'] == "galas@" . env('EMAIL_DOMAIN')) {
      $email->setReplyTo("galas+replytoautoemail@" . env('EMAIL_DOMAIN'), "Gala Administrator");
    } else if ($from['Name'] == "Chester-le-Street ASC Security") {
      $email->setReplyTo("support+security-replytoautoemail@" . env('EMAIL_DOMAIN'), env('CLUB_SHORT_NAME') . " Support");
    } else {
      $email->setReplyTo("enquiries+replytoautoemail@" . env('EMAIL_DOMAIN'), env('CLUB_SHORT_NAME') . " Enquiries");
    }

    if (isset($from['Reply-To']) && $from['Reply-To'] != null) {
      $email->setReplyTo($from['Reply-To']);
    }
  }

  if (isset($from['CC']) && $from['CC'] != null) {
    $email->addCcs($from['CC']);
  }

  if (isset($from['BCC']) && $from['BCC'] != null) {
    $email->addBccs($from['BCC']);
  }

  $plain = $mailObject->getFormattedPlain();
  $html = $mailObject->getFormattedHtml();

  if (isset($from['Unsub']['Allowed']) && $from['Unsub']['Allowed']) {
    $unsubLink = autoUrl("notify/unsubscribe/" . dechex($from['Unsub']['User']) .  "/" . urlencode($emailaddress) . "/" . urlencode($from['Unsub']['List']));
    $plain = str_replace('-unsub_link-', $unsubLink, $plain);
    $html = str_replace('-unsub_link-', $unsubLink, $html);
  }

  if (env('SENDGRID_API_KEY') && $emailaddress != null && $name != null) {
    $email->setReplyTo(env('CLUB_EMAIL'), env('CLUB_NAME'));
    $email->setFrom($from['Email'], $from['Name']);
    $email->setSubject($subject);
    $email->addTo($emailaddress, $name);
    $email->addContent("text/plain", $plain);
    if (isset($from['PlainText']) && $from['PlainText']) {
      $email->addContent(
        "text/html", $plain
      );
    } else {
      $email->addContent(
        "text/html", $html
      );
    }

    $sendgrid = new \CLSASC\SuperMailer\SuperMailer(env('SENDGRID_API_KEY'));
    try {
      $response = $sendgrid->send($email);
    } catch (Exception $e) {
      //echo $e;
      return false;
    }
    return true;

  } else {
    // Using PHP Mail is a last resort if stuff goes really wrong
    if (mail($to,$subject,$head . $message,$headers)) {
      return true;
    }

  }

  return false;
}

function getAttendanceByID($link, $id, $weeks = "all") {
  global $db;

  $output = "";
  $startWeek = 1;

  // Get the last four weeks to calculate attendance
  $latestWeek = $db->query("SELECT MAX(WeekID) FROM `sessionsWeek`;")->fetchColumn();

  if ($weeks != "all") {
    $startWeek = $latestWeek - $weeks;
    if ($startWeek < 1) {
      $startWeek = 1;
    }
  }

  $member = [
    "week" => $startWeek,
    "member" => $id
  ];

  $numPresent = $db->prepare("SELECT COUNT(*) FROM `sessionsAttendance` WHERE WeekID >= :week && MemberID = :member && AttendanceBoolean = 1");
  $numPresent->execute($member);
  $numPresent = $numPresent->fetchColumn();
  $totalNum = $db->prepare("SELECT COUNT(*) FROM `sessionsAttendance` WHERE WeekID >= :week && MemberID = :member");
  $totalNum->execute($member);
  $totalNum = $totalNum->fetchColumn();

  if ($totalNum == 0) {
    return "No Data 0";
  }

  return number_format(($numPresent/$totalNum)*100, 1, ".", "");
}

function mySwimmersTable($link, $userID) {
  global $db;
  // Get the information about the swimmer
  $swimmers = $db->prepare("SELECT members.MemberID, members.MForename, members.MSurname,
  members.ClubPays, users.Forename, users.Surname, users.EmailAddress,
  members.ASANumber, squads.SquadName, squads.SquadFee FROM ((members INNER JOIN
  users ON members.UserID = users.UserID) INNER JOIN squads ON members.SquadID =
  squads.SquadID) WHERE members.UserID = ?");
  $swimmers->execute([$userID]);
  $swimmer = $swimmers->fetch(PDO::FETCH_ASSOC);

  if ($swimmer != null) { ?>
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="thead-light">
        <tr>
          <th>Name</th>
          <th>Squad</th>
          <th>Fee</th>
          <th>Swim England Number</th>
          <th><abbr title="Approximate attendance over the last 4
          weeks">Attendance</abbr></th>
        </tr>
      </thead>
      <tbody>
      <?php do { ?>
      <tr>
        <td>
          <a href="<?=autoUrl("swimmers/" . $swimmer['MemberID'])?>">
            <?=htmlspecialchars($swimmer['MForename'] . " " . $swimmer['MSurname'])?>
          </a>
        </td>
        <td>
          <?=htmlspecialchars($swimmer['SquadName'])?>
        </td>
        <?php if (!$swimmer['ClubPays']) { ?>
          <td>&pound;<?=number_format($swimmer['SquadFee'], 2)?></td>
        <?php } else { ?>
          <td>&pound;0.00 - Exempt</td>
        <?php } ?>
        <td>
          <a href="https://www.swimmingresults.org/biogs/biogs_details.php?tiref=<?=htmlspecialchars($swimmer['ASANumber'])?>"  target="_blank" title="Swim England Biographical Data">
            <?=htmlspecialchars($swimmer['ASANumber'])?> <i class="fa fa-external-link" aria-hidden="true"></i>
          </a>
        </td>
        <td>
          <?=htmlspecialchars(getAttendanceByID($link, $swimmer['MemberID'], 4))?>%
        </td>
      </tr>
    <?php } while ( $swimmer = $swimmers->fetch(PDO::FETCH_ASSOC)); ?>
        </tbody>
      </table>
    </div>
  <?php }
}

function generateRandomString($length) {
  $characters =
  '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

function courseLengthString($string) {
  $courseLength;
  if ($string == "SHORT") {
    $courseLength = "Short Course";
  }
  else if ($string == "LONG") {
    $courseLength = "Long Course";
  }
  else {
    $courseLength = "Non Standard Pool Distance";
  }
  return $courseLength;
}

function upcomingGalas($link, $links = false, $userID = null) {
  global $db;
  $sql = $db->query("SELECT * FROM `galas` WHERE `galas`.`ClosingDate` >= CURDATE() ORDER BY `galas`.`ClosingDate` ASC;");
  $row = $sql->fetch(PDO::FETCH_ASSOC);
  if ($row != null) {
    $output= "<div class=\"media\">";
    do {
      $output .= " <ul class=\"media-body pt-2 pb-2 mb-0 lh-125 ";
      if ($i != $count-1) {
        if (app('request')->ajax) {
          $output .= "border-bottom border-white";
        } else {
          $output .= "border-bottom border-gray";
        }
      }
      $output .= " list-unstyled\"> <li><strong class=\"d-block
      text-gray-dark\">";
      if ($links == true) {
        $output .= htmlspecialchars($row['GalaName']) . " (" .
        courseLengthString($row['CourseLength']) . ") <a href=\"" .
        autoUrl("galas/competitions/" . $row['GalaID'] . "") . "\"><span
        class=\"small\">Edit Gala and View Statistics</span></a></li>";         } else {
        $output .= "" . $row['GalaName'] . " (" .
        courseLengthString($row['CourseLength']) . ")</li>";
      }
      $output .= "</strong></li>";
      $output .= "<li>" . htmlspecialchars($row['GalaVenue']) . "<br>";
      $output .= "<li>Closing Date " . date('jS F Y',
      strtotime($row['ClosingDate'])) . "</li>";
      if ($userID == null) {
        $output .= "<li>Finishes on " . date('jS F Y',
        strtotime($row['GalaDate'])) . "</li>";
      }
      if ($row['GalaFee'] > 0) {
        $output .= "<li>Entry Fee of &pound;" .
        number_format($row['GalaFee'],2,'.','') . "/Swim</li>";
      }
      else {
        $output .= "<li>Entry fee varies by event</li>";
      }
      $output .= "</ul>";
    } while ($row = $sql->fetch(PDO::FETCH_ASSOC));
    $output .= "</div>";
  }
  else {
    $output .= "<p class=\"lead mb-0 mt-2\">There are no galas available to enter</p>";
  }
  return $output;
}

function myMonthlyFeeTable($link, $userID) {
  global $db;
  $sql = $db->prepare("SELECT squads.SquadName, squads.SquadID, squads.SquadFee,
  members.MForename, members.MSurname FROM (members INNER JOIN squads ON
  members.SquadID = squads.SquadID) WHERE members.UserID = ? AND
  members.ClubPays = '0' ORDER BY `squads`.`SquadFee` DESC;");
  $sql->execute([$userID]);

  $rows = $sql->fetchAll(PDO::FETCH_ASSOC);

  $count = sizeof($rows);
  $totalsArray = [];
  $squadsOutput = "";
  $totalCost = 0;
  $reducedCost = 0;
  for ($i = 0; $i < $count; $i++) {
    $row = $rows[$i];
    $totalsArray[$i] = $row['SquadFee'];
    $totalCost += $totalsArray[$i];
    $squadsOutput .= "<tr><td>" . htmlspecialchars($row['SquadName']) . " Squad <br>for " .
    htmlspecialchars($row['MForename'] . " " . $row['MSurname']) . "</td><td>&pound;" .
    number_format($row['SquadFee'],2,'.','') . "</td></tr>";
  }
  for ($i = 0; $i < $count; $i++) {
    if ($i == 2) {
      $totalsArray[$i] = $totalsArray[$i]*0.8;
    }
    elseif ($i > 2) {
      $totalsArray[$i] = $totalsArray[$i]*0.6;
    }
    $reducedCost += $totalsArray[$i];
  }
  $sql = $db->prepare("SELECT extras.ExtraName, extras.ExtraFee, members.MForename ,
  members.MSurname FROM ((extras INNER JOIN extrasRelations ON extras.ExtraID =
  extrasRelations.ExtraID) INNER JOIN members ON members.MemberID =
  extrasRelations.MemberID) WHERE extrasRelations.UserID = '$userID' ORDER BY
  `extras`.`ExtraFee` DESC;");
  $sql->execute([$userID]);

  $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
  $count = sizeof($rows);
  $monthlyExtras = "";
  $monthlyExtrasTotal = 0;
  for ($i=0; $i<$count; $i++) {
    $row = $rows[$i];
    $monthlyExtras .= "<tr><td>" . htmlspecialchars($row['ExtraName']) . " <br>for " .
    htmlspecialchars($row['MForename'] . " " . $row['MSurname']) . "</td><td>&pound;" .
    number_format($row['ExtraFee'],2,'.','') . "</td></tr>";
    $monthlyExtrasTotal += $row['ExtraFee'];
  }
  if ($monthlyExtrasTotal+$reducedCost > 0) {
    $output = "<div class=\"table-responsive\"><table class=\"table mb-0\">
    <thead class=\"thead-light\">
      <tr>
        <th>Fee Information</th>
        <th>Price</th>
      </tr>
    </thead>
    <tbody>
    <tr><td>The monthly subtotal for Squad Fees is</td><td>&pound;" .
    number_format($totalCost,2,'.','') . "</td></tr>";
    if (($totalCost - $reducedCost) > 0) {
      $output .= "<tr><td>The monthly total payable for squads with discounts is</td><td>&pound;" . number_format($reducedCost,2,'.','') .
      "</td></tr>";
    }
    $output .= "<tr><td>The monthly subtotal for extras is</td><td>&pound;" . number_format($monthlyExtrasTotal,2,'.','') .
    "</td></tr> <tr class=\"bg-light\"><td><strong>The monthly total
    is</strong></td><td>&pound;" . number_format(($reducedCost +
    $monthlyExtrasTotal),2,'.','') . "</td></tr> </tbody></table></div>";
    return $output;
  }
  else {
    return "<p>You have no monthly fees to pay. You may need to
    add a swimmer to your account to see your fees.</p>";
  }
}

function autoUrl($relative) {
  // Returns an absolute URL
  return env('ROOT_URL') . $relative;
}

function monthlyFeeCost($link, $userID, $format = "decimal") {
  global $db;
  $query = $db->prepare("SELECT squads.SquadName, squads.SquadID, squads.SquadFee FROM (members
  INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.UserID =
  ? AND `ClubPays` = '0' ORDER BY `squads`.`SquadFee` DESC");
  $query->execute([$userID]);

  $totalCost = 0;
  $reducedCost = 0;

  $i = 0;
  while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $squadCost = $row['SquadFee'];
    if (defined("IS_CLS") && IS_CLS) {
      if ($i < 2) {
        $totalCost += $squadCost;
        $reducedCost += $squadCost;
      }
      else if ($i == 2) {
        $totalCost += $squadCost*0.8;
        $reducedCost += $squadCost*0.8;
      }
      else {
        $totalCost += $squadCost*0.6;
        $reducedCost += $squadCost*0.6;
      }
    } else {
      $totalCost += $squadCost;
      $reducedCost += $squadCost;
    }
    $i++;
  }

  $format = strtolower($format);
  if ($format == "decimal") {
    return $reducedCost;
  }
  else if ($format == "int") {
    return ((int) ($reducedCost*100));
  }
  else if ($format == "string") {
    return "&pound;" . number_format($reducedCost,2,'.','');
  }
}

function monthlyExtraCost($link, $userID, $format = "decimal") {
  global $db;
  $query = $db->prepare("SELECT extras.ExtraName, extras.ExtraFee FROM ((members
  INNER JOIN `extrasRelations` ON members.MemberID = extrasRelations.MemberID)
  INNER JOIN `extras` ON extras.ExtraID = extrasRelations.ExtraID) WHERE
  members.UserID = ?");
  $query->execute([$userID]);
  $totalCost = 0;

  while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $totalCost += $row['ExtraFee'];
  }

  $format = strtolower($format);
  if ($format == "decimal") {
    return $totalCost;
  }
  else if ($format == "int") {
    return ((int) ($totalCost*100));
  }
  else if ($format == "string") {
    return "&pound;" . number_format($totalCost,2,'.','');
  }
}

function swimmers($link, $userID, $fees = false) {
  global $db;
  $sql = $db->prepare("SELECT squads.SquadName, squads.SquadFee, members.MForename,
  members.MSurname, members.ClubPays FROM (members INNER JOIN squads ON
  members.SquadID = squads.SquadID) WHERE members.UserID = ? ORDER BY
  `squads`.`SquadFee` DESC;");
  $sql->execute([$userID]);

  $row = $sql->fetch(PDO::FETCH_ASSOC);
  if ($row != null) {
    $content .= "<ul class=\"mb-0 list-unstyled\">";

    do {

      $content .= "<li>" . htmlspecialchars($row['MForename'] . " " . $row['MSurname']);
      if ($fees) {
        $content .= ", " . htmlspecialchars($row['SquadName']) . " - &pound;";
        if ($row['ClubPays'] == 0) {
          $content .= number_format($row['SquadFee'],2,'.','');
        } else {
          $content .= "0.00 <em>(Exempt)</em>";
        }
      }
      $content .= "</li>";
    } while ($row = $sql->fetch(PDO::FETCH_ASSOC));

    $content .= "</ul>";
  } else {
    $content = '<span class="text-muted small">No swimmers on this
    account</span>';
  }

  return $content;

}

function paymentHistory($link, $user, $type = null) {
  global $db;
  $sql = $db->prepare("SELECT * FROM `payments` WHERE `UserID` = ? ORDER BY `PaymentID` DESC LIMIT 0, 5;");
  $sql->execute([$user]);
  $row = $sql->fetch(PDO::FETCH_ASSOC);

  if ($row != null) { ?>
    <div class="list-group">
    <?php do {
      if ($type == null) {
        $statementUrl = autoUrl("payments/statement/" . htmlspecialchars($row['PMkey']));
      } else if ($type == "admin") {
        $statementUrl = autoUrl("payments/history/statement/" . htmlspecialchars($row['PMkey']));
      } ?>
    <a class="list-group-item list-group-item-action" href="<?=$statementUrl?>" title="Transaction Statement">
      <div class="row align-items-center">
        <div class="col-9">
          <p class="mb-0 text-primary">
            <strong>
              <?=htmlspecialchars($row['Name'])?>
            </strong>
          </p>
          <p class="mb-0">
            <?php echo date('j F Y', strtotime($row['Date'])); ?>
          </p>
        </div>
        <div class="col text-right">
          <p class="mb-0">
            <strong>&pound;<?php echo number_format(($row['Amount']/100),2,'.',''); ?></strong>
          </p>
          <p class="mb-0">
            Status: <?php echo paymentStatusString($row['Status']); ?>
          </p>
        </div>
      </div>
    </a>
    <?php } while ($row = $sql->fetch(PDO::FETCH_ASSOC));  ?>
  </div>
  <?php } else { ?>
  <div class="alert alert-warning mb-0">
    <strong>You have no previous payments</strong> <br>
    Payments and Refunds will appear here once they have been requested from
    your bank.
  </div>
  <?php }
}

function feesToPay($link, $user) {
  global $db;
  $sql = $db->prepare("SELECT * FROM `paymentsPending` WHERE `UserID` = ? AND `PMkey` IS NULL AND `Status` = 'Pending' ORDER BY `Date` DESC LIMIT 0, 30;");
  $sql->execute([$user]);
  $row = $sql->fetch(PDO::FETCH_ASSOC);
  if ($row != null) { ?>
  <ul class="list-group">
    <?php do { ?>
    <li class="list-group-item">
      <div class="row align-items-center">
        <div class="col-9">
          <p class="mb-0">
            <strong>
              <?=htmlspecialchars($row['Name'])?>
            </strong>
          </p>
          <p class="mb-0">
            <?=date('j F Y', strtotime($row['Date']))?>
          </p>
        </div>
        <div class="col text-right">
          <p class="mb-0">
            <?php if ($row['Type'] == 'Payment') { ?>
            &pound;<?=number_format(($row['Amount']/100),2,'.','')?>
            <?php } else { ?>
            -&pound;<?=number_format(($row['Amount']/100),2,'.','')?> (Credit)
            <?php } ?>
          </p>
        </div>
      </div>
    </li>
    <?php } while ($row = $sql->fetch(PDO::FETCH_ASSOC));  ?>
  </ul>
  <?php } else { ?>
  <div class="alert alert-warning mb-0">
    <strong>You have no current fees</strong> <br>
    Fee will appear here when they have been added to your account and have not
    been requested from the bank
  </div>
  <?php }
}

function getBillingDate($link, $user) {
  global $db;
  $sql = $db->prepare("SELECT * FROM `paymentSchedule` WHERE `UserID` = ?;");
  $sql->execute([$user]);
  $row = $sql->fetch(PDO::FETCH_ASSOC);

  if ($row != null) {
    $ordinal = null;
    if ($row['Day']%10 == 1) {
      $ordinal = "st";
    }
    else if ($row['Day']%10 == 2) {
      $ordinal = "nd";
    }
    else if ($row['Day']%10 == 3) {
      $ordinal = "rd";
    }
    else {
      $ordinal = "th";
    }
    return $row['Day'] . $ordinal;
  } else {
    return "1st";
  }
}

function userHasMandates($user) {
  global $db;
  $sql = $db->prepare("SELECT COUNT(*) FROM `paymentPreferredMandate` WHERE `UserID` = ?");
  $sql->execute([$user]);
  if ($sql->fetchColumn() == 1) {
    return true;
  }
  return false;
}

function paymentExists($payment) {
  global $db;
  $sql = $db->prepare("SELECT COUNT(*) FROM `payments` WHERE `PMkey` = ?;");
  $sql->execute([$payment]);
  if ($sql->fetchColumn() == 1) {
    return true;
  } else {
    return false;
  }
}

function mandateExists($mandate) {
  global $db;
  $sql = $db->prepare("SELECT COUNT(*) FROM `paymentMandates` WHERE `Mandate` = ?");
  $sql->execute([$mandate]);

  if ($sql->fetchColumn() == 1) {
    return true;
  } else {
    return false;
  }
}

function updatePaymentStatus($PMkey) {
  global $db;
  require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';
  $sql2bool = $payment = $status = null;
  try {
    $payment = $client->payments()->get($PMkey);
    $status = $payment->status;
    $payout = null;
    if (isset($payment->links->payout) && $payment->links->payout != null) {
      $payout = $payment->links->payout;

      // Check if payout exists or not
      createOrUpdatePayout($payout);
    }
    $update = $db->prepare("UPDATE `payments` SET `Status` = ?, `Payout` = ? WHERE `PMkey` = ?");
    $update->execute([$status, $payout, $PMkey]);
  } catch (Exception $e) {
    $sql2bool = false;
  }

  // Test failure condition
  // $status = "failed";
  if ($status == "paid_out") {
    try {
      $updatePP = $db->prepare("UPDATE `paymentsPending` SET `Status` = ? WHERE `PMkey` = ?");
      $updatePP->execute(['Paid', $PMkey]);
      $sql2bool = true;
    } catch (Exception $e) {
      $sql2bool = false;
    }
  } else if ($status == "failed") {
    global $db;
    try {
      $query = $db->prepare("SELECT payments.UserID, Name, Amount, Forename, Surname FROM payments INNER JOIN users ON payments.UserID = users.UserID WHERE PMkey = ?");
      $query->execute([$PMkey]);
      $details = $query->fetch(PDO::FETCH_ASSOC);

      $new_day = date("Y-m-d", strtotime("+10 days"));

      $query = $db->prepare("SELECT COUNT(*) FROM paymentRetries WHERE PMKey = ?");
      $query->execute([$PMkey]);
      $num_retries = $query->fetchColumn();

      $subject = "Payment Failed for " . $details['Name'];
      $message = '
      <p>Your Direct Debit payment of �' . number_format($details['Amount']/100, 2, '.', '') . ', ' . $details['Name'] . ' has failed.</p>';
      if ($num_retries < 3) {
        $message .= '<p>We will automatically retry this payment on ' . date("j F Y", strtotime("+10 days")) . ' (in ten days time).</p>';
        if ($num_retries < 2) {
          $message .= '<p>You don\'t need to take any action. Should this payment fail, we will retry the payment up to ' . (2-$num_retries) . ' times.</p>';
        } else if ($num_retries == 2) {
          $message .= '<p>You don\'t need to take any action. Should this payment fail however, you will need to contact the club treasurer as we will have retried this direct debit payment 3 times.</p>';
        }
      } else {
        $message .= '<p>We have retried this payment request three times and it has still not succeeded. As a result, you will need to contact the club treasurer to take further action. Failure to pay may lead to the suspension or termination of your membership.</p>';
      }

      $message .= '<p>Kind regards,<br>The ' . htmlspecialchars(env('CLUB_NAME')) . ' Team</p>';
      $query = $db->prepare("INSERT INTO notify (UserID, Status, Subject, Message, ForceSend, EmailType) VALUES (?, ?, ?, ?, ?, ?)");
      $query->execute([$details['UserID'], 'Queued', $subject, $message, 1, 'Payments']);

      if ($num_retries < 3) {
        $query = $db->prepare("INSERT INTO paymentRetries (UserID, Day, PMKey, Tried) VALUES (?, ?, ?, ?)");
        $query->execute([$details['UserID'], $new_day, $PMkey, false]);
      }

      $sql2bool = true;
    } catch (Exception $e) {
      $sql2bool = false;
      echo "Failure in event process";
    }
  } else if ($status == "customer_approval_denied") {
    global $db;
    try {
      $query = $db->prepare("SELECT payments.UserID, Name, Amount, Forename, Surname FROM payments INNER JOIN users ON payments.UserID = users.UserID WHERE PMkey = ?");
      $query->execute([$PMkey]);
      $details = $query->fetch(PDO::FETCH_ASSOC);

      $subject = "Payment Failed for " . $details['Name'];
      $message = '
      <p>Your Direct Debit payment of £' . number_format($details['Amount']/100, 2, '.', '') . ', ' . $details['Name'] . ' has failed because customer approval was denied. This means your bank requires two people two authorise a direct debit mandate on your account and that this authorisation has not been given. You will be contacted by the treasurer to arrange payment.</p>
      <p>Kind regards,<br>The ' . htmlspecialchars(env('CLUB_NAME')) . ' Team</p>';
      $query = $db->prepare("INSERT INTO notify (UserID, Status, Subject, Message, ForceSend, EmailType) VALUES (?, ?, ?, ?, ?, ?)");
      $query->execute([$details['UserID'], 'Queued', $subject, $message, 1, 'Payments']);

      $sql2bool = true;
    } catch (Exception $e) {
      $sql2bool = false;
    }
  } else if ($status == "charged_back") {
    global $db;
    try {
      $query = $db->prepare("SELECT payments.UserID, Name, Amount, Forename, Surname FROM payments INNER JOIN users ON payments.UserID = users.UserID WHERE PMkey = ?");
      $query->execute([$PMkey]);
      $details = $query->fetch(PDO::FETCH_ASSOC);

      $subject = $details['Name'] . " Charged Back";
      $message = '
      <p>Your Direct Debit payment of �' . number_format($details['Amount']/100, 2, '.', '') . ', ' . $details['Name'] . ' has been charged back to us. You will be contacted by the treasurer to arrange payment of any outstanding amount.</p>
      <p>Please note that fraudulently charging back a Direct Debit payment is a criminal offence, covered by the 2006 Fraud Act. We recommend that if your are unsure about the amount we are charging you, you should try and contact us first.</p>
      <p>Kind regards,<br>The ' . htmlspecialchars(env('CLUB_NAME')) . ' Team</p>';
      $query = $db->prepare("INSERT INTO notify (UserID, Status, Subject, Message, ForceSend, EmailType) VALUES (?, ?, ?, ?, ?, ?)");
      $query->execute([$details['UserID'], 'Queued', $subject, $message, 1, 'Payments']);

      $sql2bool = true;
    } catch (Exception $e) {
      $sql2bool = false;
    }
  } else {
    $sql2bool = true;
  }
  if ($status == null) {
    return false;
  }
  if ($sql2bool) {
    return true;
  } else {
    return false;
  }
}

function paymentStatusString($status) {
  switch ($status) {
    case "paid_out":
      return "Paid out";
    case "paid_manually":
      return "Paid manually";
    case "pending_customer_approval":
      return "Waiting for customer to approve payment";
    case "pending_submission":
      return "Payment created, pending submission to bank";
    case "submitted":
      return "Payment sent to bank";
    case "confirmed":
      return "Payment confirmed as collected";
    case "cancelled":
      return "Payment cancelled";
    case "customer_approval_denied":
      return "Customer has denied approval for the payment. Contact the customer directly";
    case "failed":
      return "Payment failed";
    case "charged_back":
      return "Payment charged back";
      case "cust_not_dd":
        return "Customer has no Direct Debit mandate";
    default:
      return "Unknown Status Code";
  }
}

function bankDetails($user, $detail) {
  global $db;
  $sql = $db->prepare("SELECT * FROM `paymentPreferredMandate` INNER JOIN `paymentMandates` ON paymentPreferredMandate.MandateID = paymentMandates.mandateID WHERE paymentPreferredMandate.UserID = ?;");
  $sql->execute([$user]);

  $row = $sql->fetch(PDO::FETCH_ASSOC);

  if ($row == null) {
    return "Unknown";
  }

  switch ($detail) {
    case "bank_name":
      return $row['BankName'];
    case "account_holder":
      return $row['AccountHolderName'];
    case "account_number_end":
      return $row['AccountNumEnd'];
    case "mandate":
      return $row['Mandate'];
    case "bank_account":
      return $row['BankAccount'];
    case "customer":
      return $row['Customer'];
    default:
      return "Unknown";
  }
}

function getUserName($user) {
  global $db;
  $sql = $db->prepare("SELECT `Forename`, `Surname` FROM `users` WHERE `UserID` = ?;");
  $sql->execute([$user]);
  $row = $sql->fetch(PDO::FETCH_ASSOC);
  if ($row != null) {
    return $row['Forename'] . " " . $row['Surname'];
  }
  return false;
}

function getSwimmerName($swimmer) {
  global $db;
  $sql = $db->prepare("SELECT `MForename`, `MSurname` FROM `members` WHERE `MemberID` = ?;");
  $row = $sql->fetch(PDO::FETCH_ASSOC);
  if ($row != null) {
    return $row['MForename'] . " " . $row['MSurname'];
  }
  return false;
}

function setupPhotoPermissions($id) {
  global $db;
  try {
    $sql = $db->prepare("SELECT COUNT(*) FROM `memberPhotography` WHERE `MemberID` = ?;");
    $sql->execute([$id]);
    if ($sql->fetchColumn() == 0) {
      $sql = $db->prepare("INSERT INTO `memberPhotography` (`MemberID`, `Website`, `Social`, `Noticeboard`, `FilmTraining`, `ProPhoto`) VALUES (?, '0', '0', '0', '0', '0');");
      $sql->execute([$id]);
      return true;
    }
  } catch (Exception $e) {
    return false;
  }
  return false;
}

function setupMedicalInfo($id) {
  global $db;
  try {
    $sql = $db->prepare("SELECT * FROM `memberMedical` WHERE `MemberID` = ?;");
    $sql->execute([$id]);
    if ($sql->fetchColumn() == 0) {
      $sql = $db->prepare("INSERT INTO `memberMedical` (`MemberID`, `Conditions`, `Allergies`, `Medication`) VALUES (?, '', '', '');");
      $sql->execute([$id]);
      return true;
    }
  } catch (Exception $e) {
    return false;
  }
  return false;
}

function ordinal($num) {
  $ordinal = null;
  if ($num%10 == 1) {
    $ordinal = "st";
  }
  else if ($num%10 == 2) {
    $ordinal = "nd";
  }
  else if ($num%10 == 3) {
    $ordinal = "rd";
  }
  else {
    $ordinal = "th";
  }
  return $num . $ordinal;
}

use Symfony\Component\DomCrawler\Crawler;

function curl($url) {
  $ch = curl_init();  // Initialising cURL
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Origin: ' . app('request')->hostname));
  curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36');
  curl_setopt($ch, CURLOPT_URL, $url);    // Setting cURL's URL option with the $url variable passed into the function
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Setting cURL's option to return the webpage data
  $data = curl_exec($ch); // Executing the cURL request and assigning the returned data to the $data variable
  $error = true;
  //pre(curl_getinfo($ch, CURLINFO_HTTP_CODE));
  if (!curl_errno($ch) && (curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200' || curl_getinfo($ch, CURLINFO_HTTP_CODE) || '404' && curl_getinfo($ch, CURLINFO_HTTP_CODE) || '400')) {
    $error = false;
  }
  //pre($error);
  curl_close($ch);    // Closing cURL
  if (!$error) {
    return $data;   // Returning the data from the function
  } else {
    return false;
  }
}

function curl_scrape_between($data, $start, $end) {
  //echo $data . "<br>";
  $data = stristr($data, $start); // Stripping all data from before $start
  //echo $data . "<br>";
  $data = substr($data, strlen($start));  // Stripping $start
  //echo $data . "<br>";
  $stop = stripos($data, $end);   // Getting the position of the $end of the data to scrape
  //echo $stop . "<br>";
  $data = substr($data, 0, $stop);    // Stripping all data from after and including the $end of the data to scrape
  //echo $data . "<br>";
  return $data;   // Returning the scraped data from the function
}

function getTimes($asa) {
  $curlres =
  curl('https://cors-anywhere.herokuapp.com/https://www.swimmingresults.org/biogs/biogs_details.php?tiref=' . $asa);

  if ($curlres) {
    $start = '<table width="100%" style="page-break-before:always">';
    $end = '</table>';

    $output = curl_scrape_between($curlres, $start, $end);
    $output = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $output);
    $output = preg_replace('/(<[^>]+) width=".*?"/i', '$1', $output);

    $crawler = new Crawler($output);
    $crawler = $crawler->filter('tr > td');

    $array = ['Event', 'CY_SC', 'SCPB', 'CY_LC', 'LCPB'];
    $count = 0;

    foreach ($crawler as $domElement) {
      $col = $count%5;
      if ($col == 0) {
        if ($domElement->textContent == "") {
          $array['Event'][] = null;
        } else {
          $array['Event'][] = trim($domElement->textContent);
        }
      } else if ($col == 1) {
        if ($domElement->textContent == "") {
          $array['CY_SC'][] = null;
        } else {
          $array['CY_SC'][] = trim($domElement->textContent);
        }
      } else if ($col == 2) {
        if ($domElement->textContent == "") {
          $array['SCPB'][] = null;
        } else {
          $array['SCPB'][] = trim($domElement->textContent);
        }
      } else if ($col == 3) {
        if ($domElement->textContent == "") {
          $array['CY_LC'][] = null;
        } else {
          $array['CY_LC'][] = trim($domElement->textContent);
        }
      } else if ($col == 4) {
        if ($domElement->textContent == "") {
          $array['LCPB'][] = null;
        } else {
          $array['LCPB'][] = trim($domElement->textContent);
        }
      }
      $count++;
    }

    return $array;
  }
  return false;
}

function user_needs_registration($user) {
  global $db;
  try {
    $query = $db->prepare("SELECT RR FROM users WHERE UserID = ?");
    $query->execute([$user]);

    if ($query->fetchColumn()) {
      return true;
    } else {
      return false;
    }
  } catch (Exception $e) {
    return false;
  }
  return false;
}

function getPostContent($id) {
  global $db;
  $sql = "SELECT `Content` FROM `posts` WHERE `ID` = ?";
  try {
  	$query = $db->prepare($sql);
  	$query->execute([$id]);
  } catch (PDOException $e) {
  	halt(500);
  }

  $row = $query->fetch(PDO::FETCH_ASSOC);
  if (!$row) {
    return false;
  }

  $markdown = new ParsedownExtra();
  // Safe mode is disabled during the transition to markdown
  // $markdown->setSafeMode(true);

  return $markdown->text($row['Content']);
}

function isSubscribed($user, $email_type) {
  global $db;
  $sql = "SELECT `Subscribed` FROM `users` LEFT JOIN `notifyOptions` ON `users`.`UserID` = `notifyOptions`.`UserID` WHERE (`users`.`UserID` = :user AND `EmailType` = :type) OR (`users`.`UserID` = :user AND `EmailType` IS NULL)";
  $array = [
    'user' => $user,
    'type' => $email_type
  ];
  try {
    $query = $db->prepare($sql);
    $query->execute($array);
  } catch (Exception $e) {
    halt(500);
  }
  $row = $query->fetchColumn();

  if ($row == null) {
    // Try user account info
    $sql = "SELECT `EmailComms` FROM `users` WHERE `UserID` = :user";
    $array = [
      'user' => $user
    ];
    try {
      $query = $db->prepare($sql);
      $query->execute($array);
    } catch (Exception $e) {
      halt(500);
    }
    $row = $query->fetchColumn();
    if ($row) {
      return true;
    }
  }

  if ($row) {
    return true;
  }

  return false;
}

function updateSubscription($post, $list, $user = null) {
	global $db;
  if (isset($_SESSION['UserID'])) {
    $user = $_SESSION['UserID'];
  }
	$email = 0;
	$email_update = false;
	if ($post) {
		$email = 1;
	}

	if ($email != isSubscribed($user, $list)) {
		$email_update = true;
		$_SESSION['OptionsUpdate'] = true;
	}

	$sql = "SELECT COUNT(*) FROM `notifyOptions` WHERE `UserID` = ? AND `EmailType` = ?";
	try {
		$query = $db->prepare($sql);
		$query->execute([$user, $list]);
	} catch (Exception $e) {
		halt(500);
	}
	if ($query->fetchColumn() == 0) {
		// INSERT
		$sql = "INSERT INTO `notifyOptions` (`UserID`, `EmailType`, `Subscribed`) VALUES (?, ?, ?)";
		try {
			$query = $db->prepare($sql);
			$query->execute([$user, $list, $email]);
		} catch (Exception $e) {
			halt(500);
		}
	} else {
		// UPDATE
		$sql = "UPDATE `notifyOptions` SET `Subscribed` = ? WHERE `UserID` = ? AND `EmailType` = ?";
		try {
			$query = $db->prepare($sql);
			$query->execute([$email, $user, $list]);
		} catch (Exception $e) {
			halt(500);
		}
	}
}

function getUserOption($userID, $option) {
  global $db;
  $query = $db->prepare("SELECT `Value` FROM userOptions WHERE User = ? AND `Option` = ?");
  $query->execute([$userID, $option]);
  $result = $query->fetchColumn();

  if ($result == null || $result == "") {
    return null;
  }
  return $result;
}

function setUserOption($userID, $option, $value) {
  if ($value == "") {
    $value = null;
  }
  try {
    global $db;
    $query = $db->prepare("SELECT COUNT(*) FROM userOptions WHERE User = ? AND `Option` = ?");
    $query->execute([$userID, $option]);
    $result = $query->fetchColumn();

    if ($result == 0) {
      $query = $db->prepare("INSERT INTO userOptions (`User`, `Option`, `Value`) VALUES (?, ?, ?)");
      $query->execute([$userID, $option, $value]);
    } else {
      $query = $db->prepare("UPDATE userOptions SET `Value` = ? WHERE User = ? AND `Option` = ?");
      $query->execute([$value, $userID, $option]);
    }
  } catch (Exception $e) {
    return false;
  }
  return true;
}

$count = 0;

/*
if ( (empty($_SESSION['LoggedIn']) || empty($_SESSION['Username'])) && ($preventLoginRedirect != true)) {
  // Allow access to main page
  header("Location: " . autoUrl("login.php"));
}
elseif (((!empty($_SESSION['LoggedIn'])) || (!empty($_SESSION['Username']))) && ($preventLoginRedirect == true)) {
  // Don't show login etc if logged in
  header("Location: " . autoUrl(""));
}
*/

if (!function_exists('mb_ucfirst')) {
  function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = false) {
    $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
    $str_end = "";
    if ($lower_str_end) {
      $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
    }
    else {
      $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
    }
    $str = $first_letter . $str_end;
    return $str;
  }
}

function helloGreeting() {
  $date = new DateTime('now', new DateTimeZone('Europe/London'));
  $hour = (int) $date->format('H');
  if ($hour > 4 && $hour < 12) {
    return "Good Morning";
  } else if ($hour > 11 && $hour < 17) {
    return "Good Afternoon";
  } else if ($hour > 16 && $hour < 21) {
    return "Good Evening";
  } else {
    return "Good Night";
  }
}

function getCardFA($brand) {
  if ($brand == 'visa') {
    return 'fa-cc-visa';
  } else if ($brand == 'mastercard') {
    return 'fa-cc-mastercard';
  } else if ($brand == 'amex') {
    return 'fa-cc-amex';
  } else if ($brand == 'diners') {
    return 'fa-cc-diners-club';
  } else if ($brand == 'discover') {
    return 'fa-cc-discover';
  } else if ($brand == 'jcb') {
    return 'fa-cc-jcb';
  } else if ($brand == 'unionpay') {
    return 'fa-cc-stripe';
  } else {
    return 'fa-cc-stripe';
  }
}

function getCardBrand($brand) {
  if ($brand == 'visa') {
    return 'Visa';
  } else if ($brand == 'mastercard') {
    return 'Mastercard';
  } else if ($brand == 'amex') {
    return 'American Express';
  } else if ($brand == 'diners') {
    return 'Diners Club';
  } else if ($brand == 'discover') {
    return 'Discover';
  } else if ($brand == 'jcb') {
    return 'JCB';
  } else if ($brand == 'unionpay') {
    return 'UnionPay';
  } else {
    return 'Unknown Card';
  }
}

function createOrUpdatePayout($payout, $update = false) {
  global $db;
  require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

  $getCount = $db->prepare("SELECT COUNT(*) FROM paymentsPayouts WHERE ID = ?");
  $getCount->execute([
    $payout
  ]);
  $count = $getCount->fetchColumn() == 0;

  if ($count) {
    try {
      $payout = $client->payouts()->get($payout);

      $insert = $db->prepare("INSERT INTO paymentsPayouts (ID, Amount, Fees, Currency, ArrivalDate) VALUES (?, ?, ?, ?, ?)");
      $insert->execute([
        $payout->id,
        $payout->amount,
        $payout->deducted_fees,
        $payout->currency,
        $payout->arrival_date
      ]);
    } catch (Exception $e) {
      reportError($e);
      return false;
    }
  } else if ($count = 1 && $update) {
    try {
      $payout = $client->payouts()->get($payout);

      $insert = $db->prepare("UPDATE paymentsPayouts SET Amount = ?, Fees = ?, Currency = ?, ArrivalDate = ? WHERE ID = ?");
      $insert->execute([
        $payout->amount,
        $payout->deducted_fees,
        $payout->currency,
        $payout->arrival_date,
        $payout->id
      ]);
    } catch (Exception $e) {
      reportError($e);
      return false;
    }
  }
  return true;
}

include BASE_PATH . 'includes/security/Loader.php';
include BASE_PATH . 'includes/ErrorReporting.php';
include BASE_PATH . 'includes/Colours.php';
include BASE_PATH . 'includes/stripe/HandleBalanceTransactionForFees.php';
include BASE_PATH . 'includes/stripe/HandleCompletedGalaPayments.php';
include BASE_PATH . 'includes/direct-debit/BankLogos.php';
include BASE_PATH . 'includes/direct-debit/Balances.php';
include BASE_PATH . 'includes/membership-fees/Loader.php';
