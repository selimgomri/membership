<?php

$id = $_GET['squad'];

global $db;

$getSquad = $db->prepare("SELECT SquadName, SquadFee, SquadCoC, SquadTimetable, SquadCoach FROM squads WHERE SquadID = ?");
$getSquad->execute([$id]);
$squad = $getSquad->fetch(PDO::FETCH_ASSOC);

$numSwimmers = $db->prepare("SELECT COUNT(*) FROM members WHERE SquadID = ?");
$numSwimmers->execute([$id]);
$numSwimmers = $numSwimmers->fetchColumn();

$getNumSex = $db->prepare("SELECT COUNT(*) FROM members WHERE SquadID = ? AND Gender = ?");
$getNumSex->execute([$id, 'Male']);
$male = $getNumSex->fetchColumn();
$getNumSex->execute([$id, 'Female']);
$female = $getNumSex->fetchColumn();

$getBirths = $db->prepare("SELECT DateOfBirth FROM members WHERE SquadID = ?");
$getBirths->execute([$id]);
$agesArray = [];
$timeNow = new DateTime('now', new DateTimeZone('Europe/London'));
while ($dob = $getBirths->fetchColumn()) {
  $timeBirth = new DateTime($dob, new DateTimeZone('Europe/London'));
  $interval = $timeNow->diff($timeBirth);
  $age = (int) $interval->format('%y');
  if (isset($agesArray[$age])) {
    $agesArray[$age] += 1;
  } else {
    $agesArray[$age] = 1;
  }
}
$agesArrayKeys = array_keys($agesArray);
$minAge = min($agesArrayKeys);
$maxAge = max($agesArrayKeys);

$output = [
  'Labels' => [],
  'Data' => []
];

if ($maxAge - $minAge > 10) {
  foreach ($agesArray as $age => $count) {
    $output['Labels'][] = $age . " Year Olds";
    $output['Data'][] = $count;
  }
} else {
  for ($i = $minAge; $i < $maxAge+1; $i++) {
    $output['Labels'][] = $i . " Year Olds";
    if (isset($agesArray[$i])) {
      $output['Data'][] = (int) $agesArray[$i];
    } else {
      $output['Data'][] = 0;
    }
  }
}

if ($numSwimmers > 0) { ?>

var ages = <?=json_encode($output)?>;
  
var sexSplit = document.getElementById('sexSplit').getContext('2d');
var sexSplitChart = new Chart(sexSplit, {
  // The type of chart we want to create
  type: 'pie',

  // The data for our dataset
  data: {
    labels: ["Male", "Female"],
    datasets: [{
      label: <?=json_encode($squad['SquadName'] . ' Split')?>,
      data: <?=json_encode([$male, $female])?>,
      backgroundColor: <?=json_encode(chartColours(2))?>,
    }],
  },

  // Configuration options go here
  options: {}
});

var ageDistribution = document.getElementById('ageDistribution').getContext('2d');
var ageDistributionChart = new Chart(ageDistribution, {
  // The type of chart we want to create
  type: 'horizontalBar',

  // The data for our dataset
  data: {
    labels: <?=json_encode($output['Labels'])?>,
    datasets: [{
      label: <?=json_encode($squad['SquadName'] . ' Squad')?>,
      data: <?=json_encode($output['Data'])?>,
      backgroundColor: <?=json_encode(chartColours(sizeof($output['Data'])))?>,
    }],
  },

  // Configuration options go here
  options: {
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero: true,
          stepSize: 1,
        }
      }],
      xAxes: [{
        ticks: {
          beginAtZero: true,
          stepSize: 1,
        }
      }]
    }
  }
});

<?php } ?>