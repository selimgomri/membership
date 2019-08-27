/**
 * Common code for CLS Membership Payments
 */

var stripeElementStyle = {
  base: {
    iconColor: '#ced4da',
    lineHeight: '1.5',
    height: '1.5rem',
    color: '#212529',
    fontWeight: 400,
    fontFamily: 'Open Sans, Segoe UI, sans-serif',
    fontSize: '16px',
    fontSmoothing: 'antialiased',
    '::placeholder': {
      color: '#868e96',
    },
  },
  invalid: {
    color: '#212529',
  },
}

function setCardBrandIcon(brand) {
  var content = '<i class="fa fa-fw fa-credit-card" aria-hidden="true"></i>';
  if (brand === 'visa') {
    content = '<i class="fa fa-cc-visa" aria-hidden="true"></i>';
  } else if (brand === 'mastercard') {
    content = '<i class="fa fa-cc-mastercard" aria-hidden="true"></i>';
  } else if (brand === 'amex') {
    content = '<i class="fa fa-cc-amex" aria-hidden="true"></i>';
  } else if (brand === 'discover') {
    content = '<i class="fa fa-cc-discover" aria-hidden="true"></i>';
  } else if (brand === 'diners') {
    content = '<i class="fa fa-cc-diners-club" aria-hidden="true"></i>';
  } else if (brand === 'jcb') {
    content = '<i class="fa fa-cc-jcb" aria-hidden="true"></i>';
  }
  document.getElementById('card-brand-element').innerHTML = content;
}

function disableButtons() {
  document.querySelectorAll('.pm-can-disable').forEach(elem => {
    elem.disabled = true;
  });
}

function enableButtons() {
  document.querySelectorAll('.pm-can-disable').forEach(elem => {
    elem.disabled = false;
  });
}