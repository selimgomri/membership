<?php

if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == "Admin") {
  $this->group('/qualifications', function() {
    $this->get('/', function() {
      include 'admin/AdminPage.php';
    });

    $this->get('/new', function() {
      include 'admin/NewQualificationOption.php';
    });

    $this->post('/new', function() {
      include 'admin/NewQualificationOptionPost.php';
    });

    $this->get('/{id}:int', function($id) {
      include 'admin/EditQualificationOption.php';
    });

    $this->post('/{id}:int', function($id) {
      include 'admin/EditQualificationOptionPost.php';
    });

    /*
    $this->get('/new/{person}:int', function($person) {
      include 'admin/NewQualification.php';
    });

    $this->post('/new/{person}:int', function($person) {
      include 'admin/NewQualificationPost.php';
    });

    $this->get('/{id}:int', function($id) {
      include 'admin/EditQualification.php';
    });

    $this->post('/{id}:int', function($id) {
      include 'admin/EditQualificationPost.php';
    });

    */
  });
}
