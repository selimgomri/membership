<?php
/**
 * @author      Christopher Heppell <chris.heppell@chesterlestreetasc.co.uk>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/Chester-le-Street-ASC/Membership
 */
namespace ChesterLeStreet\OAuth2\Entities;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
class ScopeEntity implements ScopeEntityInterface
{
  use EntityTrait;
  public function jsonSerialize()
  {
    return $this->getIdentifier();
  }
}
