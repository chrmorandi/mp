<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use backend\models\Data;

/**
 * Message controller
 */
class DataController extends Controller
{

  // historical
  // sign ups by day
  // meetings created by day
  // meetings finalized by day
  // meetings completed by day
  public function behaviors()
  {
      return [
          'access' => [
              'class' => AccessControl::className(),
              'rules' => [
                  [
                      'allow' => true,
                      'matchCallback' => function ($rule, $action) {
                          return (!\Yii::$app->user->isGuest && \common\models\User::findOne(Yii::$app->user->getId())->isAdmin());
                        }
                  ],
              ],
          ],
          'verbs' => [
              'class' => VerbFilter::className(),
              'actions' => [
                  'logout' => ['post'],
              ],
          ],
      ];
  }
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionRecalc() {
        Data::recalc();
    }

    public function actionCurrent()
    {
      $data = Data::getRealTimeData();
      return $this->render('current', [
          'data' => $data,
      ]);

    }
}
