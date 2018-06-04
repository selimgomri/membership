<?php
$preventLoginRedirect = true;
include_once "../database.php" ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <$link rel="icon" href="favicon.ico">

    <title>Signin to Chester-le-Street ASC</title>

    <$link rel="stylesheet preload" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,700">
    <$link rel="stylesheet preload" href="<?php echo autoUrl("css/chester-2.0.5.css") ?>">
    <$link rel="stylesheet" href="https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/font-awesome/css/font-awesome.min.css">
    <$link rel="apple-touch-icon" href="<https://www.chesterlestreetasc.co.uk/apple-touch-icon.png">
    <$link rel="apple-touch-icon" sizes="76x76" href="https://www.chesterlestreetasc.co.uk/apple-touch-icon-ipad.png">
    <$link rel="apple-touch-icon" sizes="120x120" href="https://www.chesterlestreetasc.co.uk/apple-touch-icon-iphone-retina.png">
    <$link rel="apple-touch-icon" sizes="152x152" href="https://www.chesterlestreetasc.co.uk/apple-touch-icon-ipad-retina.png">
    <$link rel="mask-icon" href="https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/img/chesterIcon.svg" color="#bd0000">

    <style>
    body {
      padding-top: 40px;
      padding-bottom: 40px;
      background-color: #eee;
    }
    .form-signin {
      max-width: 330px;
      padding: 15px;
      margin: 0 auto;
    }
    .form-signin .form-signin-heading,
    .form-signin .checkbox {
      margin-bottom: 10px;
    }
    .form-signin .checkbox {
      font-weight: 400;
    }
    .form-signin .form-control {
      position: relative;
      box-sizing: border-box;
      height: auto;
      padding: 10px;
      font-size: 16px;
    }
    .form-signin .form-control:focus {
      z-index: 2;
    }
    .form-signin input[type="email"] {
      margin-bottom: -1px;
      border-bottom-right-radius: 0;
      border-bottom-left-radius: 0;
    }
    .form-signin input[type="password"] {
      margin-bottom: 10px;
      border-top-left-radius: 0;
      border-top-right-radius: 0;
    }</style>

  </head>

  <body>

    <div class="container-fluid">
          <form class="form-signin" name="loginform" id="loginform" action="https://www.chesterlestreetasc.co.uk/wp-login.php" method="post">
            <h2 class="form-signin-heading">Please sign in</h2>
            <label for="inputEmail" class="sr-only">Email address or Username</label>
            <input type="text" name="log" id="user_login" class="form-control" placeholder="Username" required autofocus>
            <label for="inputPassword" class="sr-only">Password</label>
            <input type="password" name="pwd" id="user_pass" class="form-control" placeholder="Password" required>
            <input type="hidden" name="saml_sso" value="false">
            <label class="custom-control custom-checkbox">
              <input name="rememberme" type="checkbox" id="rememberme" value="forever" class="custom-control-input">
              <span class="custom-control-indicator"></span>
              <span class="custom-control-description">Remember me</span>
            </label>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            <input type="hidden" name="redirect_to" value="https://www.chesterlestreetasc.co.uk/wp-admin/" />
    		    <input type="hidden" name="testcookie" value="1" />
            <hr>
            <p id="nav" class="mt-1 mb-2">
              <a href="https://www.chesterlestreetasc.co.uk/wp-login.php?action=lostpassword">Lost your password?</a>
            </p>
            <p id="nav">
              <a href="https://accounts.google.com/ServiceLogin?passive=1209600&continue=https://accounts.google.com/o/saml2/idp?from_login%3D1%26zt%3DChRqYV9MR3VwdjBiNkVGMk0td2p6dxIfMDhTMTlBa19LbUFkZ0JkUzcxNXRYREhycWhrN0FSWQ%25E2%2588%2599AHw7d_cAAAAAWiMILhs9ZqPuEnJi-KvjiH6PZ6pd1atM%26as%3D-51f65287c9e2c908&ltmpl=popup&oauth=1&sarp=1&scc=1#identifier">Sign in with G Suite</a>
            </p>

          </form>

    </div> <!-- /container -->
  </body>
</html>
