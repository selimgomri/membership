<?php

class PerPerson extends ClassType
{

  public static function calculate($members, $fees, $partial = false)
  {
    $fee = 0;
    if (sizeof($fees) > 0) {
      $fee = $fees[0];
    }

    $items = [];

    for ($i = 0; $i < sizeof($members); $i++) {
      $member = $members[$i];
      $items[] = new MembershipFeeItem(
        $member['MForename'] . ' ' . $member['MSurname'],
        $fee,
        $member['MemberID']
      );
    }

    return $items;
  }
}
