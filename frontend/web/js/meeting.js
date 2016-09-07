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

// automatic timezones
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

// participant button commands
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

// change participant status
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

// show the panel subject/message panel
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

// toggle add participant panel
function showParticipant() {
  if ($('#addParticipantPanel').hasClass( "hidden")) {
    $('#addParticipantPanel').removeClass("hidden");
  }else {
    $('#addParticipantPanel').addClass("hidden");
  }
};

function addParticipant(id) {
  // ajax add participant
  // adding someone from new_email
  new_email = $('#new_email').val();
  friend_id = $('#participant-email').val();
  friend_email = $('#participant-email :selected').text();
  // adding from friends
  if ((new_email!='') && (friend_email!='type or click to choose friends' && friend_email!='')) {
      alert('Please choose to add one or the other.');
      return false;
  } else if (new_email!='' && new_email!==undefined) {
    add_email = new_email;
  } else if (friend_email!='type or click to choose friends' && friend_email!='') {
    add_email = friend_email;
  } else {
    alert('Please provide at least one email.');
    return false;
  }
    $.ajax({
     url: $('#url_prefix').val()+'/participant/add',
     data: {
       id: id,
       add_email:add_email,
      },
     success: function(data) {
       // see remove below
       // to do - display acknowledgement
       // update participant buttons - id = meeting_id
       // hide panel
       $('#addParticipantPanel').addClass("hidden");
       if (data === false) {
         // show error, hide tell
         $('#participantNotifierTell').addClass("hidden");
         $('#participantNotifierError').removeClass("hidden");
         $('#participantNotifier').removeClass("hidden");
       } else {
         // clear form
         $('#new_email').val('');
         $('#participant-email').val('');
         // odd issue with resetting the combo box
         $('#participant-emailundefined').val('');
         // // show tell, hide error
         $('#participantNotifierTell').removeClass("hidden");
         $('#participantNotifierError').addClass("hidden");
         $('#participantNotifier').removeClass("hidden");
         getParticipantButtons(id);
      }
       return true;
     }
     // to do - error display flash
  });
}

function getParticipantButtons(id) {
  $.ajax({
   url: $('#url_prefix').val()+'/participant/getbuttons',
   data: {
     id: id,
    },
    type: 'GET',
   success: function(data) {
     $('#participantButtons').html(data);
   },
 });
}

function closeParticipant() {
  $('#addParticipantPanel').addClass("hidden");
}

function showTime() {
  if ($('#addTime').hasClass( "hidden")) {
    $('#addTime').removeClass("hidden");
  }else {
    $('#addTime').addClass("hidden");
  }
};

function cancelTime() {
  $('#addTime').addClass("hidden");
}

function getTimes(id) {
  $.ajax({
   url: $('#url_prefix').val()+'/meeting-time/gettimes',
   data: {
     id: id,
    },
    type: 'GET',
   success: function(data) {
     $('#timeList').html(data);
   },
 });
}

function showPlace() {
  if ($('#meeting-place-list').hasClass( "hidden")) {
    $('#meeting-place-list').removeClass("hidden");
  }else {
    $('#meeting-place-list').addClass("hidden");
  }
};

function cancelPlace() {
  $('#addPlace').addClass("hidden");
}

function getPlaces(id) {
  $.ajax({
   url: $('#url_prefix').val()+'/meeting-place/getplaces',
   data: {
     id: id,
    },
    type: 'GET',
   success: function(data) {
     $('#placeList').html(data);
   },
 });
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
