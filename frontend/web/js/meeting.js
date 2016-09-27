$(document).ready(function(){
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

    $('input[type="text"]').on('focus',function(){
      $(this).get(0).selectionStart=0;
      $(this).get(0).selectionEnd=999;
  })
  });

  // automatic timezones
  function setTimezoneInMtg(timezone) {
    // function suffix inMtg to distinguish from meeting_time.js
    $.ajax({
       url: $('#url_prefix').val()+'/user-setting/timezone/',
       data: {'timezone': timezone},
       success: function(data) {
         $('#tz_alert').hide();
         $('#tz_success').show();
         return true;
       }
    });
  }

// notifier code
  var notifierOkay; // meeting sent already and no page change session flash
  if  ($('#notifierOkay').val() == 'on') {
    notifierOkay = true;
  } else {
    notifierOkay = false;
  }

  function displayNotifier(mode) {
    if (notifierOkay) {
      if (mode == 'time') {
        $('#notifierTime').show();
      } else if (mode == 'place') {
         $('#notifierPlace').show();
       } else {
        alert("We\'ll automatically notify the organizer when you're done making changes.");
      }
      notifierOkay=false;
    }
  }

// refresh Send and Finalize

  function refreshSend() {
    $.ajax({
       url: $('#url_prefix').val()+'/meeting/cansend',
       data: {id: $('#meeting_id').val(), 'viewer_id':$('#viewer').val() },
       success: function(data) {
         if (data)
           $('#actionSend').removeClass("disabled");
          else
          $('#actionSend').addClass("disabled");
         return true;
       }
    });
  }

  function refreshFinalize() {
    $.ajax({
       url: $('#url_prefix').val()+'/meeting/canfinalize',
       data: {id: $('#meeting_id').val(), 'viewer_id': $('#viewer').val()},
       success: function(data) {
         if (data)
           $('#actionFinalize').removeClass("disabled");
          else
          $('#actionFinalize').addClass("disabled");
         return true;
       }
    });
  }

// meeting places

// switch virtual or in person
// users can say if a place is an option for them
$('input[name="meeting-switch-virtual"]').on('switchChange.bootstrapSwitch', function(e, s) {
  //console.log(e.target.id,s); // true | false
  // set intval to pass via AJAX from boolean state
  if (!s) {
    // change to virtual
    $('#meeting-add-place').prop("disabled",true);
    $('a#meeting-add-place').attr('disabled', true);
    $('a#meeting-add-place').prop('onclick', 'return false;');
    $('#meeting-place-list').addClass("hidden");
    state = 1; // state of these are backwards: true is 0, 1 is false
  } else {
    // change to in person
    $('#meeting-add-place').prop("disabled",false);
    $('a#meeting-add-place').attr('disabled', false);
    $('a#meeting-add-place').prop('onclick', 'showPlace();');
    $('a#meeting-add-place').attr('onclick', 'showPlace();');
    $('#meeting-place-list').removeClass("hidden");
    state =0; // state of these are backwards: true is 0, 1 is false
  }
  $.ajax({
     url: $('#url_prefix').val()+'/meeting/virtual',
     data: {id: $('#meeting_id').val(), 'state': state},
     success: function(data) {
       displayNotifier('place');
       refreshSend();
       refreshFinalize();
       return true;
     }
  });
});

// delegated events
  $(document).on('switchChange.bootstrapSwitch', function(e, s) {
    // console.log(e.target.value); // true | false
    if (e.target.name=="place-chooser") {
      if (s) {
        state = 1;
      } else
      {
        state =0;
      }
      $.ajax({
         url: $('#url_prefix').val()+'/meeting-place/choose',
         data: {id:   $('#meeting_id').val(), 'val': e.target.value},
         success: function(data) {
           displayNotifier('place');
           refreshSend();
           refreshFinalize();
           return true;
         }
      });
    } else if (e.target.name=="time-chooser") {
      if (s) {
        state = 1;
      } else
      {
        state =0;
      }
      $.ajax({
         url: $('#url_prefix').val()+'/meeting-time/choose',
         data: {id:   $('#meeting_id').val(), 'val': e.target.value},
         success: function(data) {
           displayNotifier('time');
           refreshSend();
           refreshFinalize();
           return true;
         }
      });
    } else if (e.target.id.match("^mpc-") ) {
      // turn on mpc for user
      // mpc- prefix is for meeting place choices
      if (s) {
        state = 1;
      } else
      {
        state =0;
      }
      $.ajax({
         url: $('#url_prefix').val()+'/meeting-place-choice/set',
         data: {id: e.target.id, 'state': state},
         // e.target.value is selected MeetingPlaceChoice model
         success: function(data) {
           displayNotifier('place');
           refreshSend();
           refreshFinalize();
           return true;
         }
      });
    } else if (e.target.id.match("^mtc-") ) {
      if (s) {
        state = 1;
      } else
      {
        state =0;
      }
      // mtc- prefix is for meeting time choices
      $.ajax({
         url: $('#url_prefix').val()+'/meeting-time-choice/set',
         data: {id: e.target.id, 'state': state},
         // e.target.value is selected MeetingPlaceChoice model
         success: function(data) {
           displayNotifier('time');
           refreshSend();
           refreshFinalize();
           return true;
         }
      });
    }
  });

