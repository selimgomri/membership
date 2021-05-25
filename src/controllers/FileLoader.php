<?php

$tenant = app()->tenant;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$client = new S3Client([
  'version'     => 'latest',
  'region'      => getenv('AWS_S3_REGION'),
]);

$file = '';
try {
  $file = $tenant->getFilePath();
} catch (Exception $e) {
  halt(404);
}
$file = $file . $filename;

$key = $tenant->getId() . '/' . $filename;

if (substr($filename, 0, 18) !== "notify/attachments") {
  halt(404);
}

$cmd = $client->getCommand('GetObject', [
  'Bucket' => getenv('AWS_S3_BUCKET'),
  'Key' => $key
]);
// ResponseContentDisposition can be set here

$exists = $client->doesObjectExist(getenv('AWS_S3_BUCKET'), $key);

if ($exists) {
  $request = $client->createPresignedRequest($cmd, '+5 minutes');

  $presignedUrl = (string)$request->getUri();

  http_response_code(302);
  header("location: " . $presignedUrl);
} else {
  halt(404);
}
