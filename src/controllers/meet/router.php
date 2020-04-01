<?php

/**
 * VIDEO CONFERENCING SOFTWARE
 * (MEET)
 */

// Commented until official release
// if ($_SESSION['LoggedIn']) {
//   $this->get('/', function() {
//     include 'list.php';
//   });

//   $this->get('/{id}:int', function($id) {
//     include 'join.php';
//   });

//   if ($_SESSION['AccessLevel'] != 'Parent') {
//     $this->get('/{id}:int/begin', function($id) {
//       include 'new.php';
//     });
    
//     $this->get('/new', function() {
//       include 'new.php';
//     });

//     $this->post('/new', function() {
//       include 'new-post.php';
//     });

//     $this->get('/end', function() {
//       include 'cancel-end.php';
//     });
//   }

// } else {

//   $this->get('/', function() {
//     include 'list.php';
//   });

//   $this->get('/{id}:int', function($id) {
//     include 'join.php';
//   });

// }