<?php

/**
 * GALA PRICING CLASS
 */

class GalaPrices {

  private $events;
  private $gala;
  private $db;

  public function __construct(PDO $db, int $gala) {
    $this->db = $db;
    $this->gala = $gala;

    // Verify the gala exists in the database
    $verifyGalaExists = $db->prepare("SELECT COUNT(*) FROM galas WHERE GalaID = ?");
    $verifyGalaExists->execute([$this->gala]);
    if ($verifyGalaExists->fetchColumn() == 0) {
      throw new Exception('Gala does not exist');
    }

    // Get the events and pricing
    $getEvents = $db->prepare("SELECT Prices, Events FROM galaData WHERE Gala = ?");
    $getEvents->execute([$this->gala]);
    $data = $getEvents->fetch(PDO::FETCH_ASSOC);

    if ($data != null) {
      // Data exists for this gala
      // Data is stored in the JSON format

      try {

        $events = json_decode($data['Events']);
        $prices = json_decode($data['Prices']);

        // Events then prices
        foreach ($events as $key => $value) {
          if ($value) {
            $this->events['Events'][$key] = true;
            $this->events['Prices'][$key] = $prices->$key;
          }
        }
      } catch (Exception $e) {
        // What to do?
      }
    } else {
      // There's no data for this gala so assume all events are allowed at the default price
    }
  }

  /**
   * Check if an event is allowed at this gala
   *
   * @param string $event the name of the event at the gala
   * @return boolean true if event can be swam
   */
  public function isEvent(string $event) {
    // Check if this event is allowed
    if (isset($events) && isset($events['Events'][$event]) && $events['Events'][$event]) {
      return true;
    }
    return false;
  }

  /**
   * Get the price of an event
   *
   * @param string $event the name of the event at the gala
   * @return int price
   */
  public function getPrice(string $event) {
    // Check if this event is allowed
    if (!$this->isEvent($event)) {
      throw new Exception('No such event');
    }
    
    // Otherwise continue
    return $event['Prices'][$event];
  }

  /**
   * Get the price as a string formatted with a decimal point
   *
   * @param string $event
   * @return string formatted event price
   */
  public function getPriceFormatted(string $event) {
    // Get the integer price
    $price = $this->getPrice($event);

    $price = \Brick\Math\BigInteger::of($price).toBigDecimal().withPointMovedLeft(2);

    return (string) $price;
  }

  /**
   * Set the price of an event
   *
   * @param string $event
   * @param integer $price
   * @return void
   */
  public function setPrice(string $event, int $price) {
    if (!$this->isEvent($event)) {
      throw new Exception('No such event');
    }

    $this->events['Prices'][$event] = $price;
  }

  /**
   * Set whether an event is allowed or not
   *
   * @param string $event
   * @param boolean $allowed
   * @return void
   */
  public function setEvent(string $event, bool $allowed) {
    $this->events['Events'][$event] = $allowed;
  }

  /**
   * Save the gala data to the database
   *
   * @return void
   */
  public function save() {
    // Turn data for events and prices into JSON data
    $outputEvents = GalaEvents::getEvents();
    $outputPrices = GalaEvents::getEvents();

    foreach (GalaEvents::getEvents() as $eventKey => $eventName) {
      if ($this->isEvent($eventKey)) {
        $outputEvents[$eventKey] = true;
        $outputPrices[$eventKey] = $this->getPrice($eventKey);
      } else {
        $outputEvents[$eventKey] = false;
        $outputPrices[$eventKey] = 0;
      }
    }

    pre(json_encode($outputEvents));
    pre(json_encode($outputPrices));
  }

  public function setupDefault() {
    // Get the standard price
    $getPrice = $this->db->prepare("SELECT GalaFee FROM galas WHERE GalaID = ?");
    $getPrice->execute([$this->gala]);
    $priceFromDb = $getPrice->fetchColumn();

    try {
      $price = Brick\Math\BigDecimal::of((string) $priceFromDb);
      $price = (string) $price->withPointMovedRight(2)->toBigInteger();

      foreach (GalaEvents::getEvents() as $eventKey => $eventName) {
        $this->setEvent($eventKey, true);
        $this->setPrice($eventKey, $price);
      }
    } catch (Exception | Error $e) {
      pre($e);
    }

    $this->save();
  }

}