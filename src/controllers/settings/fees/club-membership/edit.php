<?php

use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

$db = app()->db;
$tenant = app()->tenant;

$getClass = $db->prepare("SELECT `ID`, `Name`, `Description`, `Fees` FROM `clubMembershipClasses` WHERE `ID` = ? AND `Tenant` = ?");
$getClass->execute([
  $id,
  $tenant->getId(),
]);
$class = $getClass->fetch(PDO::FETCH_ASSOC);

if (!$class) {
  halt(404);
}

$json = json_decode($class['Fees']);

$fees = [];
foreach ($json->fees as $value) {
  $fees[] = (string) (\Brick\Math\BigDecimal::of((string) $value))->withPointMovedLeft(2)->toScale(2);
}

$fluidContainer = true;

$pagetitle = "Club Membership Fee Options (V2)";

include BASE_PATH . 'views/header.php';

?>

<div class="container-fluid">
  <div class="row justify-content-between">
    <aside class="col-md-3 d-none d-md-block">
      <?php
      $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/settings/SettingsLinkGroup.json'));
      echo $list->render('settings-fees');
      ?>
    </aside>
    <div class="col-md-9">
      <main>
        <h1><?= htmlspecialchars($class['Name']) ?></h1>
        <p class="lead">Set amounts for club membership fees</p>

        <form method="post" class="needs-validation" novalidate>

          <div class="form-group">
            <label for="class-name">Class Name</label>
            <input type="text" name="class-name" id="class-name" class="form-control" required value="<?= htmlspecialchars($class['Name']) ?>">
            <div class="invalid-feedback">
              Please provide a name for this type of membership
            </div>
          </div>

          <div class="form-group">
            <label for="class-description">Description (optional)</label>
            <textarea class="form-control" name="class-description" id="class-description" rows="5"><?= htmlspecialchars($class['Description']) ?></textarea>
          </div>

          <div class="form-group" id="fee-type">
            <p class="mb-2">Fee type</p>
            <div class="custom-control custom-radio">
              <input type="radio" id="fee-n" name="class-fee-type" class="custom-control-input" <?php if ($json->type == 'NSwimmers') { ?>checked<?php } ?> value="NSwimmers" required>
              <label class="custom-control-label" for="fee-n">N Members</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="fee-person" name="class-fee-type" class="custom-control-input" <?php if ($json->type == 'PerPerson') { ?>checked<?php } ?> value="PerPerson">
              <label class="custom-control-label" for="fee-person">Per Person</label>
            </div>
          </div>

          <div id="per-person" class="<?php if ($json->type != 'PerPerson') { ?>d-none<?php } ?>">
            <div class="form-group">
              <label for="class-price">Price</label>
              <input type="number" name="class-price" id="class-price" class="form-control person-fee-input" <?php if (isset($fees[0])) { ?> value="<?= htmlspecialchars($fees[0]) ?>" <?php } ?> min=" 0" step="0.01" placeholder="0" <?php if ($json->type == 'PerPerson') { ?>required<?php } ?>>
              <div class="invalid-feedback">
                Please provide a price for this type of membership
              </div>
            </div>
          </div>

          <div id="n-swimmers" class="<?php if ($json->type != 'NSwimmers') { ?>d-none<?php } ?>">
            <div id="fees-box" data-init="true" data-fees="<?= htmlspecialchars(json_encode($fees)) ?>"></div>

            <p>
              <button class="btn btn-primary" id="add-guest" type="button">
                Add another
              </button>
            </p>
          </div>

          <?= \SCDS\CSRF::write(); ?>

          <p>
            <button type="submit" class="btn btn-success">Save</button>
          </p>

        </form>

      </main>
    </div>
  </div>
</div>

