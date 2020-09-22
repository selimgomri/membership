let limitRadio = document.getElementById('number-limit');
console.log(limitRadio);

limitRadio.addEventListener('change', event => {
  let maxPlacesContainer = document.getElementById('max-places-container');
  let maxCount = document.getElementById('max-count');
  if (event.target.value == '1') {
    maxPlacesContainer.classList.remove('d-none');
    maxCount.required = true;
  } else {
    maxPlacesContainer.classList.add('d-none');
    maxCount.required = false;
  }
});