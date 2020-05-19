/**
 * Code for member view pages
 * 
 * Currently handles;
 * - Squad moves
 */

function handleNewMove(event) {
  let member = event.target.dataset.member;
  let title = 'New Move';
  let body = '<p>What type of move do you want to create?</p>';
  body += '<p><button class="btn btn-primary btn-block">Squad to squad</button></p>';
  body += '<p><button class="btn btn-primary btn-block">Join a squad</button></p>';
  body += '<p><button class="btn btn-primary btn-block">Leave a squad</button></p>';

  body += '<p>Usually, you\'ll want to use <strong>squad to squad</strong> moves, where a member will move from one squad to another. <strong>Join a squad</strong> means a member will join a squad (in addition to all existing squads) and <strong>leave a squad</strong> allows you to remove a member from a specific squad.</p>';

  body += '<p class="mb-0">Squad moves can be applied now or be scheduled to take place at any later date, such as the start of next month.</p>';

  document.getElementById('modal-title').textContent = title;
  document.getElementById('modal-body').innerHTML = body;

  $('#modal').modal('show');
}

let moveButton = document.getElementById('new-move-button');
console.log(moveButton);
if (moveButton) {
  moveButton.addEventListener('click', handleNewMove);
}