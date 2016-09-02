$(document).ready(function() {
    $("#tabsocial").click(function(){
      $('#model_tab').val('social');
      window.history.replaceState( {} , 'foo', '?tab=social' );
    });
    $("#tabname").click(function(){
      $('#model_tab').val('name');
      window.history.replaceState( {} , 'foo', '?tab=name' );
    });
    $("#tabphoto").click(function(){
      $('#model_tab').val('photo');
      window.history.replaceState( {} , 'foo', '?tab=photo' );
    });
    $("#tabusername").click(function(){
      $('#model_tab').val('username');
      window.history.replaceState( {} , 'foo', '?tab=username' );
    });
});
