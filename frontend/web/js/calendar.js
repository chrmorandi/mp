var callbacks = $.Callbacks();
var addItems=[]; // array of timestamps
var removeItems=[]; // array of timestamps
var duration=60; // default duration in minutes
var max_limit = 25;
var loadSolid=[]; // existing, unmovable
var loadFlex=[]; // existing, can be moved and removed
// load pre-existing times
$(document).ready(function() {
  // prepare dialog
  $( function() {
      var dialog, form,
      dialog = $( "#dialog-form" ).dialog({
        autoOpen: false,
        height: $(window).height()-20,
        width: $('.wrap').width()-20,
        modal: true,
        resizable: false,
        draggable: false,
        position: { my: "left top", at: "left top", of: $('body') },
        //position: { within: $('.wrap') }, // my: "left top", at: "left top", of: $('.wrap')
        buttons: [
              {
                  id: "Save",
                  text: $('#buttonSave').val(),
                  click: function () {
                      callbacks.fire();
                      dialog.dialog( "close" );
                  }
              },
              {
                  id: "Cancel",
                  text: $('#buttonCancel').val(),
                  click: function () {
                      $(this).dialog('close');
                  }
              }
            ],
        close: function() {
          $('body').removeClass('stop-scrolling');
          $('.resizable').each(function() {
           $(this).remove();
         });
         // reset addItems
          addItems.length=0;
          loadSolid.length=0;
          loadFlex.length=0;
        },
        open: function() {
          $('.ui-dialog').css({
              'left':'0px',
              'width':'100%',
              'height':'100%'
          });
          $( ".calendarContainer" ).width($(document).width()-10);
          $( ".calendarChooser #dialog-form" ).width($('.calendarContainer').width()-20);
          $(".calendarChooser table").width($('.calendarContainer').width());
          $(".calendarChooser tbody").height($('#dialog-form').height()-40);
          $('.calendarChooser tbody').scroll(function(e) { //detect a scroll event on the tbody
            /* Setting the thead left value to the negative valule of tbody.scrollLeft will make it track the movement
            of the tbody element. Setting an elements left value to that of the tbody.scrollLeft left makes it maintain
            it's relative position at the left of the table. */
            $('.calendarChooser thead').css("left", -$(".calendarChooser tbody").scrollLeft()); //fix the thead relative to the body scrolling
            $('.calendarChooser thead th:nth-child(1)').css("left", $(".calendarChooser tbody").scrollLeft()); //fix the first cell of the header
            $('.calendarChooser tbody td:nth-child(1)').css("left", $(".calendarChooser tbody").scrollLeft()); //fix the first column of tdbody
            });
          $('body').addClass('stop-scrolling');
          $(document).on('touchmove', function(e) {
              if (!$(e.target).parents('#dialog-form')[0]) {
                if ($('body').hasClass('stop-scrolling')) {
                  e.preventDefault(); // block touchmove on parents when dialog open
                }
              }
          });
          loadExistingTimes();
            $(this).closest(".ui-dialog")
            .find(".ui-dialog-titlebar-close")
            .html("<span class='ui-icon ui-icon-closethick'></span>");
        }
      });
      form = dialog.find( "form" ).on( "submit", function( event ) {
        event.preventDefault();
      });
      $( "#buttonTime" ).button().on( "click", function() {
        dialog.dialog( "open" );
        callbacks.add( deliverTimes );
      });
    });

  // add timeslot to cell when clicked
  $('td .dayCell').click(function() {
      if ($('.draggable').length>max_limit) {
        alert ('Sorry, we have a limit on the number of date times per meeting.');
        return;
      }
      // don't allow two items in a cell
      if ($(this).html()=='') {
        addTimeslot($(this));
      }
  });

  $(function() {
    $( ".dayCell" ).droppable({
      classes: {
      'accept': ".flexibles",
      'ui-droppable-hover': "ui-state-active",
      //'ui-droppable-active': "ui-state-hover"
      },
      tolerance: "pointer",
        drop: function( event, ui ) {
          // if less than three elements in a cell
          if ($(this).children().size()==0) {
            // cell id to add: $(this).attr('id')
            addItems.push($(this).attr('id'));
            // cell id to remove: ui.draggable.parent().attr("id")
            addItems.splice( $.inArray(ui.draggable.parent().attr("id"),addItems) ,1);
            removeItems.push(ui.draggable.parent().attr("id"));
            // move the draggable to the droppable cell
            var element = ui.draggable.detach();
            $(element).css('top','0');
            $(element).css('left','0');
            //element.css('width','79.5px');
            $(this).prepend(element);
            // update the text label
            $(element).html(calcStr($(this)));
            attachHandle(element);
          } else {
            // return to base
            var element = ui.draggable;
            $(element).css('top','0');
            $(element).css('left','0');
          }
      }
    });
  });
});

  function addTimeslot(objParent,slotType='new') {
      var div = document.createElement('div');
      if (slotType=='new') {
        // click and remove
        $(div).click(function(e) {
          y= getVertical($(div),e);
          // ignore bottom area clicks
          if (y<95) {
            $(div).addClass("hidden");
            addItems.splice( $.inArray($(div).parent().attr("id"),addItems) ,1);
            $(div).remove();
          }
          return false;
        });
        $(div).addClass("draggable");
        $(div).addClass("flexibles");
        if ($('.flexibles').length==0) {
          $(div).css('height',duration/15*20);
        } else {
          $(div).css('height',$('.flexibles').first().height()+8);
        }
        $(div).draggable({
          grid: [ 80, 20 ],
          cursor: 'crosshair',
          cursorAt: { top: 200, left: 50 },
          snap:true,
          snapMode:'inner',
          snapTolerance:20,
          revert:  function(droppedElement) {
              var validDrop = droppedElement && droppedElement.hasClass("dayCell");
              if (!validDrop) {
                var element = objParent;
                $(element).css('top','0');
                $(element).css('left','0');
              }
          }
        });
        // end new slots
      }  else {
          $(div).css('height',duration/15*20);
          $(div).click(function(e) {
            y= getVertical($(div),e);
            // ignore bottom area clicks
            if (y<90) {
              alert('Sorry, at this time we do not support removing existing times from the calendar. Organizers may be able to remove them from the meeting page.');
              return false;
            }
          });

      }
      addStyles($(div),slotType);
      attachHandle(div);
      objParent.append(div);
      if (objParent.attr('id')!=null) {
        addItems.push(objParent.attr('id'));
      }
      if ($('#'+objParent.attr('id')).length > 0){
        $(div).prepend(calcStr(objParent));
      }
  }

