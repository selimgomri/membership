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
    // Verify type is IndividualAndFamily
    $type = app()->tenant->getKey('ClubFeesType');
    if ($type != 'Family/Individual') {
      throw new \Exception('WrongType');
    }

    // Get the fees
    // Indiv
    $this->individualFee = app()->tenant->getKey('ClubFeeIndividual');

    // Family
    $this->familyFee = app()->tenant->getKey('ClubFeeFamily');

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