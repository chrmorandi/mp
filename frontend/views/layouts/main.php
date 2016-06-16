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
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
                NavBar::begin([
                'brandLabel' => Yii::t('frontend','MeetingPlanner.io'), //
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            if (Yii::$app->user->isGuest) {
                $menuItems[] = ['label' => Yii::t('frontend','Signup'), 'url' => ['/site/signup']];
                $menuItems[] = ['label' => Yii::t('frontend','Login'), 'url' => ['/site/login']];
            } else {
	            $menuItems = [
                  ['label' => Yii::t('frontend','Schedule'), 'url' => ['/meeting/create']],
                  [
                    'label' => Yii::t('frontend','Meetings'),
                    'url' => ['/meeting'],
                    'options'=>['class'=>'menuHide'],
                  ],
	            ];
            }
      			if (Yii::$app->user->isGuest) {
              $menuItems[]=['label' => Yii::t('frontend','Help'),
                'items' => [
                  ['label' => Yii::t('frontend','Support'), 'url' => 'http://support.meetingplanner.io'],
                  ['label' => Yii::t('frontend','About'), 'url' => ['/site/about']],
                ],
              ];
              echo Nav::widget([
                  'options' => ['class' => 'navbar-nav navbar-right'],
                  'items' => $menuItems,
              ]);
            } else {

      				$menuItems[] = [
      				            'label' => 'Account',
      				            'items' => [
                            [
                              'label' => Yii::t('frontend','Places'),
                               'url' => ['/place/yours'],
                               'options'=>['class'=>'menuHide'],
                             ],
    				                 [
    				                    'label' => Yii::t('frontend','Friends'),
    				                    'url' => ['/friend'],
                                'options'=>['class'=>'menuHide'],
    				                ],
      				                 [
                                 'label' => Yii::t('frontend','Profile'),
                                 'url' => ['/user-profile'],
                                 'options'=>['class'=>'menuHide'],
                             ],
                             [
                                'label' => Yii::t('frontend','Contact information'),
                                'url' => ['/user-contact'],
                                'options'=>['class'=>'menuHide'],
                            ],
                            [
                               'label' => Yii::t('frontend','Settings'),
                               'url' => ['/user-setting'],
                               'options'=>['class'=>'menuHide'],
                           ],
                           [
                              'label' => Yii::t('frontend','Reminders'),
                              'url' => ['/reminder'],
                              'options'=>['class'=>'menuHide'],
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

    <footer class="footer">
        <div class="container">
          <p class="pull-left">
          <?php
          if (!Yii::$app->user->isGuest) {
            echo Html::a(Yii::t('frontend','Support'),Url::to('http://support.meetingplanner.io')).Html::tag('span',' | ',['class'=>'itemHide']);
            echo Html::a(Yii::t('frontend','About'),Url::to(['/site/about']),['class'=>'itemHide']);
          }
           ?>
        <p class="pull-right">
        <?= Html::a('@meetingio','https://twitter.com/intent/user?screen_name=meetingio') ?><?php
        if (!Yii::$app->user->isGuest) {
          echo Html::tag('span',' | ',['class'=>'itemHide']).Html::a('&copy; Lookahead '.date('Y'),'http://lookahead.io',['class'=>'itemHide']).'';
        }
        ?>
        </p>
        </div>
    </footer>
    <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
  ga('create', '<?php echo Yii::$app->params['ga']; ?>', 'auto');
  ga('send', 'pageview');
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
