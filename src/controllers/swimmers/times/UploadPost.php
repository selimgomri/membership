<?php

use Symfony\Component\DomCrawler\Crawler;

pre($_FILES['last-12']);
$contents = file_get_contents($_FILES['last-12']['tmp_name']);

$array12 = null;
$start = '<p class="rnk_sj">Long Course</p><table id="rankTable" border="0"><tbody>';
$end = '</tbody></table>';

//pre(htmlentities($contents));

$output = curl_scrape_between($contents, $start, $end);
$output = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $output);
$output = preg_replace('/(<[^>]+) width=".*?"/i', '$1', $output);

$crawler = new Crawler($output);
$crawler = $crawler->filter('tr > td');

$obj_to_array = [];
foreach ($crawler as $domElement) {
  $obj_to_array[] = $domElement->textContent;
}

for ($x = 0; $x < sizeof($obj_to_array); $x = $x+8) {
  $array12[$obj_to_array[$x]]['Long']['EventName'] = trim($obj_to_array[$x]);
  $array12[$obj_to_array[$x]]['Long']['Time'] = trim($obj_to_array[$x+1]);
  $array12[$obj_to_array[$x]]['Long']['FINA'] = trim($obj_to_array[$x+2]);
  $array12[$obj_to_array[$x]]['Long']['Date'] = trim($obj_to_array[$x+3]);
  $array12[$obj_to_array[$x]]['Long']['Meet'] = trim($obj_to_array[$x+4]);
  $array12[$obj_to_array[$x]]['Long']['Venue'] = trim($obj_to_array[$x+5]);
  $array12[$obj_to_array[$x]]['Long']['Licence'] = trim($obj_to_array[$x+6]);
  $array12[$obj_to_array[$x]]['Long']['Level'] = trim($obj_to_array[$x+7]);
};

/*
$t1 = $array12['50 Freestyle']['Long']['Time'];
$t2 = $array12['100 Freestyle']['Long']['Time'];
$t3 = $array12['200 Freestyle']['Long']['Time'];
$t4 = $array12['400 Freestyle']['Long']['Time'];
$t5 = $array12['800 Freestyle']['Long']['Time'];
$t6 = $array12['1500 Freestyle']['Long']['Time'];
$t7 = $array12['50 Breaststroke']['Long']['Time'];
$t8 = $array12['100 Breaststroke']['Long']['Time'];
$t9 = $array12['200 Breaststroke']['Long']['Time'];
$t10 = $array12['50 Butterfly']['Long']['Time'];
$t11 = $array12['100 Butterfly']['Long']['Time'];
$t12 = $array12['200 Butterfly']['Long']['Time'];
$t13 = $array12['50 Backstroke']['Long']['Time'];
$t14 = $array12['100 Backstroke']['Long']['Time'];
$t15 = $array12['200 Backstroke']['Long']['Time'];
$t16 = $array12['200 Individual Medley']['Long']['Time'];
$t17 = $array12['400 Individual Medley']['Long']['Time'];
$t18 = null;
*/

$start = '<p class="rnk_sj">Short Course</p><table id="rankTable" border="0"><tbody>';
$end = '</tbody></table>';

//pre(htmlentities($contents));

$output = curl_scrape_between($contents, $start, $end);
$output = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $output);
$output = preg_replace('/(<[^>]+) width=".*?"/i', '$1', $output);

$crawler = new Crawler($output);
$crawler = $crawler->filter('tr > td');

$obj_to_array = [];
foreach ($crawler as $domElement) {
  $obj_to_array[] = $domElement->textContent;
}

