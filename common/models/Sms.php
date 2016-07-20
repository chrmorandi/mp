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

   function __construct() {

     $this->sms = Yii::$app->Yii2Twilio->initTwilio();
     $this->mp_number = Yii::$app->params['sms_number'];
     $this->service_id = Yii::$app->params['twilio_service_id'];
     $this->test_number = Yii::$app->params['twilio_test_number'];
  }

  public function transmit($user_id,$body='') {
    // to do - lookup usercontact to sms
    // see if they have a usercontact entry that accepts sms
    // transmit
    $to_number = $this->test_number;
    $to_number = $this->findUserNumber($user_id);
    if (!$to_number)
    {
      return false;
    }
    try {
        $message = $this->sms->account->messages->create(array(
            "From" => $this->mp_number,
            "To" => $to_number,   // Text this number
            'MessagingServiceSid' => $this->service_id,
            "Body" => $body,
        ));
    } catch (\Services_Twilio_RestException $e) {
            echo $e->getMessage();
    }
  }

  public function findUserNumber($user_id) {
    $uc = UserContact::find()->where(['user_id'=>$user_id])
      ->andWhere(['contact_type'=>UserContact::TYPE_PHONE])
      ->andWhere(['accept_sms'=>1])
      ->one();
    if (is_null($uc) || count($uc)==0) {
      return false;
    } else {
      return $uc->info;
    }
  }
}

?>
