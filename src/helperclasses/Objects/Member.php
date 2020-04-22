<?php

class Member {
  private $db;
  private int $id;
  private string $forename;
  private string $middlename;
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

  /**
   * Create an empty member object
   */
  function __construct($db) {
    $this->db = $db;
  }
}