/**
 * Code for member view pages
 * 
 * Currently handles;
 * - Squad moves
 */
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
      let body = '<form>';
      body += '<div class="form-group"><label for="leave">Leaving</label><select class="custom-select" id="leave">';
      body += '</select></div>';

      body += '<div class="form-group"><label for="join">Joining</label><select class="custom-select" id="join">';
      body += '</select></div>';

      body += '<div class="form-group mb-0"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="paying"><label class="custom-control-label" for="paying">Pays squad fees<span id="paying-squad-name"></span></label></div></div>';
      body += '</form>';
      document.getElementById('modal-body').innerHTML = body;
    })
    .catch((error) => {
      console.warn(error.message);
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
      let body = '<form>';

      body += '<div class="form-group"><label for="join">Joining</label><select class="custom-select" id="join">';
      body += '</select></div>';

      body += '<div class="form-group mb-0"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="paying"><label class="custom-control-label" for="paying">Pays squad fees<span id="paying-squad-name"></span></label></div></div>';
      body += '</form>';
      document.getElementById('modal-body').innerHTML = body;
    })
    .catch((error) => {
      console.warn(error.message);
    })
}

function leaveSquad(event) {
  let member = document.getElementById('new-move-button').dataset.member;
  let title = 'Leave a squad';

  document.getElementById('modal-title').textContent = title;
  document.getElementById('modal-body').innerHTML = pleaseWait;

  // Fetch current squads and other squads
  getSquads()
    .then((squads) => {
      let body = '<form>';
      body += '<div class="form-group mb-0"><label for="leave">Leaving</label><select class="custom-select" id="leave">';
      body += '</select></div>';
      body += '</form>';
      document.getElementById('modal-body').innerHTML = body;
    })
    .catch((error) => {
      console.warn(error.message);
    })
}

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

let moveButton = document.getElementById('new-move-button');
console.log(moveButton);
if (moveButton) {
  moveButton.addEventListener('click', handleNewMove);
}