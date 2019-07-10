<?php

$fluidContainer = true;

global $db;
global $systemInfo;

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

          <h2>SQL Database Connection Details</h2>

          <p>SQL database connection details must be defined as environment variables in your server configuration. Your database name is <span class="mono"><?=htmlspecialchars(env('DB_NAME'))?></span> and the host is <span class="mono"><?=htmlspecialchars(env('DB_HOST'))?></span>.</p>

          <h2>Name and Code</h2>

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
            <label for="CLUB_WEBSITE">Club Website</label>
            <input class="form-control mono" type="url" name="CLUB_WEBSITE" id="CLUB_WEBSITE" value="<?=htmlspecialchars($vars['CLUB_WEBSITE'])?>" <?=$disabled['CLUB_WEBSITE']?>>
          </div>

          <div class="form-group">
            <label for="CLUB_ADDRESS">Club Primary Addess</label>
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
            <input class="form-control mono" type="text" name="GOCARDLESS_SANDBOX_ACCESS_TOKEN" id="EMAIL_DOMAIN" value="<?=htmlspecialchars($vars['GOCARDLESS_SANDBOX_ACCESS_TOKEN'])?>" <?=$disabled['GOCARDLESS_SANDBOX_ACCESS_TOKEN']?>> 
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

          <p>
            <button class="btn btn-success" type="submit">
              Save
            </button>
          </p>
        </form>

        <?php if ($parentCode != null) { ?>
        <div>
          <h2>View the code of conduct</h2>
          <?=$Extra->text($codeOfConduct)?>
        </div>
        <?php } ?>
      </main>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';