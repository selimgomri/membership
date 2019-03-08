<?php

// include our OAuth2 Server object
require_once 'Main.php';

// Handle a request for an OAuth2.0 Access Token and send the response to the client
$request = OAuth2\Request::createFromGlobals();
$server->handleTokenRequest($request)->send();

$response = new OAuth2\Response();

// will set headers, status code, and json response appropriately for success or failure
$server->grantAccessToken($request, $response);
$response->send();
