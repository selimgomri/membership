<?php

$this->any('/', function() {
  include BASE_PATH . 'views/root/tenants/index.php';
});