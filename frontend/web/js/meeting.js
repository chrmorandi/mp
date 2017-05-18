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
     } else if (mode == 'chooseplace') {
        $('#notifierChoosePlace').show();
      } else if (mode == 'choosetime') {
         $('#notifierChooseTime').show();
   } else if (mode == 'activity') {
      $('#notifierPlace').show();
    } else if (mode == 'chooseactivity') {
       $('#notifierChooseActivity').show();
   } else {
      alert("We\'ll automatically notify the others when you're done making changes.");
    }
    notifierOkay=false;
  }
}

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

// changing meeting-time-choice / availability
$(document).on("change", '[id^="mtc-"]', function (e) {
  //console.log(e.currentTarget.id+' '+e.target.value);
  $.ajax({
     url: $('#url_prefix').val()+'/meeting-time-choice/set',
     data: {id: e.currentTarget.id, 'state': e.target.value},
     // e.target.value is selected MeetingPlaceChoice model
     success: function(data) {
       displayNotifier('time');
       refreshSend();
       refreshFinalize();
       return true;
     }
  });
});

// changing meeting-place-choice / availability
$(document).on("change", '[id^="mpc-"]', function (e) {
  $.ajax({
     url: $('#url_prefix').val()+'/meeting-place-choice/set',
     data: {id: e.currentTarget.id, 'state': e.target.value},
     success: function(data) {
       displayNotifier('place');
       refreshSend();
       refreshFinalize();
       return true;
     }
  });
});

// changing meeting-activity-choice / availability
$(document).on("change", '[id^="mac-"]', function (e) {
  $.ajax({
     url: $('#url_prefix').val()+'/meeting-activity-choice/set',
     data: {id: e.currentTarget.id, 'state': e.target.value},
     success: function(data) {
       displayNotifier('activity');
       refreshSend();
       refreshFinalize();
       return true;
     }
  });
});

// organizer making choices

// respond to change in meeting_place
$(document).on("click", '[id^=btn_mp_]', function(event) {
  current_id = $(this).attr('id');
  $(this).addClass("btn-success");
  $(this).removeClass("btn-default");
  $('[id^=btn_mp_]').each(function(index) {
    if ($(this).attr('id')!=current_id) {
      $(this).addClass("btn-default");
      $(this).removeClass("btn-success");
    }
  });
  $.ajax({
     url: $('#url_prefix').val()+'/meeting-place/choose',
     data: {id:   $('#meeting_id').val(), 'val': current_id},
     success: function(data) {
       displayNotifier('chooseplace');
       refreshSend();
       refreshFinalize();
       return true;
     }
  });
});

// respond to change in meeting_time
$(document).on("click", '[id^=btn_mt_]', function(event) {
  current_id = $(this).attr('id');
  $(this).addClass("btn-success");
  $(this).removeClass("btn-default");
  $('[id^=btn_mt_]').each(function(index) {
    if ($(this).attr('id')!=current_id) {
      $(this).addClass("btn-default");
      $(this).removeClass("btn-success");
    }
  });
  $.ajax({
     url: $('#url_prefix').val()+'/meeting-time/choose',
     data: {id:   $('#meeting_id').val(), 'val': current_id},
     success: function(data) {
       displayNotifier('choosetime');
       refreshSend();
       refreshFinalize();
       return true;
     }
  });
});

