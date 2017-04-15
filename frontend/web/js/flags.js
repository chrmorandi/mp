$('#flagTarget').on("click tap",function(e){
  language = $( this ).attr('id');
  if (language=='flagTarget') {
      return true;
  }
  $.ajax({
     url: $('#url_prefix').val()+'/user-setting/setlanguage',
     data: { 'language': language },
     success: function(data) {
       return true;
     }
  });
  return true;
});
