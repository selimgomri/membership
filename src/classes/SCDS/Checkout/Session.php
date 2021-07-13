<?php

namespace SCDS\Checkout;

use DateTimeZone;
use Exception;

/**
 * Checkout Session Class
 * 
 * @author Chris Heppell
 */
class Session
{
  private $id;

  private function __construct()
  {
  }

  public static function new($data, $tenant = null)
  {
    $db = app()->db;
    if (!$tenant) $tenant = app()->tenant->getId();

    // Validate user at this tenant
    if ($data['user']) {
      $userExists = $db->prepare("SELECT COUNT(*) FROM `users` WHERE `UserID` = ? AND `Tenant` = ?");
      $userExists->execute([
        $data['user'],
        $tenant,
      ]);

      if ($userExists->fetchColumn() == 0) throw new Exception('No user at tenant');
    }

    $id = \Ramsey\Uuid\Uuid::uuid4();

    // Parse data and insert into db
    $add = $db->prepare("INSERT INTO `checkoutSessions` (`id`, `user`, `amount`, `currency`, `state`, `allowed_types`, `created`, `version`, `total_details`, `metadata`, `Tenant`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $add->execute([
      $id,
      $data['user'] ?? null,
      $data['amount'] ?? 0,
      $data['currency'] ?? 'gbp',
      $data['state'] ?? 'open',
      json_encode($data['allowed_types'] ?? ['card' => true]),
      $data['created'] ?? (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
      'v1',
      json_encode($data['total_details'] ?? []),
      json_encode($data['metadata'] ?? []),
      $tenant,
    ]);

    return Session::retrieve($id);
  }

  public static function retrieve($id)
  {
    return new Session();
  }

  public function addItem($item)
  {
  }

  public function removeItem($item)
  {
  }

  public function save()
  {
  }
}