// respond to change in meeting_activity
$(document).on("click", '[id^=btn_ma_]', function(event) {
  current_id = $(this).attr('id');
  $(this).addClass("btn-success");
  $(this).removeClass("btn-default");
  $('[id^=btn_ma_]').each(function(index) {
    if ($(this).attr('id')!=current_id) {
      $(this).addClass("btn-default");
      $(this).removeClass("btn-success");
    }
  });
  $.ajax({
     url: $('#url_prefix').val()+'/meeting-activity/choose',
     data: {id:   $('#meeting_id').val(), 'val': current_id},
     success: function(data) {
       displayNotifier('chooseactivity');
       refreshSend();
       refreshFinalize();
       return true;
     }
  });
});

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
    $('#meeting-add-place-favorites').prop("disabled",true);
    $('a#meeting-add-place-favorites').attr("disabled",true);
    $('a#meeting-add-place-favorites').prop('onclick', 'return false;');
    $('#meeting-place-list').addClass("hidden");
    $('#where-choices').addClass("hidden");
    $('.meeting-place-form').addClass("hidden");
    state = 1; // state of these are backwards: true is 0, 1 is false
  } else {
    // change to in person
    $('#meeting-add-place').prop("disabled",false);
    $('a#meeting-add-place').attr('disabled', false);
    $('a#meeting-add-place').prop('onclick', 'showWherePlaces();');
    $('a#meeting-add-place').attr('onclick', 'showWherePlaces();');
    $('#meeting-add-place-favorites').prop("disabled",false);
    $('a#meeting-add-place-favorites').attr("disabled",false);
    $('a#meeting-add-place-favorites').prop('onclick', 'showWhereFavorites();');
    $('a#meeting-add-place-favorites').attr('onclick', 'showWhereFavorites();');
    $('#meeting-place-list').removeClass("hidden");
    $('#where-choices').removeClass("hidden");
    $('.meeting-place-form').removeClass("hidden");
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

function showActivity() {
  if ($('#addActivity').hasClass( "hidden")) {
    $('#addActivity').removeClass("hidden");
    $('.activity-form').removeClass("hidden");
  } else {
    $('#addActivity').addClass("hidden");
    $('.activity-form').addClass("hidden");
  }
};

function cancelActivity() {
  $('#addActivity').addClass("hidden");
  $('.activity-form').addClass("hidden");
}

function addActivity(id) {
    activity = $('#meeting_activity').val();
    // ajax submit subject and message
    $.ajax({
       url: $('#url_prefix').val()+'/meeting-activity/add',
       data: {
         id: id,
        activity: encodeURIComponent(activity),
      },
       success: function(data) {
         $('#meeting_activity').val('');
         loadActivityChoices(id);
         insertActivity(id);
         displayAlert('activityMessage','activityMsg1');
         return true;
       }
    });
    $('#addActivity').addClass('hidden');
  }

  function insertActivity(id) {
    $.ajax({
     url: $('#url_prefix').val()+'/meeting-activity/insertactivity',
     data: {
       id: id,
      },
      type: 'GET',
     success: function(data) {
       $("#possible-activities").removeClass('hidden');
       $("#number_activities").get(0).value++;
       if ($("#number_activities").val()>1) {
          $("#available-activities-msg").removeClass('hidden');
       }
      $("#meeting-activity-list").html(data).removeClass('hidden');
     },
   });
   refreshSend();
   refreshFinalize();
  }

function getActivities(id) {
  $.ajax({
   url: $('#url_prefix').val()+'/meeting-activity/getactivity',
   data: {
     id: id,
    },
    type: 'GET',
   success: function(data) {
     $('#meeting-activity-list').html(data);
   },
 });
}

// toggle add participant panel
function showWhoEmail() {
  if ($('#addParticipantPanel').hasClass( "hidden")) {
    $('#addParticipantHint').addClass("hidden");
    $('#whoFavorites').addClass("hidden");
    $('#whoEmail').removeClass("hidden");
    $('#addParticipantPanel').removeClass("hidden");
  } else {
    // panel showing
    if ($('#whoEmail').hasClass("hidden")) {
      $('#whoFavorites').addClass("hidden");
      $('#whoEmail').removeClass("hidden");
    } else {
        $('#addParticipantPanel').addClass("hidden");
        $('#addParticipantHint').removeClass("hidden");
    }
  }
  clearParticipantMessage(true);
};

function showWhoFavorites() {
    if ($('#addParticipantPanel').hasClass( "hidden")) {
      $('#addParticipantHint').addClass("hidden");
      $('#whoEmail').addClass("hidden");
      $('#whoFavorites').removeClass("hidden");
      $('#participant-emailundefined').attr('placeholder',$('#textChooseFriends').val());
      $('#addParticipantPanel').removeClass("hidden");
    } else {
      // panel showing
      if ($('#whoFavorites').hasClass("hidden")) {
        $('#whoFavorites').removeClass("hidden");
        $('#whoEmail').addClass("hidden");
      } else {
          $('#addParticipantPanel').addClass("hidden");
          $('#addParticipantHint').removeClass("hidden");
      }
    }
    clearParticipantMessage(true);
};

function addParticipant(id,mode='email') {
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
  displayAlert('participantMessage','participantMessageStatus');
  // hide panel
  $('#addParticipantPanel').addClass("hidden");
    $.ajax({
     url: $('#url_prefix').val()+'/participant/add',
     data: {
       id: id,
       add_email:add_email,
      },
     success: function(data) {
       // see remove below
       // update participant buttons - id = meeting_id
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
     $('#addParticipantHint').html('');
   },
 });
}

function closeParticipant() {
  $('#addParticipantPanel').addClass("hidden");
  $('#addParticipantHint').removeClass("hidden");
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
    duration = $('#meetingtime-duration').val();
    repeat_quantity = $('#meetingtime-repeat_quantity').val();
    repeat_unit = $('#meetingtime-repeat_unit').val();
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
        duration:encodeURIComponent(duration),
        repeat_quantity:encodeURIComponent(repeat_quantity),
        repeat_unit:encodeURIComponent(repeat_unit),
      },
       success: function(data) {
         loadTimeChoices(id);
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
       $("#possible-times").removeClass('hidden');
       $("#number_times").get(0).value++;
       if ($("#number_times").val()>1) {
          $("#available-times-msg").removeClass('hidden');
       }
       $("#meeting-time-list").html(data).removeClass('hidden');
     },
   });
   refreshSend();
   refreshFinalize();
  }

  function removeTime(id,timestamp) {
    $.ajax({
     url: $('#url_prefix').val()+'/meeting-time/removetime',
     data: {
       id: id,
      },
      type: 'GET',
     success: function(data) {
      $("#meeting-time-list").html(data).removeClass('hidden');
       // remove the time row
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

function loadTimeChoices(id) {
  $.ajax({
   url: $('#url_prefix').val()+'/meeting-time/loadchoices',
   data: {
     id: id,
    },
    type: 'GET',
   success: function(data) {
     $('#when-choices').html(data);
   },
 });
}

function deliverTimes() {
  // ajax submit subject and message
  meeting_id = $('#meeting_id').val();
  $('#meeting_duration').val(duration);
  $.ajax({
     url: $('#url_prefix').val()+'/meeting-time/addmany',
     data: {
       id: meeting_id,
       times: encodeURIComponent(JSON.stringify(addItems)),
       //removed: encodeURIComponent(JSON.stringify(removeItems)),
       duration:encodeURIComponent(duration),
    },
     success: function(data) {
       loadTimeChoices(meeting_id);
       insertTime(meeting_id);
       // to do - remove times that were removed
       displayAlert('timeMessage','timeMsg1');
       return true;
     }
  });
}

function loadPlaceChoices(id) {
  $.ajax({
   url: $('#url_prefix').val()+'/meeting-place/loadchoices',
   data: {
     id: id,
    },
    type: 'GET',
   success: function(data) {
     $('#where-choices').html(data);
   },
 });
}

function loadActivityChoices(id) {
  $.ajax({
   url: $('#url_prefix').val()+'/meeting-activity/loadchoices',
   data: {
     id: id,
    },
    type: 'GET',
   success: function(data) {
     $('#activity-choices').html(data);
   },
 });
}

// *** meeting place panel ***

// show place panel
function showWherePlaces() {
  if ($('#addPlace').hasClass( "hidden")) {
    $('#whereFavorites').addClass("hidden");
    $('#wherePlaces').removeClass("hidden");
    $('#mapRow').removeClass("hidden");
    $('.where-form').removeClass("hidden");
    $('#addPlace').removeClass("hidden");
  } else {
    // panel showing
    if ($('#wherePlaces').hasClass("hidden")) {
      $('#whereFavorites').addClass("hidden");
      $('#wherePlaces').removeClass("hidden");
      $('#mapRow').removeClass("hidden");
    } else {
      $('#addPlace').addClass( "hidden")
    }
  }
};

function showWhereFavorites() {
  if ($('#addPlace').hasClass( "hidden")) {
    $('#whereFavorites').removeClass("hidden");
    $('#wherePlaces').addClass("hidden");
    $('#mapRow').addClass("hidden");
    $('.where-form').removeClass("hidden");
    $('#addPlace').removeClass("hidden");
  } else {
    // panel showing
    if ($('#whereFavorites').hasClass("hidden")) {
      $('#whereFavorites').removeClass("hidden");
      $('#wherePlaces').addClass("hidden");
      $('#mapRow').addClass("hidden");
    } else {
      $('#addPlace').addClass( "hidden")
    }
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
  if ($('#whereFavorites').hasClass("hidden")) {
    // places mode
    gp_id = $('#meetingplace-google_place_id').val();
    if (gp_id=='') {
        displayAlert('placeMessage','placeMsg2');
        return false;
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
           loadPlaceChoices(id);
           insertPlace(id);
           $('#meetingplace-google_place_id:selected').removeAttr("selected");
           $('#meetingplace-google_place_id').val('');
           $('#meetingplace-google_place_undefined').val('');
           $('#meetingplace-searchbox').val('');
           $('#map-canvas').html('<article></article>');
           displayAlert('placeMessage','placeMsg1');
         }
      });
    }
  } else {
    place_id = $('#meetingplace-place_id').val();
    if (place_id=='') {
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
           loadPlaceChoices(id);
           insertPlace(id);
           displayAlert('placeMessage','placeMsg1');
           return true;
         }
      });
    }
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
    $("#possible-places").removeClass('hidden');
    $("#number_places").get(0).value++;
    if ($("#number_places").val()>1) {
       $("#available-places-msg").removeClass('hidden');
    }
    $("#placeTable").html(data).removeClass('hidden');
   },
 });
 refreshSend();
 refreshFinalize();
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
       tempSubj = $('#meeting-subject').val();
       if ($('#meeting-message').val().length>0) {
         tempSubj=tempSubj+': '+$('#meeting-message').val();
       }
       $('#showWhat span').text(tempSubj);
       showWhat();
     }
  });
}

