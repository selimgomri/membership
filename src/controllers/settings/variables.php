<?php

$fluidContainer = true;

$db = app()->db;
$systemInfo = app()->system;

$vars = [
  'CLUB_NAME' => null,
  'CLUB_SHORT_NAME' => null,
  'ASA_CLUB_CODE' => null,
  'CLUB_EMAIL' => null,
  'CLUB_TRIAL_EMAIL' => null,
  'EMAIL_DOMAIN' => null,
  'CLUB_WEBSITE' => null,
  'SENDGRID_API_KEY' => null,
  'GOCARDLESS_USE_SANDBOX' => null,
  'GOCARDLESS_SANDBOX_ACCESS_TOKEN' => null,
  'GOCARDLESS_ACCESS_TOKEN' => null,
  'GOCARDLESS_WEBHOOK_KEY' => null,
  'CLUB_ADDRESS' => null,
  'SYSTEM_COLOUR' => null,
  'ASA_DISTRICT' => null,
  'ASA_COUNTY' => null,
  'STRIPE' => null,
  'STRIPE_PUBLISHABLE' => null,
  'STRIPE_APPLE_PAY_DOMAIN' => null,
  'HIDE_MEMBER_ATTENDANCE' => null,
  'EMERGENCY_MESSAGE' => false,
  'EMERGENCY_MESSAGE_TYPE' => 'NONE',
];

$disabled = [];

foreach ($vars as $key => $value) {
  if ($systemInfo->isExistingEnvVar($key)) {
    $vars[$key] = env($key);
    $disabled[$key] = ' disabled ';
  } else {
    $vars[$key] = $systemInfo->getSystemOption($key);
    $disabled[$key] = '';
  }
}

if ($vars['CLUB_ADDRESS'] != null) {
  $vars['CLUB_ADDRESS'] = json_decode($vars['CLUB_ADDRESS']);
}

if ($vars['SYSTEM_COLOUR'] == null) {
  $vars['SYSTEM_COLOUR'] = '#007bff';
}

$districts = json_decode(file_get_contents(BASE_PATH . 'includes/regions/regions.json'), true);
$counties = json_decode(file_get_contents(BASE_PATH . 'includes/regions/counties.json'), true);

