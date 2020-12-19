<?php

class NSwimmers extends ClassType
{

  public static function calculate($members, $fees)
  {
    $items = [];

    $fee = 0;
    if (sizeof($fees) > 0) {
      $fee = $fees[min(sizeof($fees) - 1, sizeof($members) - 1)];
    }

    for ($i = 0; $i < sizeof($members); $i++) {
      if ($i > 0) {
        $fee = 0;
      }
      $member = $members[$i];
      $items[] = new MembershipFeeItem(
        $member['MForename'] . ' ' . $member['MForename'],
        $fee,
        $member['MemberID']
      );
    }

    return $items;
  }
}
