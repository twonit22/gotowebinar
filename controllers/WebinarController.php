<?php

namespace app\controllers;

use Yii;
use app\models\Webinar;
use app\models\WebinarSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\httpclient\Client;
use DateTime;
use DateTimeZone;

define('CLIENT_ID', Yii::$app->params['clientId']);
define('CLIENT_SECRET', Yii::$app->params['clientSecret']);
define('REDIRECT_URI', Yii::$app->params['redirectUri']);
define('ORGANIZER_KEY', Yii::$app->params['organizerKey']);

class WebinarController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * /webinar
     */
    public function actionIndex()
    {
      $models = Webinar::find()->all();

      return $this->render('index', [
          'models' => $models,
      ]);
    }

    /**
     * /webinar/view/{0}
     */
    public function actionView($event_id)
    {
      $model = $this->findModel($event_id);

      return $this->render('view', [
          'model' => $model,
      ]);
    }

    /**
     * /webinar/create
     */
    public function actionCreate()
    {
        $model = new Webinar();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (empty($model->start_time)) {
                $model->start_time = date('Y-m-d H:i:s', strtotime('+2 hours'));
            }
            if (empty($model->end_time)) {
                $model->end_time = date('Y-m-d H:i:s', strtotime('+12 hours'));
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Webinar details saved locally.');
                return $this->redirect(['view', 'event_id' => $model->event_id]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * /webinar/update/{0}
     */
    public function actionUpdate($event_id)
    {
      $model = $this->findModel($event_id);

      if ($model->load(Yii::$app->request->post()) && $model->save()) {

        if ($model->webinar_key)
            $this->actionUpdateGoToWebinar($model->event_id);
        else
            Yii::$app->session->setFlash('error', 'This webinar has no key. Please add this to GoToWebinar first.');

        return $this->redirect(['view', 'event_id' => $model->event_id]);
      }

      return $this->render('update', [
          'model' => $model,
      ]);
    }

    /**
     * /webinar/delete/{0}
     */
    public function actionDelete($event_id)
    {
        $this->actionDeleteGoToWebinar($event_id);
        $this->findModel($event_id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($event_id)
    {
      if (($model = Webinar::findOne(['event_id' => $event_id])) !== null) {
        return $model;
      }

      throw new NotFoundHttpException('The requested page does not exist.');
    }    

    /**
     * get auth code
     */
    public function actionAuthorize()
    {
        $authUrl = "https://api.getgo.com/oauth/v2/authorize?response_type=code&client_id=". CLIENT_ID ."&redirect_uri=". REDIRECT_URI;
        return $this->redirect($authUrl);
    }

    /**
     * auth code -> access token
     */
    public function actionCallback()
    {
        $code = Yii::$app->request->get('code');

        if (!$code) {
            Yii::$app->session->setFlash('error', 'Authorization code is missing.');
            return $this->redirect(['authorize']);
        }

        $accessTokenData = $this->getAccessToken($code);

        if ($accessTokenData && isset($accessTokenData['access_token'])) {
            Yii::$app->session->set('access_token', $accessTokenData['access_token']);
            Yii::$app->session->set('refresh_token', $accessTokenData['refresh_token'] ?? null);

            Yii::$app->session->setFlash('success', 'Authorization successful!');
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('error', 'Authorization failed. Please try again.');
            return $this->redirect(['index']);
        }
    }

    /**
     * get access token
     */
    private function getAccessToken($code)
    {
        $client = new Client();
        $authHeader = base64_encode(CLIENT_ID .":". CLIENT_SECRET);
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://api.getgo.com/oauth/v2/token')
            ->setHeaders([
                'Authorization' => 'Basic ' . $authHeader,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])
            ->setData([
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => CLIENT_ID,
                'client_secret' => CLIENT_SECRET,
                'redirect_uri' => REDIRECT_URI,
            ])
            ->send();

        return $response->isOk ? $response->data : null;
    }

    private function refreshAccessToken()
    {
        $refreshToken = Yii::$app->session->get('refresh_token');

        if (!$refreshToken) {
            Yii::error('Refresh token is missing.', 'webinar');
            return false;
        }

        $client = new \yii\httpclient\Client();
        $authHeader = base64_encode(CLIENT_ID .":". CLIENT_SECRET);
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://api.getgo.com/oauth/v2/token')
            ->setHeaders([
                'Authorization' => 'Basic ' . $authHeader,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])
            ->setData([
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => CLIENT_ID,
                'client_secret' => CLIENT_SECRET,
            ])
            ->send();

        if ($response->isOk) {
            $data = $response->data;

            Yii::$app->session->set('access_token', $data['access_token']);
            Yii::$app->session->set('refresh_token', $data['refresh_token']);
            return true;
        } else {
            Yii::$app->session->setFlash('error', 'Unable to refresh access token.');
            // return false;
        }
    }


    /**
     * /webinar/view/{0}
     * button: Create Webinar in GoToWebinar
     */
    public function actionCreateGotoWebinar($event_id = null)
    {
        $event_id = $event_id ?? Yii::$app->request->post('event_id');
        $start_time = date('Y-m-d\TH:i:s\Z', strtotime('+2 hours'));
        $end_time = date('Y-m-d\TH:i:s\Z', strtotime('+12 hours'));

        if (!$event_id) {
            throw new \yii\web\BadRequestHttpException("Missing required parameter: event_id");
        }

        $accessToken = Yii::$app->session->get('access_token');
        if (!$accessToken) {
            if (!$accessToken) {
                Yii::$app->session->setFlash('error', 'Authorization required.');
                return $this->redirect(['authorize']);
            }
        }

        $model = Webinar::findOne($event_id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException("Webinar with ID {$event_id} not found.");
        }

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://api.getgo.com/G2W/rest/v2/organizers/'. ORGANIZER_KEY .'/webinars')
            ->setHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])
            ->setContent(json_encode([
                'subject' => $model->name,
                'description' => $model->description,
                'times' => [[
                    'startTime' => $start_time,
                    'endTime' => $end_time,
                ]],
                'timeZone' => 'America/New_York'
            ]))
            ->send();

        if ($response->isOk) {
            $webinarData = $response->data;
            Yii::$app->session->setFlash('success', 'Webinar added to GoToWebinar successfully!');

            $model->webinar_key = $webinarData['webinarKey'] ?? null;
            $model->start_time = $start_time;
            $model->end_time = $end_time;
            $model->save(false);

            return $this->redirect(['view', 'event_id' => $model->event_id]);
        } else {
            if ($this->refreshAccessToken())
                $this->actionCreateGotoWebinar($event_id);
            else
                Yii::$app->session->setFlash('error', 'Failed to create webinar from GoToWebinar. ' . $response);
        }

        return $this->render('view', ['model' => $model]);
    }

    /**
     * /webinar/update/{0}
     * updates the database and GoToWebinar
     */
    public function actionUpdateGoToWebinar($event_id)
    {   
        $start_time = date('Y-m-d\TH:i:s\Z', strtotime('+2 hours'));
        $end_time = date('Y-m-d\TH:i:s\Z', strtotime('+12 hours'));

        $model = Webinar::findOne($event_id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException("Webinar with ID {$event_id} not found.");
        }

        $accessToken = Yii::$app->session->get('access_token');
        if (!$accessToken) {
            Yii::error('Access token is missing.', 'webinar');
            return $this->redirect(['authorize']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('PUT')
                ->setUrl("https://api.getgo.com/G2W/rest/v2/organizers/". ORGANIZER_KEY ."/webinars/{$model->webinar_key}")
                ->setHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->setContent(json_encode([
                    'subject' => $model->name,
                    'description' => $model->description,
                    'times' => [[
                        'startTime' => $start_time,
                        'endTime' => $end_time,
                    ]],
                    'timeZone' => 'America/New_York',
                ]))
                ->send();

            if ($response->isOk) {
                Yii::$app->session->setFlash('success', 'Webinar updated on GoToWebinar successfully!');

                $model->start_time = $start_time;
                $model->end_time = $end_time;
                $model->save(false);

                return $this->redirect(['view', 'event_id' => $model->event_id]);
            } else {
                if ($this->refreshAccessToken())
                    $this->actionUpdateGoToWebinar($event_id);
                else
                    Yii::$app->session->setFlash('error', 'Failed to update webinar from GoToWebinar. ' . $response);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * /webinar/delete/{0}
     * deletes the database entry and GoToWebinar entry
     */
    public function actionDeleteGoToWebinar($event_id)
    {
        $model = Webinar::findOne($event_id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException("Webinar with ID {$event_id} not found.");
        }

        $accessToken = Yii::$app->session->get('access_token');
        if (!$accessToken) {
            Yii::error('Access token is missing.', 'webinar');
            return $this->redirect(['authorize']);
        }

        $client = new \yii\httpclient\Client();
        $response = $client->createRequest()
            ->setMethod('DELETE')
            ->setUrl("https://api.getgo.com/G2W/rest/v2/organizers/". ORGANIZER_KEY ."/webinars/{$model->webinar_key}")
            ->setHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ])
            ->send();

        if ($response->isOk) {
            $model->delete();

            Yii::$app->session->setFlash('success', 'Webinar deleted successfully from GoToWebinar and database.');
        } else {
            if ($this->refreshAccessToken())
                $this->actionDeleteGoToWebinar($event_id);
            else
            Yii::$app->session->setFlash('error', 'Failed to delete webinar from GoToWebinar. ' . $response);
        }

        return $this->redirect(['index']);
    }


}
