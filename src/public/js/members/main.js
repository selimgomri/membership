/**
 * Code for member view pages
 * 
 * Currently handles;
 * - Squad moves
 */

async function displaySquads() {
  try {
    let squads = await getSquads();
    let memberLeadDesc = '';
    let squadDiv = document.createElement('DIV');
    let squadList = document.createElement('DIV');
    squadList.classList.add('list-group', 'mb-3');
    let numSquads = 0;
    squads.current.forEach(squad => {
      console.log(squad);
      numSquads += 1;
      let listItem = document.createElement('A');
      listItem.classList.add('list-group-item', 'list-group-item-action');
      listItem.href = squad.url;
      listItem.appendChild(document.createTextNode(squad.name + ' '));
      let price = document.createElement('SPAN');
      let em = document.createElement('EM');
      if (false) {
        let strike = document.createElement('S');
        strike.textContent = '£' + squad.price_string;
        em.appendChild(strike);
        em.appendChild(document.createTextNode(' £0 - Does not pay'));
      } else {
        em.textContent = '£' + squad.price_string;
      }
      price.appendChild(em);
      listItem.appendChild(price);
      squadList.appendChild(listItem);
      if (numSquads > 1) memberLeadDesc += ', ';
      memberLeadDesc += squad.name;
    });

    if (numSquads > 0) {
      let squadsDesc = document.createElement('P');
      let s = '';
      if (numSquads > 1) {
        s = 's';
        document.getElementById('squads').textContent = 'Squads';
      } else {
        document.getElementById('squads').textContent = 'Squad';
      }
      squadsDesc.textContent = squads.squads_desc_line;
      squadDiv.appendChild(squadsDesc);
      squadDiv.appendChild(squadList);
      document.getElementById('leadDesc').textContent = memberLeadDesc + ' Squad' + s;
    } else {
      let noSquads = document.createElement('DIV');
      noSquads.classList.add('alert', 'alert-warning');
      noSquads.textContent = squads.squads_desc_line;
      squadDiv.appendChild(noSquads);
      document.getElementById('squads').textContent = 'Squads';
      document.getElementById('leadDesc').textContent = 'Member';
    }

    document.getElementById('squadDetails').innerHTML = '';
    document.getElementById('squadDetails').appendChild(squadDiv);
  } catch (err) {
    console.error(err);
    document.getElementById('squadDetails').innerHTML = '<div class="alert alert-warning">We couldn\'t load the MEMBER NAME\'s squads</div>';
  }
}

const pleaseWait = '<div class="cell text-center mb-0"><p class="h2">Loading</p><p class="mb-0">Please wait</p></div>';

function handleNewMove(event) {
  let member = event.target.dataset.member;
  getSquads()
    .then((squads) => {
      let title = 'New Move';
      let body = '<p>What type of move do you want to create?</p>';
      body += '<p><button class="btn btn-primary btn-block" id="squad-to-squad">Squad to squad</button></p>';
      body += '<p><button class="btn btn-primary btn-block" id="join-squad">Join a squad</button></p>';
      body += '<p><button class="btn btn-primary btn-block" id="leave-squad">Leave a squad</button></p>';

      body += '<p>Usually, you\'ll want to use <strong>squad to squad</strong> moves, where a member will move from one squad to another. <strong>Join a squad</strong> means a member will join a squad (in addition to all existing squads) and <strong>leave a squad</strong> allows you to remove a member from a specific squad.</p>';

      body += '<p class="mb-0">Squad moves can be applied now or be scheduled to take place at any later date, such as the start of next month.</p>';

      document.getElementById('modal-title').textContent = title;
      document.getElementById('modal-body').innerHTML = body;

      let squadToSquadBtn = document.getElementById('squad-to-squad');
      let joinSquadBtn = document.getElementById('join-squad');
      let leaveSquadBtn = document.getElementById('leave-squad');

      if (squadToSquadBtn) {
        squadToSquadBtn.addEventListener('click', squadToSquad);
        if (squads.current.length == 0 || squads.can_join.length == 0) {
          squadToSquadBtn.disabled = true;
        }
      }

      if (joinSquadBtn) {
        joinSquadBtn.addEventListener('click', joinSquad);
        if (squads.can_join.length == 0) {
          joinSquadBtn.disabled = true;
        }
      }

      if (leaveSquadBtn) {
        leaveSquadBtn.addEventListener('click', leaveSquad);
        if (squads.current.length == 0) {
          leaveSquadBtn.disabled = true;
        }
      }

      $('#modal').modal('show');
    })
    .catch((error) => {
      console.warn(error.message);
      showGetSquadsFailure();
      $('#modal').modal('show');
    })
}

