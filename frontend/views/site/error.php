<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = 'Sorry, We Encountered a Problem';
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

<!-- div class="alert alert-danger" -->
<!-- nl2br(Html::encode($message)) -->

    <p>
        An error occurred while the Web server was processing your request.
    </p>
    <p>
        Please <a href="http://support.meetingplanner.io">contact us</a> if you think this is a server error. Let us know what you we're doing. Thank you.
    </p>

</div>
