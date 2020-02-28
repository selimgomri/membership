<?php

namespace SDCS;

/**
 * Class for footer
 */
class Footer {
  private $js;
  private $fluidContainer;

  public function __construct() {
    // new footer
    $this->js = [];
  }

  public function render() {
    include BASE_PATH . 'views/footer.php';
  }

  public function addJs($path) {
    $this->js[] = autoUrl($path);
  }

  public function addExternalJs($uri) {
    $this->js[] = $uri;
  }

  public function useFluidContainer($bool = true) {
    $this->fluidContainer = $bool;
  }
}