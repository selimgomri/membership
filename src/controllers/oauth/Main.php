<?php

global $db;

use OAuth2\Storage\MembershipOAuthStorage;

// configure your available scopes
$defaultScope = 'basic';
$supportedScopes = array(
  'basic'
);
$memory = new OAuth2\Storage\Memory(array(
  'default_scope' => $defaultScope,
  'supported_scopes' => $supportedScopes
));
$scopeUtil = new OAuth2\Scope($memory);

$storage = new MembershipOAuthStorage($db);
$server = new OAuth2\Server($storage, [
  'allow_implicit' => true
]);

$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
$server->addGrantType(new OAuth2\GrantType\RefreshToken($storage));
//$server->addGrantType(new OAuth2\GrantType\JwtBearer($storage));
$server->addGrantType(new OAuth2\GrantType\UserCredentials($storage));

$server->setScopeUtil($scopeUtil);
