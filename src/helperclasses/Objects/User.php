<?php

/**
 * User Class to store in session
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

  public function __construct($id, $db) {
    $this->id = (int) $id;
    $this->db = $db;
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
    } else {
      throw new Exception();
    }
  }

  public function getDirtyFirstName() {
    return $this->forename;
  }

  public function getFirstName() {
    return htmlspecialchars($this->getDirtyFirstName());
  }

  public function getDirtyLastName() {
    return $this->surname;
  }

  public function getLastName() {
    return htmlspecialchars($this->getDirtyLastName());
  }

  public function getDirtyName() {
    return $this->forename . ' ' . $this->surname;
  }

  public function getName() {
    return htmlspecialchars($this->getDirtyName());
  }

  public function getDirtyEmail() {
    return $this->emailAddress;
  }

  public function getEmail() {
    return htmlspecialchars($this->getDirtyEmail());
  }

  public function getSwimmers() {
    return [];
  }
}
