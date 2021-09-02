<?php

$db = app()->db;
$tenant = app()->tenant;
$user = app()->user;

\Stripe\Stripe::setApiKey(getenv('STRIPE'));

$location = autoUrl("");

$location = autoUrl("memberships");