$('#showWhat span').click(function() {
  showWhat();
});

// *** meeting notes ***

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

function clearParticipantMessage(clearParent=false) {
  if (clearParent) {
    $('#participantMessage').addClass('hidden');
  }
  $('#participantMessageTell').addClass('hidden');
  $('#participantMessageStatus').addClass('hidden');
  $('#participantMessageError').addClass('hidden');
  $('#participantMessageOnlyOne').addClass("hidden");
  $('#participantMessageNoEmail').addClass("hidden");
}

function displayAlert(alert_id,msg_id) {
  // which alert box i.e. which panel alert
  switch (alert_id) {
    case 'noteMessage':
      // which msg to display
      switch (msg_id) {
        case 'noteMessage1':
        $('#noteMessage1').removeClass('hidden');
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
      clearParticipantMessage();
      // which msg to display
      switch (msg_id) {
        case 'participantMessageStatus':
          $('#participantMessageStatus').removeClass('hidden');
          $('#participantMessage').removeClass('hidden').addClass('alert-info').removeClass('alert-danger alert-success');
        break;
        case 'participantMessageTell':
        $('#participantMessageTell').removeClass('hidden');
        $('#participantMessage').removeClass('hidden').addClass('alert-success').removeClass('alert-info alert-danger');
        break;
        case 'participantMessageError':
        $('#participantMessageError').removeClass("hidden");
        $('#participantMessage').removeClass("hidden").removeClass('alert-info').addClass('alert-danger alert-success');
        break;
        case 'participantMessageNoEmail':
        $('#participantMessageNoEmail').removeClass("hidden");
        $('#participantMessage').removeClass("hidden").removeClass('alert-info').addClass('alert-danger alert-success');
        break;
        case 'participantMessageOnlyOne':
        $('#participantMessageOnlyOne').removeClass("hidden");
        $('#participantMessage').removeClass("hidden").removeClass('alert-info').addClass('alert-danger alert-success');
        break;
      }
    break;
    case 'placeMessage':
      // which msg to display
      $('#placeMsg1').addClass('hidden');
      $('#placeMsg2').addClass('hidden');
      $('#placeMsg3').addClass('hidden');
      switch (msg_id) {
        case 'placeMsg1':
          $('#placeMsg1').removeClass('hidden');
          $('#placeMessage').removeClass('hidden').addClass('alert-info').removeClass('alert-danger');
        break;
        case 'placeMsg2':
          $('#placeMsg2').removeClass('hidden');
          $('#placeMessage').removeClass('hidden').removeClass('alert-info').addClass('alert-danger');
        break;
      }
    break;
    case 'timeMessage':
      // which msg to display
      $('#timeMsg1').addClass('hidden');
      $('#timeMsg2').addClass('hidden');
      //$('#timeMsg3').addClass('hidden');
      switch (msg_id) {
        case 'timeMsg1':
          $('#timeMsg1').removeClass('hidden');
          $('#timeMessage').removeClass('hidden').addClass('alert-info').removeClass('alert-danger');
        break;
        case 'timeMsg2':
          $('#timeMsg2').removeClass('hidden');
          $('#timeMessage').removeClass('hidden').removeClass('alert-info').addClass('alert-danger');
        break;
      }
    break;
    case 'activityMessage':
      // which msg to display
      $('#activityMsg1').addClass('hidden');
      $('#activityMsg2').addClass('hidden');
      $('#activityMsg3').addClass('hidden');
      switch (msg_id) {
        case 'activityMsg1':
          $('#activityMsg1').removeClass('hidden');
          $('#activityMessage').removeClass('hidden').addClass('alert-info').removeClass('alert-danger');
        break;
        case 'activityMsg2':
          $('#activityMsg2').removeClass('hidden');
          $('#activityMessage').removeClass('hidden').removeClass('alert-info').addClass('alert-danger');
        break;
      }
    break;
  }
}

function showPossible(id) {
  if ($("#"+id).hasClass('hidden')) {
    $("#"+id).removeClass('hidden');
  } else {
      $("#"+id).addClass('hidden');
  }

}
