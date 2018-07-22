<?php
/**
 * @author      Christopher Heppell <chris.heppell@chesterlestreetasc.co.uk>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/Chester-le-Street-ASC/Membership
 */
namespace ChesterLeStreet\OAuth2\Entities;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;
class RefreshTokenEntity implements RefreshTokenEntityInterface
{
  use RefreshTokenTrait, EntityTrait;
}
