
var init = true;
$('#meeting-subject').focus(function() {
  if (init ==true) {
    $('#meeting-subject').select();
    init =false;
  }
});
