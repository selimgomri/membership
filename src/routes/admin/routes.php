<?php

if (empty($_SESSION['SCDS-SuperUser']) && isset($_COOKIE[COOKIE_PREFIX . 'SUPERUSER-AutoLogin']) && $_COOKIE[COOKIE_PREFIX . 'SUPERUSER-AutoLogin'] != "") {

  $date = new DateTime('120 days ago', new DateTimeZone('UTC'));

  $data = [
    $_COOKIE[COOKIE_PREFIX . 'SUPERUSER-AutoLogin'],
    $date->format('Y-m-d H:i:s'),
    1
  ];

  try {
    $query = $db->prepare("SELECT superUsers.ID, `Time` FROM `superUsersLogins` INNER JOIN superUsers ON superUsers.ID = superUsersLogins.User WHERE `Hash` = ? AND `Time` >= ? AND `HashActive` = ?");
    $query->execute($data);
  } catch (PDOException $e) {
    //halt(500);
  }

  $row = $query->fetch(PDO::FETCH_ASSOC);
  if ($row != null) {
    $user = $row['ID'];
    $time = new DateTime($row['Time'], new DateTimeZone("UTC"));

    $_SESSION['SCDS-SuperUser'] = $user;

    $hash = hash('sha512', time() . $user . '-' . random_bytes(128));

    try {
      $query = $db->prepare("UPDATE `superUsersLogins` SET `Hash` = ? WHERE `Hash` = ?");
      $query->execute([$hash, $_COOKIE[COOKIE_PREFIX . 'SUPERUSER-AutoLogin']]);
    } catch (PDOException $e) {
      halt(500);
    }

    $expiry_time = ($time->format('U')) + 60 * 60 * 24 * 120;

    $secure = true;
    if (app('request')->protocol == 'http' && bool(getenv('IS_DEV'))) {
      $secure = false;
    }
    $cookiePath = '/';
    setcookie(COOKIE_PREFIX . 'SUPERUSER-AutoLogin', $hash, $expiry_time, $cookiePath, app('request')->hostname, $secure, false);
  }
}

if (!isset($_SESSION['SCDS-SuperUser'])) {
  $this->group('/login', function () {
    $this->get('/', function () {
      require BASE_PATH . 'controllers/global-admin/login/view.php';
    });

    $this->post('/', function () {
      require BASE_PATH . 'controllers/global-admin/login/post.php';
    });

    $this->post('/2fa', function () {
      require BASE_PATH . 'controllers/global-admin/login/post-2fa.php';
    });

    $this->group('/reset-password', function () {
      $this->get('/', function () {
        require BASE_PATH . 'controllers/global-admin/login/reset-password/view.php';
      });

      // $this->post('/', function () {
      //   require BASE_PATH . 'controllers/global-admin/login/reset-password/post.php';
      // });
    });
  });

  $this->any(['/', '/*'], function () {
    http_response_code(303);
    header("Location: " . autoUrl('admin/login?target=' . urlencode(app('request')->path)));
  });
}

$this->get('/', function () {
  require BASE_PATH . 'controllers/global-admin/dashboard.php';
});

$this->group('/register', function () {
  include 'register/routes.php';
});

$this->group('/notify', function () {
  include 'notify/routes.php';
});
