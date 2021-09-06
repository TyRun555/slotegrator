<?php

namespace app\controllers;

use app\controllers\base\BaseController;
use app\models\factory\Prize\PrizeFactory;
use app\models\factory\Prize\type\PrizeItem;
use app\services\GameService;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Cookie;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\form\UserLoginForm;
use app\models\form\PrizeDeliveryForm;

class SiteController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays game page.
     *
     * @return string - view string
     * @throws Exception - if something wrong with game service
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->render('index');
        }
        if ($this->request->isPost) {
            $gameService = new GameService();
            if ($this->request->post('play')) {
                
                $prize = $gameService->getPrize();
                return $this->render('win', compact('prize'));
                
            } elseif ($this->request->post('replay')) {
                
                $gameService->releaseCurrent();
                $prize = $gameService->getPrize();
                return $this->render('win', compact('prize'));
                
            } elseif ($this->request->post('accept')) {
                $prize = $gameService->acceptCurrent();
                return $this->render('accept', compact('prize'));
                
            }

        }
        return $this->render('game');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new UserLoginForm();
        if ($model->load($this->request->post())) {
            if ($model->login())
                return $this->redirect(Url::home());
            else {
                $model->password = '';
            }
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new PrizeDeliveryForm();
        if ($model->load($this->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
