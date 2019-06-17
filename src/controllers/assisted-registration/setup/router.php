<?php

$this->get('/{id}:int/{password}', function($id, $password) {
  include 'beginRegistration.php';
});