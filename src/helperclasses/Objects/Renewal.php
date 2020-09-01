<?php

use function GuzzleHttp\json_decode;

/**
 * Renewal class
 */
class Renewal
{

  private string $id;
  private $renewal;
  private int $user;
  private $startedOn;
  private $dueBy;
  private $allowLateCompletion;
  private $members = [];
  private $fees = [];
  private $progress;
  private $latestSave;
  private $complete;
  private $stripePaymentIntent;
  private $directDebitPayment;

  private function __construct($id)
  {
    // Create renewal object
    $this->id = $id;
  }

  public static function createUserRenewal($user, $members = [])
  {
    $db = app()->db;
  }

  public static function getUserRenewal($id)
  {
    $object = new Renewal($id);
    $object->update();
  }

  public function update()
  {
    $db = app()->db;
    $getRenewal = $db->prepare("SELECT renewalPeriods.ID PID, renewalPeriods.Opens, renewalPeriods.Closes, renewalPeriods.Name, renewalPeriods.Year, renewalData.ID, renewalData.User, renewalData.Document, renewalData.PaymentIntent, renewalData.PaymentDD FROM renewalData LEFT JOIN renewalPeriods ON renewalPeriods.ID = renewalData.Renewal WHERE renewalData.ID = ?");
    $getRenewal->execute([
      $this->id,
    ]);
    $renewal = $getRenewal->fetch(PDO::FETCH_ASSOC);

    if (!$renewal) throw new Exception('No renewal');

    $this->renewal = $renewal['PID'];
    $this->user = $renewal['User'];

    // Opens date
    if ($renewal['Opens']) {
      $date = new DateTime($renewal['Opens'], new DateTimeZone('UTC'));
      $date->setTimezone(new DateTimeZone('Europe/London'));
      $this->startedOn = $date;
    }

    // Closes date
    if ($renewal['Closes']) {
      $date = new DateTime($renewal['Closes'], new DateTimeZone('UTC'));
      $date->setTimezone(new DateTimeZone('Europe/London'));
      $this->dueBy = $date;
    }

    $json = json_decode($renewal['Document']);

    $this->allowLateCompletion = $json->allow_late_completion;
    $this->members = $json->members;
    $this->fees = $json->fees;
    $this->progress = $json->progress;
    $this->complete = $json->complete;
    $this->stripePaymentIntent = $renewal['PaymentIntent'];
    $this->directDebitPayment = $renewal['PaymentDD'];
  }

  public static function save()
  {
  }
}
