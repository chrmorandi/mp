var tour;
var steps =[];
var title = 'meeting';
var mode = $('#showGuide').val();

if (mode == 'planning') {
  tour = new Shepherd.Tour({
    defaults: {
      classes: 'shepherd-theme-arrows',
      showCancelLink: true,
      scrollTo: true,
    }
  });
  steps.push(['.nav-tabs top','Welcome','Allow me to show you how to plan a '+title+'. <p>If you prefer, you can <a href="javascript::return false;" onclick="turnOffGuide();">turn off this guide</a>.<br /><br />']);
  steps.push(['#headingWhat top','What is your meeting about?','You can customize the <strong>Subject</strong> for your '+title+'. We\'ll use it for the invitation and reminder emails.<p>Click the pencil button to edit the subject.</p>']);
  steps.push(['#headingWho top','Who would you like to invite?','You can add one person or a group of people to your '+title+'. <p>Click the person button to add participants.</p>']);
  steps.push(['#invitation-url top','Inviting by email','Alternately, you can copy the planning link and email it to your participant(s)']);
  if ($('#headingActivity').length>0) {
    steps.push(['#headingActivity top','What do you want to do?','You can suggest one or more activity ideas. With multiple ideas, your participants can help you select their favorite. <p>Click the plus button to suggest activities.</p>']);
  }
  steps.push(['#headingWhen top','When do you want to meet?','Suggest one or more dates and times for your '+title+'. With more than one, your participants can help you choose. <p>Click the + button to add them.</p>']);
  steps.push(['#headingWhere top','Where do you want to meet?','Suggest one or more places for your '+title+'. With multiple places, your participants can help you choose. <p>We use Google Places to simplify adding them. Click the + button to begin.</p>']);
  steps.push(['.virtualThing top','Is this a virtual meeting?','Switch between <em>in person</em> and <em>virtual</em> '+title+'s such as phone calls or online conferences.']);
  steps.push(['#actionSend top','Sending invitations','Scheduling is collaborative. After you add times and places, you can <strong>Invite</strong> participants to select their favorites. <em>A place isn\'t necessary for virtual '+title+'\s.</em>']);
  steps.push(['#actionFinalize top','Finalizing the plan','Once you choose a time and place, you can <strong>Complete</strong> the plan. We\'ll email the invitations and setup reminders.']);
  steps.push(['#button-options top','Additional options','From the <strong>Options</strong> menu, you can cancel a '+title+', see the planning history or select preferences for collaboration.']);
  steps.push(['#tourDiscussion top','Share messages with participants ','You can write back and forth with participants on the <strong>Discussion</strong> tab. <p>Messages are delivered via email.</p>']);
  steps.push(['.container ','Ask a question','Need help? <a href="'+$('#url_prefix').val()+'/ticket/create">Ask a question</a> and we\'ll respond as quickly as we can. <p>If you prefer, you can <a href="'+$('#url_prefix').val()+'/user-setting?tab=guide">turn off the guide</a> in settings.</p>']);

  for (i = 0; i < steps.length; i++) {
      buttons=[];
      // no back button at the start
      if (i>0) {
        buttons.push({
          text: 'Back',
          classes: 'shepherd-button-secondary',
          action: function() {
            return tour.back();
          }
        });
      }
      // no next button on last step
      if (i!=(steps.length-1)) {
        buttons.push({
          text: 'Next',
          classes: 'shepherd-button-primary',
          action: function() {
            return tour.next();
          }
        });
      } else {
        buttons.push({
          text: 'Close',
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
