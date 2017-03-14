var items=[]; // array of timestamps
var duration=60; // default duration in minutes
// load pre-existing times
$(document).ready(function() {
  var loadThese=[];
  $.each(loadThese , function(i, val) {
    var div = document.createElement('div');
    // to do - when loading prior times, they can't be moved, different color
    $(div).addClass("draggable");
    addStyles($(div));
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
            var element = $(this);
            $(element).css('top','0');
            $(element).css('left','0');
          }
      }
    });
    attachHandle(div);
    $(div).click(function(e) {
      y= getVertical($(div),e);
      if (y<95) {
        $(div).addClass("hidden");
        items.splice( $.inArray($(div).parent().attr("id"),items) ,1);
      }
      return false;
    });
   items.push('c_'+val);
   $(div).append(calcStr($('#c_'+val)));
   $('#c_'+val).append(div);
  });
  $('td .dayCell').click(function() {
      var div = document.createElement('div');
      $(div).addClass("draggable");
      if ($('.draggable').length==0) {
        $(div).css('height',$(this).parents().height());
      } else {
        $(div).css('height',$('.draggable').first().height());
      }
      addStyles($(div));
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
              var element = $(this);
              $(element).css('top','0');
              $(element).css('left','0');
            }
        }
      });
      attachHandle(div);
      $(div).click(function(e) {
        y= getVertical($(div),e);
        if (y<95) {
          $(div).addClass("hidden");
          items.splice( $.inArray($(div).parent().attr("id"),items) ,1);
        }
        return false;
      });
    $(this).append(div);
    items.push($(this).attr('id'));
    $(div).prepend(calcStr($(this)));
  });
  $(function() {
    $( ".dayCell" ).droppable({
      classes: {
      'accept': ".draggable",
      //'ui-droppable-active': "ui-state-hover",
      'ui-droppable-hover': "ui-state-active"
      },
      tolerance: "pointer",
        drop: function( event, ui ) {
          // if less than three elements in a cell
          if ($(this).children().size()<3) {
            // cell id to add: $(this).attr('id')
            items.push($(this).attr('id'));
            // cell id to remove: ui.draggable.parent().attr("id")
            items.splice( $.inArray(ui.draggable.parent().attr("id"),items) ,1);
            // move the draggable to the droppable cell
            var element = ui.draggable.detach();
            element.css('width','80px');
            $(element).css('top','0');
            $(element).css('left','0');
            $(this).prepend(element);
            // update the text label
            $(element).html(calcStr($(this)));
            attachHandle(element);
          } else {
            var element = ui.draggable;
            $(element).css('top','0');
            $(element).css('left','0');
          }
      }
    });
  });
});

$( function() {
    var dialog, form,
    dialog = $( "#dialog-form" ).dialog({
      autoOpen: false,
      height: $('.wrap').height()-110,
      width: $('.wrap').width()-10,
      modal: true,
      position: { within: $('.wrap') }, // my: "left top", at: "left top", of: $('.wrap')
      buttons: [
            {
                id: "Save",
                text: "Save times",
                click: function () {
                    alert('Duration: '+duration+' mins. Timestamps:'+items.toString());
                    dialog.dialog( "close" );
                }
            },
            {
                id: "Cancel",
                text: "Cancel",
                click: function () {
                    $(this).dialog('close');
                }
            }
          ],
      close: function() {
      }
    });
    form = dialog.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
    });

    $( "#create-user" ).button().on( "click", function() {
      $( "#dialog-form" ).width($('.calendarContainer').width()-20);
      $( ".calendarContainer" ).width($(document).width()-30);
      $("table").width($('.calendarContainer').width());
      dialog.dialog( "open" );
      $("tbody").height($('#dialog-form').height()-40);
      $('.calendarChooser tbody').scroll(function(e) { //detect a scroll event on the tbody
        /*
        Setting the thead left value to the negative valule of tbody.scrollLeft will make it track the movement
        of the tbody element. Setting an elements left value to that of the tbody.scrollLeft left makes it maintain
        it's relative position at the left of the table.
        */
        $('.calendarChooser thead').css("left", -$(".calendarChooser tbody").scrollLeft()); //fix the thead relative to the body scrolling
        $('.calendarChooser thead th:nth-child(1)').css("left", $(".calendarChooser tbody").scrollLeft()); //fix the first cell of the header
        $('.calendarChooser tbody td:nth-child(1)').css("left", $(".calendarChooser tbody").scrollLeft()); //fix the first column of tdbody
      });
    });
  } );

// attach drag handle and configure resizable event
  function attachHandle(obj) {
    var divHandle = document.createElement('div');
    $(divHandle).addClass("ui-resizable-s");
    $(divHandle).addClass("ui-resizable-handle");
    $(divHandle).addClass("centered");
    var imgHandle = document.createElement('img');
    $(imgHandle).attr("src", "/mp/img/resize-handle.gif");
    $(divHandle).append($(imgHandle));
    $(obj).append($(divHandle));
    $(obj).addClass("resizable");
    $(obj).resizable({
      alsoResize: ".draggable",
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

// calculate the string to display
// to do - change back to displaying hours for DST issue
function calcStr(obj) {
  // *1000 for Date which requires microseconds
  moment = new Date(parseInt(obj.attr('id').split('_')[1])*1000);
  hours = parseInt(moment.getHours());
  //hours = parseInt(obj.attr('id').split('_')[2]);
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

function addStyles(obj) {
  $(obj).css('display','block');
  $(obj).css('border','1px solid black');
  $(obj).css('padding','3px');
  $(obj).css('margin','0');
  $(obj).css('overflow-y','visible');
  $(obj).css('min-width','80 px !important');
  $(obj).css('max-width','80 px !important');
  $(obj).css('border-radius','.5em');
  $(obj).css('background-color','#6495ed');
  $(obj).css('color','#fff');
}
