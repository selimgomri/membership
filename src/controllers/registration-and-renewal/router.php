<?php

/**
 * New registration and renewal system
 * 
 */

$this->get('/', function () {
  include 'home.php';
});

$this->group('/{id}:uuid', function ($id) {
  $this->get('/', function ($id) {
    include 'forms/home.php';
  });

  $this->group('/account-review', function ($id) {
    $this->get('/', function ($id) {
      include 'forms/account-review/account-review.php';
    });

    $this->post('/', function ($id) {
      include 'forms/account-review/account-review-post.php';
    });
  });

  $this->group('/member-review', function ($id) {
    $this->get('/', function ($id) {
      include 'forms/member-review/member-review.php';
    });

    $this->post('/', function ($id) {
      include 'forms/member-review/member-review-post.php';
    });
  });

  $this->group('/fee-review', function ($id) {
    $this->get('/', function ($id) {
      include 'forms/fee-review/fee-review.php';
    });

    $this->post('/', function ($id) {
      include 'forms/fee-review/fee-review-post.php';
    });
  });

  $this->group('/address-review', function ($id) {
    $this->get('/', function ($id) {
      include 'forms/address-review/address-review.php';
    });

    $this->post('/', function ($id) {
      include 'forms/address-review/address-review-post.php';
    });
  });

  $this->group('/emergency-contacts', function ($id) {
    $this->get('/', function ($id) {
      include 'forms/emergency-contacts/emergency-contacts.php';
    });

    $this->post('/', function ($id) {
      include 'forms/emergency-contacts/emergency-contacts-post.php';
    });

    $this->post('/view', function ($id) {
      include 'forms/emergency-contacts/get-view.php';
    });

    $this->post('/add', function ($id) {
      include 'forms/emergency-contacts/add.php';
    });

    $this->post('/edit', function ($id) {
      include 'forms/emergency-contacts/edit.php';
    });

    $this->post('/delete', function ($id) {
      include 'forms/emergency-contacts/delete.php';
    });
  });

  $this->group('/medical-forms', function ($id) {
    $this->get('/', function ($id) {
      include 'forms/medical-forms/medical-forms.php';
    });

    $this->post('/', function ($id) {
      include 'forms/medical-forms/medical-forms-post.php';
    });

    $this->get('/{member}:int', function ($cc, $id, $member) {
      include 'forms/medical-forms/member.php';
    });

    $this->post('/{member}:int', function ($cc, $id, $member) {
      include 'forms/medical-forms/member-post.php';
    });
  });

  $this->group('/conduct-forms', function ($id) {
    $this->get('/', function ($id) {
      include 'forms/home.php';
    });
  });

  $this->group('/data-protection-and-privacy', function ($id) {
    $this->get('/', function ($id) {
      include 'forms/home.php';
    });
  });

  $this->group('/terms-and-conditions', function ($id) {
    $this->get('/', function ($id) {
      include 'forms/home.php';
    });
  });

  $this->group('/photography-permissions', function ($id) {
    $this->get('/', function ($id) {
      include 'forms/home.php';
    });
  });

  $this->group('/administration-form', function ($id) {
    $this->get('/', function ($id) {
      include 'forms/home.php';
    });
  });

  $this->group('/direct-debit', function ($id) {
    $this->get('/', function ($id) {
      include 'forms/home.php';
    });
  });

  $this->group('/renewal-fees', function ($id) {
    $this->get('/', function ($id) {
      include 'forms/home.php';
    });
  });
});
