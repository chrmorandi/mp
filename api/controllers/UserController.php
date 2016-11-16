<?php

namespace api\controllers;

use Yii;
use yii\filters\AccessControl;
use common\models\User;
use common\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionError() {
      echo 'here';
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        echo 'index';
    }

}
