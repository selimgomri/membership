<?php
$pagetitle = "Enter a Gala";
$title = "Enter a Gala";
$content = "<p class=\"lead\">Enter a gala quickly and easily, with fewer steps than before.</p>";
$content .= "<form method=\"post\" action=\"entergala-action\">
  <h2>Select Swimmer and Gala</h2>
  <div class=\"form-group row\">
    <label for=\"swimmer\" class=\"col-sm-2 col-form-label\">Select Swimmer</label>
    <div class=\"col-sm-10\">
      <select class=\"custom-select\" name=\"swimmer\"><option selected>Select</option>";
      $sql = "SELECT * FROM `members` WHERE `members`.`UserID` = '1' ORDER BY `members`.`MForename`, `members`.`MSurname` ASC;";
      $result = mysqli_query($link, $sql);
      $squadCount = mysqli_num_rows($result);
      for ($i = 0; $i < $squadCount; $i++) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $content .= "<option value=\"" . $row['MemberID'] . "\"";
        $content .= ">" . $row['MForename'] . " " . $row['MSurname'] . "</option>";
      }
      $content .= "</select>
    </div>
  </div>
  <div class=\"form-group row\">
    <label for=\"gala\" class=\"col-sm-2 col-form-label\">Select Gala</label>
    <div class=\"col-sm-10\">
      <select class=\"custom-select\" name=\"gala\"><option selected>Select</option>";
      $sql = "SELECT * FROM `galas` ORDER BY `galas`.`ClosingDate` ASC;";
      $result = mysqli_query($link, $sql);
      $squadCount = mysqli_num_rows($result);
      for ($i = 0; $i < $squadCount; $i++) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $closingDate = new DateTime($row['ClosingDate']);
        $theDate = new DateTime('now');
        $closingDate = $closingDate->format('Y-m-d');
        $theDate = $theDate->format('Y-m-d');
        if ($closingDate >= $theDate) {
          $content .= "<option value=\"" . $row['GalaID'] . "\"";
          $content .= ">" . $row['GalaName'] . "</option>";
        }
      }
      $content .= "</select>
    </div>
  </div>
  <h2>Select Swims</h2>
  <div class=\"row mb-3\">
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"50Free\" name=\"50Free\">
      <label class=\"custom-control-label\" for=\"50Free\">50 Freestyle</label>
    </div>
    </div>
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"100Free\" name=\"100Free\">
      <label class=\"custom-control-label\" for=\"100Free\">100 Freestyle</label>
    </div>
    </div>
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"200Free\" name=\"200Free\">
      <label class=\"custom-control-label\" for=\"200Free\">200 Freestyle</label>
    </div>
    </div>
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"400Free\" name=\"400Free\">
      <label class=\"custom-control-label\" for=\"400Free\">400 Freestyle</label>
    </div>
    </div>
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"800Free\" name=\"800Free\">
      <label class=\"custom-control-label\" for=\"800Free\">800 Freestyle</label>
    </div>
    </div>
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"1500Free\" name=\"1500Free\">
      <label class=\"custom-control-label\" for=\"1500Free\">1500 Freestyle</label>
    </div>
    </div>
  </div>
  <div class=\"row mb-3\">
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"50Breast\" name=\"50Breast\">
      <label class=\"custom-control-label\" for=\"50Breast\">50 Breaststroke</label>
    </div>
    </div>
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"100Breast\" name=\"100Breast\">
      <label class=\"custom-control-label\" for=\"100Breast\">100 Breaststroke</label>
    </div>
    </div>
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"200Breast\" name=\"200Breast\">
      <label class=\"custom-control-label\" for=\"200Breast\">200 Breaststroke</label>
    </div>
    </div>
  </div>
  <div class=\"row mb-3\">
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"50Fly\" name=\"50Fly\">
      <label class=\"custom-control-label\" for=\"50Fly\">50 Butterfly</label>
    </div>
    </div>
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"100Fly\" name=\"100Fly\">
      <label class=\"custom-control-label\" for=\"100Fly\">100 Butterfly</label>
    </div>
    </div>
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"200Fly\" name=\"200Fly\">
      <label class=\"custom-control-label\" for=\"200Fly\">200 Butterfly</label>
    </div>
    </div>
  </div>
  <div class=\"row mb-3\">
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"50Back\" name=\"50Back\">
      <label class=\"custom-control-label\" for=\"50Back\">50 Backstroke</label>
    </div>
    </div>
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"100Back\" name=\"100Back\">
      <label class=\"custom-control-label\" for=\"100Back\">100 Backstroke</label>
    </div>
    </div>
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"200Back\" name=\"200Back\">
      <label class=\"custom-control-label\" for=\"200Back\">200 Backstroke</label>
    </div>
    </div>
  </div>
  <div class=\"row mb-3\">
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"100IM\" name=\"100IM\">
      <label class=\"custom-control-label\" for=\"100IM\">100 IM</label>
    </div>
    </div>
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"150IM\" name=\"150IM\">
      <label class=\"custom-control-label\" for=\"150IM\">150 IM</label>
    </div>
    </div>
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"200IM\" name=\"200IM\">
      <label class=\"custom-control-label\" for=\"200IM\">200 IM</label>
    </div>
    </div>
    <div class=\"col-sm-4 col-md-2\">
    <div class=\"custom-control custom-checkbox\">
      <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"400IM\" name=\"400IM\">
      <label class=\"custom-control-label\" for=\"400IM\">400 IM</label>
    </div>
    </div>
  </div>
  <p><button type=\"submit\" class=\"btn btn-success\">Submit</button></p>
</form>";
?>
