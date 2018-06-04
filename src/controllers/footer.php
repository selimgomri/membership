<!-- THE HEPPELL FOOTER -->
<div class="cls-global-footer cls-global-footer-inverse cls-global-footer-body d-print-none">
	<div class="container">

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
              <a href="https://www.chesterlestreetasc.co.uk/policies/privacy/" target="_blank" title="CLS ASC General Privacy Policy">Privacy Policy</a>
            </li>
            <li>
              <a href="https://github.com/Chester-le-Street-ASC/Membership" target="_blank" title="Membership by CLSASC on GitHub">Membership Project</a>
            </li>
            <li>
              <a href="https://github.com/Chester-le-Street-ASC/Membership/blob/master/README.md" target="_blank" title="Project Readme">Readme</a>
            </li>
          </ul>
				</div>
			</div>
      <div class="row">
        <div class="col source-org vcard copyright">
        	<hr style="border-color:#FFF">
			    <p class="hidden-print">Designed and Built by Chester&#8209;le&#8209;Street ASC.</p>
					<?php
					$endTime = microtime();
					$creationTime = number_format((float)($endTime - $startTime), 4, '.', '');
					?>
					<p class="hidden-print"><?php echo("Page rendered in " . $creationTime . " seconds."); ?></p>
          <p class="mb-0" style="margin-bottom:0">&copy; <?php echo date( 'Y' ); ?> <span class="org fn">Chester&#8209;le&#8209;Street ASC</span>. CLS ASC is not responsible for the content of external sites.</p>
        </div>
	    </div>

    </div>
  </div> <!-- /.container -->
</div>

<!-- Modals and Other Hidden HTML -->
<script rel="preload" src="<?php echo autoUrl("js/jquery-3.2.1.slim.min.js") ?>"></script>
<script defer src="https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/js/popper.min.js"></script>
<script defer src="https://www.chesterlestreetasc.co.uk/wp-content/themes/chester/js/bootstrap.min.js"></script>
<script async src="https://static.chesterlestreetasc.co.uk/global/js/clscookies.js"></script>
</body>
</html>
<?php //mysqli_close(LINK); ?>
