<?php

global $db;

$query = $db->prepare("SELECT MemberID, MForename, MSurname, SquadName, members.UserID FROM members INNER JOIN squads ON members.SquadID = squads.SquadID WHERE MemberID = ?");
$query->execute([$id]);
$swimmer = $query->fetch(PDO::FETCH_ASSOC);

if ($_SESSION['AccessLevel'] == "Parent" && $swimmer['UserID'] != $_SESSION['UserID']) {
  halt(404);
}

include BASE_PATH . 'views/head.php';

?>

<style>
body {
  color: #000;
  background: #fff;
  margin: 1cm;
}
html {
  background: #fff;
}
</style>

<body>
  <div style="height:5.398cm;width:8.560cm;background:#eee;position:fixed;border:0.025cm solid #000">
    <img src="<?=autoUrl("img/chesterLogo.svg")?>" style="position:fixed;width:6cm;margin:0.25cm 0 0 0.25cm;">
    <p style="position:fixed;font-size:5pt;margin:0.93cm 0 0 1.33cm">A Swim England Club</p>

    <p style="position:fixed;font-size:12pt;margin:1.5cm 0 0 0.25cm;max-width:8.060cm;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
      <strong><?=$swimmer['MForename'] . " " . $swimmer['MSurname']?></strong>
    </p>
    <p style="position:fixed;font-size:12pt;margin:2.1cm 0 0 0.25cm;max-width:8.060cm;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?=$swimmer['SquadName']?> Squad</p>

    <p style="position:fixed;font-size:10pt;margin:2.9cm 0 0 0.25cm;max-width:5.76cm;white-space:normal;overflow:hidden;text-overflow:ellipsis;"><strong>CLS Coaches/Volunteers</strong><br>Scan QR Code in emergency for Medical Form and Contact Details</p>

    <p style="position:fixed;font-size:0.5cm;margin:0.25cm 0 0 7.81cm;max-width:5.76cm;white-space:normal;overflow:hidden;text-overflow:ellipsis;writing-mode:vertical-rl;text-orientation:mixed;line-height:1"><span class="mono">CLSX<?=$swimmer['MemberID']?></span></p>

    <p style="position:fixed;font-size:9pt;margin:4.83cm 0 0 0.25cm;max-width:5.76cm;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:1">Issued <?=date("d/m/Y")?></p>

    <img src="<?=autoUrl("services/qr-generator?size=300&text=" . urlencode(autoUrl("swimmers/" . $swimmer['MemberID'])))?>" style="position:fixed;height:2.3cm;width:auto;margin:2.848cm 0 0 6.01cm">
  </div>
</body>

<script>
window.print();
</script>

</html>
