<?php

$file = BASE_PATH . 'public/';
$file = $file . $filename;
if (file_exists($file) && mime_content_type($file) != 'directory') {
  header('Content-Description: File Transfer');
  if (strpos($file, '.css')) {
    header('Content-Type: text/css');
    header('Age: 0');
    header('Cache-Control: max-age=31536000, private');
  } else if (strpos($file, '.json')) {
    header('Content-Type: application/json');
    header('Age: 0');
    header('Cache-Control: max-age=31536000, private');
  } else if (strpos($file, '.js')) {
    header('Content-Type: application/javascript');
    header('Age: 0');
    header('Cache-Control: max-age=31536000, private');
  } else if (strpos($file, '.woff2')) {
    header('Content-Type: font/woff2');
    header('Age: 0');
    header('Cache-Control: max-age=31536000, private');
  } else if (strpos($file, '.png')) {
    header('Content-Type: image/png');
    header('Age: 0');
    header('Cache-Control: max-age=31536000, private');
  } else if (strpos($file, '.jpg') || strpos($file, '.jpeg')) {
    header('Content-Type: image/jpeg');
    header('Age: 0');
    header('Cache-Control: max-age=31536000, private');
  } else if (strpos($file, '.svg')) {
    header('Content-Type: image/svg+xml');
    header('Age: 0');
    header('Cache-Control: max-age=31536000, private');
  } else {
    header('Content-Type: ' . mime_content_type($file));
    header('Cache-Control: must-revalidate');
  }
  if (mime_content_type($file) == 'application/pdf' || mime_content_type($file) == 'text/html' || mime_content_type($file) == 'text/css' || strpos($file, '.css') || mime_content_type($file) == 'application/javascript') {
    header('Content-Disposition: inline');
  } else {
    header('Content-Disposition: attachment; filename="'.basename($filename).'"');
  }
  if (strpos($file, '.css') || mb_strpos($file, '.js')) {
    header('Expires: ' . date('D, d M Y H:i:s', strtotime("+1 year")) . ' GMT');
  } else {
    header('Expires: 0');
  }
  header('pragma: public');
  header('content-length: ' . filesize($file));
  readfile($file);
  exit;
} else {
  halt(404);
}
