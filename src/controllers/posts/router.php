<?

use Respect\Validation\Validator as v;

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

$this->get(['/{id}', '/*/{id}'], function($id) {
	global $link;
	$int = false;
	if (v::intVal()->validate($id)) {
		$int = true;
	}
	include 'Post.php';
});
