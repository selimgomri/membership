<?php
$id = mysqli_real_escape_string($link, $id);
$sql = "SELECT * FROM `galas` WHERE `GalaID` = '$id';";
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$course = ['LONG', 'SHORT', 'IRREGULAR'];
$courseStrings = ['Long Course', 'Short Course', 'Other Pool Length or Open Water'];

if ($count == 1) {
  $pagetitle = $row['GalaName'];
  $title = $row['GalaName'];
  $content .= "<h2>Gala Details</h2>";
  $content .= "<form method=\"post\">
  <div class=\"form-group row\">
    <label for=\"galaname\" class=\"col-sm-4 col-form-label\">Gala Name</label>
    <div class=\"col-sm-8\">
      <input type=\"text\" class=\"form-control\" id=\"galaname\" name=\"galaname\" placeholder=\"eg Chester-le-Street Open\" value=\"" . $row['GalaName'] . "\" required>
    </div>
  </div>
  <div class=\"form-group row\">
    <label for=\"length\" class=\"col-sm-4 col-form-label\">Course Length</label>
    <div class=\"col-sm-8\">
      <select class=\"custom-select\" name=\"length\" id=\"length\" required>";
      for ($i=0; $i<sizeof($course); $i++) {
        if ($course[$i] == $row['CourseLength']) {
          $content .= "<option selected value=\"" . $course[$i] . "\">" . $courseStrings[$i] . "</option>";
        }
        else {
          $content .= "<option value=\"" . $course[$i] . "\">" . $courseStrings[$i] . "</option>";
        }
      }
      $content .= "</select>
    </div>
  </div>
  <div class=\"form-group row\">
    <label for=\"venue\" class=\"col-sm-4 col-form-label\">Gala Venue</label>
    <div class=\"col-sm-8\">
      <input type=\"text\" class=\"form-control\" id=\"venue\" name=\"venue\" value=\"" . $row['GalaVenue'] . "\" placeholder=\"eg Chester-le-Street\" required>
    </div>
  </div>
  <div class=\"form-group row\">
    <label for=\"closingDate\" class=\"col-sm-4 col-form-label\">Closing Date</label>
    <div class=\"col-sm-8\">
      <input type=\"date\" class=\"form-control\" id=\"closingDate\" name=\"closingDate\" placeholder=\"YYYY-MM-DD\" pattern=\"[0-9]{4}-[0-9]{2}-[0-9]{2}\" value=\"" . $row['ClosingDate'] . "\" required>
    </div>
  </div>
  <div class=\"form-group row\">
    <label for=\"lastDate\" class=\"col-sm-4 col-form-label\">Last Day of Gala</label>
    <div class=\"col-sm-8\">
      <input type=\"date\" class=\"form-control\" id=\"galaDate\" name=\"galaDate\" placeholder=\"YYYY-MM-DD\" pattern=\"[0-9]{4}-[0-9]{2}-[0-9]{2}\" value=\"" . $row['GalaDate'] . "\" required>
    </div>
  </div>";
  if ($row['GalaFeeConstant'] == 1) {
    $content .= "
    <div class=\"form-group row\">
      <label for=\"GalaFeeConstant\" class=\"col-sm-4 col-form-label\">Gala Fee Constant?</label>
      <div class=\"col-sm-8\">
        <div class=\"custom-control custom-checkbox mt-2\">
          <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" checked id=\"GalaFeeConstant\" name=\"GalaFeeConstant\">
          <label class=\"custom-control-label\" for=\"GalaFeeConstant\">Tick if all swims are the same price</label>
        </div>
      </div>
    </div>";
  }
  else {
    $content .= "
    <div class=\"form-group row\">
      <label for=\"GalaFeeConstant\" class=\"col-sm-4 col-form-label\">Gala Fee Constant?</label>
      <div class=\"col-sm-8\">
        <div class=\"custom-control custom-checkbox mt-2\">
          <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"GalaFeeConstant\" name=\"GalaFeeConstant\">
          <label class=\"custom-control-label\" for=\"GalaFeeConstant\">Tick if all swims are the same price</label>
        </div>
      </div>
    </div>";
  }
  $content .= "
  <div class=\"form-group row\">
    <label for=\"galaFee\" class=\"col-sm-4 col-form-label\">Gala Fee</label>
    <div class=\"col-sm-8\">
      <div class=\"input-group\">
        <div class=\"input-group-prepend\">
          <span class=\"input-group-text\">&pound;</span>
        </div>
        <input type=\"text\" class=\"form-control\" id=\"galaFee\" name=\"galaFee\" aria-describedby=\"galaFeeHelp\" placeholder=\"eg 5.00\" value=\"" . $row['GalaFee'] . "\">
      </div>
      <small id=\"galaFeeHelp\" class=\"form-text text-muted\">If all swims at the gala are the same price, enter it here.</small>
    </div>
  </div>";
  if ($row['HyTek'] == 1) {
    $content .= "
    <div class=\"form-group row\">
      <label for=\"HyTek\" class=\"col-sm-4 col-form-label\">Require times?</label>
      <div class=\"col-sm-8\">
        <div class=\"custom-control custom-checkbox mt-2\">
          <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" checked id=\"HyTek\" name=\"HyTek\">
          <label class=\"custom-control-label\" for=\"HyTek\">Tick if this is a HyTek gala or needs times from parents</label>
        </div>
      </div>
    </div>";
  }
  else {
    $content .= "
    <div class=\"form-group row\">
      <label for=\"HyTek\" class=\"col-sm-4 col-form-label\">Require times?</label>
      <div class=\"col-sm-8\">
        <div class=\"custom-control custom-checkbox mt-2\">
          <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"HyTek\" name=\"HyTek\">
          <label class=\"custom-control-label\" for=\"HyTek\">Tick if this is a HyTek gala or needs times from parents</label>
        </div>
      </div>
    </div>";
  }
  $content .= "<input type=\"hidden\" value=\"" . $id . "\" name=\"galaID\"><p class=\"mb-0\"><button class=\"btn btn-success\" type=\"submit\" id=\"submit\">Save Changes</button></p>
  ";
}
else {
  halt(404);
}
include BASE_PATH . "views/header.php";
include "galaMenu.php"; ?>
<div class="container">
  <h1><?php echo $title; ?></h1>
  <p class="lead">
    <?=htmlspecialchars($row['GalaVenue'])?>
  </p>
  <div class="row">
    <div class="col-md-6">
      <div class="cell">
        <?php if (isset($_SESSION['ErrorState'])) {
          echo $_SESSION['ErrorState'];
          unset($_SESSION['ErrorState']);
        } ?>
        <?php echo $content; ?>
      </div>
    </div>
    <div class="col-md-6">
      <div class="cell">
        <h2>Gala Statistics</h2>
		    <div class="chart" id="piechart"></div>
				<div class="chart" id="barchart"></div>
        <p class="mb-0">
          <a href="<?php echo autoUrl("galas/entries?galaID=" . $id . "&sex=all&search="); ?>" class="btn btn-dark">
            View All Entries
          </a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php
