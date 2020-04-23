<?php

// include our OAuth2 Server object
require_once 'Main.php';

if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
  $server->getResponse()->send();
  die;
}

$token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());

$user_id = $token['user_id'];

$db = app()->db;

$user_details = $db->prepare("SELECT Forename, Surname, EmailAddress, Edit, Mobile FROM users WHERE UserID = ?");
$user_details->execute([$user_id]);
$user = $user_details->fetch(PDO::FETCH_ASSOC);

if ($user == null) {
  halt(401);
}

$data = [
  "sub"         => $user_id,
  "name"        => $user['Forename'] . ' ' . $user['Surname'],
  "given_name"  => $user['Forename'],
  "family_name" => $user['Surname'],
  "email"       => $user['EmailAddress'],
  "email_verified" => true,
  "locale"      => "en-GB",
  "updated_at"  => date("U", strtotime($user['Edit']))
];

//header("Content-Type: application/json");

echo json_encode($data);