function squadToSquad(event) {
  let member = document.getElementById('new-move-button').dataset.member;
  let title = 'Squad to Squad Move';

  document.getElementById('modal-title').textContent = title;
  document.getElementById('modal-body').innerHTML = pleaseWait;

  // Fetch current squads and other squads
  getSquads()
    .then((squads) => {

      let body = document.getElementById('modal-body');

      body.textContent = '';

      body.appendChild(document.createElement('P').appendChild(document.createTextNode('Please choose a squad and role for the user.')));

      let form = document.createElement('FORM');

      let fg = document.createElement('DIV');
      fg.classList.add('form-group');

      let label = document.createElement('LABEL');
      label.appendChild(document.createTextNode('Leaving'));
      label.htmlFor = 'leave';

      let select = document.createElement('SELECT');
      select.classList.add('custom-select');
      select.id = 'leave';
      select.name = 'leave';

      // Loop and add <option>s
      squads.current.forEach(squad => {
        let option = document.createElement('OPTION');
        option.appendChild(document.createTextNode(squad.name));
        option.value = squad.id;
        select.appendChild(option);
      });

      fg.appendChild(label);
      fg.appendChild(select);

      form.appendChild(fg);

      fg = document.createElement('DIV');
      fg.classList.add('form-group');

      label = document.createElement('LABEL');
      label.appendChild(document.createTextNode('Joining'));
      label.htmlFor = 'join';

      select = document.createElement('SELECT');
      select.classList.add('custom-select');
      select.id = 'join';
      select.name = 'join';

      // Loop and add <option>s
      squads.can_join.forEach(squad => {
        let option = document.createElement('OPTION');
        option.appendChild(document.createTextNode(squad.name));
        option.value = squad.id;
        select.appendChild(option);
      });

      fg.appendChild(label);
      fg.appendChild(select);

      form.appendChild(fg);

      fg = document.createElement('DIV');
      fg.classList.add('form-group');

      let customRadio = document.createElement('DIV');
      customRadio.classList.add('custom-control', 'custom-radio');

      let radio = document.createElement('input');
      radio.type = 'radio';
      radio.value = '0';
      radio.checked = true;
      radio.id = 'move-when-1';
      radio.name = 'move-when'
      radio.classList.add('custom-control-input');

      label = document.createElement('label');
      label.appendChild(document.createTextNode('Move now'));
      label.classList.add('custom-control-label');
      label.htmlFor = 'move-when-1';

      customRadio.appendChild(radio);
      customRadio.appendChild(label);

      fg.appendChild(customRadio);

      customRadio = document.createElement('DIV');
      customRadio.classList.add('custom-control', 'custom-radio');

      radio = document.createElement('input');
      radio.type = 'radio';
      radio.value = '1';
      radio.id = 'move-when-2';
      radio.name = 'move-when'
      radio.classList.add('custom-control-input');

      label = document.createElement('label');
      label.appendChild(document.createTextNode('Move on a specified date'));
      label.classList.add('custom-control-label');
      label.htmlFor = 'move-when-2';

      customRadio.appendChild(radio);
      customRadio.appendChild(label);

      fg.appendChild(customRadio);

      form.appendChild(fg);

      fg = document.createElement('DIV');
      fg.classList.add('form-group', 'collapse');
      fg.id = 'date-group'

      label = document.createElement('label');
      label.appendChild(document.createTextNode('Date of move'));
      label.htmlFor = 'move-date';

      let date = document.createElement('input');
      date.type = 'date';
      today = new Date().toISOString().split('T')[0];
      date.min = today;
      date.value = today;
      date.id = 'move-date';
      date.name = 'move-date';
      date.classList.add('form-control');

      fg.appendChild(label);
      fg.appendChild(date);

      form.appendChild(fg);

      /*
       * Is paying
       */
      fg = document.createElement('DIV');
      fg.classList.add('form-group');

      customRadio = document.createElement('DIV');
      customRadio.classList.add('custom-control', 'custom-radio');

      radio = document.createElement('input');
      radio.type = 'radio';
      radio.value = '0';
      radio.checked = true;
      radio.id = 'paying-yes';
      radio.name = 'paying'
      radio.classList.add('custom-control-input');

      label = document.createElement('label');
      label.appendChild(document.createTextNode('Will pay fees for this squad'));
      label.classList.add('custom-control-label');
      label.htmlFor = 'paying-yes';

      customRadio.appendChild(radio);
      customRadio.appendChild(label);

      fg.appendChild(customRadio);

      customRadio = document.createElement('DIV');
      customRadio.classList.add('custom-control', 'custom-radio');

      radio = document.createElement('input');
      radio.type = 'radio';
      radio.value = '1';
      radio.id = 'paying-no';
      radio.name = 'paying'
      radio.classList.add('custom-control-input');

      label = document.createElement('label');
      label.appendChild(document.createTextNode('Won\'t pay fees for this squad'));
      label.classList.add('custom-control-label');
      label.htmlFor = 'paying-no';

      customRadio.appendChild(radio);
      customRadio.appendChild(label);

      fg.appendChild(customRadio);

      form.appendChild(fg);

      fg = document.createElement('P');
      let submit = document.createElement('BUTTON');
      submit.id = 'move-submit';
      submit.classList.add('btn', 'btn-primary');
      submit.textContent = 'Save';

      fg.appendChild(submit);
      form.appendChild(fg);

      body.appendChild(form);

      form.addEventListener('submit', (event) => {
        event.preventDefault();
        let fd = new FormData(event.target);
        let button = document.getElementById('new-move-button');
        fd.append('member', button.dataset.member);
        fd.append('event', 'move');
        fd.append('SCDS-GLOBAL-CSRF', button.dataset.csrf);

        // Send form data ajax
        var req = new XMLHttpRequest();
        req.addEventListener('load', async (event) => {
          if (event.target.status == 200) {
            let result = JSON.parse(event.target.responseText);
            if (result.success) {
              body.innerHTML = '<div class="alert alert-success">Operation successful</div>';
              displaySquads();
            } else {
              body.innerHTML = '<div class="alert alert-warning"><p class="mb-0"><strong>A problem occurred</strong></p><p class="mb-0" id="errorMessage"></p></div>';
              if (result.error) {
                document.getElementById('errorMessage').textContent = result.error;
              } else {
                document.getElementById('errorMessage').textContent = 'Unknown error';
              }
            }
          } else {
          }
        });
        req.addEventListener('error', (event) => {
          // Error
        });
        req.addEventListener('abort', (event) => {
          // Error
        });
        req.open('POST', button.dataset.moveUrl);
        req.send(fd);
        body.innerHTML = '<div class="alert alert-success">SENT</div>';
      });

      document.querySelectorAll('input[name="move-when"]').forEach((radio) => {
        radio.addEventListener('change', showHideDateGroup);
      });

    })
    .catch((error) => {
      console.warn(error.message);
      showGetSquadsFailure();
    })
}

