<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<footer class="footer">
    <div class="container">
      <div class="row">
        <div class="col-xs-12 col-md-6 add-bottom-margin">
          <span class="glyphicon glyphicon-time logo"></span> <span class="heading"><?= Yii::$app->params['site']['title'] ?></span>
          <?php if ($includeLanguage) { ?>
            <div class="language">
              <?= \kmergen\LanguageSwitcher::widget([
                     'parentTemplate' => '<div class="btn-group" id="flagTarget">{activeItem}<ul class="dropdown-menu drop-up flags" role="menu">{items}</ul></div>',
                   'activeItemTemplate' => '<div class="dropdown-toggle" data-toggle="dropdown"><i class="flag flag-{language}"></i><span class="caret caret-up"></span></div>',
                   'itemTemplate' => '<li><a id="{language}" href="{url}"><i class="flag flag-{language}"></i> {label}</a></li>'
              ]);?>
            </div>
          <?php
            }
          ?>
            <?= Html::a(Html::img(Url::to('/img/social_twitter_sm.gif'), ['class'=>'bird']),'https://twitter.com/intent/user?screen_name=meetingio') ?>
          <p><?= Yii::t('frontend','{site-title} makes scheduling meetings easy',['site-title'=> Yii::$app->params['site']['title']])?></p>
          <p><?= Html::a('&copy; '.date('Y').' Lookahead Consulting','https://lookahead.io');?></p>
        </div>
        <div class="col-xs-6 col-md-3">
          <p class="heading"><?= Yii::t('frontend','Scheduling')?></p>
          <p><?= Html::a(Yii::t('frontend','Contact us'), ['/ticket'])?></p>
          <p><?= Html::a(Yii::t('frontend','Features'),Url::to(['/features'])) ?></p>
          <p><?= Html::a(Yii::t('frontend','Privacy'),Url::to(['/site/privacy'])); ?></p>
          <p><?= Html::a(Yii::t('frontend','Terms of service'),Url::to(['/site/tos'])); ?></p>
        </div>
        <div class="col-xs-6 col-md-3">
          <p class="heading"><?= Yii::t('frontend','Company')?></p>
          <p><?= Html::a(Yii::t('frontend','Blog'),Url::to('https://blog.meetingplanner.io')); ?></p>
          <p><?= Html::a(Yii::t('frontend','About'),Url::to(['/about']));?></p>
          <p><?= Html::a(Yii::t('frontend','Team'),Url::to(['site/team']));?></p>
          <p><?= Html::a(Yii::t('frontend','Startup series'),'https://code.tutsplus.com/series/building-your-startup-with-php--cms-742') ?></p>
        </div>
      </div>
    </div>
</footer>
