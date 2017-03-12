var items=[];
$(document).ready(function() {
  var loadThese=[1489507200,1489687200,1489863600,1490025600,1490033700,1490038200];
  $.each(loadThese , function(i, val) {
    var div = document.createElement('div');
    $(div).addClass("draggable");
    $(div).css('border','1px solid black');
    $(div).css('display','block');
    $(div).css('height','80px');
    $(div).css('padding','3px');
    $(div).css('overflow-y','visible');
    $(div).css('max-width','80 px !important');
    $(div).css('border-radius','.5em');
    $(div).css('background-color','#6495ed');
    $(div).css('color','#fff');
    $(div).draggable({
      grid: [ 80, 20 ],
      cursor: 'crosshair',
      cursorAt: { top: 200, left: 50 },
      snap:true,
      snapMode:'both',
      snapTolerance:0,
      revert:  function(droppedElement) {
                var validDrop = droppedElement && droppedElement.hasClass("dayCell");
                if (!validDrop) {
                  alert('invalid');
                  var element = $(this);
                  $(element).css('top','0');
                  $(element).css('left','0');
                }
            }
    });
    $(div).click(function() {
      $(div).addClass("hidden");
      items.splice( $.inArray($(div).parent().attr("id"),items) ,1);
      return false;
    });
   items.push('c_'+val);
   $(div).append(calcStr($('#c_'+val)));
   $('#c_'+val).append(div);

  });
  $('td .dayCell').click(function() {
      var div = document.createElement('div');
      $(div).addClass("draggable");
      $(div).css('border','1px solid black');
      $(div).css('display','block');
      $(div).css('height','80px');
      $(div).css('padding','3px');
      $(div).css('overflow-y','visible');
      $(div).css('max-width','80 px !important');
      $(div).css('border-radius','.5em');
      $(div).css('background-color','#6495ed');
      $(div).css('color','#fff');
      $(div).draggable({
        grid: [ 80, 20 ],
        cursor: 'crosshair',
        cursorAt: { top: 200, left: 50 },
        snap:true,
        snapMode:'both',
        snapTolerance:0,
        revert:  function(droppedElement) {
                  var validDrop = droppedElement && droppedElement.hasClass("dayCell");
                  if (!validDrop) {
                    alert('invalid');
                    var element = $(this);
                    $(element).css('top','0');
                    $(element).css('left','0');
                  }
              }
      });
      $(div).click(function() {
        $(div).addClass("hidden");
        items.splice( $.inArray($(div).parent().attr("id"),items) ,1);
        return false;
      });
    $(this).append(div);
     items.push($(this).attr('id'));
    $(div).append(calcStr($(this)));
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
          //alert($(this).children().size());
          if ($(this).children().size()<3) {
            // cell id to add $(this).attr('id')
            items.push($(this).attr('id'));
            // cell id to remove ui.draggable.parent().attr("id")
            items.splice( $.inArray(ui.draggable.parent().attr("id"),items) ,1);
            var element = ui.draggable.detach();
            element.css('width','80px');
            $(element).css('top','0');
            $(element).css('left','0');
            $(this).prepend(element);
            $(element).html(calcStr($(this)));
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
      height: $('.wrap').height()-80,
      width: $('.wrap').width()-10,
      modal: true,
      position: { within: $('.wrap') }, // my: "left top", at: "left top", of: $('.wrap')
      buttons: {
        Save: function() {
          alert(items.toString());
          dialog.dialog( "close" );
        },
        Cancel: function() {
          dialog.dialog( "close" );
        }
      },
      close: function() {
      }
    });
    form = dialog.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      //addUser();
    });

    $( "#create-user" ).button().on( "click", function() {
      $( "#dialog-form" ).width($('.calendarContainer').width()-20);
      $( ".calendarContainer" ).width($(document).width()-30);
      $("table").width($('.calendarContainer').width());
      dialog.dialog( "open" );
      $("tbody").height($('#dialog-form').height()-40);

      //$( 'dialog' ).dialog( "option", "width", '1800px' );

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