function joinSquad(event) {
  let member = document.getElementById('new-move-button').dataset.member;
  let title = 'Join a new squad';

  document.getElementById('modal-title').textContent = title;
  document.getElementById('modal-body').innerHTML = pleaseWait;

  // Fetch current squads and other squads
  getSquads()
    .then((squads) => {
      let body = document.getElementById('modal-body');

      body.textContent = '';

      let p = document.createElement('P');
      p.textContent = 'Please choose a squad to join.';
      body.appendChild(p);

      let form = document.createElement('FORM');

      let fg = document.createElement('DIV');
      fg.classList.add('form-group');

      let label = document.createElement('LABEL');
      label.appendChild(document.createTextNode('Join'));
      label.htmlFor = 'join';

      let select = document.createElement('SELECT');
      select.classList.add('custom-select');
      select.id = 'join';
      select.name = 'join';

      // Loop and add <option>s
      squads.can_join.forEach(squad => {
        let option = document.createElement('OPTION');
        option.appendChild(document.createTextNode(squad.name));
        option.value = squad.id;
        select.appendChild(option);
      });

      fg.appendChild(label);
      fg.appendChild(select);

      form.appendChild(fg);

      fg = document.createElement('DIV');
      fg.classList.add('form-group');

      let customRadio = document.createElement('DIV');
      customRadio.classList.add('custom-control', 'custom-radio');

      let radio = document.createElement('input');
      radio.type = 'radio';
      radio.value = '0';
      radio.checked = true;
      radio.id = 'move-when-1';
      radio.name = 'move-when'
      radio.classList.add('custom-control-input');

      label = document.createElement('label');
      label.appendChild(document.createTextNode('Move now'));
      label.classList.add('custom-control-label');
      label.htmlFor = 'move-when-1';

      customRadio.appendChild(radio);
      customRadio.appendChild(label);

      fg.appendChild(customRadio);

      customRadio = document.createElement('DIV');
      customRadio.classList.add('custom-control', 'custom-radio');

      radio = document.createElement('input');
      radio.type = 'radio';
      radio.value = '1';
      radio.id = 'move-when-2';
      radio.name = 'move-when'
      radio.classList.add('custom-control-input');

      label = document.createElement('label');
      label.appendChild(document.createTextNode('Move on a specified date'));
      label.classList.add('custom-control-label');
      label.htmlFor = 'move-when-2';

      customRadio.appendChild(radio);
      customRadio.appendChild(label);

      fg.appendChild(customRadio);

      form.appendChild(fg);

      fg = document.createElement('DIV');
      fg.classList.add('form-group', 'collapse');
      fg.id = 'date-group'

      label = document.createElement('label');
      label.appendChild(document.createTextNode('Date of move'));
      label.htmlFor = 'move-date';

      let date = document.createElement('input');
      date.type = 'date';
      today = new Date().toISOString().split('T')[0];
      date.min = today;
      date.value = today;
      date.id = 'move-date';
      date.name = 'move-date';
      date.classList.add('form-control');

      fg.appendChild(label);
      fg.appendChild(date);

      form.appendChild(fg);

      /*
       * Is paying
       */
      fg = document.createElement('DIV');
      fg.classList.add('form-group');

      customRadio = document.createElement('DIV');
      customRadio.classList.add('custom-control', 'custom-radio');

      radio = document.createElement('input');
      radio.type = 'radio';
      radio.value = '0';
      radio.checked = true;
      radio.id = 'paying-yes';
      radio.name = 'paying'
      radio.classList.add('custom-control-input');

      label = document.createElement('label');
      label.appendChild(document.createTextNode('Pays fees for this squad'));
      label.classList.add('custom-control-label');
      label.htmlFor = 'paying-yes';

      customRadio.appendChild(radio);
      customRadio.appendChild(label);

      fg.appendChild(customRadio);

      customRadio = document.createElement('DIV');
      customRadio.classList.add('custom-control', 'custom-radio');

      radio = document.createElement('input');
      radio.type = 'radio';
      radio.value = '1';
      radio.id = 'paying-no';
      radio.name = 'paying'
      radio.classList.add('custom-control-input');

      label = document.createElement('label');
      label.appendChild(document.createTextNode('Does not pay fees for this squad'));
      label.classList.add('custom-control-label');
      label.htmlFor = 'paying-no';

      customRadio.appendChild(radio);
      customRadio.appendChild(label);

      fg.appendChild(customRadio);

      form.appendChild(fg);

      fg = document.createElement('P');
      let submit = document.createElement('BUTTON');
      submit.id = 'move-submit';
      submit.classList.add('btn', 'btn-primary');
      submit.textContent = 'Save';

      fg.appendChild(submit);
      form.appendChild(fg);

      // Display
      body.appendChild(form);

      form.addEventListener('submit', (event) => {
        event.preventDefault();
        let fd = new FormData(event.target);
        let button = document.getElementById('new-move-button');
        fd.append('member', button.dataset.member);
        fd.append('event', 'join');
        fd.append('SCDS-GLOBAL-CSRF', button.dataset.csrf);

        // Send form data ajax
        var req = new XMLHttpRequest();
        req.addEventListener('load', async (event) => {
          if (event.target.status == 200) {
            let result = JSON.parse(event.target.responseText);
            if (result.success) {
              body.innerHTML = '<div class="alert alert-success">Operation successful</div>';
              displaySquads();
            } else {
              body.innerHTML = '<div class="alert alert-warning"><p class="mb-0"><strong>A problem occurred</strong></p><p class="mb-0" id="errorMessage"></p></div>';
              if (result.error) {
                document.getElementById('errorMessage').textContent = result.error;
              } else {
                document.getElementById('errorMessage').textContent = 'Unknown error';
              }
            }
          } else {
          }
        });
        req.addEventListener('error', (event) => {
          // Error
        });
        req.addEventListener('abort', (event) => {
          // Error
        });
        req.open('POST', button.dataset.moveUrl);
        req.send(fd);
        body.innerHTML = '<div class="alert alert-success">SENT</div>';
      });

      document.querySelectorAll('input[name="move-when"]').forEach((radio) => {
        radio.addEventListener('change', showHideDateGroup);
      });
    })
    .catch((error) => {
      console.warn(error.message);
      showGetSquadsFailure();
    })
}

