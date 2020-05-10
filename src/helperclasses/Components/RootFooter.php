<?php

namespace SCDS;

/**
 * Class for root footer
 */
class RootFooter {
  private $js;
  private $fluidContainer;

  public function __construct() {
    // new footer
    $this->js = [];
  }

  public function render() {
    include BASE_PATH . 'views/root/footer.php';
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