<div id="myCarousel" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
    <li data-target="#myCarousel" data-slide-to="1"></li>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
    <div class="item active">
      <div class="flex-video" style="margin: 0 10%;text-align:center;">
        <div class="embed-container">
        <iframe src="https://player.vimeo.com/video/188213613?api=1" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
        </div>
      </div>
      <div class="carousel-caption">
        <h3><?= Yii::t('frontend','How to Plan a Meeting')?></h3>
        <p><?= Yii::t('frontend','Simple to schedule one on one meetups')?></p>
      </div>
    </div>

    <div class="item">
      <div class="flex-video" style="margin: 0 10%;text-align:center;">
        <div class="embed-container">
        <iframe src="https://player.vimeo.com/video/188218101?api=1" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
        </div>
      </div>
      <div class="carousel-caption">
        <h3><?= Yii::t('frontend','Planning a Group Meeting')?></h3>
        <p><?= Yii::t('frontend','Easily discover optimal date times and places')?></p>
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
