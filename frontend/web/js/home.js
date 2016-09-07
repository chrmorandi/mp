function addLaunchEmail() {
  email = $('#launch_email').val();
  $.ajax({
   url: $('#url_prefix').val()+'/launch/add',
   data: {
     email: email,
    },
    type: 'GET',
   success: function(data) {
     $('#launchResult').removeClass('hidden');
     $('#launch').addClass('hidden');
   },
 });
}
