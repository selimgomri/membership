<?

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use ChesterLeStreet\OAuth2\Entities\UserEntity;
use ChesterLeStreet\OAuth2\Repositories\AccessTokenRepository;
use ChesterLeStreet\OAuth2\Repositories\AuthCodeRepository;
use ChesterLeStreet\OAuth2\Repositories\ClientRepository;
use ChesterLeStreet\OAuth2\Repositories\RefreshTokenRepository;
use ChesterLeStreet\OAuth2\Repositories\ScopeRepository;

$OAuth2ServerClientRepo = new ClientRepository();
$OAuth2ServerAccessTokenRepo = new AccessTokenRepository();
$OAuth2ServerScopeRepo = new ScopeRepository();
$OAuth2ServerAuthCodeRepo = new AuthCodeRepository();
$OAuth2ServerRefreshTokenRepo = new RefreshTokenRepository();

$server = new \League\OAuth2\Server\AuthorizationServer(
  $OAuth2ServerClientRepo,
  $OAuth2ServerAccessTokenRepo,
  $OAuth2ServerScopeRepo,
  $OAuth2ServerPrivateKeyPath,
  $OAuth2ServerKey
);

$grant = new \League\OAuth2\Server\Grant\AuthCodeGrant(
  $OAuth2ServerAuthCodeRepo,
  $OAuth2ServerRefreshTokenRepo,
  new \DateInterval('PT10M') // authorization codes will expire after 10 minutes
);

$grant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month

// Enable the authentication code grant on the server
$server->enableGrantType(
  $grant,
  new \DateInterval('PT1H') // access tokens will expire after 1 hour
);
