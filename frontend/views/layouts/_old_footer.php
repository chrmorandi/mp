<p class="pull-left">
  <?php
    echo Html::a('&copy; Lookahead '.date('Y'),'https://lookahead.io',['class'=>'itemHide']);
    echo Html::tag('span',' . ',['class'=>'itemHide']);
    echo Html::a(Yii::t('frontend','privacy'),Url::to(['/site/privacy']));
    echo Html::tag('span',' . '.Html::a(Yii::t('frontend','terms'),Url::to(['/site/tos'])));
  ?>
<p class="pull-right">
<?= Html::a(Yii::t('frontend','blog'),Url::to('https://blog.meetingplanner.io')); ?>
<?= Html::tag('span',' . '.Html::a(Html::img(Url::to('/img/social_twitter_sm.gif'), ['class'=>'bird']),'https://twitter.com/intent/user?screen_name=meetingio')) ?>
<?php
if (!Yii::$app->user->isGuest) {
echo Html::tag('span',' . '.Html::a(Yii::t('frontend','features'),Url::to(['/features'])),['class'=>'itemHide']);
echo Html::tag('span',' . '.Html::a(Yii::t('frontend','about'),Url::to(['/about'])),['class'=>'itemHide']);
}
?>
</p>
