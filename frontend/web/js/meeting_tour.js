var tour;
var steps =[];
var title = 'meeting';

tour = new Shepherd.Tour({
  defaults: {
    classes: 'shepherd-theme-arrows',
    showCancelLink: true,
    scrollTo: true
  }
});
//'+$('#url_prefix').val()+'/user-setting?tab=guide
steps.push(['.nav-tabs top','<strong>Welcome!</strong> Allow me to guide you in planning your '+title+'. <p>If you prefer, you can <a href="javascript::return false;" onclick="turnOffGuide();">turn off this guide</a>.']);
steps.push(['#headingWhat bottom','You can optionally customize the subject of your '+title+' which also appears in emails about the '+title+'. Click the pencil button.']);
steps.push(['#headingWho top','Add one or more participants to your '+title+'. Click the person button.']);
// to do - if this is an activity
steps.push(['#invitation-url bottom','Alternately, you can copy the planning link and email it to participants']);
steps.push(['#headingWhen top','Suggest one or more dates and times for your '+title+'. With more than one, your participants can help you select the best for their schedule. Click the plus button.']);
steps.push(['#headingWhere top','Suggest one or more places for your '+title+'. With more than one, your participants can help you select the best for their schedule. Click the plus button.']);
steps.push(['.virtualThing left','Or, switch from <em>in person</em> to a virtual '+title+'.']);
steps.push(['#actionSend left','Scheduling is collaborative. With multiple times and places, you can invite participants to offer feedback. <em>A place isn\'t necessary for virtual '+title+'\'s.</em>']);
steps.push(['#actionFinalize left','Or, if you select just one time and place, you can finalize the plan. We\'ll email the final invitations.']);
steps.push(['#tourDiscussion left','You can post notes back and forth with participants from the discussion tab. Notes are delivered via email.']);
steps.push(['#button-options left','From the options menu, you can cancel a '+title+', see the planning history or select preferences for collaboration.']);
steps.push(['.container ','Need help? <a href="'+$('#url_prefix').val()+'/ticket/create">Ask a question</a> and we\'ll respond as quickly as we can. <p>If you prefer, you can <a href="'+$('#url_prefix').val()+'/user-setting?tab=guide">turn off the guide</a> in settings.</p>']);

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
      text: steps[i][1],
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
       //displayNotifier('choosetime');
       $("html, body").animate({ scrollTop: 0 }, "slow");
       $('#guide_success').show();
       tour.hide();
       return true;
     }
  });
}
