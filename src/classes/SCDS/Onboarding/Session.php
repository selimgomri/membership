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
  private $currentStage;

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

  private function findCurrentTask()
  {
    // Loop through stages, Return on first match
    foreach ($this->stages as $stage => $data) {
      if ($data->required && !$data->completed) {
        $this->currentStage = $stage;
        // Return early
        return;
      }
    }

    $this->currentStage = 'done';
  }

  public function getCurrentTask() {
    if (!$this->currentStage) {
      $this->findCurrentTask();
    }

    return $this->currentStage;
  }

  public function isCurrentTask($task)
  {
    return $task == $this->getCurrentTask();
  }

  public function completeTask($task) {
    $stages = $this->stages;

    if (!isset($this->stages->$task)) {
      throw new \Exception();
    }

    $stages->$task->completed = true;

    $db = app()->db;
    $update = $db->prepare("UPDATE `onboardingSessions` SET `stages` = ? WHERE `id` = ?");
    $update->execute([
      json_encode($stages),
      $this->id,
    ]);

    $this->stages = $stages;
  }

  public static function  getStates()
  {
    return [
      'not_ready' => 'Not ready',
      'pending' => 'Pending',
      'in_progress' => 'In progress',
      'complete' => 'Complete',
    ];
  }

  public static function getDefaultStages()
  {
    return [
      'account_details' => [
        'required' => true,
        'completed' => false,
        'required_locked' => true,
        'metadata' => [],
      ],
      'model_forms' => [
        'required' => true,
        'completed' => false,
        'required_locked' => false,
        'metadata' => [],
      ],
      'direct_debit_mandate' => [
        'required' => bool(app()->tenant->getBooleanKey('USE_DIRECT_DEBIT')),
        'completed' => false,
        'required_locked' => !app()->tenant->getBooleanKey('USE_DIRECT_DEBIT'),
        'metadata' => [],
      ],
      'fees' => [
        'required' => true,
        'completed' => false,
        'required_locked' => false,
        'metadata' => [],
      ],
    ];
  }

  public static function stagesOrder()
  {
    return [
      'account_details' => 'Set your account password',
      'address_details' => 'Tell us your address',
      'communications_options' => 'Tell us your communications options',
      'emergency_contacts' => 'Tell us your emergency contact details',
      'member_forms' => 'Complete member information',
      'parent_conduct' => 'Agree to the parent/guardian Code of Conduct',
      'data_privacy_agreement' => 'Data Privacy Agreement',
      'terms_agreement' => 'Agree to the terms and conditions of club membership',
      'model_forms' => 'Complete registration forms',
      'direct_debit_mandate' => 'Set up a Direct Debit Instruction',
      'fees' => 'Pay your registration fees',
    ];
  }
}
