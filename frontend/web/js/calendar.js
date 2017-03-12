$(document).ready(function() {
  $('td .dayCell').click(function() {
      var div = document.createElement('div');
      $(div).addClass("draggable");
      $(div).css('border','1px solid black');
      $(div).css('display','block');
      $(div).css('height','80px');
      $(div).css('overflow-y','visible');
      $(div).css('max-width','80 px !important');
      $(div).css('border-radius','0px 0px 1em 0px');
      //$(div).attr('id', 'draggable');
      //$(div).css('z-index','100');
      //$(div).css('width','auto-resize');
      $(div).css('background-color','#0066CC');
      $(div).append('Selected');
      $(div).draggable({
        //axis:'y',
        grid: [ 80, 20 ],
        cursor: 'move',
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
      /*$(div).click(function() {
        $(div).addClass("hidden");
        return false;
      });*/
    $(this).append(div);
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
            var element = ui.draggable.detach();
            element.css('width','80px');
            $(element).css('top','0');
            $(element).css('left','0');
            $(this).prepend(element);
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
      height: $('.wrap').height()-100,
      width: $('.wrap').width()-10,
      modal: true,
      position: { within: $('.wrap') }, // my: "left top", at: "left top", of: $('.wrap')
      buttons: {
        Save: function() {
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
