<?php

$this->get('/', function() {
  include 'home.php';
});

$this->group('/members', function () {
  $this->get('/{id}:int/new-survey', function($id) {
    include 'screening-survey/survey.php';
  });
});