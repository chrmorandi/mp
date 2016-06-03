<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'About Meeting Planner';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p class="lead">Meeting Planner is an emerging new app designed to make scheduling easier and easier. The site has been developed in step with an <a href="https://code.tutsplus.com/tutorials/building-your-startup-with-php-table-of-contents--cms-23316">Envato Tuts+ tutorial series</a> by <a href="http://tutsplus.com/authors/jeff-reifman">founder Jeff Reifman</a>.</p>
     <p class="lead">Every piece of Meeting Planner has been built out in the open, documented in the series. The application is developed in the <a href="https://code.tutsplus.com/tutorials/programming-with-yii2-getting-started--cms-22440">Yii2 Framework</a> for PHP. Learn more about Jeff or contact him at his <a href="http://jeffreifman.com">website</a>. You can also follow him on Twitter <a href="https://twitter.com/intent/user?screen_name=reifman">@reifman</a>.</p>
    <h2><?= Yii::t('frontend','The Building Your Startup Tutorial Series'); ?></h2>

    <div class="body-content">
		<!--- begin row one --->

        <div class="row">
            <div class="col-lg-4">
                <h2><?= Yii::t('frontend','1. Getting Started') ?></h2>

                <p><?= Yii::t('frontend','Follow along with our tutorial series at Tuts+ as we build Meeting Planner step by step. In this episode we talk about startups in general and the goals for our application.') ?></p>

                <p><a class="btn btn-default" href="http://code.tutsplus.com/tutorials/building-your-startup-with-php-getting-started--cms-21948"><?= Yii::t('frontend','Episode 1') ?> &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2><?= Yii::t('frontend','2. Feature Requirements') ?> </h2>

                <p><?= Yii::t('frontend','In Episode 2, we scope out the features that we\'ll need for a minimum viable product and the database schema that will support it.') ?> </p>

                <p><a class="btn btn-default" href="http://code.tutsplus.com/tutorials/building-your-startup-with-php-feature-requirements-and-database-design--cms-22618"><?= Yii::t('frontend','Episode 2') ?> &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2><?= Yii::t('frontend','3. Building Places') ?></h2>

                <p><?= Yii::t('frontend','In Episode 3, we build code to enable meeting places, integrating with HTML 5 Geolocation, Google Maps and Google Places.') ?> </p>

                <p><a class="btn btn-default" href="https://code.tutsplus.com/tutorials/building-your-startup-with-php-geolocation-and-google-places--cms-22729"><?= Yii::t('frontend','Episode 3') ?> &raquo;</a></p>
            </div>
        </div>

		<!--- end row one --->

		<!--- begin row two --->
        <div class="row">
            <div class="col-lg-4">
                <h2><?= Yii::t('frontend','4. Localization with I18n') ?></h2>

                <p><?= Yii::t('frontend','Using Yii\'s built in localization capability we create the infrastructure for multiple languages') ?></p>

                <p><a class="btn btn-default" href="http://code.tutsplus.com/tutorials/building-your-startup-with-php-localization-with-i18n--cms-23102"><?= Yii::t('frontend','Episode 4' ) ?> &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2><?= Yii::t('frontend','5. Access Controls, Ownership &amp; Polish') ?> </h2>

                <p><?= Yii::t('frontend','We circle back to polish some of what we\'ve built to date leveraging more of the Yii Framework.') ?> </p>

                <p><a class="btn btn-default" href="http://code.tutsplus.com/tutorials/building-your-startup-access-control-active-record-relations-and-slugs--cms-23109"><?= Yii::t('frontend','Episode 5') ?> &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2><?= Yii::t('frontend','6. User Settings, Profile Images &amp; Contact Details') ?></h2>

                <p><?= Yii::t('frontend','Building support for infrastructure to support users.') ?> </p>

                <p><a class="btn btn-default" href="http://code.tutsplus.com/tutorials/building-your-startup-with-php-user-settings-profile-images-and-contact-details--cms-23196"><?= Yii::t('frontend','Episode 6') ?> &raquo;</a></p>
            </div>
        </div>

	<!--- end row two --->
		<!--- begin row three --->
        <div class="row">
            <div class="col-lg-4">
                <h2><?= Yii::t('frontend','7. Scheduling Meetings') ?></h2>

                <p><?= Yii::t('frontend','Beginning to build the schedule meeting functionality.') ?></p>

                <p><a class="btn btn-default" href="http://code.tutsplus.com/tutorials/building-your-startup-with-php-scheduling-a-meeting--cms-23252"><?= Yii::t('frontend','Episode 7' ) ?> &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2><?= Yii::t('frontend','8. Scheduling Availability &amp; Choices') ?> </h2>

                <p><?= Yii::t('frontend','Building AJAX to simplify meeting availability and selections.') ?> </p>

                <p><a class="btn btn-default" href="http://code.tutsplus.com/tutorials/building-your-startup-with-php-scheduling-availability-and-choices--cms-23268"><?= Yii::t('frontend','Episode 8') ?> &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2><?= Yii::t('frontend','9. Viewing the Meeting') ?></h2>

                <p><?= Yii::t('frontend','Extending the default view to provide appropriate commands for specific users.') ?> </p>

                <p><a class="btn btn-default" href="http://code.tutsplus.com/tutorials/building-your-startup-customizing-the-meeting-view--cms-25138"><?= Yii::t('frontend','Episode 9 (coming soon)') ?> &raquo;</a></p>
            </div>
        </div>
	<!--- end row three --->
		<!--- begin row four --->
        <div class="row">
          <div class="col-lg-4">
              <h2><?= Yii::t('frontend','10. Delivering the Meeting Announcement') ?></h2>

              <p><?= Yii::t('frontend','Building the support required to send a meeting request and handle responses from the participant.') ?> </p>

              <p><a class="btn btn-default" href="http://code.tutsplus.com/tutorials/building-your-startup-delivering-the-meeting-invitation--cms-23428"><?= Yii::t('frontend','Episode 10 (coming soon)') ?> &raquo;</a></p>
          </div>
          <div class="col-lg-4">
              <h2><?= Yii::t('frontend','11. Meeting Commands') ?></h2>

              <p><?= Yii::t('frontend','Implementing secure authenticated links within the email and implementing responses to each available command option.') ?> </p>

              <p><a class="btn btn-default" href="http://code.tutsplus.com/tutorials/building-your-startup-with-php-email-commands--cms-23288"><?= Yii::t('frontend','Episode 11 (coming soon)') ?> &raquo;</a></p>
          </div>
              <div class="col-lg-4">
                  <h2><?= Yii::t('frontend','12. Generating Calendar Files') ?> </h2>

                  <p><?= Yii::t('frontend','Exporting .ics files for third party calendar integration.') ?> </p>

                  <p><a class="btn btn-default" href="http://code.tutsplus.com/tutorials/building-your-startup-exporting-ical-files-into-calendars--cms-26435"><?= Yii::t('frontend','Episode 12 (coming soon)') ?> &raquo;</a></p>
              </div>
          </div>
      	<!--- end row four --->
        <!--- begin row five --->
            <div class="row">
              <div class="col-lg-4">
                  <h2><?= Yii::t('frontend','13. OAuth Login &amp; Signup') ?></h2>

                  <p><?= Yii::t('frontend','Integrating OAuth authentication to optimize user registration and usability.') ?></p>

                  <p><a class="btn btn-default" href="http://code.tutsplus.com/tutorials/building-your-startup-with-php-simplifying-onramp-with-oauth--cms-23512"><?= Yii::t('frontend','Episode 13 (coming soon)' ) ?> &raquo;</a></p>
              </div>
              <div class="col-lg-4">

              </div>
                  <div class="col-lg-4">

                  </div>
              </div> <!--- end row five --->

          </div> <!-- end body content -->
</div>
