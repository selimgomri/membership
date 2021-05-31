document.addEventListener('DOMContentLoaded', function () {
  // your code here
  $(function () {
    $('[data-bs-toggle="popover"]').popover({
      trigger: 'focus'
    })
  });
}, false);

let gender = document.getElementById('gender-radio');
let pronouns = document.getElementById('gender-pronoun-radio');

if (gender) {
  gender.addEventListener('change', ev => {
    let other = document.getElementById('gender-custom');
    if (ev.target.value === 'O') {
      other.disabled = false;
      other.required = true;
    } else {
      other.disabled = true;
      other.required = false;
      other.value = '';
    }
  });
}

if (pronouns) {
  pronouns.addEventListener('change', ev => {
    let other = document.getElementById('gender-pronoun-custom');
    if (ev.target.value === 'O') {
      other.disabled = false;
      other.required = true;
    } else {
      other.disabled = true;
      other.required = false;
      other.value = '';
    }
  });
}

function showPhotoIfRequired() {
  let date = new Date(photoPermissions.dataset.dateEighteen);

  let dob = new Date(dobField.value);

  if (dob > date) {
    photoPermissions.classList.remove('d-none');
  } else {
    photoPermissions.classList.add('d-none');
  }
}

let photoPermissions = document.getElementById('photography-permissions');
let dobField = document.getElementById('dob');
if (photoPermissions) {
  dobField.addEventListener('change', ev => {
    showPhotoIfRequired();
  });

  showPhotoIfRequired();
}