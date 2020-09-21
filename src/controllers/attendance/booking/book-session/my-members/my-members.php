<?php

function getMySessionBookingMembers($session, $date)
{

  $db = app()->db;
  $tenant = app()->tenant;
  $user = app()->user;

  // Get bookable members
  $members = [];
  if (bool($session['AllSquads'])) {
    $getMembers = $db->prepare("SELECT MForename fn, MSurname sn, MemberID id FROM members WHERE UserID = ? ORDER BY fn ASC, sn ASC");
    $getMembers->execute([
      $user->getId(),
    ]);
    $members = $getMembers->fetchAll(PDO::FETCH_ASSOC);
  } else {
    // Get session squads
    $getSquads = $db->prepare("SELECT `Squad` FROM `sessionsSquads` WHERE `Session` = ?");
    $getSquads->execute([
      $session['SessionID'],
    ]);

    while ($squad = $getSquads->fetchColumn()) {
      // Get members for this user in this squad
      $getMembers = $db->prepare("SELECT MForename fn, MSurname sn, MemberID id FROM members INNER JOIN squadMembers ON members.MemberID = squadMembers.Member WHERE UserID = ? AND squadMembers.Squad = ? ORDER BY fn ASC, sn ASC");
      $getMembers->execute([
        $user->getId(),
        $squad,
      ]);

      while ($member = $getMembers->fetch(PDO::FETCH_ASSOC)) {
        $members[] = $member;
      }
    }

    // Remove duplicated
    // $members = array_unique($members);
    $members = array_map("unserialize", array_unique(array_map("serialize", $members)));
  }

  $getBooking = $db->prepare("SELECT `BookedAt` FROM `sessionsBookings` WHERE `Session` = ? AND `Date` = ? AND `Member` = ?");

?>

  <h2>Book</h2>
  <p class="lead">
    Book a place for a member linked to your account.
  </p>

  <?php if (sizeof($members) > 0) { ?>

    <ul class="list-group mb-3" id="member-booking-list">
      <?php foreach ($members as $member) {

        // Check if booked
        $getBooking->execute([
          $session['SessionID'],
          $date->format('Y-m-d'),
          $member['id'],
        ]);
        $booking = $getBooking->fetch(PDO::FETCH_ASSOC);

        $bookingTime = null;
        if ($booking) {
          $bookingTime = new DateTime($booking['BookedAt'], new DateTimeZone('UTC'));
          $bookingTime->setTimezone(new DateTimeZone('Europe/London'));
        }

      ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span>
            <span class="d-block"><strong><a href="<?= htmlspecialchars(autoUrl('members/' . $member['id'])) ?>"><?= htmlspecialchars($member['fn'] . ' ' . $member['sn']) ?></a></strong></span>
            <?php if ($booking) { ?></strong><span class="d-block">Booked at <?= htmlspecialchars($bookingTime->format('H:i, j F Y')) ?></span><?php } ?>
          </span>
          <?php if (!$booking) { ?>
            <span>
              <button class="btn btn-primary" type="button" data-member-name="<?= htmlspecialchars($member['fn'] . ' ' . $member['sn']) ?>" data-member-id="<?= htmlspecialchars($member['id']) ?>" data-operation="book-place" data-session-id="<?= htmlspecialchars($session['SessionID']) ?>" data-session-name="<?= htmlspecialchars($session['SessionName']) ?> on <?= htmlspecialchars($date->format('j F Y')) ?>" data-session-location="<?= htmlspecialchars($session['Location']) ?>" data-session-date="<?= htmlspecialchars($date->format('Y-m-d')) ?>">Book</button>
            </span>
          <?php } ?>
        </li>
      <?php } ?>
    </ul>

  <?php } else { ?>
    <div class="alert alert-warning">
      <p class="mb-0">
        <strong>You have no members</strong>
      </p>
      <p class="mb-0">
        Only members can be booked onto sessions.
      </p>
    </div>
<?php }
}
