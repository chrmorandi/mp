$("#tz_new").ready(function(){  
    // detect user timezone
    var tz = jstz.determine(); // Determines the time zone of the browser client
    var timezone = tz.name(); //e.g. 'Asia/Kolhata'
    $('#tz_dynamic').val(timezone);
    // compare to current setting
    if (timezone != $('#tz_current').val()) {
      // set the text span alert
      $('#tz_new').html('<a onclick="setTimezoneInMtg(\''+timezone+'\')" href="javascript:void(0);">'+timezone+'</a>');
      $('#tz_alert').show();
    }
  });
