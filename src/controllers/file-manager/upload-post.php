<?php

$acceptedMimeTypes = [
  'text/*',
  'application/zip',
  'application/pdf',
  'image/*',
  'audio/*',
  'video/*',
];

if (!\SCDS\FormIdempotency::verify()) {
  halt(404);
}
if (!\SCDS\CSRF::verify()) {
  halt(404);
}

global $db;

pre($_POST);
pre($_FILES);

if (is_uploaded_file($_FILES['file-upload']['tmp_name'])) {

  if ($_FILES['file-upload']['size'] > 5000000) {
    // Too large, stop
    $_SESSION['TooLargeError'] = true;
  } else if (bool($_FILES['file-upload']['error'])) {
    // Error
    $_SESSION['UploadError'] = true;
  } else if ($_FILES['file-upload']['type'] == 'x/x') {
    // MIME type is probably not allowed
    $_SESSION['UploadError'] = true;
  } else {

  }
} else if ((int) $_FILES['file-upload']['error'] == 2) {
  // Too large, stop
  $_SESSION['TooLargeError'] = true;
} else {
  $_SESSION['UploadError'] = true;
}

//header("Location: " . autoUrl("file-manager/upload"));