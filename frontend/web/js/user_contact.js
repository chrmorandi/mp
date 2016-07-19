$("#user-contact-type" ).change(function() {
  if ($("#user-contact-type" ).val()==10) {
    $(".setting-label").removeClass('hidden');
  } else {
    $(".setting-label").addClass('hidden');
  }
});