// participant button commands

// toggle a participant as an organizer
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

// change participant status between remove, withdraw, attending
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

// show the message at top of what subject panel
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
  friend_id = $('#participant-email').val(); // also an email. blank before selection
  friend_email = $('#participant-email :selected').text();  // placeholder text before select
  // adding from friends
  if (new_email!='' && (friend_id !== undefined && friend_id!='')) {
      displayAlert('participantMessage','participantMessageOnlyOne');
      return false;
  } else if (new_email!='' && new_email!==undefined) {
    add_email = new_email;
  } else if (friend_id!='') {
    add_email = friend_email;
  } else {
    displayAlert('participantMessage','participantMessageNoEmail');
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
         displayAlert('participantMessage','participantMessageError');
         return false;
       } else {
         // clear form
         $('#new_email').val('');
         // odd issue with resetting the combo box
         $("#participant-email:selected").removeAttr("selected");
         $("#participant-email").val('');
         $("#participant-emailundefined").val('');
        // show tell, hide error
         getParticipantButtons(id);
         displayAlert('participantMessage','participantMessageTell');
         refreshSend();
         refreshFinalize();
         return true;
      }
     }
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

// meeting time
//show the panel
function showTime() {
  if ($('#addTime').hasClass( "hidden")) {
    $('#addTime').removeClass("hidden");
    $('.when-form').removeClass("hidden");
  }else {
    $('#addTime').addClass("hidden");
    $('.when-form').addClass("hidden");
  }
};

function cancelTime() {
  $('#addTime').addClass("hidden");
  $('.when-form').addClass("hidden");
}

function addTime(id) {
    start_time = $('#meetingtime-start_time').val();
    start = $('#meetingtime-start').val();
    if (start_time =='' || start=='') {
      displayAlert('timeMessage','timeMsg2');
      return false;
    }
    // ajax submit subject and message
    $.ajax({
       url: $('#url_prefix').val()+'/meeting-time/add',
       data: {
         id: id,
        start_time: encodeURIComponent(start_time),
        start:encodeURIComponent(start),
      },
       success: function(data) {
         //$('#meeting-note').val('');
         insertTime(id);
         displayAlert('timeMessage','timeMsg1');
         return true;
       }
    });
    $('#addTime').addClass('hidden');
  }

  function insertTime(id) {
    $.ajax({
     url: $('#url_prefix').val()+'/meeting-time/inserttime',
     data: {
       id: id,
      },
      type: 'GET',
     success: function(data) {
      $("#meeting-time-list").html(data).removeClass('hidden');
       $("input[name='time-chooser']").map(function(){
          //$(this).bootstrapSwitch();
          $(this).bootstrapSwitch('onText','<i class="glyphicon glyphicon-ok"></i>&nbsp;choose');
          $(this).bootstrapSwitch('offText','<i class="glyphicon glyphicon-remove"></i>');
          $(this).bootstrapSwitch('onColor','success');
          $(this).bootstrapSwitch('handleWidth',70);
          $(this).bootstrapSwitch('labelWidth',10);
          $(this).bootstrapSwitch('size','small');
        });
        $("input[name='meeting-time-choice']").map(function(){
          //$(this).bootstrapSwitch();
          $(this).bootstrapSwitch('onText','<i class="glyphicon glyphicon-thumbs-up"></i>&nbsp;yes');
          $(this).bootstrapSwitch('offText','<i class="glyphicon glyphicon-thumbs-down"></i>&nbsp;no');
          $(this).bootstrapSwitch('onColor','success');
          $(this).bootstrapSwitch('offColor','danger');
          $(this).bootstrapSwitch('handleWidth',50);
          $(this).bootstrapSwitch('labelWidth',10);
          $(this).bootstrapSwitch('size','small');
        });
     },
   });
   refreshSend();
   refreshFinalize();
  }

