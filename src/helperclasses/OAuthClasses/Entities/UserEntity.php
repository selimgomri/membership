<?php
/**
 * @author      Christopher Heppell <chris.heppell@chesterlestreetasc.co.uk>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/Chester-le-Street-ASC/Membership
 */
namespace ChesterLeStreet\OAuth2\Entities;
use League\OAuth2\Server\Entities\UserEntityInterface;
class UserEntity implements UserEntityInterface
{
  /**
   * Return the user's identifier.
   *
   * @return mixed
   */
  public function getIdentifier()
  {
    return $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'];
  }
}
