
  $("#adjust_how" ).change(function() {
    if ($("#adjust_how" ).val()==50) {
      $("#choose_earlier").addClass('hidden');
      $("#choose_another").addClass('hidden');
    } else if ($("#adjust_how" ).val()==60) {
      $("#choose_earlier").removeClass('hidden');
      $("#choose_another").addClass('hidden');
    } else {
      $("#choose_earlier").addClass('hidden');
      $("#choose_another").removeClass('hidden');
    }
  });