// attach drag handle and configure resizable event
function attachHandle(obj) {
  var divHandle = document.createElement('div');
  $(divHandle).addClass("ui-resizable-s");
  $(divHandle).addClass("ui-resizable-handle");
  $(divHandle).addClass("centered");
  var imgHandle = document.createElement('img');
  $(imgHandle).attr("src",  "/img/resize-handle.gif"); // mp/  $('#url_prefix').val()+
  $(divHandle).append($(imgHandle));
  $(obj).append($(divHandle));
  $(obj).addClass("resizable");
  $(obj).resizable({
    alsoResize: ".resizable",
    handles: {'s': $(obj).find('.ui-resizable-s')},
    minWidth:80,
    minHeight:20,
    maxHeight:$(this).parents().height()*8,
    grid: [ 0,$(this).parents().height()/4 ],
    //distance: 10
    stop: function( event, ui ) {
      duration = Math.ceil(($(obj).height()/20*15)/15)*15;
    },
  });
}

// calculate percentage placement of click to remove
// prevents unwanted removals after a resize event
function getVertical(obj,e) {
  var $this = $(obj); // or use $(e.target) in some cases;
  var offset = $this.offset();
  var height = $this.height();
  var posY = offset.top;
  var y = e.pageY-posY;
      y = parseInt(y/height*100,10);
      y = y<0?0:y;
      y = y>100?100:y;
  return y;
}

function addStyles(obj,slotType) {
  $(obj).css('display','block');
  $(obj).css('border','1px solid black');
  $(obj).css('padding','3px');
  $(obj).css('margin','0');
  $(obj).css('overflow-y','visible');
  $(obj).css('min-width','80px !important');
  $(obj).css('max-width','80px !important');
  $(obj).css('min-height','20px');
  $(obj).css('height','80px !important');
  $(obj).css('border-radius','.5em');
  $(obj).css('color','#fff');
  if (slotType=='new') {
    slotColor = '#6495ED';
  } else {
    slotColor = '#A2B5CD';
    $(obj).css('height','80px !important');
  }
  $(obj).css('background-color',slotColor);
}
// calculate the string to display
// to do - change back to displaying hours for DST issue
function calcStr(obj) {
  // takes the cell id and determines hours, minutes, meridian to display
  // *1000 for Date which requires microseconds
  moment = new Date(parseInt((obj.attr('id').split('_')[1])*1000));
  //hours = parseInt(moment.getHours());
  hours = parseInt(obj.attr('id').split('_')[2]);
  minutes = parseInt(moment.getMinutes());
  if (minutes==0) {
    minutes='00';
  }
  if (hours==0) {
    showHours='00';
    meridian = 'am';
  } else if (hours>12) {
    showHours=hours-12;
    meridian = 'pm';
  } else if (hours==12) {
    showHours=12;
    meridian = 'pm';
  } else {
    showHours=hours;
    meridian = 'am';
  }
  displayDate = showHours+':'+minutes+' '+meridian;
  return displayDate;
}

function loadExistingTimes() {
  duration = $('#meeting_duration').val();
  $('td.table-list-first[id^=t_id_]').each(function(index) {
    $timestamp=$(this).attr('id').split('_')[3];
    if ($timestamp>($.now()/1000)) {
        loadSolid.push($timestamp);
    }
  });
  // preload existing timeslots
  // to do load with duration
  $.each(loadSolid , function(i, val) {
    // to do - when loading prior times, they can't be moved, different color
    // shift git
    addTimeslot($('div[id^="c_'+val+'"]'),'solid');
  });

  /*$.each(loadFlex , function(i, val) {
    // to do - when loading prior times, they can't be moved, different color
    addTimeslot($('#c_'+val),'new');
  });
  */
}
