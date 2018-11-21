<?php
$pagetitle = "Attendance";
include BASE_PATH . "views/header.php";
include "attendanceMenu.php"; ?>
<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>About this system</h1>
      <p class="lead">This software is licenced to <?=CLUB_NAME?> by Chester-le-Street ASC Club Digital Systems, an operational division of Chester-le-Street ASC.</p>

      <p>You can use this software by subscribing to the Chester-le-Street ASC Club Digital Systems Software as a Service Package.</p>

      <h2>Automatic Member Management</h2>
      <p>The application is built on a database of club members. Members are assigned to squads and parents can link swimmers to their account. This allows us to automatically calculate monthly fees and other things.</p>

      <h2>Online Gala Entries</h2>
      <p>Galas are added to the system by admins. Parents can enter their children into swims by selecting their name, gala and swims. This cuts down on duplicated data from existing arrangements. Parents recieve emails detailing their entries.</p>

      <h2>Online Attendance Records</h2>
      <p>Attendance records are online, facilitating automatic attendance calculation. Squads are managed online and swimmer moves between squads can be scheduled in the system.</p>

      <h2>Notify</h2>
      <p>Notify is our E-Mail mailing list solution. Administrators can send emails to selected groups of parents for each squad. The system is GDPR compliant and users can opt in or out of receiving emails at any time.</p>

      <h2>Direct Debit Payments</h2>
      <p>This application has been integrated with GoCardless and their APIs to allow <?=CLUB_NAME?> to bill members by Direct Debit. The GoCardless client library which is included in this software is copyright of GoCardless.</p>

    </div>
  </div>
</div>
<?php include BASE_PATH . "views/footer.php";
