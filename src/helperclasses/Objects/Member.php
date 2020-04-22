<?php

class Member {
  private $db;
  private int $id;
  private string $forename;
  private string $middlenames;
  private string $surname;
  private $dob;
  private int $user;
  private bool $activeMembership;
  private bool $requiresRegistration;
  private string $sex;
  private string $notes;
  private bool $swimEnglandMember;
  private string $swimEnglandNumber;
  private int $swimEnglandCategory;
  private bool $clubPaysFees;
  private bool $clubPaysSwimEngland;
  private bool $swimEnglandPayingClub;
  private bool $clubMember;
  private int $clubCategory;
  private bool $clubPaysMembership;
  private string $country;
  private bool $current;
  private string $accessKey;

  /**
   * Create an empty member object
   */
  function __construct($id) {
    $this->id = $id;

    $db = app()->db;
    $getInfo = $db->prepare("SELECT * FROM members WHERE MemberID = ?");
    $getInfo->execute([
      $this->id
    ]);
    $info = $getInfo->fetch(PDO::FETCH_ASSOC);

    if ($info == null) {
      throw new Exception('No member');
    }

    $this->current = bool($info['Active']);

    // Basic info
    $this->forename = $info['MForename'];
    $this->middlenames = $info['MMiddlenames'];
    $this->surname = $info['MSurname'];
    $this->dob = new DateTime($info['DateOfBirth'], new DateTimeZone('Europe/London'));
    $this->user = $info['UserID'];
    $this->sex = $info['Gender'];
    $this->notes = $info['OtherNotes'];

    // Membership info
    $this->activeMembership = bool($info['Status']);
    $this->requiresRegistration = bool($info['RR']);
    $this->swimEnglandMember = bool($info['ASAMember']);
    $this->swimEnglandNumber = $info['ASANumber'];
    $this->swimEnglandCategory = $info['ASACategory'];
    $this->swimEnglandPayingClub = bool($info['ASAPrimary']);
    $this->clubCategory = $info['ClubCategory'];
    $this->country = $info['Country'];

    // Fees
    $this->clubPaysFees = bool($info['ClubPays']);
    $this->clubPaysSwimEngland = bool($info['ASAPaid']);
    $this->clubPaysMembership = bool($info['ClubPaid']);

    // Other
    $this->accessKey = $info['AccessKey'];
  }
}