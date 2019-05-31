<?php

use Respect\Validation\Validator as v;

if ($people !== true) {
  if ($_SESSION['AccessLevel'] != "Parent" && $_SESSION['AccessLevel'] != "Coach") {
  	$this->get('/new', function() {
  		global $link;
  		include 'NewPost.php';
  	});

  	$this->post('/new', function() {
  		global $link;
  		include 'NewPostServer.php';
  	});

  	$this->get(['/{id}:int/edit', '/{string}/edit'], function($id) {
  		global $link;
  		$int = false;
  		if (v::intVal()->validate($id)) {
  			$int = true;
  		}
  		include 'EditPost.php';
  	});

  	$this->post(['/{id}:int/edit', '/{string}/edit'], function($id) {
  		global $link;
  		$int = false;
  		if (v::intVal()->validate($id)) {
  			$int = true;
  		}
  		include 'EditPostServer.php';
  	});
  }

  $this->get(['/', '/list/{page}:int'], function($page = null) {
  	global $link;

  	if ($_SESSION['AccessLevel'] == "Parent") {
  		header("Location: " . autoUrl(""));
  	} else {
  		include 'PostList.php';
  	}
  });

  $this->get(['/{id}:int', '/*/{id}:int'], function($id) {
  	global $link;
    $int = true;
  	include 'Post.php';
  });

  $this->get(['/*'], function() {
  	global $link;
    $int = false;
    $id = ltrim($this[0], '/');
  	include 'Post.php';
  });
} else {
  if (isset($_SESSION['AccessLevel']) && $_SESSION['AccessLevel'] != "Parent") {
    $this->get(['/me'], function() {
    	global $link;
    	global $db;
    	$people = true;
    	$id = strtolower(str_replace(' ', '', getUserName($_SESSION['UserID'])));

    	$sql = "SELECT * FROM `posts` WHERE `Path` = ?";
    	try {
    		$query = $db->prepare($sql);
    		$query->execute([$id]);
    	} catch (PDOException $e) {
    		halt(500);
    	}
      $row = $query->fetch(PDO::FETCH_ASSOC);
      $test = true;

      if (!$row) {
        $test = false;
      }

    	if (!$test) {
  	    include 'NewPost.php';
    	} else {
  	    include 'EditPost.php';
    	}
    });

    $this->post(['/me'], function() {
    	global $link;
    	global $db;
    	$people = true;
    	$id = strtolower(str_replace(' ', '', getUserName($_SESSION['UserID'])));

    	$sql = "SELECT * FROM `posts` WHERE `Path` = ?";
    	try {
    		$query = $db->prepare($sql);
    		$query->execute([$id]);
    	} catch (PDOException $e) {
    		halt(500);
    	}
      $row = $query->fetch(PDO::FETCH_ASSOC);
      $test = true;

      if (!$row) {
        $test = false;
      }

    	if (!$test) {
  	    include 'NewPostServer.php';
    	} else {
  	    include 'EditPostServer.php';
    	}
    });
  }

  $this->get(['/', '/{page}:int'], function($page = null) {
  	global $link;

  	include 'PeopleList.php';
  });

  $this->get(['/{id}'], function($id) {
  	global $link;
  	$people = true;
  	$int = false;
  	include 'People.php';
  });
}
