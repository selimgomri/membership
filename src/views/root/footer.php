<?php

$time = new DateTime('now', new DateTimeZone('Europe/London'));

?>

</div>

<!-- THE HEPPELL FOOTER -->
<?php if ($this->chrome) { ?>
  <footer>
    <div class="cls-global-footer cls-global-footer-inverse cls-global-footer-body d-print-none mt-3 pb-0 focus-highlight">
      <div class="<?php if (isset($this->fluidContainer) && $this->fluidContainer == true) { ?>container-fluid<?php } else { ?>container<?php } ?>">
        <div class="row">
          <div class="col-lg-6">
            <div class="row">
              <div class="col-sm-6">
                <address>
                  <strong>Swimming Club Data Systems</strong><br>
                  Newcastle-upon-Tyne
                </address>
                <!--<p><i class="fa fa-envelope fa-fw" aria-hidden="true"></i> <a href="mailto:enquiries@chesterlestreetasc.co.uk" target="new">E-Mail Us</a></p>-->
                <p><i class="fa fa-flag fa-fw" aria-hidden="true"></i> <a href="<?= autoUrl("reportanissue?url=" . urlencode(currentUrl())) ?>">Report an issue with this page</a>
                </p>
              </div>
              <div class="col-sm-6">
                <ul class="list-unstyled cls-global-footer-link-spacer">
                  <?php if (!isset($_SESSION['SCDS-SuperUser'])) { ?>
                    <li><strong>Admin</strong></li>
                    <li>
                      <a href="<?= htmlspecialchars(autoUrl("admin")) ?>" title="Sign in to your admin account">
                        Login
                      </a>
                    </li>
                  <?php } ?>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="row">
              <div class="col-sm-6 col-lg-6">
                <ul class="list-unstyled cls-global-footer-link-spacer">
                  <li><strong>Membership System Support</strong></li>
                  <li>
                    <a href="<?= htmlspecialchars(autoUrl('help-and-support')) ?>" title="Help and Support">
                      Help and Support
                    </a>
                  </li>
                  <li>
                    <a href="https://membership.git.myswimmingclub.uk/whats-new/" target="_blank" title="New membership system features">
                      What's new?
                    </a>
                  </li>
                  <li>
                    <a href="https://github.com/Chester-le-Street-ASC/Membership" target="_blank" title="Membership by CLSASC on GitHub">
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
                  <li><a title="the Amateur Swimming Association" target="_blank" href="https://www.swimming.org/swimengland/">Swim England</a></li>
                </ul>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="cls-global-footer-legal">
      <div class="<?php if (isset($this->fluidContainer) && $this->fluidContainer == true) { ?>container-fluid<?php } else { ?>container<?php } ?>">
        <div class="row align-items-center">
          <div class="col-sm-auto">
            <a href="https://myswimmingclub.uk" target="_blank" title="Swimming Club Data Systems Website">
              <img src="<?= autoUrl("img/corporate/scds.png") ?>" width="100">
            </a>
            <div class="d-block d-sm-none mb-3"></div>
          </div>
          <div class="col">

            <?php if (defined('SOFTWARE_VERSION')) { ?>
              <p class="mb-2">
                Software version <?= mb_substr(SOFTWARE_VERSION, 0, 7); ?>.
              </p>
            <?php } ?>

            <p class="mb-0 source-org vcard copyright">
              &copy; <?= $time->format('Y') ?> <span class="org fn">Swimming Club Data Systems</span>. Swimming Club Data Systems is not responsible
              for the content of external sites.
            </p>
          </div>
        </div>
      </div>
    </div><!-- /.container -->
  </footer>
<?php } ?>

<div id="app-js-info" data-root="<?= htmlspecialchars(autoUrl("")) ?>" data-service-worker-url="<?= htmlspecialchars(autoUrl("sw.js")) ?>"></div>

<!-- Modals and Other Hidden HTML -->
<?php

$script = "";
try {
  $hash = file_get_contents(BASE_PATH . 'cachebuster.json');
  $hash = json_decode($hash);
  $hash = $hash->resourcesHash;
  $script = autoUrl('compiled/js/main.' . $hash . '.js');
} catch (Exception $e) {
  $script = autoUrl('compiled/js/main.js');
}

?>
<script rel="preload" src="<?= htmlspecialchars($script) ?>"></script>
<script async src="<?= htmlspecialchars(autoUrl("js/Cookies.js")) ?>"></script>

<?php if (isset($this->js)) { ?>
  <!-- Load per page JS -->
  <?php foreach ($this->js as $script) {
  ?><script <?php if ($script['module']) { ?>type="module"<?php } ?> src="<?= htmlspecialchars($script['url']) ?>"></script><?php
                                                          }
                                                        } ?>

</body>

</html>