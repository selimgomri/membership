<?php

/**
 * ClubMembership superclass
 * 
 *
 */

namespace SCDS\Membership;

class IndividualAndFamily extends ClubMembership {

  private $familyFee;
  private $individualFee;
  private $items;
  private $total;

  /**
   * Object constructor
   */
  public function __construct($db, $user, $upgrade) {
    $getValue = $db->prepare("SELECT `Value` FROM systemOptions WHERE `Option` = ?");

    // Verify type is IndividualAndFamily
    $getValue->execute(['ClubFeesType']);
    if ($getValue->fetchColumn() != 'Family/Individual') {
      throw new Exception('WrongType');
    }

    // Get the fees
    // Indiv
    $getValue->execute(['ClubFeeIndividual']);
    $this->individualFee = $getValue->fetchColumn();

    // Family
    $getValue->execute(['ClubFeeFamily']);
    $this->familyFee = $getValue->fetchColumn();

    $this->fetchUpgradeType($db);
    $this->upgrade = $upgrade;

    $counts = $this->getSwimmers($db, $user);
    $countReg = $counts['RRCount'];
    $count = $counts['RCount'];
    $totalMembers = $counts['Total'];

    if ($this->upgrade && $totalMembers > 1) {
      if ($count == 1 && $countReg > 0 && $this->upgradeType == 'TopUp') {
        $this->total = max([$this->familyFee - $this->individualFee, 0]);
        $this->items[] = [
          'description' => 'Club Membership (Top up fee)',
          'amount' => $this->total
        ];
      } else if ($this->upgradeType == 'FullFee' && $countReg > 1) {
        $this->total = $this->familyFee;
        $this->items[] = [
          'description' => 'Club Membership (family membership)',
          'amount' => $this->total
        ];
      } else if ($this->upgradeType == 'FullFee' && $countReg == 1) {
        $this->total = $this->individualFee;
        $this->items[] = [
          'description' => 'Club Membership (individual membership)',
          'amount' => $this->total
        ];
      } else {
        $this->total = 0;
        $this->items[] = [
          'description' => 'Club Membership (No fee to apply)',
          'amount' => $this->total
        ];
      }
    } else if ($totalMembers > 1) {
      $this->total = $this->familyFee;
      $this->items[] = [
        'description' => 'Club Membership (family membership)',
        'amount' => $this->total
      ];
    } else {
      $this->total = $this->individualFee;
      $this->items[] = [
        'description' => 'Club Membership (individual membership)',
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