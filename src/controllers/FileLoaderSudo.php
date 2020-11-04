<?php

$tenant = Tenant::fromId($tenant);

$file = '';
try {
  $file = $tenant->getFilePath();
} catch (Exception $e) {
  halt(404);
}
$file = $file . $filename;

if (file_exists($file)) {
  header('Content-Description: File Transfer');
  header('Content-Type: ' . mime_content_type($file));
  if (mime_content_type($file) == 'application/pdf' || mb_substr(mime_content_type($file), 0, mb_strlen('image')) === 'image') {
    header('Content-Disposition: inline');
  } else {
    header('Content-Disposition: attachment; filename="'.basename($filename).'"');
  }
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Content-Length: ' . filesize($file));
  readfile($file);
  exit;
} else {
  halt(404);
}
