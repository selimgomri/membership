<?php

class MembershipFees {

  private $classes;
  private $user;

  private function __construct($user, $classes) {
    $this->user = $user;
    $this->classes = $classes;
  }

  public static function getByUser($user) {
    $db = app()->db;

    // Get classes
    $getClasses = $db->prepare("SELECT DISTINCT `ID` FROM clubMembershipClasses INNER JOIN members ON members.ClubCategory = clubMembershipClasses.ID WHERE members.UserID = ?");
    $getClasses->execute([
      $user,
    ]);
    $classes = $getClasses->fetchAll(\PDO::FETCH_COLUMN);

    $objects = [];
    foreach ($classes as $class) {
      $objects[] = MembershipFeeClass::get($class, $user);
    }

    $object = new MembershipFees($user, $objects);
    return $object;
  }

  public function getTotal() {
    $total = 0;
    foreach ($this->classes as $class) {
      $total += $class->getTotal();
    }

    return $total;
  }

  public function getFormattedTotal() {
    return (string) (\Brick\Math\BigDecimal::of((string) $this->getTotal()))->withPointMovedLeft(2)->toScale(2);
  }
}