for ($x = 0; $x < sizeof($obj_to_array); $x = $x+8) {
  $array12[$obj_to_array[$x]]['Short']['EventName'] = trim($obj_to_array[$x]);
  $array12[$obj_to_array[$x]]['Short']['Time'] = trim($obj_to_array[$x+1]);
  $array12[$obj_to_array[$x]]['Short']['FINA'] = trim($obj_to_array[$x+2]);
  $array12[$obj_to_array[$x]]['Short']['Date'] = trim($obj_to_array[$x+3]);
  $array12[$obj_to_array[$x]]['Short']['Meet'] = trim($obj_to_array[$x+4]);
  $array12[$obj_to_array[$x]]['Short']['Venue'] = trim($obj_to_array[$x+5]);
  $array12[$obj_to_array[$x]]['Short']['Licence'] = trim($obj_to_array[$x+6]);
  $array12[$obj_to_array[$x]]['Short']['Level'] = trim($obj_to_array[$x+7]);
};

/*
$t1 = $array12['50 Freestyle']['Short']['Time'];
$t2 = $array12['100 Freestyle']['Short']['Time'];
$t3 = $array12['200 Freestyle']['Short']['Time'];
$t4 = $array12['400 Freestyle']['Short']['Time'];
$t5 = $array12['800 Freestyle']['Short']['Time'];
$t6 = $array12['1500 Freestyle']['Short']['Time'];
$t7 = $array12['50 Breaststroke']['Short']['Time'];
$t8 = $array12['100 Breaststroke']['Short']['Time'];
$t9 = $array12['200 Breaststroke']['Short']['Time'];
$t10 = $array12['50 Butterfly']['Short']['Time'];
$t11 = $array12['100 Butterfly']['Short']['Time'];
$t12 = $array12['200 Butterfly']['Short']['Time'];
$t13 = $array12['50 Backstroke']['Short']['Time'];
$t14 = $array12['100 Backstroke']['Short']['Time'];
$t15 = $array12['200 Backstroke']['Short']['Time'];
$t16 = $array12['200 Individual Medley']['Short']['Time'];
$t17 = $array12['400 Individual Medley']['Short']['Time'];
$t18 = $array12['100 Individual Medley']['Short']['Time'];
*/

pre($array12);

pre($_FILES['all-time']);
$contents = file_get_contents($_FILES['all-time']['tmp_name']);

$arrayAllTime = null;
$start = '<p class="rnk_sj">Long Course</p><table id="rankTable" border="0"><tbody>';
$end = '</tbody></table>';

//pre(htmlentities($contents));

$output = curl_scrape_between($contents, $start, $end);
$output = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $output);
$output = preg_replace('/(<[^>]+) width=".*?"/i', '$1', $output);

$crawler = new Crawler($output);
$crawler = $crawler->filter('tr > td');

$obj_to_array = [];
foreach ($crawler as $domElement) {
  $obj_to_array[] = $domElement->textContent;
}

for ($x = 0; $x < sizeof($obj_to_array); $x = $x+8) {
  $arrayAllTime[$obj_to_array[$x]]['Long']['EventName'] = trim($obj_to_array[$x]);
  $arrayAllTime[$obj_to_array[$x]]['Long']['Time'] = trim($obj_to_array[$x+1]);
  $arrayAllTime[$obj_to_array[$x]]['Long']['FINA'] = trim($obj_to_array[$x+2]);
  $arrayAllTime[$obj_to_array[$x]]['Long']['Date'] = trim($obj_to_array[$x+3]);
  $arrayAllTime[$obj_to_array[$x]]['Long']['Meet'] = trim($obj_to_array[$x+4]);
  $arrayAllTime[$obj_to_array[$x]]['Long']['Venue'] = trim($obj_to_array[$x+5]);
  $arrayAllTime[$obj_to_array[$x]]['Long']['Licence'] = trim($obj_to_array[$x+6]);
  $arrayAllTime[$obj_to_array[$x]]['Long']['Level'] = trim($obj_to_array[$x+7]);
};

