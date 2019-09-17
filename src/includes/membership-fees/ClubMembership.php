<?php

/**
 * ClubMembership superclass
 * 
 *
 */

namespace SCDS\Membership;

class ClubMembership {

  protected $upgradeType;
  protected $upgrade;

  public function __construct($db, $user, $upgrade) {
    $this->upgrade = $upgrade;
  }

  public function getFeeItems() {

  }

  public function getFee() {

  }

  protected function fetchUpgradeType($db) {
    $getValue = $db->prepare("SELECT `Value` FROM systemOptions WHERE `Option` = ?"); 
    // Upgrade type -> FullFee | TopUp | None
    $getValue->execute(['ClubFeeUpgradeType']);
    $type = $getValue->fetchColumn();
    if ($type == null) {
      // Default to no upgrade fee (safest option)
      $this->upgradeType = 'None';
    } else {
      $this->upgradeType = $type;
    }

  }

  public function getUpgradeType() {
    return $this->upgradeType;
  }

  public function isUpgrade() {
    return $this->upgrade;
  }

}