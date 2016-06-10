<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use frontend\models\Meeting;
use frontend\models\UserPlace;
use common\models\User;
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
                      'actions' => [''],
                      'allow' => true,
                  ],
                  [
                      'actions' => ['current'],
                      'allow' => true,
                      'roles' => ['@'],
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

    public function actionCurrent()
    {
      $data = new \stdClass();

      $data->meetings = Meeting::find()
->select(['status,COUNT(*) AS dataCount'])
//->where('approved = 1')
->groupBy(['status'])
->all();

    $data->users = User::find()
    ->select(['status,COUNT(*) AS dataCount'])
    ->groupBy(['status'])
    ->all();

    // to do - count meetings per user and average meetings per user

    // to do - average time from creation to completion

    $user_places = UserPlace::find()
      ->select(['user_id,COUNT(*) AS dataCount'])
      ->groupBy(['user_id'])
      ->orderBy('dataCount DESC')
      ->limit(5)
      ->all();
      $totalPlaces = 0;
      foreach ($user_places as $up) {
        $totalPlaces+=$up->dataCount;
      }
      $data->avgUserPlaces = $totalPlaces / count($user_places);
      $data->userPlaces = $user_places;
      return $this->render('current', [
          'data' => $data,
      ]);

    }
}
