<div id="myCarousel" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
    <li data-target="#myCarousel" data-slide-to="1"></li>
    <li data-target="#myCarousel" data-slide-to="2"></li>
    <li data-target="#myCarousel" data-slide-to="3"></li>
    <li data-target="#myCarousel" data-slide-to="4"></li>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
    <div class="item active">
      <img src="<?= $urlPrefix ?>/img/carousel-1.jpg" alt="adding participants to meetings">
      <div class="carousel-caption">
        <h3>Adding Participants</h3>
        <p>Add one or many people to a meeting.</p>
      </div>
    </div>

    <div class="item">
      <img src="<?= $urlPrefix ?>/img/carousel-2.jpg" alt="choosing times and places">
      <div class="carousel-caption">
        <h3>Choosing Times and Places</h3>
        <p>Adding dates, times and places is fast and easy.</p>
      </div>
    </div>

    <div class="item">
      <img src="<?= $urlPrefix ?>/img/carousel-3.jpg" alt="choose date times and places">
      <div class="carousel-caption">
        <h3>Choose the Date, Time and Place Together</h3>
        <p>Finalize the time and place based on particpant's availability.</p>
      </div>
    </div>

    <div class="item">
      <img src="<?= $urlPrefix ?>/img/carousel-4.jpg" alt="download to your calendar">
      <div class="carousel-caption">
        <h3>Add to Your Calendar</h3>
        <p>Everyone will receive a calendar entry via email or can download it.</p>
      </div>
    </div>
    <div class="item">
      <img src="<?= $urlPrefix ?>/img/carousel-5.jpg" alt="email reminders">
      <div class="carousel-caption">
        <h3>Reminders</h3>
        <p>Everyone will receive convenient email reminders.</p>
      </div>
    </div>
  </div>

  <!-- Left and right controls -->
  <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
<hr />
