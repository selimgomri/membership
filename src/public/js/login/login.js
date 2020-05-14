/*
let login = document.getElementById('login-form');
login.addEventListener('submit', (event) => {
  event.preventDefault();

  let emailInput = document.getElementById('email-address');
  let pwInput = document.getElementById('password');
  
  // Try signin
  let email = emailInput.value;
  let pw = pwInput.value;
  let csrf = document.getElementById('SCDS-GLOBAL-CSRF').value;
  let target = null;
  let org = null;
  let urlParams = new URLSearchParams(window.location.search);
  if (urlParams.had('target')) {
    target = urlParams.get('target')
  }
  if (urlParams.had('club')) {
    org = urlParams.get('club')
  }

  let data = {
    email: email,
    password: pw,
    csrfToken: csrf,
    target: target,
    organisation: org,
    staySignedIn: false
  }

  // XHR
  let xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      var json = JSON.parse(this.responseText);
      if (json.next === 'pass') {
        // Success, go forth
        window.location.href = json.next.target;
      } else if (json.next === '2fa') {
        // Show Two Factor Auth
      } else if (json.next === 'incorrect') {
        // Handle invalid pw
      } else {
        // Handle error
      }
    }
  }
  xhttp.open('POST', document.getElementById('login-form').dataset.ajaxLoginUrl, true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send(JSON.stringify(data));

});
*/

function canSubmit(event) {
  let user = document.getElementById('email-address');
  let pw = document.getElementById('password');
  let btn = document.getElementById('submit');

  if (user.checkValidity() && pw.checkValidity()) {
    btn.disabled = false;
  } else {
    btn.disabled = true;
  }
}

let user = document.getElementById('email-address');
let pw = document.getElementById('password');

user.addEventListener('input', canSubmit);
pw.addEventListener('input', canSubmit);