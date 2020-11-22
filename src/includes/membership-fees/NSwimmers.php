<?php

/**
 * ClubMembership superclass
 * 
 *
 */

namespace SCDS\Membership;

use Exception;

class NSwimmers extends ClubMembership
{

  private $fees;
  private $items;
  private $total;

  /**
   * Object constructor
   */
  public function __construct($db, $user, $upgrade)
  {
    // Verify type is IndividualAndFamily
    $type = app()->tenant->getKey('ClubFeesType');
    if ($type != 'NSwimmers') {
      throw new \Exception('WrongType');
    }

    // Get the fees
    try {
      $this->fees = json_decode(app()->tenant->getKey('ClubFeeNSwimmers'));

      if ($this->fees == null) {
        $this->fees = [0];
      }

      $this->items = [];

      $this->fetchUpgradeType($db);
      $this->upgrade = $upgrade;

      $counts = $this->getSwimmers($db, $user);
      $countReg = $counts['RRCount'];
      $count = $counts['RCount'];
      $totalMembers = $counts['Total'];

      $numMemStr = null;
      if ($this->fees != null && $totalMembers >= sizeof($this->fees)) {
        $numMemStr = sizeof($this->fees) . ' or more members';
      } else if ($totalMembers > 1) {
        $numMemStr = $totalMembers . ' members';
      } else {
        $numMemStr = '1 member';
      }

      // Work out full membership fee
      $fullAmount = 0;
      if ($totalMembers > 0 && $this->fees != null && $totalMembers > sizeof($this->fees)) {
        $fullAmount = $this->fees[sizeof($this->fees) - 1];
      } else if ($totalMembers > 0 && $this->fees != null) {
        $fullAmount = $this->fees[$totalMembers - 1];
      }

      // Work out previously paid membership fee
      $paidAmount = 0;
      if ($this->fees != null && $count > 0 && $count > sizeof($this->fees) && isset($this->fees[sizeof($this->fees) - 1])) {
        $paidAmount = $this->fees[sizeof($this->fees) - 1];
      } else if ($this->fees != null && $count > 0) {
        $paidAmount = $this->fees[$count - 1];
      }

      if ($this->upgradeType == 'FullFee' || !$this->upgrade) {
        $this->total = (int) $fullAmount;
        $this->items[] = [
          'description' => 'Club membership (' . $numMemStr . ')',
          'amount' => $this->total
        ];
      } else if ($this->upgradeType == 'TopUp') {
        $this->total = (int) max([0, $fullAmount - $paidAmount]);
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
    } catch (Exception $e) {
    }
  }

  public function getFeeItems()
  {
    return $this->items;
  }

  public function getFee()
  {
    return $this->total;
  }
}
