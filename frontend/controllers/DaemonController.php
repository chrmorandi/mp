<?php

// TO DO: Move to backend controllers when a domain is set up
namespace frontend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use frontend\models\Meeting;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class DaemonController extends Controller
{

    public function behaviors()
    {
        return [
          'access' => [
              'class' => \yii\filters\AccessControl::className(),
              'only' => ['index','hourly'],
              'rules' => [
                // allow authenticated users
                 [
                     'allow' => true,
                     'actions'=>['index'],
                     'roles' => ['@'],
                 ],
                [
                    'allow' => true,
                    'actions'=>['quarter','frequent'],
                    'roles' => ['?'],
                ],
                // everything else is denied
              ],
                    ],
        ];
    }


  public function actionIndex()
  {
    \frontend\models\Friend::fixPreFriends();
  }


public function actionFrequent() {
  // called every five minutes
  Meeting::findFresh();
  // to do - turn off output
}

public function actionQuarter() {
    // called every fifteen minutes
    $m = new Meeting;
    $past = $m->checkPast();
    // to do - turn off output
  }

  public function actionHourly() {
  	  $current_hour = date('G');
  	  if ($current_hour%6) {
        // every six hours

      }
  	}
}
