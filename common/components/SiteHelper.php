<?php
namespace common\components;
use yii;
use yii\helpers\Url;
class SiteHelper extends \yii\base\Component{
  const SITE_MP = 0;
  const SITE_SP = 1;
  const SITE_FD = 2;

  public $description = 'Scheduling app and meeting planner for one on one or group meetings. Choose dates, times and places together without all the emails.';
  public $keywords = 'scheduling app,meeting planner,schedule planner,schedule a meeting,event planner,doodle survey';

    public function init() {
      $baseUrl = Url::home(true);
      if (stristr($baseUrl,'1')) {
        // block direct access of site by ip address
        Yii::$app->response->redirect('https://simpleplanner.io');
      } if (stristr($baseUrl,'/mp/')!==false) {
        // local mp
        $this->commonMeetingPlanner();
        Yii::$app->params['site']['url'] = 'http://localhost:8888/mp/';
        Yii::$app->params['site']['ga'] = '';
      } else if (stristr($baseUrl,'/sp/')!==false) {
        // local sp
        $this->commonSimplePlanner();
        Yii::$app->params['site']['url'] = 'http://localhost:8888/sp/';
        Yii::$app->params['site']['ga'] = '';
      } else if (stristr($baseUrl,'/fd/')!==false) {
        // local sp
        $this->commonFirstDate();
        Yii::$app->params['site']['url'] = 'http://localhost:8888/fd/';
        Yii::$app->params['site']['ga'] = '';
      } else if (stristr($baseUrl,'simple')!==false) {
        // simpleplanner.io
        $this->commonSimplePlanner();
      } else if (stristr($baseUrl,'first')!==false) {
        // firstdate.io
        $this->commonFirstDate();
      } else {
        // default meetingplanner.io
        $this->commonMeetingPlanner();
      }
      parent::init();
    }

    private function commonMeetingPlanner() {
      Yii::$app->params['site']['id'] = SiteHelper::SITE_MP;
      Yii::$app->params['site']['domain'] = 'meetingplanner.io';
      Yii::$app->params['site']['url'] = 'https://meetingplanner.io';
      Yii::$app->params['site']['title'] = Yii::t('frontend', 'Meeting Planner');
      Yii::$app->params['site']['mtg'] = Yii::t('frontend', 'Meetings');
      Yii::$app->params['site']['mtg_singular'] = Yii::t('frontend', 'Meeting');
      Yii::$app->params['site']['img'] = rand(2,3);
      Yii::$app->params['site']['navbar'] = 'navbar-inverse';
      Yii::$app->params['site']['email_logo'] = 'https://meetingplanner.io/img/email-logo-mp.gif';
      Yii::$app->params['site']['ga'] = 'UA-37244292-18';
      Yii::$app->params['site']['description']=$this->description;
      Yii::$app->params['site']['keywords']=$this->keywords;
    }

    private function commonSimplePlanner() {
      Yii::$app->params['site']['id'] = SiteHelper::SITE_SP;
      Yii::$app->params['site']['domain'] = 'simpleplanner.io';
      Yii::$app->params['site']['url'] = 'https://simpleplanner.io';
      Yii::$app->params['site']['title'] = Yii::t('frontend', 'Simple Planner');
      Yii::$app->params['site']['mtg'] = Yii::t('frontend', 'Meetups');
      Yii::$app->params['site']['mtg_singular'] = Yii::t('frontend', 'Meetup');
      Yii::$app->params['site']['img'] = rand(0,1);
      Yii::$app->params['site']['navbar'] = 'navbar-default';
      Yii::$app->params['site']['email_logo'] = 'https://simpleplanner.io/img/email-logo-sp.gif';
      Yii::$app->params['site']['ga'] = 'UA-37244292-21';
      Yii::$app->params['site']['description']= $this->description;
      Yii::$app->params['site']['keywords']=$this->keywords;
    }

    private function commonFirstDate() {
      Yii::$app->params['site']['id'] = SiteHelper::SITE_FD;
      Yii::$app->params['site']['domain'] = 'firstdate.io';
      Yii::$app->params['site']['url'] = 'https://firstdate.io';
      Yii::$app->params['site']['title'] = Yii::t('frontend', 'FirstDate');
      Yii::$app->params['site']['mtg'] = Yii::t('frontend', 'Dates');
      Yii::$app->params['site']['mtg_singular'] = Yii::t('frontend', 'Date');
      Yii::$app->params['site']['img'] = 0;
      Yii::$app->params['site']['navbar'] = 'navbar-default';
      Yii::$app->params['site']['email_logo'] = 'https://simpleplanner.io/img/email-logo-sp.gif';
      Yii::$app->params['site']['ga'] = 'UA-37244292-25';
      Yii::$app->params['site']['description'] = 'plan your first date safely and easily';
      Yii::$app->params['site']['keywords']='plan your date, dating safely';
    }
}
?>
