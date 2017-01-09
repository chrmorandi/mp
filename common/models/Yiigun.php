<?php
// invoke Mailgun sdk
namespace common\models;
use Yii;
use yii\base\Model;
use Mailgun\Mailgun;

class Yiigun
{
  private $mg;
  private $mailgun_api_key;
  private $mailgun_public_api_key;
  public $mailgun_domain;
  private $mail_from;

   public function __construct($mode='secure') {

     // initialize mailgun connection
     $this->mailgun_api_key = Yii::$app->params['mailgun_api_key'];
     $this->mailgun_public_api_key = Yii::$app->params['mailgun_public_api_key'];
     $this->mailgun_domain = Yii::$app->params['site']['domain'];
     $this->mail_from = Yii::$app->params['site']['title'].' <support@'.Yii::$app->params['site']['domain'].'>';
     $client = new \Http\Adapter\Guzzle6\Client();
     if ($mode=='secure') {
       $this->mg = new Mailgun($this->mailgun_api_key,$client);
     } else {
       $this->mg = new Mailgun($this->mailgun_public_api_key,$client);
     }
  }

  public function get($url='') {
    $result = $this->mg->get($url);
    return $result;
  }

  public function delete($url='') {
    $result = $this->mg->delete($url);
    return $result;
  }

  public function send_simple_message($from='',$to='',$subject='Monitor Test Result',$body='') {
    if ($to=='') return false;
    if ($from == '')
      $from = $this->mail_from;
    // use only if supportEmail and from email are in mailgun account
  //  $domain = substr(strrchr($from, "@"), 1);
    $result = $this->mg->sendMessage($this->mailgun_domain, ['from' => $from,
                                               'to' => $to,
                                               'subject' => $subject,
                                               'text' => $body,
    ]);
    return $result->http_response_body;
  }

  public function send_html_message($from='',$to='',$subject='',$bodyHtml='') {
    if ($from == '')
      $from = $this->mail_from;
    # instantiate a Message Builder object from the SDK.
    $messageBldr = $this->mg->MessageBuilder();
    # Define the from address.
    $messageBldr->setFromAddress($from);
    # Define a to recipient.
    $messageBldr->addToRecipient($to);
    # Define the subject.
    $messageBldr->setSubject($subject);
    # Define the body of the message.
//    $messageBldr->setTextBody($message->body);
    $messageBldr->setHtmlBody($bodyHtml);
    # Finally, send the message.
    $result = $this->mg->post($this->mailgun_domain.'/messages', $messageBldr->getMessage());
  }

   public function verifyWebHook($timestamp='', $token='', $signature='') {
     // Concatenate timestamp and token values
     $combined=$timestamp.$token;
    //lg('Combined:'.$combined);
     // Encode the resulting string with the HMAC algorithm
     // (using your API Key as a key and SHA256 digest mode)
     $result= hash_hmac('SHA256', $combined, $this->mailgun_api_key);
     //lg ('Result: '.$result);
     //lg ('Signature: '.$signature);
     if ($result == $signature)
       return true;
      else
      return false;
   }

   public function validate($email='') {
      $result = $this->mg->get('address/validate', ['address' => $email]);
      return $result->http_response_body;
    }
}

?>
