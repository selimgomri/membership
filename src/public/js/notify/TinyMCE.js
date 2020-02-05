tinymce.init({
  selector: '#message',
  branding: false,
  plugins: [
    'autolink lists link image charmap print preview anchor textcolor',
    'searchreplace visualblocks code autoresize insertdatetime media table',
    'contextmenu paste code help wordcount'
  ],
  paste_as_text: true,
  toolbar: 'insert | undo redo |  formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
  content_css: [
    'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i',
    document.getElementById('message').dataset.tinymceCssLocation
  ]
  //toolbar: "link",
});

window.addEventListener('keydown', function (e) {
  if (e.keyIdentifier == 'U+000A' || e.keyIdentifier == 'Enter' || e.keyCode == 13) {
    if (e.target.nodeName == 'INPUT' && e.target.type == 'text') {
      e.preventDefault(); return false;
    }
  }
}, true);