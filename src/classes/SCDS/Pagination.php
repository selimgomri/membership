<?php

namespace SCDS;

/**
 * Pagination class which provides pagination
 */
class Pagination extends \Zebra_Pagination {


  /**
   * Create new SCDS Pagination object
   */
  public function __construct()
  {
    parent::__construct();
    $this->reset();
  }

  /**
   * Reset object to default SCDS settings
   */
  public function reset() {
    $this->selectable_pages(5);
    $this->labels('Previous', 'Next', 'Page %d of %d');
  }

}