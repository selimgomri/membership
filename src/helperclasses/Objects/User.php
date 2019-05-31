<?php

/**
 * User Class
 * Cannot store this in the session
 *
 * @copyright Chester-le-Street ASC https://github.com/Chester-le-Street-ASC
 * @author Chris Heppell https://github.com/clheppell
 */
class User {
  private $db;
  private $userId;
  private $forename;
  private $surname;
  private $emailAddress;
  private $accessLevel;
  private $userOptions;
  private $userOptionsRetrieved;

  public function __construct($id, $db) {
    $this->id = (int) $id;
    $this->db = $db;
    $this->userOptionsRetrieved = false;
    $this->revalidate();
  }

  public function revalidate() {
    // Get the user
    $query = $this->db->prepare("SELECT Forename, Surname, EmailAddress, AccessLevel FROM users WHERE UserID = ?");
    $query->execute([$this->id]);
    $row = $query->fetch(PDO::FETCH_ASSOC);

    if ($row != null) {
      $this->forename = $row['Forename'];
      $this->surname = $row['Surname'];
      $this->emailAddress = $row['EmailAddress'];
      $this->accessLevel = $row['AccessLevel'];

      // Set legacy user details
      $_SESSION['Forename'] = $this->forename;
      $_SESSION['Surname'] = $this->surname;
      $_SESSION['EmailAddress'] = $this->emailAddress;
      $_SESSION['AccessLevel'] = $this->accessLevel;
    } else {
      throw new Exception();
    }
  }

  public function getDirtyFirstName() {
    return $this->forename;
  }

  public function getFirstName() {
    return $this->forename;
  }

  public function getDirtyLastName() {
    return $this->surname;
  }

  public function getLastName() {
    return $this->surname;
  }

  public function getDirtyName() {
    return $this->forename . ' ' . $this->surname;
  }

  public function getName() {
    return $this->forename . ' ' . $this->surname;
  }

  public function getDirtyEmail() {
    return $this->emailAddress;
  }

  public function getEmail() {
    return $this->getDirtyEmail();
  }

  private function getUserOptions() {
    try {
      $getOptions = $this->db->prepare("SELECT Option, Value FROM userOptions WHERE User = ? LIMIT 100");
      $getOptions->execute([$this->id]);
      $this->userOptions = $getOptions->fetchAll(PDO::FETCH_KEY_PAIR);
      $this->userOptionsRetrieved = true;
    } catch (Exception $e) {
      // Couldn't get options
    }
  }

  public function getUserOption($name) {
    if (!$this->userOptionsRetrieved) {
      $this->getUserOptions();
    }
    // Get the options
    if ($this->userOptions[$name] != null) {
      return $this->userOptions[$name];
    } else {
      return null;
    }
  }

  public function getUserBooleanOption($name) {
    return filter_var($this->getUserOption($name), FILTER_VALIDATE_BOOLEAN);
  }

  public function setUserOption($option, $value) {
    if ($value == "") {
      $value = null;
    }

    // Update value in memory
    $this->userOptions[$option] = $value;

    // Any PDO exceptions will be propagated
    $query = $this->db->prepare("SELECT COUNT(*) FROM userOptions WHERE User = ? AND Option = ?");
    $query->execute([$this->id, $option]);
    $result = $query->fetchColumn();

    if ($result == 0) {
      $query = $this->db->prepare("INSERT INTO userOptions (User, Option, Value) VALUES (?, ?, ?)");
      $query->execute([$this->id, $option, $value]);
    } else {
      $query = $this->db->prepare("UPDATE userOptions SET Value = ? WHERE User = ? AND Option = ?");
      $query->execute([$value, $this->id, $option]);
    }
  }
}
