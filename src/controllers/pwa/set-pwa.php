<?php

$_SESSION['PWA'] = true;

$secure = true;
if (app('request')->protocol == 'http') {
  $secure = false;
}
$time = new \DateTime('now', new \DateTimeZone('UTC'));
$expiry_time = ($time->format('U'))+60*60*24*120;
setcookie(COOKIE_PREFIX . "PWA", true, $expiry_time , "/", app('request')->hostname, $secure, false);

header("Location: " . autoUrl(""));