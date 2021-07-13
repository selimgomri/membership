<?php

namespace SCDS\Checkout;

use DateTimeZone;
use DateTime;
use Exception;

/**
 * Checkout Session Class
 * 
 * @author Chris Heppell
 */
class Session
{
  public $id;
  public $user;
  public $amount;
  public $currency;
  public $state;
  public $allowedTypes;
  public $created;
  public $succeeded;
  public $intent;
  public $method;
  public $version;
  public $creator;
  public $taxId;
  public $totalDetails;
  public $metadata;
  public $tenant;
  private $items;

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
      $data['created'] ?? (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
      'v1',
      json_encode($data['total_details'] ?? []),
      json_encode($data['metadata'] ?? []),
      $tenant,
    ]);

    return Session::retrieve($id);
  }

  public static function retrieve($id, $tenant = null)
  {
    $db = app()->db;
    if (!$tenant) $tenant = app()->tenant->getId();

    $get = $db->prepare("SELECT * FROM `checkoutSessions` WHERE `id` = ? AND `Tenant` = ?");
    $get->execute([
      $id,
      $tenant,
    ]);

    $sessionInfo = $get->fetch(\PDO::FETCH_OBJ);

    if (!$sessionInfo) throw new Exception('No checkout session');

    $session = new Session();

    $session->id = $id;
    $session->user = $sessionInfo->user;
    $session->amount = $sessionInfo->amount;
    $session->currency = $sessionInfo->currency;
    $session->state = $sessionInfo->state;
    $session->allowedTypes = json_decode($sessionInfo->allowed_types);
    $session->created = new \DateTime($sessionInfo->created, new \DateTimeZone(('UTC')));
    if ($sessionInfo->succeeded) {
      $session->succeeded = new \DateTime($sessionInfo->succeeded, new \DateTimeZone(('UTC')));
    } else {
      $session->succeeded = null;
    }
    $session->intent = $sessionInfo->intent;
    $session->method = $sessionInfo->method;
    $session->version = $sessionInfo->version;
    $session->taxId = $sessionInfo->tax_id;
    $session->totalDetails = json_decode($sessionInfo->total_data);
    $session->metadata = json_decode($sessionInfo->metadata);

    return $session;
  }

  public function addItem($data)
  {
    $id = \Ramsey\Uuid\Uuid::uuid4();

    $db = app()->db;

    $add = $db->prepare("INSERT INTO checkoutItems (`id`, `checkout_session`, `name`, `description`, `amount`, `currency`, `tax_amount`, `tax_data`, `sub_items`, `type`, `attributes`, `metadata`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $add->execute([
      $id,
      $this->session,
      $data['name'] ?? 'Item',
      $data['description'] ?? null,
      $data['amount'] ?? 0,
      $data['currency'] ?? 'gbp',
      $data['tax_amount'] ?? 0,
      json_encode($data['tax_data'] ?? []),
      json_encode($data['sub_items'] ?? []),
      $data['type'] ?? 'debit',
      json_encode($data['attributes'] ?? []),
      json_encode($data['metadata'] ?? []),
    ]);
  }

  public function removeItem($itemId)
  {
    $db = app()->db;

    $remove = $db->prepare("DELETE FROM checkoutItems WHERE `id` = ? AND `checkout_session` = ?");
    $remove->execute([
      $itemId,
      $this->id,
    ]);
  }

  public function getItems()
  {
    $this->loadItems();
    return $this->items;
  }

  public function save()
  {
    $db = app()->db;

    $save = $db->prepare("UPDATE checkoutSessions SET `amount` = ?, `currency` = ?, `state` = ?, `allowed_types` = ?, `created` = ?, succeeded = ?, intent = ?, method = ?, `version` = ?, creator = ?, tax_id = ?, `total_details` = ?, `metadata` = ? WHERE `id` = ?");
    $save->execute([
      $this->amount,
      $this->currency,
      $this->state,
      json_encode($this->allowedTypes),
      $this->created,
      $this->succeeded,
      $this->intent,
      $this->method,
      $this->version,
      $this->creator,
      $this->taxId,
      json_encode($this->totalDetails),
      json_encode($this->metadata),
    ]);
  }

  public function getUrl()
  {
    return autoUrl('payments/checkout/' . $this->version . '/' . $this->id);
  }

  private function loadItems()
  {
    $db = app()->db;
    $this->items = [];

    // Load the checkout session items
    $getItemIds = $db->prepare("SELECT `id` FROM `checkoutItems` WHERE `checkout_session` = ?");
    $getItemIds->execute([
      $this->id,
    ]);

    while ($item = $getItemIds->fetchColumn()) {
      $this->items[] = Item::retrieve($item);
    }
  }
}
