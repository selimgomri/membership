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
  private $id;
  private $forename;
  private $surname;
  private $emailAddress;
  private $accessLevel;
  private $userOptions;
  private $userOptionsRetrieved;
  private $setSession;
  private $permissions;

  public function __construct($id, $db, $setSession = true) {
    $this->id = (int) $id;
    $this->db = $db;
    $this->userOptionsRetrieved = false;
    $this->setSession = $setSession;
    $this->revalidate();
  }

  public function revalidate() {
    // Get the user
    $query = $this->db->prepare("SELECT Forename, Surname, EmailAddress FROM users WHERE UserID = ? AND Active");
    $query->execute([$this->id]);
    $row = $query->fetch(PDO::FETCH_ASSOC);

    $this->permissions = [];
    try {
      // Get access permissions
      $getPermissions = $this->db->prepare("SELECT `Permission` FROM `permissions` WHERE `User` = ?");
      $getPermissions->execute([
        $this->id
      ]);
      $this->permissions = $getPermissions->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
      // Table does not exist
    }

    if (sizeof($this->permissions) == 0) {
      $this->permissions[] = 'Parent';
    }

    if ($row) {
      $defaultAccessLevel = $this->getUserOption('DefaultAccessLevel');

      if ($defaultAccessLevel != null && in_array($defaultAccessLevel, $this->permissions)) {
        $this->accessLevel = $defaultAccessLevel;
      } else if (isset($_SESSION['AccessLevel']) && in_array($_SESSION['AccessLevel'], $this->permissions)) {
        $this->accessLevel = $_SESSION['AccessLevel'];
      } else if (in_array('Admin', $this->permissions)) {
        $this->accessLevel = $defaultAccessLevel;
      } else {
        $this->accessLevel = $this->permissions[0];
      }


      $this->forename = $row['Forename'];
      $this->surname = $row['Surname'];
      $this->emailAddress = $row['EmailAddress'];

      if ($this->setSession) {
        // Set legacy user details
        $_SESSION['Forename'] = $this->forename;
        $_SESSION['Surname'] = $this->surname;
        $_SESSION['EmailAddress'] = $this->emailAddress;
        $_SESSION['AccessLevel'] = $this->accessLevel;
      }
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
      $getOptions = $this->db->prepare("SELECT `Option`, `Value` FROM userOptions WHERE User = ? LIMIT 100");
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
    if (!isset($this->userOptions[$name])) {
      return null;
    }
    // Get the options
    if ($this->userOptions[$name] != null) {
      return $this->userOptions[$name];
    } else {
      return null;
    }
  }

  public function getUserBooleanOption($name) {
    return bool($this->getUserOption($name));
  }

  public function setUserOption($option, $value) {
    if ($value == "") {
      $value = null;
    }

    // Update value in memory
    $this->userOptions[$option] = $value;

    // Any PDO exceptions will be propagated
    $query = $this->db->prepare("SELECT COUNT(*) FROM userOptions WHERE User = ? AND `Option` = ?");
    $query->execute([$this->id, $option]);
    $result = $query->fetchColumn();

    if ($result == 0) {
      $query = $this->db->prepare("INSERT INTO userOptions (User, `Option`, `Value`) VALUES (?, ?, ?)");
      $query->execute([$this->id, $option, $value]);
    } else {
      $query = $this->db->prepare("UPDATE userOptions SET `Value` = ? WHERE User = ? AND `Option` = ?");
      $query->execute([$value, $this->id, $option]);
    }
  }

  public function getPermissions() {
    return $this->permissions;
  }

  public function getPrintPermissions() {
    $permissions = [];
    foreach ($this->permissions as $perm) {
      if ($perm == 'Admin') {
        $permissions[$perm] = 'Admin';
      } else if ($perm == 'Parent') {
        $permissions[$perm] = 'Parent/Member';
      } else if ($perm == 'Coach') {
        $permissions[$perm] = 'Coach';
      } else if ($perm == 'Galas') {
        $permissions[$perm] = 'Gala Staff';
      }
    }
    return $permissions;
  }

  public function hasPermission($permission) {
    return in_array($permission, $this->permissions);
  }

  public function grantPermission($permission) {
    try {
      $setPerm = $this->db->prepare("INSERT INTO `permissions` (`Permission`, `User`) VALUES (?, ?)");
      $setPerm->execute([
        $permission,
        $this->id
      ]);
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }

  public function revokePermission($permission) {
    try {
      $deletePerm = $this->db->prepare("DELETE FROM `permissions` WHERE `Permission` = ? AND `User` = ?");
      $deletePerm->execute([
        $permission,
        $this->id
      ]);
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }
}
