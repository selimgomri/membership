<?php

$this->get('/upload', function() {
	include 'Upload.php';
});

$this->post('/upload', function() {
	include 'UploadPost.php';
});