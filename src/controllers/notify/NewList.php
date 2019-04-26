<?php

$pagetitle = "Add a Targetted List";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

?>

<div class="container">
  <div class="">
    <h1 class="">
      Create a New Targetted List
    </h1>
    <p class="lead">
      Targetted lists are custom mailing lists for messaging groups of parents
      outside of normal squads.
    </p>
    <p>
      A useful use case would be for the Junior League.
    </p>

    <hr>

    <div class="row">
      <div class="col-lg-8">
        <?
        if (isset($_SESSION['ErrorState'])) {
          echo $_SESSION['ErrorState'];
          unset($_SESSION['ErrorState']);
        }
        ?>
        <form method="post">
          <div class="form-group">
            <label for="name">List Name</label>
            <input type="text" class="form-control" id="name" name="name" aria-describedby="nameHelp" placeholder="Enter name">
            <small id="nameHelp" class="form-text text-muted">You will look for this name to send messages to the group</small>
          </div>

          <div class="form-group">
            <label for="desc">Description</label>
            <input type="text" class="form-control" id="desc" name="desc" placeholder="Describe this group">
          </div>

          <p class="mb-0">
            <button type="submit" class="btn btn-success">
              Create new list
            </button>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>

<?php

include BASE_PATH . "views/footer.php";

?>
