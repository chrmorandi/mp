<?php

namespace frontend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\Ticket;
use frontend\models\Meeting;
use frontend\models\TicketReply;
use frontend\models\TicketSearch;
use common\models\User;
use common\components\MiscHelpers;
/**
 * TicketController implements the CRUD actions for Ticket model.
 */
class TicketController extends Controller
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

    /**
     * Lists all Ticket models.
     * @return mixed
     */
    public function actionIndex()
    {
      if (!\Yii::$app->user->isGuest && \common\models\User::findOne(Yii::$app->user->getId())->isAdmin()) {
        // admin
        $guest_id = Yii::$app->user->getId();
        $query = Ticket::find()
          ->where(['status'=>[Ticket::STATUS_OPEN,Ticket::STATUS_PENDING]]);
      } else {
        // user
        $guest_id = Ticket::getGuestId();
        $query = Ticket::find()
          ->where(['posted_by'=>Yii::$app->user->getId()])
          ->orWhere(['posted_by'=>$guest_id])
          ->andWhere(['status'=>[Ticket::STATUS_OPEN,Ticket::STATUS_PENDING]]);
      }
        $ticketProvider = new ActiveDataProvider([
              'query' => $query,
              'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
              'pagination' => [
                  'pageSize' => 7,
                ],
          ]);
        if ($ticketProvider->getTotalCount()==0) {
          return $this->redirect(['ticket/create']);
        } else {
          return $this->render('index', [
              'ticketProvider' => $ticketProvider,
          ]);
        }
    }

    /**
     * Displays a single Ticket model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
      if (!\Yii::$app->user->isGuest && \common\models\User::findOne(Yii::$app->user->getId())->isAdmin()) {
        $mode = 'admin';
      } else {
        $mode = 'user';
      }
      $model = $this->findModel($id);
      $reply = new TicketReply();
      if ($reply->load(Yii::$app->request->post())) {
          $reply->ticket_id = $id;
          if ($mode=='admin') {
            $model->status = Ticket::STATUS_PENDING_USER;
            $reply->posted_by = Yii::$app->user->getId();
            Yii::$app->session->setFlash('success', 'Thank you, we\'ve notified the user of your update.');
          } else {
            $model->status = Ticket::STATUS_PENDING;
            $reply->posted_by = Ticket::getGuestId();
            Yii::$app->session->setFlash('success', 'Thank you, we\'ve notified our staff of your update. We\'ll get back to you as soon as possible.');
          }
          $reply->save();
          $model->update();
          return $this->redirect(['view','id'=>$id]);
      } else {
        return $this->render('view', [
            'model' => $model,
            'reply' => $reply,
        ]);
      }
    }

    /**
     * Creates a new Ticket model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        // to dos
        // posted_by needs to be a string
        // also need to ask for email address

        $model = new Ticket();
        if ($model->load(Yii::$app->request->post())) {
            $model->posted_by = Ticket::getGuestId();
            $model->status = Ticket::STATUS_OPEN;
            $model->save();
            if (!Yii::$app->user->isGuest) {
                $u=User::findOne($model->posted_by);
                $link = MiscHelpers::buildCommand($model->id,Meeting::COMMAND_VIEW_TICKET,0,$u->id,$u->auth_key);
            } else {
              $link='';
            }
            Yii::$app->session->setFlash('success', 'Thank you, we\'ve created a new ticket and notified our staff. We\'ll get back to you as soon as possible.');
            $content=[
              'subject' => Yii::t('frontend','New support ticket: {subject}',['subject'=>$model->subject]),
              'heading' => Yii::t('frontend','Support Request'),
              'p1' => $model->subject,
              'p2' => $model->details,
              'plain_text' => $model->subject.'...'.Html::a(Yii::t('frontend','Open the ticket'),$link)
            ];
            $button= [
              'text' => Yii::t('frontend','Open the Ticket'),
              'command' => Meeting::COMMAND_VIEW_TICKET,
              'obj_id' => $model->id,
            ];
            // Build the absolute links to the meeting and commands
            $links=[
              'home'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_HOME,0,$u->id,$u->auth_key,0),
              'view'=>MiscHelpers::buildCommand($model->id,Meeting::COMMAND_VIEW_TICKET,$model->id,$u->id,$u->auth_key,0),
              'footer_email'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_FOOTER_EMAIL,0,$u->id,$u->auth_key,0),
              'footer_block'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_FOOTER_BLOCK,$u->id,$u->id,$u->auth_key,0),
              'footer_block_all'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_FOOTER_BLOCK_ALL,0,$u->id,$u->auth_key,0),
            ];

            if ($button!==false) {
              $links['button_url']=MiscHelpers::buildCommand(0,$button['command'],$button['obj_id'],$u->id,$u->auth_key);

              $content['button_text']=$button['text'];
            }
            if ($link<>'') {
                // send the message
                $message = Yii::$app->mailer->compose([
                  'html' => 'generic-html',
                  'text' => 'generic-text'
                ],
                [
                  'meeting_id' => 0,
                  'sender_id'=> $model->posted_by,
                  'user_id' => $model->posted_by,
                  'auth_key' => $u->auth_key,
                  'content'=>$content,
                  'links'=>$links,
                  'button'=>$button,
              ]);
                // to do - add full name
              $message->setFrom(array('support@meetingplanner.io'=>'Support'));
              $message->setReplyTo('support@meetingplanner.io');
              $message->setTo($u->email)
                  ->setSubject($content['subject'])
                  ->send();
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionClose($id)
    {
        $t = Ticket::findOne($id);
        $t->status = Ticket::STATUS_CLOSED;
        $t->update();
        Yii::$app->session->setFlash('success', 'Thank you, we\'ve closed this ticket. Let us know when we can help you again.');
        return $this->redirect(['index']);
    }

    /**
     * Updates an existing Ticket model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Ticket model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Ticket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ticket::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
