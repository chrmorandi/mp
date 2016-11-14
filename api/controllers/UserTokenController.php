<?php

namespace api\controllers;
use Yii;
use yii\filters\AccessControl;
use common\models\User;
use common\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class UserTokenController extends \yii\web\Controller
{
    public function actionIndex()
    {
        //return $this->render('index');
        echo 'index token';
    }

}
