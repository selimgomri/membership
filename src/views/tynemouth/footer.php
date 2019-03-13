<!-- THE HEPPELL FOOTER -->
<div class="cls-global-footer cls-global-footer-inverse cls-global-footer-body d-print-none bg-primary text-white mt-3">
	<?php if (isset($fluidContainer) && $fluidContainer == true) { ?>
	<div class="container-fluid">
	<?php } else { ?>
	<div class="container">
	<?php } ?>
		<div class="row">
		  <div class="col-lg-6">
		    <div class="row">
		      <div class="col-sm-6 col-lg-6">
		        <address>
		          <strong>Tynemouth A.S.C. Ltd.</strong><br>
		          Unit 2-4 Protection House, Albion Road<br>
		          North Shields<br>
		          NE30 2RH
		        </address>
		        <p><i class="fa fa-envelope fa-fw" aria-hidden="true"></i> <a href="mailto:enquiries@chesterlestreetasc.co.uk" target="new">E-Mail Us</a></p>
		        <p class="mb-0"><i class="fa fa-commenting fa-fw" aria-hidden="true"></i> <a target="new" href="mailto:websitefeedback@chesterlestreetasc.co.uk">Website Feedback</a></p>
						<p><i class="fa fa-flag fa-fw" aria-hidden="true"></i> <a href="https://account.chesterlestreetasc.co.uk/reportanissue?url=<?=urlencode(app('request')->curl)?>">Report an issue with this page</a></p>
		      </div>
		      <div class="col-sm-6 col-lg-6">
						<ul class="list-unstyled cls-global-footer-link-spacer">
							<li><strong>Membership System Support</strong></li>
	  					<li>
	              <a href="https://www.chesterlestreetasc.co.uk/policies/privacy/"
	              target="_blank" title="CLS ASC General Privacy Policy">
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
	              <a href="<?php echo autoUrl("notify"); ?>" target="_self"
	              title="About our Notify Email Service">
									Emails from us
								</a>
	            </li>
	            <li>
	              <a href="https://github.com/Chester-le-Street-ASC/Membership"
	              target="_blank" title="Membership by CLSASC on GitHub">
									Software by CLS ASC on GitHub
									</a>
	            </li>
	          </ul>
		      </div>
		    </div>
		  </div>
		  <div class="col-lg-6">
		    <div class="row">
		      <div class="col-sm-6 col-lg-6">
		        <!--<ul class="list-unstyled cls-global-footer-link-spacer">
		          <li><strong>Downloads</strong></li>
		          <li><i class="fa fa-file-pdf-o fa-fw"></i> <a title="Entry Form" target="_blank" href="http://www.chesterlestreetasc.co.uk/wp-content/uploads/2016/06/GalaEntryForm.pdf">Gala Entry Form</a></li>
		          <li><i class="fa fa-file-pdf-o fa-fw"></i> <a title="Order Form" target="_blank" href="http://www.chesterlestreetasc.co.uk/wp-content/uploads/2016/06/ClothingOrderFormChild.pdf">Children's Kit Order Form</a></li>
		          <li><i class="fa fa-file-pdf-o fa-fw"></i> <a title="Order Form" target="_blank" href="http://www.chesterlestreetasc.co.uk/wp-content/uploads/2016/06/ClothingOrderFormAdult.pdf">Adult Kit Order Form</a></li>
		        </ul>-->
		        <!--<ul class="list-unstyled cls-global-footer-link-spacer">
		          <li><strong>Social Media and More</strong></li>
		          <li><i class="fa fa-twitter fa-fw" aria-hidden="true"></i> <a title="CLSASC on Twitter" target="_blank" href="https://twitter.com/CLSASC">Twitter</a></li>
		          <li><i class="fa fa-facebook fa-fw" aria-hidden="true"></i> <a title="CLSASC on Facebook" target="_blank" href="https://www.facebook.com/Chester-le-Street-ASC-349933305154137/">Facebook</a></li>
		          <li><i class="fa fa-rss fa-fw" aria-hidden="true"></i> <a title="Stay up to date with RSS" target="_blank" href="https://www.chesterlestreetasc.co.uk/feed/">RSS Feeds</a></li>
		          <li><i class="fa fa-github fa-fw" aria-hidden="true"></i> <a title="CLSASC on GitHub - A Home for our Software Development Projects" target="_blank" href="https://github.com/Chester-le-Street-ASC/">GitHub</a></li>
		        </ul>-->
		      </div>
		      <div class="col-sm-6 col-lg-6">
		        <ul class="list-unstyled cls-global-footer-link-spacer">
		          <li><strong>Related Sites</strong></li>
		          <li><a title="British Swimming" target="_blank" href="http://www.swimming.org/britishswimming/">British Swimming</a></li>
		          <li><a title="the Amateur Swimming Association" target="_blank" href="http://www.swimming.org/swimengland/">Swim England</a></li>
							<li><a title="Swim England North East Region" target="_blank" href="http://asaner.org.uk/">Swim England North East</a></li>
							<li><a title="Northumberland and Durham Swimming" target="_blank" href="http://asaner.org.uk/northumberland-durham-swimming-association/">Northumberland &amp; Durham Swimming</a></li>
		        </ul>

		        <!--<p><strong>Committee Services</strong><br><a title="Login to G Suite" target="_blank" href="http://mail.chesterlestreetasc.co.uk/">G Suite Login</a></p>-->

		      </div>
	      </div>
			</div>
		</div>
    <div class="row">
      <div class="col source-org vcard copyright">
	      	<hr class="mt-0" style="border-color:#FFF">
			    <p class="hidden-print">
						Designed and Built by Chester&#8209;le&#8209;Street ASC Club Digital Services. Licenced to Tynemouth Amateur Swimming Club Limited.
					</p>
					<?php
					$executionEndTime = microtime();
					$creationTime = number_format((float)($executionEndTime -
					$executionStartTime), 3, '.', '');
					?>
					<!--<p class="hidden-print"><?php echo("Page produced in " . $creationTime . "
					seconds."); ?> Version v1.0, Production Build 267. <?php echo
					app('request')->browser() . " for " .
					ucwords(app('request')->platform()); ?>.</p>-->
	        <p class="mb-0" style="margin-bottom:0">&copy; <?php echo date( 'Y' ); ?>
	        <span class="org fn">Chester&#8209;le&#8209;Street ASC</span>. CLS ASC Club Digital Services is not responsible for the content of external sites.
					</p>
      	</div>
    	</div>
  	</div>
	</div> <!-- /.container -->
</div>

<!-- Modals and Other Hidden HTML -->
<script rel="preload" src="<?php echo autoUrl("js/jquery-3.2.1.slim.min.js") ?>"></script>
<script defer src="https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/js/popper.min.js"></script>
<script defer src="https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/js/bootstrap.min.js"></script>
<script async src="https://www.chesterlestreetasc.co.uk/static/global/js/clscookies.js"></script>
</body>
</html>
<?php //mysqli_close(LINK); ?>
