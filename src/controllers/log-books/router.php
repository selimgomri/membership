<?php

/**
 * Router for new emergency log-book feature
 */

if (isset($_SESSION['LoggedIn']) && bool($_SESSION['LoggedIn'])) {
  // Logged in access to log books
  if ($_SESSION['AccessLevel'] == 'Parent') {
    // Log books for parents/users
    $this->get('/', function() {
      include 'members.php';
    });

    // Logs by members
    $this->get('/members/{member}:int', function($member) {
      include 'member-logs.php';
    });

    // View log
    $this->get('/logs/{id}:int', function($id) {
      include 'log.php';
    });

    // New log
    $this->get('/members/{member}:int/new', function($member) {
      include 'new-log.php';
    });

    // New log post
    $this->post('/members/{member}:int/new', function($member) {
      include 'new-log-post.php';
    });

    // Edit log
    $this->get('/logs/{id}:int/edit', function($id) {
      include 'edit-log.php';
    });

    // Edit log post
    $this->post('/logs/{id}:int/edit', function($id) {
      include 'edit-log-post.php';
    });

  } else if ($_SESSION['AccessLevel'] != 'Galas') {
    // Access for others

    // Log books welcome
    $this->get(['/squads', '/'], function() {
      include 'squads.php';
    });

    // Logs by members
    $this->get('/squads/{squad}:int', function($squad) {
      include 'squad-members.php';
    });

    // Logs by members
    $this->get('/members/{member}:int', function($member) {
      include 'member-logs.php';
    });

    // View log
    $this->get('/logs/{id}:int', function($id) {
      include 'log.php';
    });

    // New log
    $this->get('/members/{member}:int/new', function($member) {
      include 'new-log.php';
    });

    // New log post
    $this->post('/members/{member}:int/new', function($member) {
      include 'new-log-post.php';
    });

    // Edit log
    $this->get('/logs/{id}:int/edit', function($id) {
      include 'edit-log.php';
    });

    // Edit log post
    $this->post('/logs/{id}:int/edit', function($id) {
      include 'edit-log-post.php';
    });
  }


} else {
  // Public access to log-books
}