<?php

$pagetitle = "Add new linked account";
$fluidContainer = true;
include BASE_PATH . 'views/header.php';

?>

<div class="container-fluid">
  <div class="row justify-content-between">
    <aside class="col-md-3 d-none d-md-block">
      <?php
        $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/myaccount/ProfileEditorLinks.json'));
        echo $list->render('linked-accounts');
      ?>
    </aside>
    <div class="col-md-9">
      <main>
        <h1>Add a new linked account</h1>

        <?php if (isset($_SESSION['LinkedUserIncorrectDetails']) && $_SESSION['LinkedUserIncorrectDetails']) { ?>
        <div class="alert alert-danger">
          Your email address or password was incorrect
        </div>
        <?php unset($_SESSION['LinkedUserIncorrectDetails']); } ?>

        <form method="post">
          <p class="lead">We just need some details about the account you're adding.</p>
          <p>When you're done, we'll send you an email to that account to confirm it should be linked to this one.</p>

          <div class="form-group">
            <label for="email-addr">Email address</label>
            <input type="email" class="form-control" id="email-addr" name="email-addr" placeholder="Enter email">
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
          </div>

          <p>
            <button type="submit" class="btn btn-success">Add account</button>
          </p>
        </form>
      </main>
    </div>
  </div>
</div>

<?php

$footer = new \SDCS\Footer();
$footer->useFluidContainer();
$footer->render();