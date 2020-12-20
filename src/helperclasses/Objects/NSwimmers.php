<?php

class NSwimmers extends ClassType
{

  public static function calculate($members, $fees, $partial = false)
  {
    $items = [];
    $existingItems = [];

    $fee = $originalFee = 0;
    if (sizeof($fees) > 0) {
      $originalFee = $fee = $fees[min(sizeof($fees) - 1, sizeof($members) - 1)];
    }

    for ($i = 0; $i < sizeof($members); $i++) {
      if ($i > 0) {
        $fee = 0;
      }
      $member = $members[$i];
      $items[] = new MembershipFeeItem(
        $member['MForename'] . ' ' . $member['MSurname'],
        $fee,
        $member['MemberID']
      );
    }

    if ($partial) {
      // Get list of partial members
      $existingMembers = [];
      for ($i = 0; $i < sizeof($members); $i++) {
        if (!bool($members[$i]['RR'])) {
          $existingMembers[] = $members[$i];
        }
      }

      $paidFee = 0;
      if (sizeof($fees) > 0) {
        $paidFee = $fees[min(sizeof($fees) - 1, sizeof($existingMembers) - 1)];
      }

      $fee = max(0, $originalFee - $paidFee);
      if (sizeof($items) > 0) {
        $items[0]->setAmount($fee);
      }
      
    }

    return $items;
  }
}
