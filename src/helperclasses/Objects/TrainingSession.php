<?php

/**
 * Class to represent a training session
 */
class TrainingSession
{
  private int $tenant;
  protected int $id;
  protected string $name;
  protected $dayOfWeek;
  protected $startTime;
  protected $endTime;
  protected $displayFrom;
  protected $displayUntil;
  protected $venue;
  protected $squads;

  /**
   * Create an empty squad object
   */
  function __construct()
  {
  }

  /**
   * Returns a squad object by ID
   * 
   * @param id The squad id
   * @return Squad object
   */
  public static function get(int $id)
  {
    $session = new TrainingSession();
    $session->id = $id;
    $session->revalidate();
    return $session;
  }

  /**
   * Returns a list of training sessions
   * 
   * @param Array args
   * @return Array array of training sessions
   */
  public static function list($args = [
    'date' => 'now',
  ])
  {
    $list = [];

    $date = 'now';
    if (isset($args['date'])) {
      $date = $args['date'];
    }
    $today = new DateTime($date, new DateTimeZone('Europe/London'));

    $getSessions = app()->db->prepare("SELECT SessionID FROM sessions WHERE Tenant = :tenant AND DisplayFrom <= :today AND DisplayUntil >= :today AND SessionDay = :sday ORDER BY StartTime ASC, EndTime ASC;");
    $getSessions->execute([
      'tenant' => app()->tenant->getId(),
      'today' => $today->format("Y-m-d"),
      'sday' => $today->format('w'),
    ]);

    while ($session = $getSessions->fetchColumn()) {
      $list[] = TrainingSession::get($session);
    }

    return $list;
  }

  /**
   * Refetch stored information from the database
   */
  public function revalidate()
  {
    $db = app()->db;
    $tenant = app()->tenant;

    $this->tenant = $tenant->getId();

    $getSession = $db->prepare("SELECT SessionName, VenueID, SessionDay, StartTime, EndTime, DisplayFrom, DisplayUntil FROM `sessions` WHERE SessionID = ? AND Tenant = ?");
    $getSession->execute([
      $this->id,
      $this->tenant
    ]);
    $session = $getSession->fetch(PDO::FETCH_ASSOC);

    if ($session == null) {
      throw new Exception('No session');
    }

    $this->venue = TrainingVenue::get($session['VenueID']);

    $this->name = $session['SessionName'];
    $this->dayOfWeek = $session['SessionDay'];
    $this->startTime = $session['StartTime'];
    $this->endTime = $session['EndTime'];
    $this->displayFrom = $session['DisplayFrom'];
    $this->displayUntil = $session['DisplayUntil'];

    // Get squads
    $getSquads = $db->prepare("SELECT `sessionsSquads`.`Squad` FROM `sessionsSquads` INNER JOIN squads ON `sessionsSquads`.`Squad` = squads.SquadID WHERE `Session` = ? ORDER BY SquadFee DESC, SquadName ASC");
    $getSquads->execute([
      $this->id,
    ]);
    $squads = [];
    while ($squad = $getSquads->fetchColumn()) {
      $squads[] = Squad::get($squad);
    }

    $this->squads = $squads;
  }

  /**
   * Get the name of a session
   * 
   * @return string session name
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Get the session id
   * 
   * @return int id
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * Get the venue object for the session
   * 
   * @return TrainingVenue the venue
   */
  public function getVenue()
  {
    return $this->venue;
  }

  public function getSquads()
  {
    return $this->squads;
  }

  public function getStartTime()
  {
    return (new DateTime($this->startTime, new DateTimeZone('UTC')));
  }

  public function getEndTime()
  {
    return (new DateTime($this->endTime, new DateTimeZone('UTC')));
  }

  public function getDayOfWeekInt(): int
  {
    return $this->dayOfWeek;
  }
}
