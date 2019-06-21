<?php

if ($_SESSION['AccessLevel'] != 'Parent') {
  $this->get(['/m/{form}/{date}/{member}:int/{about}', '/s/{form}/{date}/{member}:int/{about}'], function($form, $date, $member, $about) {
    $type = 'member';
    include 'MarkCompleted.php';
  });

  $this->get('/u/{form}/{date}/{member}:int/{about}', function($form, $date, $member, $about) {
    $type = 'user';
    include 'MarkCompleted.php';
  });
}