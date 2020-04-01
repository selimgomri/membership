document.addEventListener("DOMContentLoaded", function(event) { 
  function apply() {
    var type = document.getElementById('accountType');
    var typeValue = type.value;
    var user = type.dataset.userId;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById('accountTypeOutput').innerHTML = this.responseText;
      }
    }
    xhttp.open("POST", type.dataset.ajaxUrl + user, true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send('accountType=' + typeValue);
  }

  function confirmResendRegistrationEmail(event) {
    event.preventDefault();

    var resendButton = event.target;

    // Set modal title
    document.getElementById('main-modal-title').textContent = 'Confirm resending registration email';

    // Set buttons
    document.getElementById('main-modal-footer').innerHTML = '<button type="button" class="btn btn-dark" data-dismiss="modal">Cancel</button><button type="button" id="modal-confirm-button" class="btn btn-success">Confirm resend</button>';

    // Compose modal body content
    var bodyContent = '<p>Are you sure you want to resend the registration email to <span id="modal-user-name"></span>?</p>';
    bodyContent += '<p>If the original email did not arrive, please check if the email address for this user is correct and <a href="#" id="modal-user-edit-link">edit it</a> before sending another registration email.</p>';
    bodyContent += '<div class="alert alert-warning mb-0">As a security precaution, we\'ll cancel the old registration link sent to this user.</div>';
    document.getElementById('main-modal-body').innerHTML = bodyContent;
    document.getElementById('modal-user-name').textContent = resendButton.dataset.userName;
    document.getElementById('modal-user-edit-link').href = resendButton.dataset.userEditLink;

    // Setup confirm event listeners
    document.getElementById('modal-confirm-button').addEventListener('click', resendRegistrationEmail);

    $('#main-modal').modal('show');
  }

  function resendRegistrationEmail() {
    var resendButton = document.getElementById('registration-resend-button');
    document.getElementById('modal-confirm-button').disabled = true;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        var json = JSON.parse(this.responseText);

        $('#main-modal').modal('hide');
        // Setup resend status alert
        var resendStatus = document.getElementById('resend-status');
        resendStatus.innerHTML = '<div id="resend-status-alert"><div id="resend-status-content"></div><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>';

        var resendStatusAlert = document.getElementById('resend-status-alert');
        resendStatusAlert.classList.add('alert', 'alert-dismissible', 'fade', 'show');

        // Setup resend status alert content
        var resendStatusContent = document.getElementById('resend-status-content');
        resendStatusContent.innerHTML = json.alertContent;

        // Setup resend status alert contextual class based on server response
        resendStatusAlert.classList.add(json.alertContextualClass);

        if (json.status === true) {
          resendButton.disabled = true;
        }

        document.getElementById('modal-confirm-button').disabled = false;
      }
    }
    xhttp.open("POST", resendButton.dataset.ajaxUrl, true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send('user=' + resendButton.dataset.user);
  }

  var resendButton = document.getElementById('registration-resend-button');
  if (resendButton) {
    resendButton.addEventListener('click', confirmResendRegistrationEmail)
  }

  document.getElementById('accountType').onchange = apply;

  let button = document.getElementById('delete-button');

  button.addEventListener('click', function(event) {
    document.getElementById('main-modal-title').textContent = 'Delete ' + button.dataset.userName + '\'s account';

    let body = '<p>By deleting this account, we will remove all personal information. Financial information about direct debit or card payments will be retained for the completeness of your club\'s records.</p>';

    body = body + '<p>We will cancel all existing direct debit mandates for this use. This may cause any pending payments or payment retries to fail.</p>';

    // body = body + '<p>We will send a confirmation email to yourself and the user you are deleting when we have successfully deleted their information.</p>';

    body = body + '<div class="form-group mb-0"><label for="confirm-password">Confirm your password to proceed</label><input autocomplete="current-password" type="password" class="form-control" id="confirm-password" aria-describedby="pwHelp"><small id="pwHelp" class="form-text text-muted">This is an added security measure. Deleting a user\'s account is not reversible.</small></div>';

    document.getElementById('main-modal-body').innerHTML = body;

    document.getElementById('main-modal-footer').innerHTML = '<button type="button" class="btn btn-dark" data-dismiss="modal">Cancel</button><button type="button" id="modal-confirm-button" class="btn btn-danger">Delete account</button>';

    document.getElementById('modal-confirm-button').addEventListener('click', function(event) {
      let button = document.getElementById('delete-button');
      document.getElementById('modal-confirm-button').disabled = true;
      let password = document.getElementById('confirm-password').value;
      let oldContent = document.getElementById('main-modal-body').innerHTML;
      document.getElementById('main-modal-body').innerHTML = '<div class="py-5 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>';
      
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function () {
        if (this.readyState == 4) {
          var json = JSON.parse(this.responseText);
          if (json.status == 500) {
            // Failed
            document.getElementById('main-modal-body').innerHTML = '<div class="alert alert-danger"><p class="mb-0"><strong>An error occurred.</strong></p><p id="alert-text" class="mb-0"></p></div>' + oldContent;
            document.getElementById('modal-confirm-button').disabled = false;
          } else {
            // Success
            document.getElementById('main-modal-body').innerHTML = '<div class="alert alert-success mb-0"><p class="mb-0"><strong>Success.</strong></p><p id="alert-text" class="mb-0"></p></div>';
            document.getElementById('main-modal-footer').innerHTML = '<a id="close-link" href="" class="btn btn-dark">Close</a>';
            document.getElementById('close-link').href = document.getElementById('delete-button').dataset.usersUrl;
          }
          document.getElementById('alert-text').textContent = json.message;
        }
      }
      xhttp.open('POST', button.dataset.ajaxUrl, true);
      xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      xhttp.send('user=' + button.dataset.userId + '&password=' + password);
    });

    $('#main-modal').modal('show');

    console.log(event);
  });
});