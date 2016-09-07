$(document).ready(function(){
    // detect user timezone
    var tz = jstz.determine(); // Determines the time zone of the browser client
    var timezone = tz.name(); //e.g. 'Asia/Kolhata'
    $('#tz_dynamic').val(timezone);
    // compare to current setting
    if (timezone != $('#tz_current').val()) {
      // set the text span alert
      $('#tz_new').html('<a onclick="setTimezone(\''+timezone+'\')" href="javascript:void(0);">'+timezone+'</a>');
      $('#tz_alert').show();
    }

    $('input[type="text"]').on('focus',function(){
      $(this).get(0).selectionStart=0;
      $(this).get(0).selectionEnd=999;
  })
  });

function setTimezone(timezone) {
  $.ajax({
     url: $('#url_prefix').val()+'/user-setting/timezone',
     data: {'timezone': timezone},
     success: function(data) {
       $('#tz_alert').hide();
       $('#tz_success').show();
       return true;
     }
  });
}

function toggleOrganizer(id, val) {
  if (val === true) {
    arg2 = 1;
  } else {
    arg2 =0;
  }
  $.ajax({
     url: $('#url_prefix').val()+'/participant/toggleorganizer',
     data: {id: id, val: arg2},
     success: function(data) {
       if (data) {
         if (val===false) {
            $('#star_'+id).addClass("hidden");
            $('#ro_'+id).addClass("hidden");
            $('#mo_'+id).removeClass("hidden");
         } else {
           $('#star_'+id).removeClass("hidden");
           $('#ro_'+id).removeClass("hidden");
           $('#mo_'+id).addClass("hidden");
         }
       }
        return true;
     }
  });
}

function toggleParticipant(id, val, original_status) {
  if (val === true) {
    arg2 = 1;
  } else {
    arg2 =0;
  }
  $.ajax({
     url: $('#url_prefix').val()+'/participant/toggleparticipant',
     data: {id: id, val: arg2, original_status: original_status},
     success: function(data) {
       if (data) {
         if (val===false) {
            $('#rp_'+id).addClass("hidden");
            $('#rstp_'+id).removeClass("hidden");
            $('#btn_'+id).addClass("btn-danger");
            $('#btn_'+id).removeClass("btn-default");
         } else {
           $('#rp_'+id).removeClass("hidden");
           $('#rstp_'+id).addClass("hidden");
           if (original_status==100) {
             $('#btn_'+id).addClass("btn-warning");
             $('#btn_'+id).removeClass("btn-danger");
           } else {
             $('#btn_'+id).addClass("btn-default");
             $('#btn_'+id).removeClass("btn-danger");
           }
         }
       }
        return true;
     }
  });
}

function showWhat() {
  if ($('#showWhat').hasClass( "hidden")) {
    $('#showWhat').removeClass("hidden");
    $('#editWhat').addClass("hidden");
  }else {
    $('#showWhat').addClass("hidden");
    $('#editWhat').removeClass("hidden");
    $('#meeting-subject').select();
  }
};

function cancelWhat() {
  showWhat();
}

function showNote() {
  if ($('#editNote').hasClass( "hidden")) {
    $('#editNote').removeClass("hidden");
  }else {
    $('#editNote').addClass("hidden");
  }
};

function cancelNote() {
  $('#editNote').addClass("hidden");
}

function updateWhat(id) {
  // ajax submit subject and message
  $.ajax({
     url: $('#url_prefix').val()+'/meeting/updatewhat',
     data: {id: id,
        subject: $('#meeting-subject').val(),
        message: $('#meeting-message').val()},
     success: function(data) {
       $('#showWhat').text($('#meeting-subject').val());
       showWhat();
       return true;
     }
     // to do - error display flash
  });
}

function updateNote(id) {
  // ajax submit subject and message
  $.ajax({
     url: $('#url_prefix').val()+'/meeting-note/updatenote',
     data: {id: id,
        note: $('#meeting-note').val()},
     success: function(data) {
       $('#editNote').addClass("hidden");
       $('#meeting-note').val('');
       updateNoteThread(id);
       return true;
     }
     // to do - error display flash
  });
}

function updateNoteThread(id) {
  // ajax submit subject and message
  $.ajax({
     url: $('#url_prefix').val()+'/meeting-note/updatethread',
     data: {id: id},
     type: 'GET',
     success: function(data){
        $('#noteThread').html(data); // data['responseText']
    },
    error: function(error){
    }
  });
  $('#notifierNote').show();
}
