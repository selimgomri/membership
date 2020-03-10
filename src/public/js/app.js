if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register(document.getElementById('app-js-info').dataset.serviceWorkerUrl);
}

// document.querySelector('#show').addEventListener('click', () => {
//   const iconUrl = document.querySelector('select').selectedOptions[0].value;
//   let imgElement = document.createElement('img');
//   imgElement.src = iconUrl;
//   document.querySelector('#container').appendChild(imgElement);
// });