/**
 * Handle a member leaving a squad, now or in future.
 * 
 * @param {Event} event 
 */
function leaveSquad(event) {
  let member = document.getElementById('new-move-button').dataset.member;
  let title = 'Leave a squad';

  document.getElementById('modal-title').textContent = title;
  document.getElementById('modal-body').innerHTML = pleaseWait;

  // Fetch current squads and other squads
  getSquads()
    .then((squads) => {
      let body = document.getElementById('modal-body');

      body.textContent = '';

      let p = document.createElement('P');
      p.textContent = 'Please choose a squad to leave.';
      body.appendChild(p);

      let form = document.createElement('FORM');

      let fg = document.createElement('DIV');
      fg.classList.add('form-group');

      let label = document.createElement('LABEL');
      label.appendChild(document.createTextNode('Leaving'));
      label.htmlFor = 'leave';

      let select = document.createElement('SELECT');
      select.classList.add('custom-select');
      select.id = 'leave';
      select.name = 'leave';

      // Loop and add <option>s
      squads.current.forEach(squad => {
        let option = document.createElement('OPTION');
        option.appendChild(document.createTextNode(squad.name));
        option.value = squad.id;
        select.appendChild(option);
      });

      fg.appendChild(label);
      fg.appendChild(select);

      form.appendChild(fg);

      fg = document.createElement('DIV');
      fg.classList.add('form-group');

      let customRadio = document.createElement('DIV');
      customRadio.classList.add('custom-control', 'custom-radio');

      let radio = document.createElement('input');
      radio.type = 'radio';
      radio.value = '0';
      radio.checked = true;
      radio.id = 'move-when-1';
      radio.name = 'move-when'
      radio.classList.add('custom-control-input');

      label = document.createElement('label');
      label.appendChild(document.createTextNode('Leave now'));
      label.classList.add('custom-control-label');
      label.htmlFor = 'move-when-1';

      customRadio.appendChild(radio);
      customRadio.appendChild(label);

      fg.appendChild(customRadio);

      customRadio = document.createElement('DIV');
      customRadio.classList.add('custom-control', 'custom-radio');

      radio = document.createElement('input');
      radio.type = 'radio';
      radio.value = '1';
      radio.id = 'move-when-2';
      radio.name = 'move-when'
      radio.classList.add('custom-control-input');

      label = document.createElement('label');
      label.appendChild(document.createTextNode('Leave on a specified date'));
      label.classList.add('custom-control-label');
      label.htmlFor = 'move-when-2';

      customRadio.appendChild(radio);
      customRadio.appendChild(label);

      fg.appendChild(customRadio);

      form.appendChild(fg);

      fg = document.createElement('DIV');
      fg.classList.add('form-group', 'collapse');
      fg.id = 'date-group'

      label = document.createElement('label');
      label.appendChild(document.createTextNode('Date of move'));
      label.htmlFor = 'move-date';

      let date = document.createElement('input');
      date.type = 'date';
      today = new Date().toISOString().split('T')[0];
      date.min = today;
      date.value = today;
      date.id = 'move-date';
      date.name = 'move-date';
      date.classList.add('form-control');

      fg.appendChild(label);
      fg.appendChild(date);

      form.appendChild(fg);

      fg = document.createElement('P');
      let submit = document.createElement('BUTTON');
      submit.id = 'move-submit';
      submit.classList.add('btn', 'btn-primary');
      submit.textContent = 'Save';

      fg.appendChild(submit);
      form.appendChild(fg);

      // Display
      body.appendChild(form);

      form.addEventListener('submit', (event) => {
        event.preventDefault();
        let fd = new FormData(event.target);
        let button = document.getElementById('new-move-button');
        fd.append('member', button.dataset.member);
        fd.append('event', 'leave');
        fd.append('SCDS-GLOBAL-CSRF', button.dataset.csrf);

        // Send form data ajax
        var req = new XMLHttpRequest();
        req.addEventListener('load', async (event) => {
          if (event.target.status == 200) {
            let result = JSON.parse(event.target.responseText);
            if (result.success) {
              body.innerHTML = '<div class="alert alert-success">Member removed from squad</div>';
              displaySquads();
            } else {
              body.innerHTML = '<div class="alert alert-warning"><p class="mb-0"><strong>A problem occurred</strong></p><p class="mb-0" id="errorMessage"></p></div>';
              if (result.error) {
                document.getElementById('errorMessage').textContent = result.error;
              } else {
                document.getElementById('errorMessage').textContent = 'Unknown error';
              }
            }
          } else {
          }
        });
        req.addEventListener('error', (event) => {
          // Error
        });
        req.addEventListener('abort', (event) => {
          // Error
        });
        req.open('POST', button.dataset.moveUrl);
        req.send(fd);
        body.innerHTML = '<div class="alert alert-success">SENT</div>';
      });

      document.querySelectorAll('input[name="move-when"]').forEach((radio) => {
        radio.addEventListener('change', showHideDateGroup);
      });
    })
    .catch((error) => {
      console.warn(error.message);
      showGetSquadsFailure();
    })
}

