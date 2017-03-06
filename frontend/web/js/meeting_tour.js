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
}
//'+$('#url_prefix').val()+'/user-setting?tab=guide
steps.push(['.nav-tabs top','Welcome','Allow me to show you how to plan '+title+'s. <p>If you prefer, you can <a href="javascript::return false;" onclick="turnOffGuide();">turn off this guide</a>.']);
steps.push(['#headingWhat bottom','What is your meeting about?','You can customize the <strong>Subject</strong> line for your '+title+' and related invitation and reminder emails. Click the pencil button to edit the subject.']);
steps.push(['#headingWho top','Who would you like to invite?','Add one or more people to your '+title+' invitation. Click the person button to add participants.']);
steps.push(['#invitation-url bottom','Inviting by email','Alternately, you can copy the planning link and email it to participants']);
// to do - if this is an activity
if ($('#headingActivity').length>0) {
  steps.push(['#headingActivity top','What do you want to do?','Suggest one or more activity ideas. With more than one, your participants can help you select their favorite. Click the plus button to suggest activities.']);
}
steps.push(['#headingWhen top','When do you want to meet?','Suggest one or more dates and times for your '+title+'. With more than one, your participants can help you select the best day and time for their schedule. Click the plus button to add dates and times.']);
steps.push(['#headingWhere top','Where do you want to meet?','Suggest one or more places for your '+title+'. With more than one, your participants can help you select the best place for their schedule. Click the plus button to add places.']);
steps.push(['.virtualThing top','Is this a virtual meeting?','Switch between <em>in person</em> and virtual '+title+'s such as phone calls or online conferences.']);
steps.push(['#actionSend top','Sending invitations','Scheduling is collaborative. With multiple times and places, you can invite participants to offer feedback. <em>A place isn\'t necessary for virtual '+title+'\s.</em>']);
steps.push(['#actionFinalize top','Finalizing the plan','Or, if you select just one time and place, you can finalize the plan. We\'ll email the invitations and setup reminders.']);
steps.push(['#tourDiscussion bottom','Share notes with participants ','You can post notes back and forth with participants from the <strong>Discussion</strong> tab. Notes are delivered via email.']);
steps.push(['#button-options top','Additional options','From the <strong>Options</strong> menu, you can cancel a '+title+', see the planning history or select preferences for collaboration.']);
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
      title: '<strong>'+steps[i][1]+'</strong>',
      attachTo: steps[i][0],
      //classes: 'shepherd shepherd-open shepherd-theme-arrows shepherd-transparent-text',
      buttons: buttons,
    });
}
tour.start();

function turnOffGuide() {
  $.ajax({
     url: $('#url_prefix').val()+'/user-setting/guide',
    // data: {val: 0},
     //data: {id:   $('#meeting_id').val(), 'val': current_id},
     success: function() {
       $("html, body").animate({ scrollTop: 0 }, "slow");
       $('#guide_success').show();       
       tour.hide();
       return true;
     }
  });
}
