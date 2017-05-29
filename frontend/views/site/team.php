<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = Yii::t('frontend','Our Team').' '.Yii::t('frontend',Yii::$app->params['site']['title']);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-team">
    <h1><?= Yii::t('frontend','Our Team') ?></h1>
    <div class="row">
      <div class="col-xs-12 col-md-3 pull-right">
        <?= Html::img('/img/team/jeff-reifman.jpg', ['class'=>'img-responsive','alt'=>'jeff reifman']);?>
      </div>
      <div class="col-xs-12 col-md-9">
    <h3><?= Yii::t('frontend','Jeff Reifman') ?>, <?= Yii::t('frontend','Founder') ?></h3>
    <p ><?= Yii::t('frontend','Jeff helped lead the technology launch of MSNBC.com as part of an eight year career at Microsoft. He left Microsoft to found GiftSpot, later acquired by GiftCertificates.com. He’s a three time grantee of the John S. and James L. Knight Foundation. In 2009, he led the capture of missing writer Evan Ratliff in ')?>
    <?= Html::a(Yii::t('frontend','Wired magazine’s Vanish contest'),'http://www.seattleweekly.com/home/928794-129/technology')?>.
  </p>
  <p><?= Yii::t('frontend','He pitched the idea of a ');?><?= Html::a(Yii::t('frontend','building your own startup series'),'https://code.tutsplus.com/series/building-your-startup-with-php--cms-742')?>&nbsp;
    <?= Yii::t('frontend','to Envato Tuts+ in August 2014. After ');?>
    <?= Html::a(Yii::t('frontend','diagnosis of a brain tumor'),'http://jeffreifman.com/2015/11/24/the-gift-of-brain-surgery/')?>&nbsp;
    <?= Yii::t('frontend','in 2015, he put the project on hold indefinitely. By 2016, he had resumed development. He\'s built Meeting Planner as a solo startup with ');?>
    <?= Html::a(Yii::t('frontend','broad assistance from the open source development community'),'https://code.tutsplus.com/tutorials/building-your-startup-the-open-source-foundation-behind-meeting-planner--cms-26664')?>&nbsp;
    <?= Yii::t('frontend','and his advisors Alex Knight and Alex Makarov.')?>
  </p>
  <p>
    <?= Html::a(Yii::t('frontend','Learn more about Jeff here'),'http://jeffreifman.com');?>&nbsp;<?= Yii::t('frontend','or'); ?>&nbsp;<?= Html::a(Yii::t('frontend','send him a message'),['/ticket/create']); ?>.
      </div>
    </div>
    <br />
    <h2><?= Yii::t('frontend','Advisors'); ?></h2>
    <div class="row">
      <div class="col-xs-12 col-md-3 pull-right">
        <?= Html::img('/img/team/alex-knight.jpg', ['class'=>'img-responsive','alt'=>'alex knight']);?>
      </div>
      <div class="col-xs-12 col-md-9">
    <h3><?= Yii::t('frontend','Alex Knight') ?>, <?= Yii::t('frontend','Edge Labs') ?></h3>
    <p ><?= Yii::t('frontend','Alex Knight has spent more than twenty five years as an advisor, investor and executive helping drive innovation in technology, media, and finance, often at the complicated intersection of those industries. His experience includes leading new businesses at Apple, Microsoft and News Corporation, co-managing a global innovation fund for Intellectual Ventures, investing as a partner in an early-stage venture capital firm, and advising/sitting on the boards of a broad range of start-ups including About.com, Classmates and Corbis.')?>
    </div>
  </div>
      <div class="row">
        <div class="col-xs-12 col-md-3 pull-right">
          <?= Html::img('/img/team/alex-makarov.jpg', ['class'=>'alex img-responsive','alt'=>'alex makarov']);?>
        </div>
      <div class="col-xs-12 col-md-9">
    <h3><?= Yii::t('frontend','Alex Makarov') ?>, &lt;<?= Yii::t('frontend','rmcreative') ?>&gt;</h3>
    <p><?= Yii::t('frontend','Alex is one of the core maintainers of');?>&nbsp;
<?= Html::a(Yii::t('frontend','Yii PHP framework'),'http://www.yiiframework.com/');?>&nbsp;
<?= Yii::t('frontend','for more than 7 years, its representative in ');?>
<?= Html::a(Yii::t('frontend','PHP-FIG'),'http://www.php-fig.org/');?>,&nbsp;
<?= Html::a(Yii::t('frontend','active conference speaker'),'http://www.php-fig.org/');?>&nbsp;<?= Yii::t('frontend','and')?>&nbsp;
<?= Html::a(Yii::t('frontend','participant of various other OpenSource projects'),'https://github.com/samdark/');?>.
      </p>
      <p><?= Html::a(Yii::t('frontend','Learn more about Alex here'),'http://en.rmcreative.ru/about/');?>.
</p>
  </div>

  </div>

</div>
