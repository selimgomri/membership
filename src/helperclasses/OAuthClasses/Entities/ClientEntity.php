<?php
/**
 * @author      Christopher Heppell <chris.heppell@chesterlestreetasc.co.uk>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/Chester-le-Street-ASC/Membership
 */
namespace ChesterLeStreet\OAuth2\Entities;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
class ClientEntity implements ClientEntityInterface
{
  use EntityTrait, ClientTrait;
  public function setName($name)
  {
    $this->name = $name;
  }
  public function setRedirectUri($uri)
  {
    $this->redirectUri = $uri;
  }
}
