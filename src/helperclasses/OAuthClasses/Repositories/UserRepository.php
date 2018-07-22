<?php
/**
 * @author      Christopher Heppell <chris.heppell@chesterlestreetasc.co.uk>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/Chester-le-Street-ASC/Membership
 */
namespace ChesterLeStreet\OAuth2\Repositories;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use ChesterLeStreet\OAuth2\Entities\UserEntity;
class UserRepository implements UserRepositoryInterface
{
  /**
   * {@inheritdoc}
   */
  public function getUserEntityByUserCredentials(
    $username,
    $password,
    $grantType,
    ClientEntityInterface $clientEntity
  ) {
    if ($username === 'alex' && $password === 'whisky') {
      return new UserEntity();
    }
    return;
  }
}
