<?php

$pagetitle = "Assisted Registration";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>Welcome to Assisted Registration</h1>
      <p class="lead">
        Assisted registration allows you to create an account for new parents and automatically connect their swimmers to it.
      </p>

      <p>
        Parent's will be led through a registration process to check their details and fill out required forms when they first log in.
      </p>

      <p>
        To get started you will need;
      </p>

      <ul>
        <li>
          The parent's name,
        </li>
        <li>
          Their email address,
        </li>
        <li>
          If possible their phone number
        </li>
      </ul>

      <p>
        Given these details, we'll then ask you to select swimmers from a list of all unregistered swimmers. We'll send the parent an email which includes instructions on how to log in.
      </p>

      <p>
        <a href="<?=autoUrl("assisted-registration/start")?>" class="btn btn-success">
          Get started
        </a>
      </p>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';