<!-- <script>
  document.getElementById('fee-type').addEventListener('change', ev => {
    let type = ev.target.value;

    let perPerson = document.getElementById('per-person');
    let nSwimmers = document.getElementById('n-swimmers');

    let personFields = document.getElementsByClassName('person-fee-input');
    let nSwimmersFields = document.getElementsByClassName('nswimmers-fee-input');

    if (type == 'PerPerson') {
      perPerson.classList.remove('d-none');
      nSwimmers.classList.add('d-none');

      // Set required
      for (let field of personFields) {
        field.required = true;
      }
      // Unrequire
      for (let field of nSwimmersFields) {
        field.required = false;
      }
    } else if (type == 'NSwimmers') {
      perPerson.classList.add('d-none');
      nSwimmers.classList.remove('d-none');

      // Set required
      for (let field of personFields) {
        field.required = false;
      }
      // Unrequire
      for (let field of nSwimmersFields) {
        field.required = true;
      }
    }
  });

  function uuidv4() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
      var r = Math.random() * 16 | 0,
        v = c == 'x' ? r : (r & 0x3 | 0x8);
      return v.toString(16);
    });
  }

  function addField(event, hideRemove = false, value = null) {
    var id = uuidv4();

    var container = document.createElement('DIV');
    container.classList.add('row', 'mb-3', 'align-items-end');
    container.id = id;

    var formContainer = document.createElement('DIV');
    formContainer.classList.add('col');

    var name = document.createElement('DIV');
    name.classList.add('form-group', 'mb-0');
    var nameLabel = document.createElement('LABEL');
    var nameField = document.createElement('INPUT');
    var invalidFeedback = document.createElement('DIV');
    invalidFeedback.classList.add('invalid-feedback');
    invalidFeedback.textContent = 'Please enter a valid price';
    nameField.type = 'number';
    nameField.setAttribute('min', '0');
    nameField.setAttribute('step', '0.01');
    nameField.required = true;
    nameField.placeholder = '0';
    nameField.classList.add('form-control', 'nswimmers-fee-input');
    nameField.name = 'class_fee[]';
    nameField.id = id + '-class-fee';
    if (value) {
      nameField.value = value;
    }
    nameLabel.textContent = 'Person N+';
    nameLabel.htmlFor = id + '-class-fee';
    nameLabel.classList.add('fee-name-label');

    name.appendChild(nameLabel);
    name.appendChild(nameField);
    name.appendChild(invalidFeedback);

    formContainer.appendChild(name);

    var removeContainer = document.createElement('DIV');
    removeContainer.classList.add('col-md-auto');
    var removeButton = document.createElement('BUTTON');
    removeButton.classList.add('btn', 'btn-warning', 'remove-buttons');
    removeButton.textContent = 'Remove';
    removeButton.type = 'button';
    removeButton.dataset.removeTarget = id;

    var removeButtonTopSpace = document.createElement('DIV');
    removeButtonTopSpace.classList.add('mb-2', 'd-md-none');

    removeContainer.appendChild(removeButtonTopSpace);
    removeContainer.appendChild(removeButton);

    container.appendChild(formContainer);
    if (!hideRemove) {
      container.appendChild(removeContainer);
    }

    document.getElementById('fees-box').appendChild(container);

    assignNames();
  }

  function assignNames() {
    let labels = document.getElementsByClassName('fee-name-label');
    let i = 1;
    for (let label of labels) {
      if (i < labels.length) {
        label.textContent = 'Total for ' + i + ' person';
        if (i != 1) {
          label.textContent += 's';
        }
      } else {
        label.textContent = 'Total for ' + i + '+ persons';
      }
      i++;
    };
  }

  document.getElementById('add-guest').addEventListener('click', addField);

  let box = document.getElementById('fees-box');
  let fees = JSON.parse(box.dataset.fees);
  if (box.dataset.init === 'true') {
    if (fees.length > 0) {
      fees.forEach(fee => {
        addField(null, true, fee);
      });
    } else {
      addField(null, true);
    }
  }

  document.getElementById('fees-box').addEventListener('click', (event) => {
    if (event.target.dataset.removeTarget) {
      var remove = document.getElementById(event.target.dataset.removeTarget);
      if (remove) {
        remove.parentElement.removeChild(remove);
      }
      assignNames();
    }
  });
</script> -->

<?php

$footer = new \SCDS\Footer();
$footer->useFluidContainer();
$footer->addJs('public/js/NeedsValidation.js');
$footer->addJs('public/js/settings/club-membership-fees.js');
$footer->render();
