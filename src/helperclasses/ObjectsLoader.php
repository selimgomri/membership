<?php

// function loadMembership($class)
// {
//   $path = 'MembershipFees/';
//   require_once $path . $class . '.php';
// }

function loadObjects($className)
{
  $path = BASE_PATH . 'helperclasses/Objects/';
  $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
  $filename = $path . $className . '.php';
  if (file_exists($filename)) {
    require_once $filename;
  }
}

spl_autoload_register('loadObjects');
// // spl_autoload_register('loadMembership');