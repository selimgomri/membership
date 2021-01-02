<?php

// function loadMembership($class)
// {
//   $path = 'MembershipFees/';
//   require_once $path . $class . '.php';
// }

function loadObjects($className)
{
  $path = 'Objects/';
  $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
  require_once $path . $className . '.php';
}

spl_autoload_register('loadObjects');
// // spl_autoload_register('loadMembership');