$clubs = [];
$row = 1;
if (($handle = fopen(BASE_PATH . "includes/regions/clubs.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000)) !== false) {
    if ($row > 1) {
      $clubs += [$data[1] => [
        'Name' => $data[0],
        'Code' => $data[1],
        'District' => $data[2],
        'County' => $data[3],
        'MeetName' => $data[4],
      ]];
    }
    $row++;
  }
  fclose($handle);
}

$pagetitle = "System Variables";

include BASE_PATH . 'views/header.php';

?>

<div class="container-fluid">
  <div class="row justify-content-between">
    <aside class="col-md-3 d-none d-md-block">
      <?php
        $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/settings/SettingsLinkGroup.json'));
        echo $list->render('settings-vars');
      ?>
    </aside>
    <div class="col-md-9">
      <main>
        <h1>System Variables</h1>
        <p class="lead">Change your club name and other settings.</p>
        <p>If you cannot edit a variable, it is because it has been set as an environment variable at a server level. Contact your administrator if you need to change these.</p>
        <form method="post">

          <?php if (isset($_SESSION['PCC-SAVED']) && $_SESSION['PCC-SAVED']) { ?>
          <div class="alert alert-success">System variables saved.</div>
          <?php unset($_SESSION['PCC-SAVED']); } ?>

          <?php if (isset($_SESSION['PCC-ERROR']) && $_SESSION['PCC-ERROR']) { ?>
          <div class="alert alert-danger">Changes were not saved.</div>
          <?php unset($_SESSION['PCC-ERROR']); } ?>

          <h2 id="emergency-message">Emergency Message</h2>
          <div class="row">
            <div class="col col-lg-8">
              <p>
                To help clubs during the coronavirus (COVID-19) outbreak, we've added an emergency message function. The emergency message will be shown in a banner at the top of every page.
              </p>
              <p>
                You can see an example of this banner in the image to the right.
              </p>
            </div>
            <div class="col col-lg-4">
              <img src="<?=htmlspecialchars(autoUrl("public/img/settings/emergency-message.png"))?>" class="border" alt="Picture of membership software displaying emergency message">
            </div>
          </div>

          <div class="form-group">
            <label>Message type</label>
            <div class="custom-control custom-radio">
              <input type="radio" value="NONE" id="EMERGENCY_MESSAGE_TYPE_NONE" name="EMERGENCY_MESSAGE_TYPE" class="custom-control-input" <?php if ($vars['EMERGENCY_MESSAGE_TYPE'] == 'NONE') { ?>checked<?php } ?> <?=$disabled['EMERGENCY_MESSAGE_TYPE']?>>
              <label class="custom-control-label" for="EMERGENCY_MESSAGE_TYPE_NONE">No emergency message</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" value="SUCCESS" id="EMERGENCY_MESSAGE_TYPE_SUCCESS" name="EMERGENCY_MESSAGE_TYPE" class="custom-control-input" <?php if ($vars['EMERGENCY_MESSAGE_TYPE'] == 'SUCCESS') { ?>checked<?php } ?> <?=$disabled['EMERGENCY_MESSAGE_TYPE']?>>
              <label class="custom-control-label" for="EMERGENCY_MESSAGE_TYPE_SUCCESS">Safe/good (green)</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" value="WARN" id="EMERGENCY_MESSAGE_TYPE_WARN" name="EMERGENCY_MESSAGE_TYPE" class="custom-control-input" <?php if ($vars['EMERGENCY_MESSAGE_TYPE'] == 'WARN') { ?>checked<?php } ?> <?=$disabled['EMERGENCY_MESSAGE_TYPE']?>>
              <label class="custom-control-label" for="EMERGENCY_MESSAGE_TYPE_WARN">Warning (yellow)</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" value="DANGER" id="EMERGENCY_MESSAGE_TYPE_DANGER" name="EMERGENCY_MESSAGE_TYPE" class="custom-control-input" <?php if ($vars['EMERGENCY_MESSAGE_TYPE'] == 'DANGER') { ?>checked<?php } ?> <?=$disabled['EMERGENCY_MESSAGE_TYPE']?>>
              <label class="custom-control-label" for="EMERGENCY_MESSAGE_TYPE_DANGER">Danger (red)</label>
            </div>
          </div>

          <div class="form-group">
            <label for="EMERGENCY_MESSAGE">Emergency message</label>
            <textarea class="form-control mono" rows="7" name="EMERGENCY_MESSAGE" id="EMERGENCY_MESSAGE" <?=$disabled['EMERGENCY_MESSAGE']?>><?=htmlspecialchars($vars['EMERGENCY_MESSAGE'])?></textarea>
          </div>

          <p>
            <button type="submit" class="btn btn-success">
              Save changes
            </button>
          </p>

          <h2>SQL Database Connection Details</h2>

          <p>SQL database connection details must be defined as environment variables in your server configuration. Your database name is <span class="mono"><?=htmlspecialchars(env('DB_NAME'))?></span> and the host is <span class="mono"><?=htmlspecialchars(env('DB_HOST'))?></span>.</p>

          <h2>Name and Code</h2>

          <div class="form-group">
            <label for="CLUB_INFO">Select club</label>
            <select class="custom-select" name="CLUB_INFO" id="CLUB_INFO">
              <?php foreach ($clubs as $club) { ?>
              <option <?php if ($club['Code'] == env('ASA_CLUB_CODE')) { ?>selected<?php } ?> value="<?=htmlspecialchars($club['Code'])?>">
                <?=htmlspecialchars($club['Name'])?> (<?=htmlspecialchars($club['Code'])?>)
              </option>
              <?php } ?>
            </select>
            <small id="CLUB_ADDRESS_HELP" class="form-text text-muted">Selecting your club from this list lets us automatically fill out your clubs details including Swim England code, short name, county and district. We use your county and district to show useful links to your users. You can change your club name once you've selected it.</small>
          </div>

          <div class="form-group">
            <label for="CLUB_NAME">Club Name</label>
            <input class="form-control" type="text" name="CLUB_NAME" id="CLUB_NAME" value="<?=htmlspecialchars($vars['CLUB_NAME'])?>" <?=$disabled['CLUB_NAME']?>>
          </div>

          <div class="form-group">
            <label for="CLUB_SHORT_NAME">Club Short Name</label>
            <input class="form-control" type="text" name="CLUB_SHORT_NAME" id="CLUB_SHORT_NAME" value="<?=htmlspecialchars($vars['CLUB_SHORT_NAME'])?>" <?=$disabled['CLUB_SHORT_NAME']?>>
          </div>

          <div class="form-group">
            <label for="ASA_CLUB_CODE">Swim England Club Code</label>
            <input class="form-control mono" type="text" name="ASA_CLUB_CODE" id="CLUB_SHORT_NAME" value="<?=htmlspecialchars($vars['ASA_CLUB_CODE'])?>" <?=$disabled['ASA_CLUB_CODE']?>>
          </div>

          <div class="form-group">
            <label for="ASA_COUNTY">Swim England County</label>
            <input class="form-control" type="url" name="ASA_COUNTY" id="ASA_COUNTY" value="<?=htmlspecialchars($counties[$vars['ASA_COUNTY']]['name'])?>" disabled>
          </div>

          <div class="form-group">
            <label for="ASA_DISTRICT">Swim England District</label>
            <input class="form-control" type="url" name="ASA_DISTRICT" id="ASA_DISTRICT" value="<?=htmlspecialchars($districts[$vars['ASA_DISTRICT']]['name'])?>" disabled>
          </div>

          <div class="form-group">
            <label for="CLUB_WEBSITE">Club Website</label>
            <input class="form-control mono" type="url" name="CLUB_WEBSITE" id="CLUB_WEBSITE" value="<?=htmlspecialchars($vars['CLUB_WEBSITE'])?>" <?=$disabled['CLUB_WEBSITE']?>>
          </div>

          <div class="form-group">
            <label for="CLUB_ADDRESS">Club Primary Address</label>
            <textarea class="form-control" rows="5" id="CLUB_ADDRESS" name="CLUB_ADDRESS" aria-describedby="CLUB_ADDRESS_HELP" <?=$disabled['CLUB_ADDRESS']?>><?php
            for ($i = 0; $i < sizeof($vars['CLUB_ADDRESS']); $i++) {
              echo htmlspecialchars($vars['CLUB_ADDRESS'][$i]);
              if ($i+1 < sizeof($vars['CLUB_ADDRESS'])) {
                echo "\r\n";
              }
            }
            ?></textarea>
            <small id="CLUB_ADDRESS_HELP" class="form-text text-muted">Enter the address of your primary location. Do not include your club name and do not place commas at the end of lines.</small>
          </div>

          <h2>Email Options</h2>

          <div class="form-group">
            <label for="CLUB_EMAIL">Main Club Email Address</label>
            <input class="form-control" type="email" name="CLUB_EMAIL" id="CLUB_EMAIL" value="<?=htmlspecialchars($vars['CLUB_EMAIL'])?>" <?=$disabled['CLUB_EMAIL']?>>
          </div>

          <div class="form-group">
            <label for="CLUB_TRIAL_EMAIL">Trial Request Email Address</label>
            <input class="form-control" type="email" name="CLUB_TRIAL_EMAIL" id="CLUB_TRIAL_EMAIL" value="<?=htmlspecialchars($vars['CLUB_TRIAL_EMAIL'])?>" <?=$disabled['CLUB_TRIAL_EMAIL']?>>
          </div>

          <div class="form-group">
            <label for="EMAIL_DOMAIN">Email Domain</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">@</span>
              </div>
              <input class="form-control" type="text" name="EMAIL_DOMAIN" id="EMAIL_DOMAIN" value="<?=htmlspecialchars($vars['EMAIL_DOMAIN'])?>" <?=$disabled['EMAIL_DOMAIN']?> aria-describedby="EMAIL_DOMAIN_HELP">
            </div>
            <small id="EMAIL_DOMAIN_HELP" class="form-text text-muted">Your email domain must be listed in your Twilio SendGrid account.</small>
          </div>

          <?php if (!env('SENDGRID_API_KEY')) { ?>
          <div class="form-group">
            <label for="EMAIL_DOMAIN">Twilio SendGrid API Key</label>
            <input class="form-control mono" type="text" name="SENDGRID_API_KEY" id="SENDGRID_API_KEY" value="<?=htmlspecialchars($vars['SENDGRID_API_KEY'])?>" <?=$disabled['SENDGRID_API_KEY']?>> 
          </div>
          <?php } else { ?>
          <p>Your <a href="https://sendgrid.com/">Twilio SendGrid API key</a> is set at system level and cannot be viewed or modified here.</p>
          <?php } ?>

          <h2>Display options</h2>
          <div class="form-group">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input" id="HIDE_MEMBER_ATTENDANCE" name="HIDE_MEMBER_ATTENDANCE" <?php if (!bool($vars['HIDE_MEMBER_ATTENDANCE'])) { ?>checked<?php } ?> <?=$disabled['HIDE_MEMBER_ATTENDANCE']?>>
              <label class="custom-control-label" for="HIDE_MEMBER_ATTENDANCE">Show member attendance percentage to parents</label>
            </div>
          </div>

          <h2>GoCardless API keys (for direct debit)</h2>

          <div class="form-group">
            <label>GoCardless Mode</label>
            <div class="custom-control custom-radio">
              <input type="radio" value="1" id="GOCARDLESS_USE_SANDBOX_TRUE" name="GOCARDLESS_USE_SANDBOX" class="custom-control-input" <?php if (bool($vars['GOCARDLESS_USE_SANDBOX'])) { ?>checked<?php } ?> <?=$disabled['GOCARDLESS_USE_SANDBOX']?>>
              <label class="custom-control-label" for="GOCARDLESS_USE_SANDBOX_TRUE">Use sandbox (development/testing) mode</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" value="0" id="GOCARDLESS_USE_SANDBOX_FALSE" name="GOCARDLESS_USE_SANDBOX" class="custom-control-input" <?php if (!bool($vars['GOCARDLESS_USE_SANDBOX'])) { ?>checked<?php } ?> <?=$disabled['GOCARDLESS_USE_SANDBOX']?>>
              <label class="custom-control-label" for="GOCARDLESS_USE_SANDBOX_FALSE">Use live (production) mode</label>
            </div>
          </div>

          <div class="form-group">
            <label for="GOCARDLESS_SANDBOX_ACCESS_TOKEN">GoCardless Sandbox (dev) Access Token</label>
            <input class="form-control mono" type="text" name="GOCARDLESS_SANDBOX_ACCESS_TOKEN" id="GOCARDLESS_SANDBOX_ACCESS_TOKEN" value="<?=htmlspecialchars($vars['GOCARDLESS_SANDBOX_ACCESS_TOKEN'])?>" <?=$disabled['GOCARDLESS_SANDBOX_ACCESS_TOKEN']?>> 
          </div>

          <div class="form-group">
            <label for="GOCARDLESS_ACCESS_TOKEN">GoCardless Live Access Token</label>
            <input class="form-control mono" type="text" name="GOCARDLESS_ACCESS_TOKEN" id="GOCARDLESS_ACCESS_TOKEN" value="<?=htmlspecialchars($vars['GOCARDLESS_ACCESS_TOKEN'])?>" <?=$disabled['GOCARDLESS_ACCESS_TOKEN']?>> 
          </div>

          <div class="form-group">
            <label for="GOCARDLESS_WEBHOOK_KEY">GoCardless Webhook Key</label>
            <input class="form-control mono" type="text" name="GOCARDLESS_WEBHOOK_KEY" id="GOCARDLESS_WEBHOOK_KEY" value="<?=htmlspecialchars($vars['GOCARDLESS_WEBHOOK_KEY'])?>" <?=$disabled['GOCARDLESS_WEBHOOK_KEY']?>> 
          </div>

          <p>Your GoCardless webhook endpoint URL is</p>
          <div class="form-group">
            <input class="form-control mono" type="text" readonly value="<?=autoUrl("payments/webhooks")?>"> 
          </div>

          <h2>Stripe API keys (for card payments)</h2>

          <p>Warning: Please do not mix up your publishable and private keys. Mistakenly publishing your private key might give a bad actor full access to your Stripe account.</p>
          
          <div class="form-group">
            <label for="STRIPE">Stripe Private Key</label>
            <input class="form-control mono" type="text" name="STRIPE" id="STRIPE" value="<?=htmlspecialchars($vars['STRIPE'])?>" <?=$disabled['STRIPE']?>> 
          </div>

          <div class="form-group">
            <label for="STRIPE_PUBLISHABLE">Stripe Publishable Key</label>
            <input class="form-control mono" type="text" name="STRIPE_PUBLISHABLE" id="STRIPE_PUBLISHABLE" value="<?=htmlspecialchars($vars['STRIPE_PUBLISHABLE'])?>" <?=$disabled['STRIPE_PUBLISHABLE']?>> 
          </div>

          <div class="form-group">
            <label for="STRIPE_APPLE_PAY_DOMAIN">Stripe Apple Pay Domain</label>
            <input class="form-control mono" type="text" name="STRIPE_APPLE_PAY_DOMAIN" id="STRIPE_APPLE_PAY_DOMAIN" value="<?=htmlspecialchars($vars['STRIPE_APPLE_PAY_DOMAIN'])?>" <?=$disabled['STRIPE_APPLE_PAY_DOMAIN']?>> 
          </div>

          <div class="form-group">
            <label for="STRIPE_WEBHOOK_URL">Your Stripe webhook endpoint URL is</label>
            <input class="form-control mono" type="text" name="STRIPE_WEBHOOK_URL" id="STRIPE_WEBHOOK_URL" readonly value="<?=autoUrl("payments/stripe/webhooks")?>" aria-describedby="STRIPE_WEBHOOK_URL_HELP"> 
            <small id="STRIPE_WEBHOOK_URL_HELP" class="form-text text-muted">
              <p>We plan to automatically set up webhooks for you in future but for now you must do so manually.</p>
            </small>

            <p>You must tell Stripe that the webhook supports the following event types;</p>

            <ul>
              <li>payout.paid</li>
              <li>payout.updated</li>
              <li>payout.failed</li>
              <li>payout.created</li>
              <li>payout.canceled</li>
              <li>payment_method.detached</li>
              <li>payment_intent.succeeded</li>
              <li>payment_intent.created</li>
              <li>payment_method.updated</li>
              <li class="text-truncate">payment_method.card_automatically_updated</li>
            </ul>
              
              
          </div>

          <h2>Customisation</h2>

          <?php if (!bool(env('IS_CLS'))) { ?>
          <div class="form-group">
            <label for="SYSTEM_COLOUR">Primary Colour</label>
            <input class="form-control" type="color" name="SYSTEM_COLOUR" id="SYSTEM_COLOUR" value="<?=htmlspecialchars($vars['SYSTEM_COLOUR'])?>" <?=$disabled['SYSTEM_COLOUR']?> aria-describedby="SYSTEM_COLOUR_HELP">
            <small id="SYSTEM_COLOUR_HELP" class="form-text text-muted">The system primary colour is used in PDF documents generated by the membership system.</small>
          </div>
          <?php } else { ?>
          <p>There are currently no customisation options available for your version of the membership system.</p>
          <?php } ?>

          <p>
            <button class="btn btn-success" type="submit">
              Save
            </button>
          </p>
        </form>
      </main>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->useFluidContainer();
$footer->render();