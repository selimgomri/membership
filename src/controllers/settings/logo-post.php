<?php

pre($_FILES['file-upload']);

// // import the Intervention Image Manager Class
// use Intervention\Image\ImageManager;

// // create an image manager instance with favored driver
// $manager = new ImageManager(['driver' => 'gd']);

// // to finally create image instances
// $image = $manager->make($_FILES['file-upload']['tmp_name']);

// $image->resize(225, 150);

// echo $image->response('jpg', 70);