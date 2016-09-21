<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = 'Encountered a Problem';
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

<?
//= nl2br(Html::encode($message))
//<div class="alert alert-danger">
//</div>
?>

    <p>
        The above error occurred while the Web server was processing your request.
    </p>
    <p>
        Please <a href="http://support.meetingplanner.io">contact us</a> if you think this is a server error. Thank you.
    </p>

</div>
