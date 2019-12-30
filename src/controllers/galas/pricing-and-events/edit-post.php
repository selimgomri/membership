<?php

global $db;

try {
  // Get price and event information
  $galaData = new GalaPrices($db, $id);

  $swimsArray = GalaEvents::getEvents();

  foreach ($swimsArray as $key => $value) {
    $event = $galaData->getEvent($key);

    if (bool($_POST[$key . '-check'])) {
      $event->enableEvent();
      $event->setPriceFromString((string) $_POST[$key . '-price']);
    } else {
      $event->disableEvent();
    }
  }

  $galaData->save();

  $_SESSION['PricesSaved'] = true;
} catch (Exception $e) {
  $_SESSION['PricesNotSaved'] = true;
}

header("Location: " . autoUrl("galas/" . $id . "/pricing-and-events"));