<?php
use yii\helpers\Html;
/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo Yii::$app->charset; ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo Html::encode($this->title); ?></title>
  <?php $this->head(); ?>
  <style type="text/css" media="screen">@media screen {
    /* Thanks Outlook 2013! */
    * {
        font-family: "Oxygen", "Helvetica Neue", "Arial", "sans-serif"
        }
    }</style>
<style type="text/css" media="only screen and (max-width: 480px)">
  /* Mobile styles */
  @media only screen and (max-width: 480px) {
    table[class*="container-for-gmail-android"] {
      min-width: 290px;
      width: 100%;
    }
    table[class="w320"] {
      width: 320px;
    }
    img[class="force-width-gmail"] {
      display: none;
      width: 0;
      height: 0;
    }
    a[class="button-width"],
    a[class="button-mobile"] {
      width: 248px;
    }
    td[class*="mobile-header-padding-left"] {
      width: 160px;
      padding-left: 0;
    }
    td[class*="mobile-header-padding-right"] {
      width: 160px;
      padding-right: 0;
    }
    td[class="header-lg"] {
      font-size: 24px;
      padding-bottom: 5px;
    }
    td[class="header-md"] {
      font-size: 18px;
      padding-bottom: 5px;
    }
    td[class="content-padding"] {
      padding: 5px 0 30px;
    }
     td[class="button"] {
      padding: 5px;
    }
    td[class*="free-text"] {
      padding: 10px 18px 30px;
    }
    td[class="info-block"] {
      display: block;
      width: 280px;
      padding-bottom: 40px;
    }
    td[class="info-img"],
    img[class="info-img"] {
      width: 278px;
    }
    td[class~="mobile-hide-img"] {
      display: none;
      height: 0;
      width: 0;
      line-height: 0;
    }
    td[class~="item"] {
      width: 140px;
      vertical-align: top;
    }
    td[class~="quantity"] {
      width: 50px;
    }
    td[class~="price"] {
      width: 90px;
    }
    td[class="item-table"] {
      padding: 30px 20px;
    }
    td[class="mini-container-left"],
    td[class="mini-container-right"] {
      padding: 0 15px 15px;
      display: block;
      width: 290px;
    }
  }
</style>
</head>
<body style="-webkit-font-smoothing:antialiased; -webkit-text-size-adjust:none; color:#676767; height:100%; margin:0; width:100%" height="100%" width="100%">
<?php $this->beginBody(); ?>
<table align="center" cellpadding="0" cellspacing="0" width="100%" style="min-width:600px; border-collapse:collapse">
  <tr>
    <td align="center" valign="top" width="100%" style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; background:repeat-x url(https://meetingplanner.io/img/bg_top_02.jpg) #fff">
      <center>
        <img src="https://meetingplanner.io/img/transparent.png" style="-ms-interpolation-mode:bicubic; max-width:600px; outline:none; text-decoration:none; min-width:600px; font-size:1px; height:0; line-height:1px" height="0">
        <table cellspacing="0" cellpadding="0" width="100%" bgcolor="#ffffff" background="https://meetingplanner.io/img/bg_top_02.jpg" style="border-collapse:collapse; background-color:transparent">
          <tr>
            <td width="100%" height="80" valign="middle" style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; vertical-align:middle" align="center">
            <!--[if gte mso 9]>
            <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="mso-width-percent:1000;height:80px; v-text-anchor:middle;">
              <v:fill type="tile" src="http://s3.amazonaws.com/swu-filepicker/4E687TRe69Ld95IDWyEg_bg_top_02.jpg" color="#ffffff" />
              <v:textbox inset="0,0,0,0">
            <![endif]-->
              <center>
                <table cellpadding="0" cellspacing="0" width="600" style="border-collapse:collapse">
                  <tr>
                    <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:left; border-collapse:collapse; padding-left:10px; width:290px; vertical-align:middle" align="left" width="290" valign="middle">
                      <a href="<?php echo Yii::$app->params['site']['url'] ?>" style="color:#676767; text-decoration:none">
                        <img src="<?php echo Yii::$app->params['site']['email_logo'] ?>" alt="logo" height="47" width="137" style="-ms-interpolation-mode:bicubic; max-width:600px; outline:none; text-decoration:none; border:none">
                      </a>
                    </td>
                    <td style="color:#4d4d4d; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:right; border-collapse:collapse; padding-left:10px; width:290px" align="right" width="290">
                      <a href="https://twitter.com/intent/user?screen_name=meetingio" style="color:#676767; text-decoration:none">
                        <img src="https://meetingplanner.io/img/social_twitter.gif" alt="@mp" height="32" width="38" style="-ms-interpolation-mode:bicubic; max-width:600px; outline:none; text-decoration:none; border:none">
                      </a>
                      <!-- <a href=""><img width="38" height="47" src="http://s3.amazonaws.com/swu-filepicker/LMPMj7JSRoCWypAvzaN3_social_09.gif" alt="facebook" /></a>-->
                      <!-- <a href=""><img width="40" height="47" src="http://s3.amazonaws.com/swu-filepicker/hR33ye5FQXuDDarXCGIW_social_10.gif" alt="rss" /></a>-->
                    </td>
                  </tr>
                </table>
              </center>
              <!--[if gte mso 9]>
              </v:textbox>
            </v:rect>
            <![endif]-->
            </td>
          </tr>
        </table>
      </center>
    </td>
  </tr>
  <?php echo $content; ?>
  </table>
  <?php $this->endBody(); ?>
  </body>
  </html>
  <?php $this->endPage(); ?>
