<?php

$this->any('/', function() {
	global $link;
	require 'webhooks/handler.php';
});
