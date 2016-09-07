$(document).ready(function() {
    idstr = '&id='+$('#up_id').val();
    $("#tabsocial").click(function(){
      $('#model_tab').val('social');
      window.history.replaceState( {} , 'foo', '?tab=social'+idstr );
    });
    $("#tabname").click(function(){
      $('#model_tab').val('name');
      window.history.replaceState( {} , 'foo', '?tab=name'+idstr );
    });
    $("#tabphoto").click(function(){
      $('#model_tab').val('photo');
      window.history.replaceState( {} , 'foo', '?tab=photo'+idstr );
    });
    $("#tabusername").click(function(){
      $('#model_tab').val('username');
      window.history.replaceState( {} , 'foo', '?tab=username'+idstr );
    });
});