/*
$t1 = $arrayAllTime['50 Freestyle']['Long']['Time'];
$t2 = $arrayAllTime['100 Freestyle']['Long']['Time'];
$t3 = $arrayAllTime['200 Freestyle']['Long']['Time'];
$t4 = $arrayAllTime['400 Freestyle']['Long']['Time'];
$t5 = $arrayAllTime['800 Freestyle']['Long']['Time'];
$t6 = $arrayAllTime['1500 Freestyle']['Long']['Time'];
$t7 = $arrayAllTime['50 Breaststroke']['Long']['Time'];
$t8 = $arrayAllTime['100 Breaststroke']['Long']['Time'];
$t9 = $arrayAllTime['200 Breaststroke']['Long']['Time'];
$t10 = $arrayAllTime['50 Butterfly']['Long']['Time'];
$t11 = $arrayAllTime['100 Butterfly']['Long']['Time'];
$t12 = $arrayAllTime['200 Butterfly']['Long']['Time'];
$t13 = $arrayAllTime['50 Backstroke']['Long']['Time'];
$t14 = $arrayAllTime['100 Backstroke']['Long']['Time'];
$t15 = $arrayAllTime['200 Backstroke']['Long']['Time'];
$t16 = $arrayAllTime['200 Individual Medley']['Long']['Time'];
$t17 = $arrayAllTime['400 Individual Medley']['Long']['Time'];
$t18 = null;
*/

$start = '<p class="rnk_sj">Short Course</p><table id="rankTable" border="0"><tbody>';
$end = '</tbody></table>';

//pre(htmlentities($contents));

$output = curl_scrape_between($contents, $start, $end);
$output = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $output);
$output = preg_replace('/(<[^>]+) width=".*?"/i', '$1', $output);

$crawler = new Crawler($output);
$crawler = $crawler->filter('tr > td');

$obj_to_array = [];
foreach ($crawler as $domElement) {
  $obj_to_array[] = $domElement->textContent;
}

for ($x = 0; $x < sizeof($obj_to_array); $x = $x+8) {
  $arrayAllTime[$obj_to_array[$x]]['Short']['EventName'] = trim($obj_to_array[$x]);
  $arrayAllTime[$obj_to_array[$x]]['Short']['Time'] = trim($obj_to_array[$x+1]);
  $arrayAllTime[$obj_to_array[$x]]['Short']['FINA'] = trim($obj_to_array[$x+2]);
  $arrayAllTime[$obj_to_array[$x]]['Short']['Date'] = trim($obj_to_array[$x+3]);
  $arrayAllTime[$obj_to_array[$x]]['Short']['Meet'] = trim($obj_to_array[$x+4]);
  $arrayAllTime[$obj_to_array[$x]]['Short']['Venue'] = trim($obj_to_array[$x+5]);
  $arrayAllTime[$obj_to_array[$x]]['Short']['Licence'] = trim($obj_to_array[$x+6]);
  $arrayAllTime[$obj_to_array[$x]]['Short']['Level'] = trim($obj_to_array[$x+7]);
};

/*
$t1 = $arrayAllTime['50 Freestyle']['Short']['Time'];
$t2 = $arrayAllTime['100 Freestyle']['Short']['Time'];
$t3 = $arrayAllTime['200 Freestyle']['Short']['Time'];
$t4 = $arrayAllTime['400 Freestyle']['Short']['Time'];
$t5 = $arrayAllTime['800 Freestyle']['Short']['Time'];
$t6 = $arrayAllTime['1500 Freestyle']['Short']['Time'];
$t7 = $arrayAllTime['50 Breaststroke']['Short']['Time'];
$t8 = $arrayAllTime['100 Breaststroke']['Short']['Time'];
$t9 = $arrayAllTime['200 Breaststroke']['Short']['Time'];
$t10 = $arrayAllTime['50 Butterfly']['Short']['Time'];
$t11 = $arrayAllTime['100 Butterfly']['Short']['Time'];
$t12 = $arrayAllTime['200 Butterfly']['Short']['Time'];
$t13 = $arrayAllTime['50 Backstroke']['Short']['Time'];
$t14 = $arrayAllTime['100 Backstroke']['Short']['Time'];
$t15 = $arrayAllTime['200 Backstroke']['Short']['Time'];
$t16 = $arrayAllTime['200 Individual Medley']['Short']['Time'];
$t17 = $arrayAllTime['400 Individual Medley']['Short']['Time'];
$t18 = $arrayAllTime['100 Individual Medley']['Short']['Time'];
*/

pre($arrayAllTime);