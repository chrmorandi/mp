var tour;
var steps =[];
var title = 'meeting';
var mode = $('#showGuide').val();

switch (mode) {
  case 'planning':
  tourButtons=jQuery.parseJSON($('#tourButtons').html());
  data=jQuery.parseJSON($('#tour').html());
    tour = new Shepherd.Tour({
      defaults: {
        classes: 'shepherd-theme-arrows',
        showCancelLink: true,
        scrollTo: true,
      }
    });
    $.each(data,function(obj,val) {
      steps.push(
        [
          $('<div/>').html(val[0]).text(),
          $('<div/>').html(val[1]).text(),
          $('<div/>').html(val[2]).text()
        ]
      );
  });
    break;
    case 'participant':
      tour = new Shepherd.Tour({
        defaults: {
          classes: 'shepherd-theme-arrows',
          showCancelLink: true,
          scrollTo: true,
        }
      });
      steps.push(['.nav-tabs top','Welcome','You\'ve been invited to a '+title+'. Let me show you how you can respond. <p>If you prefer, <a href="javascript::return false;" onclick="turnOffGuide();">permanently turn off this guide</a>.<br /><br />']);
      steps.push(['#tourDiscussion top','Share messages with participants ','You can write back and forth with participants on the <strong>Discussion</strong> tab. <p>Messages are delivered via email.</p>']);
      steps.push(['#headingWho top','Who would you like to invite?','You can add one person or a group of people to your '+title+'. <p>Click the person button to add participants.</p>']);
      steps.push(['#invitation-url top','Inviting by email','Alternately, you can copy the planning link and email it to your participant(s)']);
      steps.push(['#headingWhen top','When do you want to meet?','Suggest one or more dates and times for your '+title+'. With more than one, your participants can help you choose. <p>Click the + button to add them.</p>']);
      steps.push(['#headingWhere top','Where do you want to meet?','Suggest one or more places for your '+title+'. With multiple places, your participants can help you choose. <p>We use Google Places to simplify adding them. Click the + button to begin.</p>']);
      steps.push(['.virtualThing top','Is this a virtual meeting?','Switch between <em>in person</em> and <em>virtual</em> '+title+'s such as phone calls or online conferences.']);
      steps.push(['#actionFinalize top','Finalizing the plan','Once you choose a time and place, you can <strong>Complete</strong> the plan. We\'ll email the invitations and setup reminders.']);
      steps.push(['#button-options top','Additional options','From the <strong>Options</strong> menu, you can cancel a '+title+', see the planning history or select preferences for collaboration.']);
      steps.push(['.container ','Ask a question','Need help? <a href="'+$('#url_prefix').val()+'/ticket/create">Ask a question</a> and we\'ll respond as quickly as we can. <p>If you prefer, you can <a href="'+$('#url_prefix').val()+'/user-setting?tab=guide">turn off the guide</a> in settings.</p>']);
      break;
}

if (steps.length>0) {
  for (i = 0; i < steps.length; i++) {
      buttons=[];
      // no back button at the start
      if (i>0) {
        buttons.push({
          text: tourButtons[0],
          classes: 'shepherd-button-secondary',
          action: function() {
            return tour.back();
          }
        });
      }
      // no next button on last step
      if (i!=(steps.length-1)) {
        buttons.push({
          text: tourButtons[1],
          classes: 'shepherd-button-primary',
          action: function() {
            return tour.next();
          }
        });
      } else {
        buttons.push({
          text: tourButtons[2],
          classes: 'shepherd-button-primary',
          action: function() {
            return tour.hide();
          }
        });
      }
      tour.addStep('step_'+i,{
        text: steps[i][2],
        title: steps[i][1],
        attachTo: steps[i][0],
        //classes: 'shepherd shepherd-open shepherd-theme-arrows shepherd-transparent-text',
        buttons: buttons,
      });
  }
  tour.start();
}

function turnOffGuide() {
  $.ajax({
     url: $('#url_prefix').val()+'/user-setting/guide',
     success: function() {
       $("html, body").animate({ scrollTop: 0 }, "slow");
       $('#guide_success').show();
       tour.hide();
       return true;
     }
  });
}
