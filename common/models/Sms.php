<?php
namespace common\models;
use Yii;
use yii\base\Model;
use frontend\models\UserContact;

class Sms
{
  private $sms;
  private $service_id;
  private $mp_number;
  private $test_number;

   public function __construct() {

     $this->sms = Yii::$app->Yii2Twilio->initTwilio();
     $this->mp_number = Yii::$app->params['sms_number'];
     $this->service_id = Yii::$app->params['twilio_service_id'];
     $this->test_number = Yii::$app->params['twilio_test_number'];
  }

  public function transmit($to_number,$body='') {
    // Twilio notes
    // during trial, can only send to registered number
    // to do - lookup usercontact to sms
    // see if they have a usercontact entry that accepts sms
    // transmit
    //$to_number = $this->test_number;
    //$to_number = $this->findUserNumber($user_id);
    /*if (!$to_number)
    {
      return false;
    }*/
    //echo $this->mp_number;exit;
           try {
               $message = $this->sms->account->messages->create(
                   "12067067770", // To a number that you want to send sms
                   array(
                   "from" => "+15036765100",   // From a number that you are sending
                   "body" => $body,
               ));
           } catch (\Twilio\Exceptions\RestException $e) {
                   echo $e->getMessage();
           }
  }

}
