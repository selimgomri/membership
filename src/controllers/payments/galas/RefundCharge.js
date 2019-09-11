/**
 * JS code for gala entry refunds
 * 
 * Amounts are shown in pence
 * Amounts entered by users are in decimal an MUST be converted
 */

// Set up a listener to listen for confirm
var modalButton = document.getElementById('modalConfirmButton');
modalButton.addEventListener('click', function(event) {
  console.log(modalButton);

  // Extract the data
  // If any are missing, stop
  var entry = modalButton.dataset.entry;
  var refundAmount = modalButton.dataset.refundAmount;
  var amountRefunded = modalButton.dataset.amountRefunded;
  // TODO GET AMOUNT ALREADY REFUNDED AND COMPARE SERVER SIDE TO AVOID ERRORS

  // On confirm make ajax request to server
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      // On response (JSON), show error state in modal or dismiss modal
      // and update details shown on the page (data supplied in JSON)
      // Updated details must include max refund, total refunded, disabling
      // field and hiding button if no money is refundable
      console.log(this.responseText);
    }
  };
  xhttp.open('POST', <?=json_encode(autoUrl('galas/payments/ajax-refund-handler'))?>, true);
  xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xhttp.send(encodeURI('entry=' + entry + '&refundAmount=' + refundAmount + '&amountRefunded=' + amountRefunded));
});

document.getElementById('entries-list').addEventListener("click", clickPropogation, false);

function clickPropogation(e) {
  if (e.target !== e.currentTarget) {
    var element = e.target;
    //console.log(e.target);
    if (element.classList.contains('refund-button')) {
      console.log(element.dataset.entryId);

      // Get the refund amount
      var refundBox = document.getElementById(element.dataset.entryId + '-refund');
      var refundAmount = 0;
      if (refundBox.value) {
        refundAmount = parseFloat(refundBox.value);
      }
      refundAmount = parseInt(refundAmount*100);
      var maxRefundable = parseFloat(refundBox.dataset.maxRefundable);
      var amountRefunded = parseFloat(refundBox.dataset.amountRefunded);

      document.getElementById('refund-form-' + element.dataset.entryId).classList.remove('was-validated');

      if (isNaN(refundAmount) || refundAmount === 0) {
        document.getElementById(element.dataset.entryId + '-refund-error-warning-box').innerHTML = '<div class="alert alert-warning alert-dismissible fade show" role="alert"><p class="mb-0"><strong>Sorry!</strong></p><p class="mb-0">You cannot make a refund for &pound;0.00.</p><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
      } else if (refundAmount > maxRefundable) {
        document.getElementById(element.dataset.entryId + '-refund-error-warning-box').innerHTML = '<div class="alert alert-warning alert-dismissible fade show" role="alert"><p class="mb-0"><strong>You can\'t refund that much!</strong></p><p>You tried to make a refund for &pound;' + (refundAmount/100).toFixed(2) + ' when the maximum you can refund is &pound;' + (maxRefundable/100).toFixed(2) + '.</p><p class="mb-0">This safeguard is in place to prevent you refunding more than was paid for the gala entry.</p><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

        document.getElementById('refund-form-' + element.dataset.entryId).classList.add('was-validated');
      } else if (refundAmount < 0) {
        document.getElementById(element.dataset.entryId + '-refund-error-warning-box').innerHTML = '<div class="alert alert-warning alert-dismissible fade show" role="alert"><p class="mb-0"><strong>You can\'t make a negative refund!</strong></p><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

        document.getElementById('refund-form-' + element.dataset.entryId).classList.add('was-validated');
      } else {
        // Hide an error if one exists
        document.getElementById(element.dataset.entryId + '-refund-error-warning-box').innerHTML = '';

        // Set modal title
        document.getElementById('myModalTitle').textContent = 'Confirm refund amount';

        // Set modal body content
        document.getElementById('myModalBody').innerHTML = '<p class="mb-0">Please confirm that you want to refund &pound;' + (refundAmount/100).toFixed(2) + ' to <span id="modal-refund-location"></span>.</p>';

        document.getElementById('modal-refund-location').textContent = element.dataset.refundLocation;

        //confirm('Are you sure you want to refund £' + refundBox.value + '? You won\'t be able to modify refunds once you proceed.');
        $('#myModal').modal('show');

        modalButton.dataset.entry = element.dataset.entryId;
        modalButton.dataset.refundAmount = refundAmount;
        modalButton.dataset.amountRefunded = amountRefunded;
      }

      element.blur();
    }
  }
  e.stopPropagation();
}