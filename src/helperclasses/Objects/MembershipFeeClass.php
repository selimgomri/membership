<?php

class MembershipFeeClass
{

  private int $user;
  private $class;
  private $name;
  private $description;
  private $type;
  private $upgradeType;
  private $classFees;
  private $members;
  private $fees;

  private function __construct($user, $class, $name, $description, $fees)
  {
    $db = app()->db;

    // Assign values
    $this->user = $user;
    $this->class = $class;
    $this->name = $name;
    $this->description = $description;
    $fees = json_decode($fees);
    $this->type = $fees->type;
    $this->upgradeType = $fees->upgrade_type;
    $this->classFees = $fees->fees;

    // Get members with this class
    $getMembers = $db->prepare("SELECT MemberID, MForename, MSurname, ClubPaid FROM members WHERE UserID = ? AND ClubCategory = ? ORDER BY ClubPaid ASC, MForename ASC, MSurname ASC");
    $getMembers->execute([
      $this->user,
      $class,
    ]);
    $this->members = $getMembers->fetchAll(\PDO::FETCH_ASSOC);

    if ($this->type == 'NSwimmers') {
      $this->fees = NSwimmers::calculate($this->members, $this->classFees);
    } else if ($this->type == 'PerPerson') {
      $this->fees = PerPerson::calculate($this->members, $this->classFees);
    }
  }

  public static function get($class, $user)
  {
    $db = app()->db;
    $tenant = app()->tenant;

    // Get the class
    $getClass = $db->prepare("SELECT `Name`, `Description`, `Fees` FROM `clubMembershipClasses` WHERE `ID` = ? AND `Tenant` = ?");
    $getClass->execute([
      $class,
      $tenant->getId(),
    ]);
    $classDetails = $getClass->fetch(\PDO::FETCH_ASSOC);

    if (!$classDetails) {
      throw new \Exception('No club membership class');
    }

    $feeClass = new MembershipFeeClass(
      $user,
      $class,
      $classDetails['Name'],
      $classDetails['Description'],
      $classDetails['Fees'],
    );

    return $feeClass;
  }

  public function getTotal()
  {
    $total = 0;
    foreach ($this->fees as $item) {
      $total += $item->getAmount();
    }

    return $total;
  }

  public function getFormattedTotal()
  {
    return (string) (\Brick\Math\BigDecimal::of((string) $this->getTotal()))->withPointMovedLeft(2)->toScale(2);
  }

  public function getFeeItems()
  {
    return $this->fees;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getDescription()
  {
    return $this->description;
  }
}
