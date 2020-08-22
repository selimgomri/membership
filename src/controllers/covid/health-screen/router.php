<?php

$this->get('/', function() {
  include 'home.php';
});

$this->group('/members', function () {
  $this->get('/{id}:int', function($id) {
    include 'submissions/member.php';
  });

  $this->get('/{id}:int/new-survey', function($id) {
    include 'screening-survey/survey.php';
  });

  $this->post('/{id}:int/new-survey', function($id) {
    include 'screening-survey/survey-post.php';
  });
});