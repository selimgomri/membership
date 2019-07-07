<?php

/**
 * System Class
 * Cannot store this in the session
 *
 * @copyright Chester-le-Street ASC https://github.com/Chester-le-Street-ASC
 * @author Chris Heppell https://github.com/clheppell
 */
class SystemInfo {
  private $db;
  private $systemOptions;
  private $systemOptionsRetrieved;

  public function __construct($db) {
    $this->db = $db;
    $this->systemOptionsRetrieved = false;
  }

  public function revalidate() {
    // Get all options
    $this->getSystemOptions();
  }

  private function getSystemOptions() {
    try {
      $getOptions = $this->db->query("SELECT `Option`, `Value` FROM systemOptions");
      $this->systemOptions = $getOptions->fetchAll(PDO::FETCH_KEY_PAIR);
      $this->systemOptionsRetrieved = true;
    } catch (Exception $e) {
      // Couldn't get options
    }
  }

  public function getSystemOption($name) {
    if (!$this->systemOptionsRetrieved) {
      $this->getSystemOptions();
    }
    // Get the options
    if (isset($this->systemOptions[$name])) {
      return $this->systemOptions[$name];
    } else {
      return null;
    }
  }

  public function getSystemBooleanOption($name) {
    return filter_var($this->getSystemOption($name), FILTER_VALIDATE_BOOLEAN);
  }

  public function setSystemOption($option, $value) {
    if (!is_numeric($value) && $value == "") {
      $value = null;
    }

    // Update value in memory
    $this->systemOptions[$option] = $value;

    // Any PDO exceptions will be propagated
    $query = $this->db->prepare("SELECT COUNT(*) FROM systemOptions WHERE `Option` = ?");
    $query->execute([$option]);
    $result = $query->fetchColumn();

    if ($result == 0 && $value == null) {
      $query = $this->db->prepare("DELETE FROM systemOptions WHERE `Option` = ?");
      $query->execute([$option]);
    } else if ($result == 0) {
      $query = $this->db->prepare("INSERT INTO systemOptions (`Option`, `Value`) VALUES (?, ?)");
      $query->execute([$option, $value]);
    } else {
      $query = $this->db->prepare("UPDATE systemOptions SET `Value` = ? WHERE `Option` = ?");
      $query->execute([$value, $option]);
    }
  }
}
