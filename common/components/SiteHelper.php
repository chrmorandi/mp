<?php
namespace common\components;
use yii;
use yii\helpers\Url;
class SiteHelper extends \yii\base\Component{
  const SITE_MP = 0;
  const SITE_SP = 1;
  const SITE_FD = 2;

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
      } else if (stristr($baseUrl,'simple')!==false) {
        // simpleplanner.io
        $this->commonSimplePlanner();
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
      Yii::$app->params['site']['img'] = rand(2,3);
      Yii::$app->params['site']['navbar'] = 'navbar-inverse';
      Yii::$app->params['site']['email_logo'] = 'https://meetingplanner.io/img/email-logo-mp.gif';
      Yii::$app->params['site']['ga'] = 'UA-37244292-18';
    }

    private function commonSimplePlanner() {
      Yii::$app->params['site']['id'] = SiteHelper::SITE_SP;
      Yii::$app->params['site']['domain'] = 'simpleplanner.io';
      Yii::$app->params['site']['url'] = 'https://simpleplanner.io';
      Yii::$app->params['site']['title'] = Yii::t('frontend', 'Simple Planner');
      Yii::$app->params['site']['mtg'] = Yii::t('frontend', 'Meetups');
      Yii::$app->params['site']['img'] = rand(0,1);
      Yii::$app->params['site']['navbar'] = 'navbar-default';
      Yii::$app->params['site']['email_logo'] = 'https://simpleplanner.io/img/email-logo-sp.gif';
      Yii::$app->params['site']['ga'] = 'UA-37244292-21';
    }
}
?>
