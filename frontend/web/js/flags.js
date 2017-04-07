$('.flags li > a').click(function(e){
  language = $( this ).attr('id');
  alert(language);
  $.ajax({
     url: $('#url_prefix').val()+'/user-setting/setlanguage',
     data: { 'language': language },
     success: function(data) {
       return true;
     }
  });
  return false;
});
