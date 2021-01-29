<?php

class MedicalNotes {
  private int $id;
  private $conditions;
  private $allergies;
  private $medication;
  private $hasInfo;

  public function __construct($id)
  {
    $this->id = $id;
    $db = app()->db;

    $getDetails = $db->prepare("SELECT Conditions, Allergies, Medication FROM memberMedical WHERE MemberID = ?");
    $getDetails->execute([
      $this->id
    ]);

    $row = $getDetails->fetch(PDO::FETCH_ASSOC);
    if ($row) {
      $this->conditions = $row['Conditions'];
      $this->allergies = $row['Allergies'];
      $this->medication = $row['Medication'];
    } else {
      $this->conditions = null;
      $this->allergies = null;
      $this->medication = null;
    }

    $this->hasInfo = mb_strlen($this->conditions) > 0 || mb_strlen($this->allergies) > 0 || mb_strlen($this->medication) > 0;
  }

  public function getRawConditions() {
    if (mb_strlen($this->conditions) > 0) {
      return $this->conditions;
    }
    return 'N/A';
  }

  public function getRawAllergies() {
    if (mb_strlen($this->allergies) > 0) {
      return $this->allergies;
    }
    return 'N/A';
  }

  public function getRawMedication() {
    if (mb_strlen($this->medication) > 0) {
      return $this->medication;
    }
    return 'N/A';
  }

  public function getConditions() {
    $md = $this->getRawConditions();
    $markdown = new \ParsedownExtra();
    $markdown->setSafeMode(true);
    return $markdown->text($md);
  }

  public function getAllergies() {
    $md = $this->getRawAllergies();
    $markdown = new \ParsedownExtra();
    $markdown->setSafeMode(true);
    return $markdown->text($md);
  }

  public function getMedication() {
    $md = $this->getRawMedication();
    $markdown = new \ParsedownExtra();
    $markdown->setSafeMode(true);
    return $markdown->text($md);
  }

  public function hasMedicalNotes() {
    return $this->hasInfo;
  }
}