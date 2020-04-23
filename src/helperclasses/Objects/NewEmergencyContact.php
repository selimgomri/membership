<?php

/**
 * New replacement emergency contact
 */
class NewEmergencyContact {
  private $id;
  private $number;
  private $name;
  private $relation;

  public function __construct($number, $name, $relation)
  {
    $this->number = $number;
    $this->name = $name;
    $this->relation = $relation;
  }
}