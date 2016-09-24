$(document).ready(function(){
    // detect user timezone
    var tz = jstz.determine(); // Determines the time zone of the browser client
    var timezone = tz.name(); //e.g. 'Asia/Kolhata'
    $('#tz_dynamic').val(timezone);
    // compare to current setting
    if (timezone != $('#tz_combo').val()) {
      // set the text span alert
      $('#tz_new').html('<a onclick="setTimezone(\''+timezone+'\')" href="javascript:void(0);">'+timezone+'</a>');
      $('#tz_alert').show();
    }
  });

function setTimezone(timezone) {
  $.ajax({
     url: $('#url_prefix').val()+'/user-setting/timezone/',
     data: {'timezone': timezone},
     success: function(data) {
       $('#tz_alert').hide();
       $('#tz_success').show();
       $('#tz_combo').val(timezone);
       return true;
     }
  });
}