/* Stats Section */
$swimsCountArray = [];
$strokesCountArray = [0, 0, 0, 0, 0];
$strokesCountTextArray = ["Freestyle", "Breaststroke", "Butterfly", "Backstroke", "Individual Medley"];
$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
$strokesArray = ['0','0','0','0','0','0','1','1','1','2','2','2','3','3','3','4','4','4','4',];
$swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
$counter = 0;
for ($i=0; $i<sizeof($swimsArray); $i++) {
  $col = $swimsArray[$i];
  $sql = "SELECT `$col` FROM `galaEntries` WHERE `$col` = '1' AND `GalaID` = '$id';";
  $result = mysqli_query($link, $sql);
  $count = mysqli_num_rows($result);
  $swimsCountArray[$i] = $count;
  $strokesCountArray[$strokesArray[$i]] += $count;
  $counter += $count;
}
if ($counter>0) { ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});

  google.charts.setOnLoadCallback(drawPieChart);
  google.charts.setOnLoadCallback(drawBarChart);

  function drawPieChart() {

    var data = google.visualization.arrayToDataTable([
      ['Stroke', 'Total Number of Entries'],
      <?php for ($i=0; $i<sizeof($strokesCountArray); $i++) { ?>
        ['<?php echo $strokesCountTextArray[$i]; ?>', <?php echo $strokesCountArray[$i]; ?>],
      <?php } ?>
    ]);

    var options = {
      title: 'Gala Entries by Stroke',
      fontName: 'Open Sans',
      backgroundColor: {
        fill:'transparent'
      },
      chartArea: {
        left: '0',
        right: '0',
      }
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));

    chart.draw(data, options);
  }
  function drawBarChart() {

    var data = google.visualization.arrayToDataTable([
      ['Stroke', 'Total Number of Entries'],
      <?php for ($i=0; $i<sizeof($swimsArray); $i++) {
        if ($swimsCountArray[$i] > 0) { ?>
          ['<?php echo $swimsTextArray[$i]; ?>', <?php echo $swimsCountArray[$i]; ?>],
        <?php }
      } ?>
    ]);

    var options = {
      title: 'Gala Entries by Event',
      fontName: 'Open Sans',
      backgroundColor: {
        fill:'transparent'
      },
      chartArea: {
        left: '0',
        right: '0',
      },
      backgroundColor: {
        fill:'transparent'
      },
      legend: {
        position: 'none',
      }
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('barchart'));

    chart.draw(data, options);
  }
</script>
<?php } ?>
<?php include BASE_PATH . "views/footer.php";
?>
