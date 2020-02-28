<?php

$pagetitle = "About this software";
include BASE_PATH . "views/header.php";

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>About this system</h1>
      <p class="lead">This software is licenced to <?=htmlspecialchars(env('CLUB_NAME'))?> by Swimming Club Data Systems, a partnership of <a href="https://www.chesterlestreetasc.co.uk/" target="_blank">Chester-le-Street ASC</a> and Chris Heppell.</p>

      <p>You can use this software by subscribing to the <a href="https://corporate.myswimmingclub.co.uk/" target="_blank">Swimming Club Data Systems Software as a Service Package</a>.</p>

      <h2>Features</h2>
      <p class="lead">
        Features include;
      </p>

      <h3>Automatic Member Management</h3>
      <p>The application is built on a database of club members. Members are assigned to squads and parents can link swimmers to their account. This allows us to automatically calculate monthly fees and other things.</p>

      <h3>Online Gala Entries</h3>
      <p>Galas are added to the system by admins. Parents can enter their children into swims by selecting their name, gala and swims. This cuts down on duplicated data from existing arrangements. Parents receive emails detailing their entries.</p>

      <h3>Online Attendance Records</h3>
      <p>Attendance records are online, facilitating automatic attendance calculation. Squads are managed online and swimmer moves between squads can be scheduled in the system.</p>

      <h3>Notify</h3>
      <p>Notify is our E-Mail mailing list solution. Administrators can send emails to selected groups of parents for each squad. The system is GDPR compliant and users can opt in or out of receiving emails at any time.</p>

      <h3>Direct Debit Payments</h3>
      <p>This application has been integrated with GoCardless and their APIs to allow <?=htmlspecialchars(env('CLUB_NAME'))?> to bill members by Direct Debit. The GoCardless client library which is included in this software is copyright of GoCardless.</p>

      <h2>Legal</h2>
      This product includes GeoLite2 data created by MaxMind, available from <a href="https://www.maxmind.com">https://www.maxmind.com</a>.

    </div>
  </div>
</div>

<?php $footer = new \SDCS\Footer();
$footer->render();
