<?php

/**
 * ClubMembership superclass
 * 
 *
 */

namespace SCDS\Membership;

class NSwimmers extends ClubMembership {

  private $fees;
  private $items;
  private $total;

  /**
   * Object constructor
   */
  public function __construct($db, $user, $upgrade) {
    $getValue = $db->prepare("SELECT `Value` FROM systemOptions WHERE `Option` = ?");

    // Verify type is IndividualAndFamily
    $getValue->execute(['ClubFeesType']);
    if ($getValue->fetchColumn() != 'NSwimmers') {
      throw new Exception('WrongType');
    }

    // Get the fees
    $getValue->execute(['ClubFeeNSwimmers']);
    $this->fees = json_decode($getValue->fetchColumn());

    $this->fetchUpgradeType($db);
    $this->upgrade = $upgrade;

    $counts = $this->getSwimmers($db, $user);
    pre($counts);
    $countReg = $counts['RRCount'];
    $count = $counts['RCount'];
    $totalMembers = $counts['Total'];

    $numMemStr = null;
    if ($totalMembers >= sizeof($this->fees)) {
      $numMemStr = sizeof($this->fees) . ' or more members';
    } else if ($totalMembers > 1) {
      $numMemStr = $totalMembers . ' members';
    } else {
      $numMemStr = '1 member';
    }

    // Work out full membership fee
    $fullAmount = 0;
    if ($totalMembers > 0 && $totalMembers > sizeof($this->fees)) {
      $fullAmount = $this->fees[sizeof($this->fees)-1];
    } else if ($totalMembers > 0) {
      $fullAmount = $this->fees[$totalMembers-1];
    }

    // Work out previously paid membership fee
    $paidAmount = 0;
    if ($count > 0 && $count > sizeof($this->fees)) {
      $paidAmount = $this->fees[sizeof($this->fees)-1];
    } else if ($count > 0) {
      $paidAmount = $this->fees[$count-1];
    }

    if ($this->upgradeType == 'FullFee' || !$this->upgrade) {
      $this->total = $fullAmount;
      $this->items[] = [
        'description' => 'Club membership (' . $numMemStr . ')',
        'amount' => $this->total
      ];
    } else if ($this->upgradeType == 'TopUp') {
      $this->total = max([0, $fullAmount - $paidAmount]);
      $this->items[] = [
        'description' => 'Club membership (' . $numMemStr . ' (top up fee))',
        'amount' => $this->total
      ];
    } else if ($this->upgradeType == 'None') {
      $this->total = 0;
      $this->items[] = [
        'description' => 'Club membership (' . $numMemStr . ' (no fee to pay))',
        'amount' => $this->total
      ];
    }
  }

  public function getFeeItems() {
    return $this->items;
  }

  public function getFee() {
    return $this->total;
  }

}