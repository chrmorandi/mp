<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" >
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <meta name="description" content="<?= Yii::$app->params['site']['description'] ?>">
    <meta name="keywords" content="<?= Yii::$app->params['site']['keywords'] ?>">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?= \common\components\MiscHelpers::buildHreflang(); ?>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
        <div class="wrap">
        <?php
        if (Yii::$app->params['site']['id'] == \common\components\SiteHelper::SITE_MP) {
          Yii::$app->params['site']['title'] = $siteTitle = Yii::t('frontend', 'Meeting Planner');
          Yii::$app->params['site']['mtg'] = $meetingLabel = Yii::t('frontend', 'Meetings');
        } else {
          Yii::$app->params['site']['title'] = $siteTitle = Yii::t('frontend', 'Simple Planner');
          Yii::$app->params['site']['mtg'] = $meetingLabel = Yii::t('frontend', 'Meetups');
        }
                NavBar::begin([
                'brandLabel' => $siteTitle, // '&nbsp;<span class="badge">'.Yii::t('frontend','preview').'</span>',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => Yii::$app->params['site']['navbar'].' navbar-fixed-top',
                ],
            ]);
            if (Yii::$app->user->isGuest) {
                $menuItems[] = ['label' => Yii::t('frontend','Features'), 'url' => ['/features']];
                $menuItems[] = ['label' => Yii::t('frontend','Register'), 'url' => ['/site/signup']];
                $menuItems[] = ['label' => Yii::t('frontend','Login'), 'url' => ['/site/login']];
            } else {
	            $menuItems = [
                  ['label' => Yii::t('frontend','Schedule'), 'url' => ['/meeting/create']],
                  [
                    'label' => $meetingLabel,
                    'url' => ['/meeting'],
                    'options'=>['class'=>'menuHide'],
                  ],
                  [
                    'label' => Yii::t('frontend','Help'),
                    'url'=>['/ticket'],
                  ],
	            ];
            }
      			if (Yii::$app->user->isGuest) {
              $menuItems[]=['label' => Yii::t('frontend','Help'),
                'items' => [
                  ['label' => Yii::t('frontend','Support'), 'url' => ['/ticket']],
                  ['label' => Yii::t('frontend','Blog'), 'url' => 'https://blog.meetingplanner.io'],
                  ['label' => Yii::t('frontend','About'), 'url' => ['/about']],
                ],
              ];
              echo Nav::widget([
                  'options' => ['class' => 'navbar-nav navbar-right'],
                  'items' => $menuItems,
              ]);
            } else {
      				$menuItems[] = [
                          'label' => Yii::t('frontend','Account'),
      				            'items' => [
                            [
                              'label' => Yii::t('frontend','Places'),
                               'url' => ['/place/yours'],
                             ],
    				                 [
    				                    'label' => Yii::t('frontend','Friends'),
    				                    'url' => ['/friend'],
                                'options'=>['class'=>'menuHide'],
    				                ],
                            [
                               'label' => Yii::t('frontend','Reminders'),
                               'url' => ['/reminder'],
                               //'options'=>['class'=>'menuHide'],
                           ],[
                                'label' => Yii::t('frontend','Contact details'),
                                'url' => ['/user-contact'],
                                'options'=>['class'=>'menuHide'],
                            ],
       				                 [
                                  'label' => Yii::t('frontend','Profile details'),
                                  'url' => ['/user-profile'],
                                  //'options'=>['class'=>'menuHide'],
                              ],
                              [
                               'label' => Yii::t('frontend','Settings'),
                               'url' => ['/user-setting'],
                               //'options'=>['class'=>'menuHide'],
                             ],
      				                 [
      				                    'label' => Yii::t('frontend','Logout').' (' . \common\components\MiscHelpers::getDisplayName(Yii::$app->user->id) . ')',
      				                    'url' => ['/site/logout'],
      				                    'linkOptions' => ['data-method' => 'post']
      				                ],
      				            ],
      				        ];
                      echo Nav::widget([
                          'options' => ['class' => 'navbar-nav navbar-right'],
                          'items' => $menuItems,
                      ]);
      			}
            NavBar::end();
        ?>

        <div class="container">
        <?php
        echo Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
        </div>
    </div>
    <?= $this->render('_footer',  ['includeLanguage'=>true ]) ?>
    <?= Html::hiddenInput('url_prefix',\common\components\MiscHelpers::getUrlPrefix(),['id'=>'url_prefix']); ?>
    <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
  ga('create', '<?php echo Yii::$app->params['site']['ga']; ?>', 'auto');
  ga('send', 'pageview');
</script>
<?= $this->render('_statcounter') ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
