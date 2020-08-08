<?php

$this->get('/', function() {
  include 'home.php';
});

$this->group('/members', function () {
  $this->get('/{id}:int/new-form', function($id) {
    include 'risk-form/form.php';
  });
});