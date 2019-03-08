<?

/**
 * Cron Job to scrape times from the ASA
 */

ignore_user_abort(true);
set_time_limit(0);

global $db;

// Create pdo objects for insert and update

$sql = "INSERT INTO `times` (`MemberID`, `LastUpdate`,  `Type`,
`50Free`, `100Free`, `200Free`, `400Free`, `800Free`, `1500Free`,
`50Breast`, `100Breast`, `200Breast`, `50Fly`, `100Fly`, `200Fly`,
`50Back`, `100Back`, `200Back`, `100IM`, `200IM`, `400IM`) VALUES
(:user, :lastdate, :type, :t1, :t2, :t3, :t4, :t5, :t6, :t7, :t8, :t9,
:t10, :t11, :t12, :t13, :t14, :t15, :t18, :t16, :t17)";
$insert_time = $db->prepare($sql);

$sql = "UPDATE `times` SET `LastUpdate` = :lastdate, `50Free` = :t1,
`100Free` = :t2, `200Free` = :t3, `400Free` = :t4, `800Free` =
:t5, `1500Free` = :t6, `50Breast` = :t7, `100Breast` = :t8,
`200Breast` = :t9, `50Fly` = :t10, `100Fly` = :t11, `200Fly` =
:t12, `50Back` = :t13, `100Back` = :t14, `200Back` = :t15,
`200IM` = :t16, `400IM` = :t17, `100IM` = :t18 WHERE `MemberID` =
:user AND `Type` = :type";
$update_time = $db->prepare($sql);

use Symfony\Component\DomCrawler\Crawler;

// Get some members and ASA numbers

$sql = "SELECT DISTINCT `ASANumber`, `members`.`MemberID` FROM `members` LEFT JOIN `times` ON `members`.`MemberID` = `times`.`MemberID` WHERE `LastUpdate` IS NULL OR `LastUpdate` < CURDATE() LIMIT 4;";
$result = $db->query($sql);

$row = $result->fetchAll(PDO::FETCH_ASSOC);

//pre($row);

$count_to_get = sizeof($row);

$date = date("Y-m-d");
$type = null;
$array12 = null;

// For each get their times (from two sources)

for ($i = 0; $i < $count_to_get; $i++) {
	$user = $row[$i]['MemberID'];
	//echo "I = $i, ";

	if (!strpos($row[$i]['ASANumber'], 'CLSX') && $row[$i]['ASANumber'] != "") {
		$curlres =
		curl('https://www.swimmingresults.org/individualbest/personal_best.php?print=1&mode=L&tiref=' . $row[$i]['ASANumber']);
		$array = getTimes($row[$i]['ASANumber']);
		$array12 = null;
		for ($y = 0; $y < 4; $y++) {
			//echo "Y = $y, ";
			if ($y == 0) {
				$type = "CY_SC";
			} else if ($y == 1) {
				$type = "SCPB";
			} else if ($y == 2) {
			 	$type = "CY_LC";
		 	} else if ($y == 3) {
				$type = "LCPB";
			}

			$t1 = null;
			$t2 = null;
			$t3 = null;
			$t4 = null;
			$t5 = null;
			$t6 = null;
			$t7 = null;
			$t8 = null;
			$t9 = null;
			$t10 = null;
			$t11 = null;
			$t12 = null;
			$t13 = null;
			$t14 = null;
			$t15 = null;
			$t16 = null;
			$t17 = null;
			$t18 = null;

			if ($y == 2) {
				// Get the last 12 months

		    // Long Course

		    $start = '<p class="rnk_sj">Long Course</p><table id="rankTable"><tbody>';
		    $end = '</tbody></table>';

		    $output = curl_scrape_between($curlres, $start, $end);
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

			} else if ($y == 0) {

		    $start = '<p class="rnk_sj">Short Course</p><table id="rankTable"><tbody>';
		    $end = '</tbody></table>';

		    $output = curl_scrape_between($curlres, $start, $end);
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

			} else {
				$t1 = $array[$type][1];
				$t2 = $array[$type][2];
				$t3 = $array[$type][3];
				$t4 = $array[$type][4];
				$t5 = $array[$type][5];
				$t6 = $array[$type][6];
				$t7 = $array[$type][7];
				$t8 = $array[$type][8];
				$t9 = $array[$type][9];
				$t10 = $array[$type][10];
				$t11 = $array[$type][11];
				$t12 = $array[$type][12];
				$t13 = $array[$type][13];
				$t14 = $array[$type][14];
				$t15 = $array[$type][15];
				$t16 = $array[$type][16];
				$t17 = $array[$type][17];
				$t18 = $array[$type][18];
			}

			$sql = "SELECT COUNT(*) FROM `times` WHERE `MemberID` = ? AND `Type` = ?";
			$num_query = $db->prepare($sql);
			$num_query->execute([$user, $type]);
			$num = $num_query->fetchColumn();

			$sql = "";

			$values = null;

			$values = [
				'user'		=> $user,
				'lastdate'=> $date,
				'type'		=> $type,
				't1'			=> $t1,
				't2'			=> $t2,
				't3'			=> $t3,
				't4'			=> $t4,
				't5'			=> $t5,
				't6'			=> $t6,
				't7'			=> $t7,
				't8'			=> $t8,
				't9'			=> $t9,
				't10'			=> $t10,
				't11'			=> $t11,
				't12'			=> $t12,
				't13'			=> $t13,
				't14'			=> $t14,
				't15'			=> $t15,
				't16'			=> $t16,
				't17'			=> $t17,
				't18'			=> $t18,
			];

			try {
				if ($num == 0) {
          try {
					  $insert_time->execute($values);
          } catch (Exception $e) {
            halt(500);
          }
				} else {
          try {
					  $update_time->execute($values);
          } catch (Exception $e) {
            halt(500);
          }
				}
			} catch (Exception $e) {
				//halt(500);
			}
		}
	}
}

echo "Success";

?>
