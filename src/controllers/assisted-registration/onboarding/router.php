<?php

/**
 * Membership
 * Copyright Chester-le-Street ASC/Chris Heppell/SCDS
 * 
 * Onboarding for new members
 * Router
 */

$this->get('/go', function() {
  // Go to the latest step
  $db = app()->db;

  if (!isset($_SESSION['Onboarding'])) {
    $getFirstSwimmer = $db->prepare("SELECT MemberID FROM members WHERE UserID = ? ORDER BY MemberID ASC LIMIT 1");
    if ($first = $getFirstSwimmer->fetchColumn()) {
      $_SESSION['Onboarding']['Stage'] = 1;
      $_SESSION['Onboarding']['Part'] = $first;
    } else {
      $_SESSION['Onboarding']['Stage'] = 2;
      $_SESSION['Onboarding']['Part'] = null;
    }
  }
  if ($_SESSION['Onboarding']['Stage'] == 1) {
    header("Location: " . autoUrl("onboarding/swimmer-information-form/" . $_SESSION['Onboarding']['Part']));
  } else if ($_SESSION['Onboarding']['Stage'] == 2) {
    header("Location: " . autoUrl("onboarding/emergency-contacts"));
  } else if ($_SESSION['Onboarding']['Stage'] == 3) {
    header("Location: " . autoUrl("onboarding/parent-code-of-conduct-form"));
  } else if ($_SESSION['Onboarding']['Stage'] == 4) {
    header("Location: " . autoUrl("onboarding/swimmer-code-of-conduct-form/" . $_SESSION['Onboarding']['Part']));
  } else if ($_SESSION['Onboarding']['Stage'] == 5) {
    header("Location: " . autoUrl("onboarding/admin-form"));
  } else if ($_SESSION['Onboarding']['Stage'] == 6) {
    header("Location: " . autoUrl("onboarding/setup-direct-debit"));
  } else if ($_SESSION['Onboarding']['Stage'] == 7) {
    header("Location: " . autoUrl("onboarding/review"));
  } else {
    halt(404);
  }
});

if (isset($_SESSION['Onboarding'])) {
  if ($_SESSION['Onboarding']['Stage'] > 0) {
    $this->get('/swimmer-information-form/{id}:int', function($id) {
      include 'swimmer-details.php';
    });

    $this->post('/swimmer-information-form/{id}:int', function($id) {
      include 'swimmer-details.php';
    });
  }

  if ($_SESSION['Onboarding']['Stage'] > 1) {
    $this->get('/emergency-contacts', function() {
      include 'emergency-contacts.php';
    });

    $this->post('/emergency-contacts/done', function() {
      include 'emergency-contacts-next.php';
    });
  }
}