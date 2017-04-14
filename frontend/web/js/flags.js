$('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { e.stopPropagation(); });

$('#flagTarget').on("tap",function(e){
  alert('i');
  language = $( this ).attr('id');
  $.ajax({
     url: $('#url_prefix').val()+'/user-setting/setlanguage',
     data: { 'language': language },
     success: function(data) {
       return true;
     }
  });
  return true;
});
