<? if (!$renewal_trap) {
$access = $_SESSION['AccessLevel']; ?>

<!--<div class="bg-warning box-shadow mb-3 py-2" style="margin-top:-1rem;">
  <div class="<?=$container_class?>">
    <nav class="nav nav-underline">
      <strong>
        Remember to cancel your Standing Order for Monthly Fees
      </strong>
    </nav>
  </div>
</div>-->

<? } else {
  include 'renewalTitleBar.php';
}
