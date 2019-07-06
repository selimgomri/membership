<?php

?>
{
  "name": <?=json_encode(env('CLUB_NAME') . " Membership")?>,
  "short_name": "My Club Membership",
  "start_url": <?=json_encode(autoUrl(""))?>,
  "display": "standalone",
  "background_color": "#fff",
  "description": <?=json_encode("My " . env('CLUB_NAME') . " Membership")?>,
  <?php
  if (env('IS_CLS')) { 
  ?>"icons": [{
    "src": "<?=json_encode(autoUrl("public/img/touchicons/touch-icon-72x72.png"))?>",
    "sizes": "72x72",
    "type": "image/png"
  }, {
    "src": <?=json_encode(autoUrl("public/img/touchicons/touch-icon-192x192.png"))?>,
    "sizes": "192x192",
    "type": "image/png"
  }]<?php
  } else { 
  ?>"icons": [{
    "src": <?=json_encode(autoUrl("public/img/corporate/scds.png"))?>,
    "sizes": "800x800",
    "type": "image/png"
  }]<?php
  } ?>
}