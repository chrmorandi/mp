$(document).ready(function() {
  $('tbody').scroll(function(e) { //detect a scroll event on the tbody
  	/*
    Setting the thead left value to the negative valule of tbody.scrollLeft will make it track the movement
    of the tbody element. Setting an elements left value to that of the tbody.scrollLeft left makes it maintain
    it's relative position at the left of the table.
    */
    $('thead').css("left", -$("tbody").scrollLeft()); //fix the thead relative to the body scrolling
    $('thead th:nth-child(1)').css("left", $("tbody").scrollLeft()); //fix the first cell of the header
    $('tbody td:nth-child(1)').css("left", $("tbody").scrollLeft()); //fix the first column of tdbody
  });
  $('td .dayCell').click(function() {
      var div = document.createElement('div');
      $(div).addClass("draggable");
      $(div).css('border','1px solid black');
      //$(div).css('width','auto-resize');
      $(div).css('display','block');
      $(div).css('height','50px');
      $(div).css('overflow-y','visible');
      $(div).css('max-width','80 px !important');
      //$(div).css('z-index','100');
      $(div).css('border-radius','0px 0px 1em 0px');
      //$(div).attr('id', 'draggable');
      $(div).css('background-color','red');
      $(div).append('apple');
      $(div).draggable({
    cursorAt: { left: 1, top: 1, bottom:-20 },
    snap:true,
    snapMode:'inner',
    snapTolerance:1,
    revert:  function(droppedElement) {
              var validDrop = droppedElement && (droppedElement.hasClass("droppable2") || droppedElement.hasClass("apple2"));
              if (!validDrop) {
                alert('invalid');
                var element = $(this);
                $(element).css('top','0');
                $(element).css('left','0');
              }
          }

  });
      $(this).append(div);

      });
      $(function() {
                $( "#draggable, #draggable-nonvalid" ).draggable();
                $( "#droppable" ).droppable({
                  tolerance: "pointer",
                  accept: "#draggable",
                  activeClass: "ui-state-hover",
                  hoverClass: "ui-state-active",
                  drop: function( event, ui ) {
                    $( this )
                      .addClass( "ui-state-highlight" )
                      .find( "p" )
                        .html( "Dropped!" );
                  }
                });
                $( "#droppable2,.apple2" ).droppable({

                  classes: {
                  'accept': ".draggable",
                  'ui-droppable-active': "ui-state-hover",
                  'ui-droppable-hover': "ui-state-active"
                },
                tolerance: "pointer",
                  drop: function( event, ui ) {
                    alert($(this).children().size());
                    if ($(this).children().size()<3) {
                      var element = ui.draggable.detach();
                      element.css('width','auto');
                      element.css('float','left');
                      //element.resizable();
                      $(element).css('top','0');
                      $(element).css('left','0');
                      $(this).prepend(element);
                      $( this )
                        .addClass( "ui-state-highlight" )
                        .find( "p" )
                          .html( "Dropped2!" );
                    } else {
                      var element = ui.draggable;
                      $(element).css('top','0');
                      $(element).css('left','0');
                    }

                  }
                });
              });
});
