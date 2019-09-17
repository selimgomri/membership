<?php

/**
 * ClubMembership superclass
 * 
 *
 */

namespace SCDS\Membership;

class ZeroFee extends ClubMembership {

  private $items;
  private $total;

  /**
   * COnstructor
   *
   * @param PDO $db
   * @param int $user
   * @param boolean $upgrade
   */
  public function __construct($db, $user, $upgrade) {
    $this->items = [];
    $this->total = 0;
  }

  /**
   * Returns an array of membership fee line items
   *
   * @return array
   */
  public function getFeeItems() {
    return $this->items;
  }

  /**
   * Returns the membership fee
   *
   * @return int
   */
  public function getFee() {
    return $this->total;
  }

}