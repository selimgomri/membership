<?php

namespace SCDS\Onboarding;

use DateTimeZone;
use DateTime;
use Exception;

/**
 * Onboarding Session Class
 * 
 * @author Chris Heppell
 */
class Session
{
  public $id;
  public $user;
  public $created;
  public $creator;
  public $start;
  public $chargeOutstanding;
  public $chargeProRata;
  public $welcomeText;
  public $token;
  public $tokenOn;
  public $status;
  public $dueDate;
  public $completedAt;
  public $stages;
  public $metadata;
  public $batch;
  public $members;

  private function __construct()
  {
  }

  public static function retrieve($id, $tenant = null)
  {
    $db = app()->db;
    if (!$tenant) $tenant = app()->tenant->getId();

    $get = $db->prepare("SELECT * FROM `onboardingSessions` INNER JOIN users ON users.UserID = onboardingSessions.user WHERE `id` = ? AND `Tenant` = ?");
    $get->execute([
      $id,
      $tenant,
    ]);

    $sessionInfo = $get->fetch(\PDO::FETCH_OBJ);

    if (!$sessionInfo) throw new Exception('No onboarding session');

    $session = new Session();

    $session->id = $id;
    $session->user = $sessionInfo->user;
    $session->created = new \DateTime($sessionInfo->created, new \DateTimeZone(('UTC')));
    $session->creator = $sessionInfo->creator;
    $session->start = new \DateTime($sessionInfo->start, new \DateTimeZone(('UTC')));
    $session->chargeOutstanding = $sessionInfo->charge_outstanding;
    $session->chargeProRata = $sessionInfo->charge_pro_rata;
    $session->welcomeText = $sessionInfo->welcome_text;
    $session->token = $sessionInfo->token;
    $session->tokenOn = $sessionInfo->token_on;
    $session->status = $sessionInfo->status;
    $session->dueDate = new \DateTime($sessionInfo->due_date, new \DateTimeZone(('UTC')));
    $session->completedAt = new \DateTime($sessionInfo->completed_at, new \DateTimeZone(('UTC')));
    $session->stages = json_decode($sessionInfo->stages);
    $session->metadata = json_decode($sessionInfo->metadata);
    $session->batch = $sessionInfo->batch;

    // Get members
    $session->loadMembers();

    return $session;
  }

  private function loadMembers()
  {
    $db = app()->db;
    $getMembers = $db->prepare("SELECT MemberID, MForename, MSurname FROM members INNER JOIN onboardingMembers ON members.MemberID = onboardingMembers.member WHERE `session` = ? AND `UserID` = ? ORDER BY MemberID ASC");
    $getMembers->execute([
      $this->id,
      $this->user,
    ]);

    $members = [];

    while ($member = $getMembers->fetch(\PDO::FETCH_OBJ)) {
      $memberObject = new \stdClass;

      $memberObject->id = $member->MemberID;
      $memberObject->firstName = $member->MForename;
      $memberObject->lastName = $member->MSurname;

      $members[] = $memberObject;
    }

    $this->members = $members;
  }

  public function getMembers()
  {
    $this->loadMembers();
    return $this->members;
  }

  public function getUser()
  {
    return new \User($this->user);
  }

  public function getCreator()
  {
    return new \User($this->creator);
  }

  private function getUrl()
  {
    return autoUrl("onboarding/go?session=" . urlencode($this->id) . "&token=" . urlencode($this->token));
  }

  public function enableToken()
  {
    $updateSession = app()->db->prepare("UPDATE `onboardingSessions` SET `token_on` = ? WHERE `id` = ?");
    $updateSession->execute([
      (int) true,
      $this->id,
    ]);
  }

  public function sendEmail()
  {
    $this->enableToken();

    $user = $this->getUser();

    $subject = 'Complete your registration at ' . app()->tenant->getName();
    $content = '<p>Dear ' . htmlspecialchars($user->getFullName()) . ',</p>';

    $content .= '<p><a href="' . htmlspecialchars($this->getUrl()) . '">Please complete your registration tasks online</a>.</p>';
    $content .= '<p><a href="' . htmlspecialchars($this->getUrl()) . '">' . htmlspecialchars($this->getUrl()) . '</a></p>';

    $content .= '<p>Thank you, <br>The ' . htmlspecialchars(app()->tenant->getName()) . ' team.</p>';

    notifySend(null, $subject, $content, $user->getFullName(), $user->getEmail(), ['Name' => app()->tenant->getName() . ' Membership Secretary']);
  }
}
