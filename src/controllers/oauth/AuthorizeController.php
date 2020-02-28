<?php

// include our OAuth2 Server object
require_once 'Main.php';

$request = OAuth2\Request::createFromGlobals();
$response = new OAuth2\Response();

// validate the authorize request
if (!$server->validateAuthorizeRequest($request, $response)) {
  $response->send();
  //halt(404);
  die();
}
// display an authorization form
if (empty($_POST)) {

include BASE_PATH . 'views/header.php'; ?>
<form method="post">
  <label>Do You Authorize CLIENT_NAME?</label><br />
  <input type="submit" name="authorized" value="yes">
  <input type="submit" name="authorized" value="no">
</form>

<?php

$footer = new \SDCS\Footer();
$footer->render();

die();

}

// print the authorization code if the user has authorized your client
$is_authorized = ($_POST['authorized'] === 'yes');
$server->handleAuthorizeRequest($request, $response, $is_authorized, $_SESSION['UserID']);
if ($is_authorized) {
  // this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
  header($response->getHttpHeader('Location'));
}
$response->send();
