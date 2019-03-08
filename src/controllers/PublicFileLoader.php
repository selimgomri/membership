<?php

$file = BASE_PATH . 'public/';
$file = $file . $filename;
if (file_exists($file) && mime_content_type($file) != 'directory') {
  header('Content-Description: File Transfer');
  if (strpos($file, '.css')) {
    header('Content-Type: text/css');
  } else if (strpos($file, '.json')) {
    header('Content-Type: application/json');
  } else if (strpos($file, '.js')) {
    header('Content-Type: application/javascript');
  } else {
    header('Content-Type: ' . mime_content_type($file));
  }
  if (mime_content_type($file) == 'application/pdf') {
    header('Content-Disposition: inline');
  } else {
    header('Content-Disposition: attachment; filename="'.basename($filename).'"');
  }
  if (strpos($file, '.css') || strpos($file, '.js')) {
    header('Expires: ' . date('D, d M Y H:i:s', strtotime("+28 days")) . ' GMT');
  } else {
    header('Expires: 0');
  }
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Content-Length: ' . filesize($file));
  readfile($file);
  exit;
} else {
  halt(404);
}
