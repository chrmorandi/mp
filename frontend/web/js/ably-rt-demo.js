<script src="//cdn.ably.io/lib/ably.min.js"></script>
<script type="text/javascript">
  var realtime = new Ably.Realtime({key: 'KqTFOw.Av_YnA:dT3V7kmT6jO-T6Ju', clientId: 'apple'});
  var channel = realtime.channels.get('chatroom');
channel.attach(function(err) {
  if(err) { return console.error("Error attaching to the channel"); }
  console.log('We are now attached to the channel');

  channel.presence.update('Comments!!', function(err) {
    if(err) { return console.error("Error updating presence data"); }
    console.log('We have successfully updated our data');
  })
});

channel.presence.get(function(err, members) {
  if(err) { return console.error("Error fetching presence data"); }
  console.log('There are ' + members.length + ' clients present on this channel');
  var first = members[0];
  console.log('The first member is ' + first.clientId);
  console.log('and their data is ' + first.data);

});
channel.presence.subscribe(function(presenceMsg) {
  console.log('Received a ' + presenceMsg.action + ' from ' + presenceMsg.clientId);
  channel.presence.get(function(err, members) {
    console.log('There are now ' + members.length + ' clients present on this channel');
  });
});
</script>