function getTimes(id) {
  $.ajax({
   url: $('#url_prefix').val()+'/meeting-time/gettimes',
   data: {
     id: id,
    },
    type: 'GET',
   success: function(data) {
     $('#meeting-time-list').html(data);
   },
 });
}

// meeting place panel
// show place panel
function showPlace() {
  if ($('#addPlace').hasClass( "hidden")) {
    $('#addPlace').removeClass("hidden");
    $('.where-form').removeClass("hidden");
  } else {
    $('#addPlace').addClass("hidden");
    $('.where-form').addClass("hidden");
  }
};

function cancelPlace() {
  $('#addPlace').addClass("hidden");
  $('.where-form').addClass("hidden");
}

function addPlace(id) {
  //var clonedRow = $("#placeTable>tbody tr:last").clone(); //this will grab the lasttable row.
  //$("#placeTable tbody>tr:last").append(clonedRow);
  //return;
  place_id = $('#meetingplace-place_id').val();
  gp_id = $('#meetingplace-google_place_id').val();
  if ((place_id=='') && (gp_id=='')) {
      displayAlert('placeMessage','placeMsg2');
      return false;
  }
  if (typeof place_id !== 'undefined' && place_id) {
    $.ajax({
       url: $('#url_prefix').val()+'/meeting-place/add',
       data: {
         id: id,
         place_id: place_id,
       },
       success: function(data) {
         // clear fields
         // odd issue with resetting the combo box
         $('#meetingplace-place_id:selected').removeAttr("selected");
         $('#meetingplace-place_id').val('');
         $('#meetingplace-place_idundefined').val('');
         insertPlace(id);
         displayAlert('placeMessage','placeMsg1');
       }

    });
  }
  if (gp_id!='') {
    gp=[];
    gp['name']= $('#meetingplace-name').val();
    gp['location']= $('#meetingplace-location').val();
    gp['website']= $('#meetingplace-website').val();
    gp['vicinity']= $('#meetingplace-vicinity').val();
    gp['full_address']= $('#meetingplace-full_address').val();
    $.ajax({
       url: $('#url_prefix').val()+'/meeting-place/addgp',
       data: {
         id: id,
         gp_id: encodeURIComponent(gp_id),
         name: encodeURIComponent(gp['name']),
         location: encodeURIComponent(gp['location']),
         website: encodeURIComponent(gp['website']),
         vicinity: encodeURIComponent(gp['vicinity']),
         full_address: encodeURIComponent(gp['full_address']),
       },
       success: function(data) {
         // clear fields
         // odd issue with resetting the combo box
         insertPlace(id);
         $('#meetingplace-google_place_id:selected').removeAttr("selected");
         $('#meetingplace-google_place_id').val('');
         $('#meetingplace-google_place_undefined').val('');
         displayAlert('placeMessage','placeMsg1');
       }
    });
  }

  $('#addPlace').addClass('hidden');
}

function insertPlace(id) {
  $.ajax({
   url: $('#url_prefix').val()+'/meeting-place/insertplace',
   data: {
     id: id,
    },
    type: 'GET',
   success: function(data) {
    $("#placeTable").html(data).removeClass('hidden');
     $("input[name='place-chooser']").map(function(){
        //$(this).bootstrapSwitch();
        $(this).bootstrapSwitch('onText','<i class="glyphicon glyphicon-ok"></i>&nbsp;choose');
        $(this).bootstrapSwitch('offText','<i class="glyphicon glyphicon-remove"></i>');
        $(this).bootstrapSwitch('onColor','success');
        $(this).bootstrapSwitch('handleWidth',70);
        $(this).bootstrapSwitch('labelWidth',10);
        $(this).bootstrapSwitch('size','small');
      });
      $("input[name='meeting-place-choice']").map(function(){
        //$(this).bootstrapSwitch();
        $(this).bootstrapSwitch('onText','<i class="glyphicon glyphicon-thumbs-up"></i>&nbsp;yes');
        $(this).bootstrapSwitch('offText','<i class="glyphicon glyphicon-thumbs-down"></i>&nbsp;no');
        $(this).bootstrapSwitch('onColor','success');
        $(this).bootstrapSwitch('offColor','danger');
        $(this).bootstrapSwitch('handleWidth',50);
        $(this).bootstrapSwitch('labelWidth',10);
        $(this).bootstrapSwitch('size','small');
      });
   },
 });
 refreshSend();
 refreshFinalize();
}

