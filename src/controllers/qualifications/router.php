<?php

$this->get('/', function() {
  include 'MyQualifications.php';
});

if ($_SESSION['AccessLevel'] == "Admin") {
  $this->group('/admin', function() {
    $this->get('/new/{person}:int', function($person) {
      include 'NewQualification.php';
    });

    $this->post('/new/{person}:int', function($person) {
      include 'NewQualificationPost.php';
    });

    $this->get('/{id}:int', function($id) {
      include 'EditQualification.php';
    });

    $this->post('/{id}:int', function($id) {
      include 'EditQualificationPost.php';
    });
  });
}
