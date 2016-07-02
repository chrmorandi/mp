<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

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
                'brandLabel' => Yii::t('backend','Meeting Planner'),
                'brandUrl' => 'https://meetingplanner.io',
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            $menuItems[] = [
                        'label' => 'Real Time',
                        'items' => [
                          ['label' => Yii::t('frontend','Usage'), 'url' => ['/data/current']],
                        ]
                      ];
            $menuItems[] = [
                        'label' => 'Yesterday',
                        'items' => [
                          ['label' => Yii::t('frontend','User Data'), 'url' => ['/user-data']],
                        ]
                      ];
              $menuItems[]=[
                        'label' => 'Historical',
                        'items' => [
                          ['label' => Yii::t('frontend','Statistics'), 'url' => ['/historical-data']],
                        ],
                      ];
              $menuItems[]=[
                        'label' => 'Messages',
                        'url' => ['/message'],
                      ];
            if (Yii::$app->user->isGuest) {
                $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
            } else {
                $menuItems[] = [
                  'label' => 'Account',
                  'items' => [
                    ['label' => 'Logout (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post'],
                    ],
                  ],
                ];
            }
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
            ]);
            NavBar::end();
        ?>

        <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
          <p class="pull-left">
          <?php
          if (!Yii::$app->user->isGuest) {
            echo Html::a(Yii::t('frontend','Support'),Url::to('http://support.meetingplanner.io'));
          }
           ?>
        <p class="pull-right">
        <?= Html::a('@meetingio','https://twitter.com/intent/user?screen_name=meetingio') ?><?php
        if (!Yii::$app->user->isGuest) {
          echo '&nbsp;|&nbsp;'.Html::a('&copy; Lookahead '.date('Y'),'http://lookahead.io').'';
        }
        ?>
        </p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
