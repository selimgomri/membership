<?php

$districts = json_decode(file_get_contents(BASE_PATH . 'includes/regions/regions.json'), true);
$counties = json_decode(file_get_contents(BASE_PATH . 'includes/regions/counties.json'), true);

?>

</div>

<!-- THE HEPPELL FOOTER -->
<footer>
  <?php if (!isset($_SESSION['PWA']) || !$_SESSION['PWA']) { ?>
  <div
    class="cls-global-footer cls-global-footer-inverse cls-global-footer-body d-print-none mt-3 pb-0 focus-highlight">
    <div
      class="<?php if (isset($fluidContainer) && $fluidContainer == true) { ?>container-fluid<?php } else { ?>container<?php } ?>">
      <div class="row">
        <div class="col-lg-6">
          <div class="row">
            <div class="col">
              <address>
                <?php $addr = json_decode(env('CLUB_ADDRESS')); ?>
                <strong><?=env('CLUB_NAME')?></strong><br>
                <?php
                for ($i = 0; $i < sizeof($addr); $i++) { ?>
                <?=$addr[$i]?><br>
                <?php } ?>
              </address>
              <!--<p><i class="fa fa-envelope fa-fw" aria-hidden="true"></i> <a href="mailto:enquiries@chesterlestreetasc.co.uk" target="new">E-Mail Us</a></p>-->
              <p><i class="fa fa-flag fa-fw" aria-hidden="true"></i> <a
                  href="<?=autoUrl("reportanissue?url=" . urlencode(currentUrl()))?>">Report an issue with this page</a>
              </p>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="row">
            <div class="col-sm-6 col-lg-6">
              <ul class="list-unstyled cls-global-footer-link-spacer">
                <li><strong>Membership System Support</strong></li>
                <li>
                  <a href="<?=autoUrl("privacy")?>" target="_blank"
                    title="<?=htmlspecialchars(env('CLUB_NAME'))?> Privacy Policy">
                    Our Privacy Policy
                  </a>
                </li>
                <li>
                  <a href="https://www.chesterlestreetasc.co.uk/support/onlinemembership/" target="_self"
                    title="Chester-le-Street ASC Help and Support">
                    Help and Support
                  </a>
                </li>
                <li>
                  <a href="<?php echo autoUrl("notify"); ?>" target="_self" title="About our Notify Email Service">
                    Emails from us
                  </a>
                </li>
                <li>
                  <a href="https://github.com/Chester-le-Street-ASC/Membership" target="_blank"
                    title="Membership by CLSASC on GitHub">
                    GitHub
                  </a>
                </li>
              </ul>
            </div>
            <div class="col-sm-6 col-lg-6">
              <ul class="list-unstyled cls-global-footer-link-spacer">
                <li><strong>Related Sites</strong></li>
                <li><a title="British Swimming" target="_blank" href="https://www.swimming.org/britishswimming/">British
                    Swimming</a></li>
                <li><a title="the Amateur Swimming Association" target="_blank"
                    href="https://www.swimming.org/swimengland/">Swim England</a></li>
                <li><a title="<?=htmlspecialchars($districts[env('ASA_DISTRICT')]['title'])?>" target="_blank"
                    href="<?=htmlspecialchars($districts[env('ASA_DISTRICT')]['website'])?>"><?=htmlspecialchars($districts[env('ASA_DISTRICT')]['name'])?></a></li>
                <li><a title="<?=htmlspecialchars($counties[env('ASA_COUNTY')]['title'])?>" target="_blank"
                    href="<?=htmlspecialchars($counties[env('ASA_COUNTY')]['website'])?>"><?=htmlspecialchars($counties[env('ASA_COUNTY')]['name'])?></a></li>
              </ul>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="cls-global-footer-legal">

  <?php } else { ?>

  <div class="cls-global-footer-legal mt-3">

  <?php } ?>
    <div
      class="<?php if (isset($fluidContainer) && $fluidContainer == true) { ?>container-fluid<?php } else { ?>container<?php } ?>">
      <div class="row">
        <div class="col source-org vcard copyright">
          <a href="https://corporate.myswimmingclub.co.uk" target="_blank" class="d-block mb-3">
            <img src="<?=autoUrl("public/img/corporate/scds.png")?>" width="100">
          </a>
          <?php
      global $time_start;
      $time_end = microtime(true);

      $seconds = $time_end - $time_start;
      ?>
          <p class="hidden-print">
            Designed and Built by <a class="text-white" href="https://corporate.myswimmingclub.co.uk"
              target="_blank">Swimming Club Data Systems</a>. Licenced to <?=htmlspecialchars(env('CLUB_NAME'))?>.
          </p>
          <p>Page rendered in <?=number_format($seconds, 3)?> seconds. <?php if (defined('SOFTWARE_VERSION')) { ?>Software version <?=mb_substr(SOFTWARE_VERSION, 0, 7);?>.<?php } ?>
          </p>
          <p class="mb-0">
            &copy; <span class="org fn">Chris Heppell (Swimming Club Data Systems) and Chester&#8209;le&#8209;Street ASC</span>. Swimming Club Data Systems is not responsible
            for the content of external sites.
          </p>
        </div>
      </div>
    </div>
  </div><!-- /.container -->
  </div>
</footer>

<!-- Modals and Other Hidden HTML -->
<script rel="preload" src="<?=autoUrl("public/js/jquery-3.4.1.slim.min.js")?>"></script>
<script defer src="<?=autoUrl("public/js/popper.min.js")?>"></script>
<script defer src="<?=autoUrl("public/js/bootstrap.min.js")?>"></script>
<?php if (!isset($_SESSION['PWA']) || !$_SESSION['PWA']) { ?>
<script async src="<?=autoUrl("public/js/Cookies.js")?>"></script>
<?php } ?>
</body>

</html>
<?php //mysqli_close(LINK); ?>
