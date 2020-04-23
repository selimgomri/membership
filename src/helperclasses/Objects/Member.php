<?php

class Member extends Person
{
  private $db;
  // private int $id;
  // private string $forename;
  private $middlenames;
  // private string $surname;
  private $dob;
  private $user;
  private bool $activeMembership;
  private bool $requiresRegistration;
  private string $sex;
  private $notes;
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
  function __construct($id)
  {
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
    $this->middlenames = $info['MMiddleNames'];
    $this->surname = $info['MSurname'];
    $this->dob = new DateTime($info['DateOfBirth'], new DateTimeZone('Europe/London'));
    $this->user = $info['UserID'];
    $this->squad = $info['SquadID'];
    $this->sex = $info['Gender'];
    $this->notes = $info['OtherNotes'];

    // Membership info
    $this->activeMembership = bool($info['Status']);
    $this->requiresRegistration = bool($info['RR']);
    $this->requiresRegistrationIsTransfer = bool($info['RRTransfer']);
    $this->clubMember = bool($info['ClubMember']);
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

  /**
   * Get member's middlename
   */
  public function getMiddlenames()
  {
    return $this->middlenames;
  }

  /**
   * Get a member's squads
   * 
   * @return Squad[] array of Squad objects
   */
  public function getSquads()
  {
    return [
      Squad::get($this->squad)
    ];
  }

  /**
   * Get the member age
   * 
   * @return string age in years
   */
  public function getAge($date = 'now')
  {
    $date = new DateTime($date, new DateTimeZone('Europe/London'));
    return $this->dob->diff($date)->y;
  }

  public function getUser()
  {
    if ($this->user) {
      return new User($this->user);
    }
    return null;
  }

  public function getDateOfBirth()
  {
    return clone $this->dob;
  }

  public function getSwimEnglandNumber()
  {
    return $this->swimEnglandNumber;
  }

  public function getSwimEnglandCategory()
  {
    return $this->swimEnglandCategory;
  }

  public function getCountryCode()
  {
    return $this->country;
  }

  public function getCountry()
  {
    $countries = getISOAlpha2CountriesWithHomeNations();
    return $countries[$this->getCountryCode()];
  }

  public function getMedicalNotes()
  {
    return new MedicalNotes($this->id);
  }

  /**
   * Get the user's emergency contacts
   * 
   * @return NewEmergencyContact[] an array of emergency contacts
   */
  public function getEmergencyContacts()
  {
    $contacts = [];

    if ($this->user) {
      $db = app()->db;
      $getECs = $db->prepare("SELECT Forename, Surname, Mobile FROM users WHERE UserID = ?");
      $getECs->execute([
        $this->user
      ]);
      $ec = $getECs->fetch(PDO::FETCH_ASSOC);

      if ($ec) {
        $contacts[] = new NewEmergencyContact(
          $ec['Mobile'],
          $ec['Forename'] . ' ' . $ec['Surname'],
          null,
          $this->user,
          true
        );
      }

      $getECs = $db->prepare("SELECT ID, `Name`, ContactNumber, Relation FROM emergencyContacts WHERE UserID = ?");
      $getECs->execute([
        $this->user
      ]);
      while ($ec = $getECs->fetch(PDO::FETCH_ASSOC)) {
        $contacts[] = new NewEmergencyContact(
          $ec['ContactNumber'],
          $ec['Name'],
          $ec['Relation'],
          $this->user
        );
      }
    }

    return $contacts;
  }
}
