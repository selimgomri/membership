<!-- THE HEPPELL FOOTER -->
<div class="cls-global-footer cls-global-footer-sponsors d-print-none" style="margin:0">
	<?php if (isset($fluidContainer) && $fluidContainer == true) { ?>
	<div class="container-fluid">
	<?php } else { ?>
	<div class="container">
	<?php } ?>
		<div class="row align-items-center">
			<div class="col-xs-12 col-sm-6 col-md-4 col-lg">
				<a href="https://www.chesterlestreetasc.co.uk/servicestoclubs/" target="_blank">
					<img class="img-responsive sponsor center-block" src="<? echo autoUrl("img/S2C.png"); ?>"
					srcset="<? echo autoUrl("img/S2C@2x.png"); ?> 2x, <? echo
					autoUrl("img/S2C@3x.png"); ?> 3x" alt="Services to Clubs Logo">
				</a>
			</div>
		</div>
	</div>
</div>
<div class="cls-global-footer cls-global-footer-inverse cls-global-footer-body d-print-none">
	<?php if (isset($fluidContainer) && $fluidContainer == true) { ?>
	<div class="container-fluid">
	<?php } else { ?>
	<div class="container">
	<?php } ?>

    <div class="hidden-print">
			<div class="row">
				<div class="col-sm-6 col-md-3">
			    <address>
			    	<strong>Chester-le-Street ASC</strong><br>
			      Burns Green Leisure Centre<br>
			      Chester-le-Street<br>
			      DH3 3QH
			    </address>
				</div>
				<div class="col-sm-6 col-md-3">
          <ul class="list-unstyled">
  					<li>
              <a href="https://www.chesterlestreetasc.co.uk/policies/privacy/"
              target="_blank" title="CLS ASC General Privacy Policy">Privacy
              Policy</a>
            </li>
						<li>
              <a href="<? echo autoUrl("notify"); ?>" target="_self"
              title="About our Notify Email Service">Notify Help</a>
            </li>
            <li>
              <a href="https://github.com/Chester-le-Street-ASC/Membership"
              target="_blank" title="Membership by CLSASC on GitHub">GitHub</a>
            </li>
          </ul>
				</div>
			</div>
      <div class="row">
        <div class="col source-org vcard copyright">
        	<hr style="border-color:#FFF">
			    <p class="hidden-print">
						Designed and Built by Chester&#8209;le&#8209;Street ASC.
					</p>
					<?php
					$executionEndTime = microtime();
					$creationTime = number_format((float)($executionEndTime -
					$executionStartTime), 3, '.', '');
					?>
					<!--<p class="hidden-print"><?php echo("Page produced in " . $creationTime . "
					seconds."); ?> Version v1.0, Production Build 267. <? echo
					app('request')->browser() . " for " .
					ucwords(app('request')->platform()); ?>.</p>-->
          <p class="mb-0" style="margin-bottom:0">&copy; <?php echo date( 'Y' ); ?>
          <span class="org fn">Chester&#8209;le&#8209;Street ASC</span>.
          Provided to CLS ASC by CLS ASC Services to Clubs</p>
        </div>
	    </div>

    </div>
  </div> <!-- /.container -->
</div>

<div class="modal fade" id="paymentsBetaModal" tabindex="-1" role="dialog" aria-labelledby="paymentsBetaModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentsBetaModalTitle">Payments <span class="badge badge-secondary">BETA</span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Payments by Direct Debit are currently being developed. Please only proceed if you are taking part in any trials.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        <a href="<? echo autoUrl("payments"); ?>" class="btn btn-link">Go to payments</a>
      </div>
    </div>
  </div>
</div>

<!-- Modals and Other Hidden HTML -->
<script rel="preload" src="<?php echo autoUrl("js/jquery-3.2.1.slim.min.js") ?>"></script>
<script defer src="https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/js/popper.min.js"></script>
<script defer src="https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/js/bootstrap.min.js"></script>
<script async src="https://static.chesterlestreetasc.co.uk/global/js/clscookies.js"></script>
</body>
</html>
<?php //mysqli_close(LINK); ?>