/**
 * Get squads from the db and return a JSON object
 */
function getSquads() {
  return new Promise(function (resolve, reject) {
    var oReq = new XMLHttpRequest();
    oReq.addEventListener('load', (event) => {
      if (event.target.status == 200)
        resolve(JSON.parse(event.target.responseText));
      else
        reject(JSON.parse(event.target.responseText));
    });
    oReq.open('POST', document.getElementById('new-move-button').dataset.squadsUrl);
    oReq.send();
  });
}

/**
 * Show or hide the move-date date input based on radio value
 * 
 * @param {Event} event 
 */
function showHideDateGroup(event) {
  if (document.querySelector('input[name="move-when"]:checked').value == true) {
    $('#date-group').collapse('show');
  } else {
    $('#date-group').collapse('hide');
  }
}

/**
 * Report an error when getting squads
 * 
 * @param {Event} event 
 */
function showGetSquadsFailure(event) {
  let title = 'Squad Moves - Error';
  let body = '<div class="alert alert-danger mb-0">';
  body += '<p class="mb-0"><strong>An error occurred while trying to gather the member\'s squads.</strong></p>';
  body += '<p class="mb-0">Please try again later.</p>';
  body += '</div>';

  document.getElementById('modal-title').textContent = title;
  document.getElementById('modal-body').innerHTML = pleaseWait;
}

let moveButton = document.getElementById('new-move-button');
console.log(moveButton);
if (moveButton) {
  moveButton.addEventListener('click', handleNewMove);
}

displaySquads();