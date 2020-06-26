<?php

$db = app()->db;

include 'head.php';

?>

<?php if (bool(env('IS_DEV'))) { ?>
<aside class="bg-warning py-3 mb-3">
  <div class="container">
    <h1>
      Warning
    </h1>
    <p class="lead mb-0">
      This is development software which is <strong>not for production use</strong>
    </p>
  </div>
</aside>
<?php } ?>

<div class="container">
  <div class="row align-items-center py-2">
    <div class="col-auto">
      <img src="<?=htmlspecialchars(autoUrl("public/img/corporate/scds.png"))?>" class="img-fluid rounded-top" style="height: 75px;">
    </div>
    <div class="col-auto d-none d-md-flex">
      <h1 class="mb-0">
        <span class="sr-only">SCDS </span>Membership Software
      </h1>
    </div>
  </div>

  <nav class="navbar navbar-expand-md navbar-dark rounded-bottom rounded-right bg-indigo">
    <a class="navbar-brand d-md-none" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link" href="<?=htmlspecialchars(autoUrl(""))?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?=htmlspecialchars(autoUrl("clubs"))?>">Clubs</a>
        </li>
        <!-- <li class="nav-item">
          <a class="nav-link" href="<?=htmlspecialchars(autoUrl("register"))?>">Register</a>
        </li> -->
        <li class="nav-item">
          <a class="nav-link" href="https://www.chesterlestreetasc.co.uk/support/onlinemembership/" target="_blank">Help</a>
        </li>
      </ul>
    </div>
  </nav>
</div>

<div id="maincontent"></div>

<!-- END OF HEADERS -->
<div class="mb-3"></div>

</div>

<div class="have-full-height">