// meeting notes
// add panel
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

// meeting subject panel
function updateWhat(id) {
  // ajax submit subject and message
  $.ajax({
     url: $('#url_prefix').val()+'/meeting/updatewhat',
     data: {id: id,
        subject: $('#meeting-subject').val(),
        message: $('#meeting-message').val()
      },
     success: function(data) {
       $('#showWhat').text($('#meeting-subject').val());
       showWhat();
     }
  });
}

function updateNote(id) {
  note = $('#meeting-note').val();
  if (note =='') {
    displayAlert('noteMessage','noteMessage2');
    return false;
  }
  // ajax submit subject and message
  $.ajax({
     url: $('#url_prefix').val()+'/meeting-note/updatenote',
     data: {id: id,
      note: note},
     success: function(data) {
       $('#editNote').addClass("hidden");
       $('#meeting-note').val('');
       updateNoteThread(id);
       displayAlert('noteMessage','noteMessage1');
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
}

  function displayAlert(alert_id,msg_id) {
    // which alert box i.e. which panel alert
    switch (alert_id) {
      case 'noteMessage':
        // which msg to display
        switch (msg_id) {
          case 'noteMessage1':
          $('#noteMessage1').removeClass('hidden'); // will share the note
          $('#noteMessage2').addClass('hidden');
          $('#noteMessage').removeClass('hidden').addClass('alert-info').removeClass('alert-danger');
          break;
          case 'noteMessage2':
          $('#noteMessage1').addClass('hidden');
          $('#noteMessage2').removeClass('hidden'); // no note
          $('#noteMessage').removeClass('hidden').removeClass('alert-info').addClass('alert-danger');
          break;
        }
      break;
      case 'participantMessage':
        // which msg to display
        $('#participantMessageTell').addClass('hidden'); // will share the note
        $('#participantMessageError').addClass('hidden');
        $('#participantMessageOnlyOne').addClass("hidden");
        $('#participantMessageNoEmail').addClass("hidden");
        switch (msg_id) {
          case 'participantMessageTell':
          $('#participantMessageTell').removeClass('hidden'); // will share the note
          $('#participantMessage').removeClass('hidden').addClass('alert-info').removeClass('alert-danger');
          break;
          case 'participantMessageError':
          $('#participantMessageError').removeClass("hidden");
          $('#participantMessage').removeClass("hidden").removeClass('alert-info').addClass('alert-danger');
          break;
          case 'participantMessageNoEmail':
          $('#participantMessageNoEmail').removeClass("hidden");
          $('#participantMessage').removeClass("hidden").removeClass('alert-info').addClass('alert-danger');
          break;
          case 'participantMessageOnlyOne':
          $('#participantMessageOnlyOne').removeClass("hidden");
          $('#participantMessage').removeClass("hidden").removeClass('alert-info').addClass('alert-danger');
          break;
        }
      break;
      case 'placeMessage':
        // which msg to display
        $('#placeMsg1').addClass('hidden'); // will share the note
        $('#placeMsg2').addClass('hidden'); // will share the note
        $('#placeMsg3').addClass('hidden'); // will share the note
        switch (msg_id) {
          case 'placeMsg1':
            $('#placeMsg1').removeClass('hidden'); // will share the note
            $('#placeMessage').removeClass('hidden').addClass('alert-info').removeClass('alert-danger');
          break;
          case 'placeMsg2':
            $('#placeMsg2').removeClass('hidden'); // will share the note
            $('#placeMessage').removeClass('hidden').removeClass('alert-info').addClass('alert-danger');
          break;
        }
      break;
      case 'timeMessage':
        // which msg to display
        $('#timeMsg1').addClass('hidden'); // will share the note
        $('#timeMsg2').addClass('hidden'); // will share the note
        //$('#timeMsg3').addClass('hidden'); // will share the note
        switch (msg_id) {
          case 'timeMsg1':
            $('#timeMsg1').removeClass('hidden'); // will share the note
            $('#timeMessage').removeClass('hidden').addClass('alert-info').removeClass('alert-danger');
          break;
          case 'timeMsg2':
            $('#timeMsg2').removeClass('hidden'); // will share the note
            $('#timeMessage').removeClass('hidden').removeClass('alert-info').addClass('alert-danger');
          break;
        }
      break;
    }
  }
