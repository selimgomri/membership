const ajaxInfo = document.getElementById('ajax-info').dataset;

function resetRegisterArea() {
  var register = document.getElementById('output');
  register.innerHTML = '<div class="ajaxPlaceholder mb-0">Fill in the details above and we can load the register</div>';
}

function getSessions() {
  var squad = document.getElementById('squad');
  var squadValue = squad.options[squad.selectedIndex].value;
  if (squadValue == 0) {
    document.getElementById('output').innerHTML = '<div class="ajaxPlaceholder"><strong>Session Manager will appear here</strong> <br>Select a squad first</div>';
    return;
  } else {
    var sessAjax = new XMLHttpRequest();
    sessAjax.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById('output').innerHTML = this.responseText;
        window.history.replaceState('string', 'Title', ajaxInfo.pageUrl + '?squad=' + squadValue);
      }
    }
    sessAjax.open('POST', ajaxInfo.ajaxUrl, true);
    sessAjax.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    sessAjax.send('action=getSessions&squadID=' + squadValue);
  }
}

function addSession() {
  var squad = document.getElementById('squad');
  var squadValue = squad.options[squad.selectedIndex].value;

  var sessionName = document.getElementById('newSessionName');
  var sessionNameValue = sessionName.value;

  var sessionDay = document.getElementById('newSessionDay');
  var sessionDayValue = sessionDay.options[sessionDay.selectedIndex].value;

  var sessionVenue = document.getElementById('newSessionVenue');
  var sessionVenueValue = sessionVenue.options[sessionVenue.selectedIndex].value;

  var sessionStart = document.getElementById('newSessionStartTime');
  var sessionStartValue = sessionStart.value;

  var sessionEnd = document.getElementById('newSessionEndTime');
  var sessionEndValue = sessionEnd.value;

  var startDate = document.getElementById('newSessionStartDate');
  var startDateValue = startDate.value;

  var endDate = document.getElementById('newSessionEndDate');
  var endDateValue = endDate.value;

  var mainSequenceValue = null;
  var radios = document.getElementsByName('newSessionMS');
  for (var i = 0, length = radios.length; i < length; i++) {
    if (radios[i].checked) {
      // do whatever you want with the checked radio
      mainSequenceValue = radios[i].value;

      // only one radio can be logically checked, don\'t check the rest
      break;
    }
  }

  var sessAjax = new XMLHttpRequest();
  sessAjax.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      var json = JSON.parse(this.responseText);
      document.getElementById('output').innerHTML = json.sessions_view;
      
      // Show alert
      document.getElementById('status-message').innerHTML = '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> We\'ve added the session to the system. If the session starts in the future, it won\'t appear on this page until that day.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    } else if (this.readyState == 4 && this.status != 200) {
      // Show alert
      document.getElementById('status-message').innerHTML = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>We could not add the session!</strong> Please check the details and try again.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    }
  }
  sessAjax.open('POST', ajaxInfo.ajaxUrl, true);
  sessAjax.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  sessAjax.send('action=addSession&squadID=' + squadValue + '&sessionName=' + sessionNameValue + '&venueID=' + sessionVenueValue + '&sessionDay=' + sessionDayValue + '&startTime=' + sessionStartValue + '&endTime=' + sessionEndValue + '&newSessionMS=' + mainSequenceValue + '&newSessionStartDate=' + startDateValue + '&newSessionEndDate=' + endDateValue);
}

// Load squad sessions and add event listener
getSessions();
document.getElementById('squad').onchange = getSessions;