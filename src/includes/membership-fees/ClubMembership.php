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

  public static function create($db, $user, $upgrade) {
    // Work out the fee object class and return it
    $getType = $db->query("SELECT `Value` FROM systemOptions WHERE `Option` = 'ClubFeesType'");
    $type = $getType->fetchColumn();

    if ($type == 'Family/Individual') {
      return new IndividualAndFamily($db, $user, $upgrade);
    } else if ($type == 'NSwimmers') {
      return new NSwimmers($db, $user, $upgrade);
    } else {
      return new ZeroFee($db, $user, $upgrade);
    }
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

  protected function getSwimmers($db, $user) {
    $getMemberCount = $db->prepare("SELECT COUNT(*) FROM members WHERE UserID = ? AND RR = ? AND NOT ClubPays");
    $getMemberCount->execute([
      $user,
      '1'
    ]);
    $countReg = $getMemberCount->fetchColumn();

    $getMemberCount->execute([
      $user,
      '0'
    ]);
    $count = $getMemberCount->fetchColumn();

    $totalMembers = $countReg + $count;

    return [
      'RRCount' => $countReg,
      'RCount' => $count,
      'Total' => $totalMembers
    ];
  }

  public function getUpgradeType() {
    return $this->upgradeType;
  }

  public function isUpgrade() {
    return $this->upgrade;
  }

}